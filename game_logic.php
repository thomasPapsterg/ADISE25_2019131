<?php
declare(strict_types=1);

function get_card_rank(string $card): string {
    // π.χ. "10D" -> "10", "7S" -> "7", "JC" -> "J"
    return substr($card, 0, -1);
}

function initialize_game(): array {
    $suits = ['H', 'D', 'C', 'S'];
    $ranks = ['A','2','3','4','5','6','7','8','9','10','J','Q','K'];

    $deck = [];
    foreach ($suits as $suit) {
        foreach ($ranks as $rank) {
            $deck[] = $rank.$suit;
        }
    }
    shuffle($deck);

    $player1_hand = array_splice($deck, 0, 6);
    $player2_hand = array_splice($deck, 0, 6);
    $table_pile   = array_splice($deck, 0, 4);

    return [
        'deck' => $deck,

        'player1_hand' => $player1_hand,
        'player2_hand' => $player2_hand,
        'table_pile'   => $table_pile,

        'player1_collected' => [],
        'player2_collected' => [],

        'p1_xeri_count' => 0,
        'p1_xeri_jack_count' => 0,
        'p2_xeri_count' => 0,
        'p2_xeri_jack_count' => 0,

        'last_collector_id' => null,
        'game_rounds_left' => 6,
    ];
}

/**
 * Κλασική Ξερή:
 * - Παίζεις ένα φύλλο.
 * - Αν ταιριάζει με το ΠΑΝΩ φύλλο στο τραπέζι (ίδιο rank) ή είναι Βαλές (J) -> μαζεύεις ΟΛΗ τη στοίβα.
 * - Αλλιώς -> το ρίχνεις πάνω στη στοίβα.
 * Σημείωση: table_cards_to_collect αγνοείται (το UI δεν χρειάζεται να κλικάρει τραπέζι).
 */
function apply_move_and_check_rules(array $game_data, int $player_id, string $player_card, array $table_cards_to_collect): array {
    $board_state = $game_data['board_state'];

    $is_p1 = ($player_id == $game_data['player1_id']);
    $player_hand_key = $is_p1 ? 'player1_hand' : 'player2_hand';
    $collected_key   = $is_p1 ? 'player1_collected' : 'player2_collected';
    $xeri_count_key  = $is_p1 ? 'p1_xeri_count' : 'p2_xeri_count';
    $xeri_jack_key   = $is_p1 ? 'p1_xeri_jack_count' : 'p2_xeri_jack_count';

    // υπάρχει στο χέρι;
    $card_index = array_search($player_card, $board_state[$player_hand_key], true);
    if ($card_index === false) {
        return ['error' => "The card '$player_card' is not in your hand."];
    }

    // βγάζουμε από χέρι
    unset($board_state[$player_hand_key][$card_index]);
    $board_state[$player_hand_key] = array_values($board_state[$player_hand_key]);

    $table_count_before = count($board_state['table_pile']);

    // αν τραπέζι άδειο -> απλά ρίχνει
    if ($table_count_before === 0) {
        $board_state['table_pile'][] = $player_card;
        $board_state['last_collector_id'] = null;
        return ['board_state' => $board_state, 'error' => null];
    }

    // πάνω φύλλο
    $top_table_card = $board_state['table_pile'][$table_count_before - 1];
    $top_rank = get_card_rank($top_table_card);
    $rank = get_card_rank($player_card);

    $is_jack = ($rank === 'J');
    $is_matching = ($rank === $top_rank);

    if ($is_jack || $is_matching) {
        // μαζεύει όλη τη στοίβα + το φύλλο του
        $collected_cards = array_merge([$player_card], $board_state['table_pile']);
        $board_state[$collected_key] = array_merge($board_state[$collected_key], $collected_cards);

        // άδειασμα τραπεζιού
        $board_state['table_pile'] = [];
        $board_state['last_collector_id'] = $player_id;

        // Ξερή: αν πριν υπήρχε ΜΟΝΟ 1 φύλλο
        if ($table_count_before === 1) {
            if ($is_jack) $board_state[$xeri_jack_key] += 1;
            else $board_state[$xeri_count_key] += 1;
        }

        return ['board_state' => $board_state, 'error' => null];
    }

    // αλλιώς απλά ρίχνει
    $board_state['table_pile'][] = $player_card;
    $board_state['last_collector_id'] = null;

    return ['board_state' => $board_state, 'error' => null];
}

/**
 * Τέλος γύρου: όταν αδειάσουν και τα δύο χέρια.
 * - Αν δεν υπάρχει deck -> τέλος παιχνιδιού + ο last_collector παίρνει τα φύλλα τραπεζιού.
 * - Αλλιώς μοιράζει 6-6.
 */
