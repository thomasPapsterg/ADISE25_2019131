<?php

//ΣΥΝΑΡΤΗΣΗ ΓΙΑ ΤΟ ΑΝΑΚΑΤΕΜΑ ΚΑΙ ΤΟΥ ΜΟΙΡΑΣΜΟΥ ΤΗΣ ΤΡΑΠΟΥΛΑΣ
function initialize_game(){
    $suits = ['H', 'D', 'C', 'S']; // Κούπες, Καρό, Μπαστούνια, Σπαθιά
    $ranks = ['A', '2', '3', '4', '5', '6', '7', '8', '9', '10', 'J', 'Q', 'K']; 

    $deck =[];
    foreach ($suits as $suit){
        foreach($ranks as $rank){
            $deck[] = $rank . $suit;
        }
    }

    shuffle($deck);

    // Αρχικό μοίρασμα (6 φύλλα στον κάθε παίκτη και 4 στο τραπέζι)
    $player1_hand = array_splice($deck, 0, 6);
    $player2_hand = array_splice($deck, 0, 6);
    $table_pile = array_splice($deck, 0, 4);

    // Αρχική κατάσταση του παιχνιδιού
    $initial_state = [
        'deck' => $deck,
        'player1_hand' => $player1_hand,
        'player2_hand' => $player2_hand,
        'table_pile' => $table_pile,
    
        // Στοίβες μαζεμένων χαρτιών (για υπολογισμό πόντων)
        'player1_collected' => [],
        'player2_collected' => [],
        
        'p1_xeri_count' => 0, // Μετρητής Ξερή (10 πόντοι)
        'p1_xeri_jack_count' => 0, // Μετρητής Ξερή με Βαλέ (20 πόντοι)
        'p2_xeri_count' => 0,
        'p2_xeri_jack_count' => 0,
        
        'last_collector_id' => null, // Ο ID του παίκτη που μάζεψε τελευταίος (σημαντικό στο τέλος)
        'game_rounds_left' => 6 // Εάν μοιράζονται 6 φύλλα, τότε 6 γύροι* (αν επαναλαμβάνεται το μοίρασμα)
    ];
    
    return $initial_state;

}

