<?php
/**
 * ====================================================================
 * WEB API FOR XERI GAME - ADISE25 (FINAL CLEAN VERSION)
 * ====================================================================
 */

// 1. ΕΛΕΓΧΟΣ ΠΕΡΙΒΑΛΛΟΝΤΟΣ (Laptop vs Server)
$is_localhost = ($_SERVER['REMOTE_ADDR'] == '127.0.0.1' || $_SERVER['REMOTE_ADDR'] == '::1' || $_SERVER['SERVER_NAME'] == 'localhost');

if ($is_localhost) {
    // Ρυθμίσεις για LAPTOP (XAMPP)
    define('DB_SERVER', 'localhost');
    define('DB_USERNAME', 'root');
    define('DB_PASSWORD', ''); 
    define('DB_NAME', 'iee2019131'); 
} else {
    // Ρυθμίσεις για SERVER (IEE IHU)
    define('DB_SERVER', 'localhost');
    define('DB_USERNAME', 'iee2019131');
    define('DB_PASSWORD', 'Kodikosieeihu4535*');
    define('DB_NAME', 'iee2019131');
}

// Ενεργοποίηση αναφοράς σφαλμάτων
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

try {
    $link = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);
    $link->set_charset("utf8mb4");
} catch (mysqli_sql_exception $e) {
    header('Content-Type: application/json');
    http_response_code(500);
    echo json_encode([
        "status" => "error", 
        "message" => "DB Connection Failed: " . $e->getMessage(),
        "env" => ($is_localhost ? "Local/Laptop" : "University Server")
    ]);
    exit();
}

// 2. HEADERS & CORS
header('Content-Type: application/json');
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit();
}

// --- HELPER FUNCTIONS ---

function get_player_id_from_token($link) {
    $headers = function_exists('apache_request_headers') ? apache_request_headers() : [];
    if (empty($headers)) {
        foreach ($_SERVER as $name => $value) {
            if (substr($name, 0, 5) == 'HTTP_') {
                $headers[str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr($name, 5)))))] = $value;
            }
        }
    }
    $auth_header = $headers['Authorization'] ?? $headers['authorization'] ?? '';
    $token = str_replace('Bearer ', '', $auth_header);
    if (!$token) return null;

    $stmt = $link->prepare("SELECT player_id FROM players WHERE token = ?");
    $stmt->bind_param("s", $token);
    $stmt->execute();
    $res = $stmt->get_result()->fetch_assoc();
    $stmt->close();
    return $res ? $res['player_id'] : null;
}

function load_game_state($link, $game_id) {
    $stmt = $link->prepare("SELECT * FROM games WHERE game_id = ?");
    $stmt->bind_param("i", $game_id);
    $stmt->execute();
    $data = $stmt->get_result()->fetch_assoc();
    $stmt->close();
    if ($data) {
        $data['board_state'] = json_decode($data['board_state'], true);
        $data['board_state']['player1_id'] = $data['player1_id'];
        $data['board_state']['player2_id'] = $data['player2_id'];
    }
    return $data;
}

function update_game_state_and_turn($link, $game_id, $current_player_id, $new_state_container) {
    $board = $new_state_container['board_state'];
    $next_player_id = ($current_player_id == $board['player1_id']) ? $board['player2_id'] : $board['player1_id'];
    $board_json = json_encode($board);
    $stmt = $link->prepare("UPDATE games SET board_state = ?, current_turn = ?, last_move = NOW() WHERE game_id = ?");
    $stmt->bind_param("sii", $board_json, $next_player_id, $game_id);
    $success = $stmt->execute();
    $stmt->close();
    return $success;
}

function get_card_rank($card_string) {
    return substr($card_string, 0, -1);
}

// ΣΥΜΠΕΡΙΛΗΨΗ ΛΟΓΙΚΗΣ
if (file_exists('game_logic.php')) {
    require_once 'game_logic.php';
} else {
    echo json_encode(["status" => "error", "message" => "game_logic.php is missing."]);
    exit();
}