function check_end_of_round_or_game(array $game_data, array $board_state, int $player_id, int $opponent_id): array {

    // ΠΟΙΟΣ ΕΙΝΑΙ P1 / P2 απο το game_data (DB fields)
    $p1_id = (int)$game_data['player1_id'];
    $p2_id = (int)$game_data['player2_id'];

    $p_hand_key   = ($player_id === $p1_id) ? 'player1_hand' : 'player2_hand';
    $opp_hand_key = ($opponent_id === $p1_id) ? 'player1_hand' : 'player2_hand';

    // Τέλος γύρου: και τα 2 χέρια άδεια
    if (empty($board_state[$p_hand_key]) && empty($board_state[$opp_hand_key])) {

        // Τέλος παρτίδας: δεν υπάρχουν άλλα φύλλα στην τράπουλα
        if (empty($board_state['deck'])) {

            // ο τελευταίος που μάζεψε παίρνει ό,τι έχει μείνει στο τραπέζι
            if (!empty($board_state['table_pile']) && !empty($board_state['last_collector_id'])) {
                $collector_key = ((int)$board_state['last_collector_id'] === $p1_id) ? 'player1_collected' : 'player2_collected';
                $board_state[$collector_key] = array_merge($board_state[$collector_key], $board_state['table_pile']);
                $board_state['table_pile'] = [];
            }

            return ['status' => 'ended', 'board_state' => $board_state];
        }

        // Νέο μοίρασμα (έχει ακόμα deck)
        $num = 6;
        if (count($board_state['deck']) >= $num * 2) {
            $board_state['player1_hand'] = array_splice($board_state['deck'], 0, $num);
            $board_state['player2_hand'] = array_splice($board_state['deck'], 0, $num);
            $board_state['game_rounds_left'] = max(0, (int)($board_state['game_rounds_left'] ?? 0) - 1);
        }

        return ['status' => 'active', 'board_state' => $board_state];
    }

    return ['status' => 'active', 'board_state' => $board_state];
}

function calculate_live_score(array $board_state): array {
    $p1 = 0;
    $p2 = 0;

    // Ξερές
    $p1 += ((int)$board_state['p1_xeri_count'] * 10);
    $p1 += ((int)$board_state['p1_xeri_jack_count'] * 20);

    $p2 += ((int)$board_state['p2_xeri_count'] * 10);
    $p2 += ((int)$board_state['p2_xeri_jack_count'] * 20);

    // Ειδικά χαρτιά & φιγούρες
    foreach ($board_state['player1_collected'] as $card) {
        $rank = get_card_rank($card);
        if ($card === '2S') $p1 += 1;
        if ($card === '10D') $p1 += 1;
        if (in_array($rank, ['K','Q','J','10'], true) && $card !== '10D') $p1 += 1;
    }

    foreach ($board_state['player2_collected'] as $card) {
        $rank = get_card_rank($card);
        if ($card === '2S') $p2 += 1;
        if ($card === '10D') $p2 += 1;
        if (in_array($rank, ['K','Q','J','10'], true) && $card !== '10D') $p2 += 1;
    }

    return [
        'player1_score' => $p1,
        'player2_score' => $p2
    ];
}



function calculate_final_score(array $game_data, array $board_state): array {
    $p1_id = $game_data['player1_id'];
    $p2_id = $game_data['player2_id'];

    $p1_score = 0;
    $p2_score = 0;

    $p1_collected = $board_state['player1_collected'];
    $p2_collected = $board_state['player2_collected'];

    // Ξερές
    $p1_score += ((int)$board_state['p1_xeri_count'] * 10) + ((int)$board_state['p1_xeri_jack_count'] * 20);
    $p2_score += ((int)$board_state['p2_xeri_count'] * 10) + ((int)$board_state['p2_xeri_jack_count'] * 20);

    // ειδικά / φιγούρες
    foreach ($p1_collected as $card) {
        $rank = get_card_rank($card);
        if ($card === '2S') $p1_score += 1;
        if ($card === '10D') $p1_score += 1;
        if (in_array($rank, ['K','Q','J','10'], true) && $card !== '10D') $p1_score += 1;
    }
    foreach ($p2_collected as $card) {
        $rank = get_card_rank($card);
        if ($card === '2S') $p2_score += 1;
        if ($card === '10D') $p2_score += 1;
        if (in_array($rank, ['K','Q','J','10'], true) && $card !== '10D') $p2_score += 1;
    }

    // περισσότερα χαρτιά +3
    $p1_cnt = count($p1_collected);
    $p2_cnt = count($p2_collected);
    if ($p1_cnt > $p2_cnt) $p1_score += 3;
    elseif ($p2_cnt > $p1_cnt) $p2_score += 3;

    $winner_id = null;
    if ($p1_score > $p2_score) $winner_id = $p1_id;
    elseif ($p2_score > $p1_score) $winner_id = $p2_id;

    return [
        'player1_score' => $p1_score,
        'player2_score' => $p2_score,
        'winner_id' => $winner_id
    ];
}