function apply_move_and_check_rules($game_data, $player_id, $player_card, $table_cards_to_collect) {
    $board_state = $game_data['board_state'];
    
    // 1. Αναγνώριση χεριού και στοίβας συλλογής του παίκτη
    $is_p1 = ($player_id == $game_data['player1_id']);
    $player_hand_key = $is_p1 ? 'player1_hand' : 'player2_hand';
    $collected_key = $is_p1 ? 'player1_collected' : 'player2_collected';
    $xeri_count_key = $is_p1 ? 'p1_xeri_count' : 'p2_xeri_count';
    $xeri_jack_count_key = $is_p1 ? 'p1_xeri_jack_count' : 'p2_xeri_jack_count';

    // 2. Έλεγχος: Υπάρχει το φύλλο στο χέρι του παίκτη;
    $card_index = array_search($player_card, $board_state[$player_hand_key]);
    if ($card_index === false) {
        return ['error' => "The card '$player_card' is not in your hand."];
    }
    
    $table_pile_count_before_move = count($board_state['table_pile']);
    $card_rank = get_card_rank($player_card);
    $table_cards_valid = true;
    
    // 3. Αφαίρεση του φύλλου από το χέρι του παίκτη
    unset($board_state[$player_hand_key][$card_index]);
    $board_state[$player_hand_key] = array_values($board_state[$player_hand_key]); // Αναδιοργάνωση του array

    $collected_cards = [$player_card]; // Το φύλλο που παίχτηκε

    // --- ΚΑΝΟΝΕΣ ΚΙΝΗΣΗΣ ---

    if (empty($table_cards_to_collect)) {
        // Περίπτωση Α: Ο παίκτης ΑΠΛΑ ΡΙΧΝΕΙ
        
        if ($table_pile_count_before_move > 0) {
             // Εάν υπάρχει ήδη στοίβα, απλά προσθέτει το φύλλο στην κορυφή.
            $board_state['table_pile'][] = $player_card;
        } else {
             // Το τραπέζι ήταν άδειο. Τοποθετείται το φύλλο.
             $board_state['table_pile'][] = $player_card;
        }
        
        $board_state['last_collector_id'] = null; // Δεν έγινε μάζεμα
        
    } else {
        // Περίπτωση Β: Ο παίκτης ΜΑΖΕΥΕΙ
        
        // 4. Έλεγχος: Είναι όλα τα φύλλα που θέλει να μαζέψει όντως στο τραπέζι;
        foreach ($table_cards_to_collect as $card) {
            if (!in_array($card, $board_state['table_pile'])) {
                return ['error' => "The card '$card' is not available on the table."];
            }
        }

        $top_table_card = $board_state['table_pile'][count($board_state['table_pile']) - 1]; // Το πάνω φύλλο
        $top_card_rank = get_card_rank($top_table_card);
        
        $is_jack = ($card_rank === 'J');
        $is_matching = ($card_rank === $top_card_rank);

        // 5. Έλεγχος Κανόνων Μάζεματος
        
        if (!$is_matching && !$is_jack) {
            return ['error' => "Invalid move: Player card must match the top card or be a Jack (Βαλέ)."];
        }

        // 6. Εκτέλεση Μάζεματος
        
        // Προσθέτουμε τα μαζεμένα φύλλα στη στοίβα συλλογής
        $collected_cards = array_merge($collected_cards, $table_cards_to_collect);
        $board_state[$collected_key] = array_merge($board_state[$collected_key], $collected_cards);

        // Αφαιρούμε τα μαζεμένα φύλλα από τη στοίβα του τραπεζιού
        $board_state['table_pile'] = array_diff($board_state['table_pile'], $table_cards_to_collect);
        $board_state['table_pile'] = array_values($board_state['table_pile']); // Αναδιοργάνωση

        // Ενημέρωση τελευταίου παίκτη που μάζεψε
        $board_state['last_collector_id'] = $player_id;
        
        // 7. Έλεγχος για Ξερή
        if ($table_pile_count_before_move === 1 && count($table_cards_to_collect) === 1) {
            // Συνθήκη Ξερής: Υπήρχε μόνο 1 φύλλο και ο παίκτης το μάζεψε
            if ($is_jack) {
                // Ξερή με Βαλέ (20 πόντοι)
                $board_state[$xeri_jack_count_key]++;
            } else {
                // Κανονική Ξερή (10 πόντοι)
                $board_state[$xeri_count_key]++;
            }
        }
        
        // Ειδικός κανόνας: Αν το τραπέζι είναι τώρα άδειο, ο παίκτης κερδίζει.
        // Αν το τραπέζι άδειασε, δεν έγινε Ξερή (αν είχε >1 φύλλα), αλλά ο παίκτης θα πάρει τα τυχόν φύλλα που έμειναν στο τέλος.
    }
    
    // 8. Έλεγχος Τέλους Γύρου/Παιχνιδιού (θα γίνει στο update_game_state_and_turn)
    
    return ['board_state' => $board_state, 'error' => null];
}

/**
 * Ελέγχει αν πρέπει να γίνει ανανέωση χεριών (τέλος γύρου) ή αν το παιχνίδι τελείωσε (τέλος παρτίδας).
 */