// --- ROUTING ---
$path = trim($_SERVER['PATH_INFO'] ?? $_SERVER['REDIRECT_URL'] ?? '', '/');
$path = str_replace(['api/v1/', 'api.php/'], '', $path);
$parts = explode('/', $path);
$resource = $parts[0] ?? ''; 
$id = $parts[1] ?? null; 
$action = $parts[2] ?? null;

// --- API ENDPOINTS ---
switch ($resource) {
    case 'auth':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $token = bin2hex(random_bytes(16));
            $stmt = $link->prepare("INSERT INTO players (token) VALUES (?)");
            $stmt->bind_param("s", $token);
            if ($stmt->execute()) {
                echo json_encode(["status" => "success", "token" => $token, "player_id" => $stmt->insert_id]);
            }
            $stmt->close();
        }
        break;

    case 'games':
        $player_id = get_player_id_from_token($link);
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && !$id) {
            if (!$player_id) { http_response_code(401); exit; }
            $initial_state = initialize_game();
            $state_json = json_encode($initial_state);
            $stmt = $link->prepare("INSERT INTO games (player1_id, current_turn, board_state, status) VALUES (?, ?, ?, 'waiting')");
            $stmt->bind_param("iis", $player_id, $player_id, $state_json);
            if ($stmt->execute()) {
                echo json_encode(["status" => "waiting", "game_id" => $stmt->insert_id, "board_state" => $initial_state]);
            }
        } elseif ($_SERVER['REQUEST_METHOD'] === 'POST' && $id && $action === 'join') {
            if (!$player_id) { http_response_code(401); exit; }
            $stmt = $link->prepare("UPDATE games SET player2_id = ?, status = 'active' WHERE game_id = ? AND player1_id != ? AND player2_id IS NULL");
            $stmt->bind_param("iii", $player_id, $id, $player_id);
            if ($stmt->execute() && $link->affected_rows > 0) {
                echo json_encode(["status" => "active", "message" => "Player 2 joined."]);
            } else {
                echo json_encode(["status" => "error", "message" => "Cannot join game."]);
            }
        } elseif ($_SERVER['REQUEST_METHOD'] === 'GET' && $id) {
            $game = load_game_state($link, $id);
            if ($game) {
                unset($game['board_state']['deck']); 
                echo json_encode($game);
            } else {
                http_response_code(404);
            }
        } elseif ($_SERVER['REQUEST_METHOD'] === 'POST' && $id && $action === 'move') {
            if (!$player_id) { http_response_code(401); exit; }
            $game_data = load_game_state($link, $id);
            if ($game_data['current_turn'] != $player_id) {
                echo json_encode(["status" => "error", "message" => "Not your turn."]);
                exit;
            }
            $input = json_decode(file_get_contents('php://input'), true);
            $move_res = apply_move_and_check_rules($game_data, $player_id, $input['player_card'], $input['table_cards'] ?? []);
            if ($move_res['error']) {
                echo json_encode(["status" => "error", "message" => $move_res['error']]);
            } else {
                $opp_id = ($player_id == $game_data['player1_id']) ? $game_data['player2_id'] : $game_data['player1_id'];
                $end_check = check_end_of_round_or_game($move_res['board_state'], $player_id, $opp_id);
                if ($end_check['status'] === 'ended') {
                    $scores = calculate_final_score($end_check['board_state']);
                    $stmt = $link->prepare("UPDATE games SET board_state = ?, status = 'ended' WHERE game_id = ?");
                    $json = json_encode($end_check['board_state']);
                    $stmt->bind_param("si", $json, $id);
                    $stmt->execute();
                    echo json_encode(["status" => "ended", "final_scores" => $scores, "board_state" => $end_check['board_state']]);
                } else {
                    update_game_state_and_turn($link, $id, $player_id, $end_check);
                    echo json_encode(["status" => "active", "board_state" => $end_check['board_state']]);
                }
            }
        }
        break;

    default:
        http_response_code(404);
        echo json_encode(["status" => "error", "message" => "Endpoint not found."]);
        break;
}

$link->close();
?>