function check_end_of_round_or_game($board_state, $player_id, $opponent_id) {
    
    $p_hand_key = ($player_id == $board_state['player1_id']) ? 'player1_hand' : 'player2_hand';
    $opp_hand_key = ($opponent_id == $board_state['player1_id']) ? 'player1_hand' : 'player2_hand';

    // Έλεγχος: Τέλος Γύρου (και τα δύο χέρια είναι άδεια)
    if (empty($board_state[$p_hand_key]) && empty($board_state[$opp_hand_key])) {
        
        // 1. ΤΕΛΟΣ ΠΑΡΤΙΔΑΣ; (Δεν υπάρχουν άλλα φύλλα στην τράπουλα)
        if (empty($board_state['deck'])) {
            
            // Το παιχνίδι τελείωσε. Ο τελευταίος που μάζεψε παίρνει τα φύλλα του τραπεζιού.
            if ($board_state['last_collector_id'] !== null && !empty($board_state['table_pile'])) {
                
                $collector_key = ($board_state['last_collector_id'] == $board_state['player1_id']) ? 'player1_collected' : 'player2_collected';
                
                // Μεταφορά φύλλων τραπεζιού στη στοίβα του τελευταίου νικητή
                $board_state[$collector_key] = array_merge($board_state[$collector_key], $board_state['table_pile']);
                $board_state['table_pile'] = []; // Άδειασμα τραπεζιού
            }
            
            return [
                'status' => 'ended', 
                'board_state' => $board_state
            ];
            
        } else {
            // 2. ΤΕΛΟΣ ΓΥΡΟΥ (Υπάρχουν ακόμα φύλλα για μοίρασμα)
            
            $num_to_deal = 6; // Μοιράζουμε ξανά 6 φύλλα
            
            if (count($board_state['deck']) >= $num_to_deal * 2) {
                // Μοίρασμα νέων φύλλων
                $new_p1_cards = array_splice($board_state['deck'], 0, $num_to_deal);
                $new_p2_cards = array_splice($board_state['deck'], 0, $num_to_deal);
                
                $board_state['player1_hand'] = $new_p1_cards;
                $board_state['player2_hand'] = $new_p2_cards;
                
                // Μείωση μετρητή γύρων (προαιρετικό αν θέλουμε να μετρήσουμε γύρους)
                $board_state['game_rounds_left']--;
            }
            
            return [
                'status' => 'active', 
                'board_state' => $board_state
            ];
        }
    }
    
    // Το παιχνίδι συνεχίζεται κανονικά
    return [
        'status' => 'active', 
        'board_state' => $board_state
    ];
}

/**
 * Υπολογίζει την τελική βαθμολογία με βάση τους κανόνες της Ξερής.
 */
function calculate_final_score($board_state) {
    $p1_score = 0;
    $p2_score = 0;
    
    $p1_collected = $board_state['player1_collected'];
    $p2_collected = $board_state['player2_collected'];
    
    $p1_cards_count = count($p1_collected);
    $p2_cards_count = count($p2_collected);

    // --- 1. Βαθμολογία Ξερής (10 & 20 πόντοι) ---
    $p1_score += ($board_state['p1_xeri_count'] * 10);
    $p1_score += ($board_state['p1_xeri_jack_count'] * 20);
    $p2_score += ($board_state['p2_xeri_count'] * 10);
    $p2_score += ($board_state['p2_xeri_jack_count'] * 20);

    // --- 2. Ειδικά Χαρτιά & Φιγούρες (1 πόντος έκαστο) ---
    $p1_tens_figures = 0;
    $p2_tens_figures = 0;
    
    foreach ($p1_collected as $card) {
        $rank = get_card_rank($card);
        $suit = substr($card, -1);
        
        // 2α. 2 Σπαθί (S2)
        if ($card === '2S') $p1_score += 1;
        // 2β. 10 Καρό (D10)
        if ($card === '10D') $p1_score += 1;
        
        // 2γ. Φιγούρες (K, Q, J, 10, εκτός του 10 Καρό)
        if (in_array($rank, ['K', 'Q', 'J', '10']) && $card !== '10D') {
            $p1_score += 1;
        }
    }

    foreach ($p2_collected as $card) {
        $rank = get_card_rank($card);
        $suit = substr($card, -1);
        
        if ($card === '2S') $p2_score += 1;
        if ($card === '10D') $p2_score += 1;
        
        if (in_array($rank, ['K', 'Q', 'J', '10']) && $card !== '10D') {
            $p2_score += 1;
        }
    }

    // --- 3. Περισσότερα Χαρτιά (3 πόντοι) ---
    $winner_cards = 0;
    if ($p1_cards_count > $p2_cards_count) {
        $p1_score += 3;
        $winner_cards = $board_state['player1_id'];
    } elseif ($p2_cards_count > $p1_cards_count) {
        $p2_score += 3;
        $winner_cards = $board_state['player2_id'];
    }
    // Εάν είναι ίσα, κανένας δεν παίρνει τους 3 πόντους.

    $final_scores = [
        'player1_score' => $p1_score,
        'player2_score' => $p2_score,
        'winner_id' => ($p1_score > $p2_score) ? $board_state['player1_id'] : (($p2_score > $p1_score) ? $board_state['player2_id'] : null)
    ];

    return $final_scores;
}