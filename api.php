<?php
/**
 * ====================================================================
 * WEB API FOR XERI GAME - ADISE25 (LOCAL & SERVER COMPATIBLE)
 * ====================================================================
 */

// 1. ΡΥΘΜΙΣΕΙΣ ΣΦΑΛΜΑΤΩΝ & HEADERS
ini_set('display_errors', 1);
error_reporting(E_ALL);

header('Content-Type: application/json');
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

// 2. ΔΥΝΑΜΙΚΗ ΣΥΝΔΕΣΗ ΜΕ ΤΗ ΒΑΣΗ
$is_server = (gethostname() == 'users.iee.ihu.gr');

if ($is_server) {
    // ΡΥΘΜΙΣΕΙΣ ΓΙΑ ΤΟΝ SERVER ΤΗΣ ΣΧΟΛΗΣ
    $db_host = 'localhost';
    $db_user = 'root'; 
    $db_pass = 'Kodikosmysql123!'; 
    $db_name = 'iee2019131_db';
    $socket  = '/home/student/iee/2019/iee2019131/mysql/run/mysql.sock';
    
    $mysqli = new mysqli($db_host, $db_user, $db_pass, $db_name, null, $socket);
} else {
    // ΡΥΘΜΙΣΕΙΣ ΓΙΑ ΤΟΠΙΚΗ ΠΑΡΟΥΣΙΑΣΗ (XAMPP / WAMP)
    $db_host = '127.0.0.1';
    $db_user = 'root'; 
    $db_pass = ''; // Στο XAMPP ο κωδικός είναι συνήθως κενός
    $db_name = 'xeri_db'; // Βεβαιώσου ότι έχεις φτιάξει αυτή τη βάση τοπικά
    
    $mysqli = new mysqli($db_host, $db_user, $db_pass, $db_name);
}

// Έλεγχος σφάλματος σύνδεσης
if ($mysqli->connect_errno) {
    echo json_encode([
        "status" => "error",
        "message" => "Database Connection Failed: " . $mysqli->connect_error,
        "environment" => ($is_server ? "Server" : "Local")
    ]);
    exit();
}

// 3. ΕΝΣΩΜΑΤΩΣΗ ΛΟΓΙΚΗΣ ΠΑΙΧΝΙΔΙΟΥ
if (file_exists('game_logic.php')) {
    require_once 'game_logic.php';
} else {
    echo json_encode(["status" => "error", "message" => "game_logic.php missing"]);
    exit();
}

// 4. ΒΟΗΘΗΤΙΚΕΣ ΣΥΝΑΡΤΗΣΕΙΣ
function get_bearer_token() {
    // Συμβατότητα για διαφορετικούς Web Servers
    $headers = array_change_key_case(getallheaders(), CASE_LOWER);
    if (isset($headers['authorization'])) {
        if (preg_match('/bearer\s(\S+)/', $headers['authorization'], $matches)) {
            return $matches[1];
        }
    }
    return null;
}

function get_player_id_by_token($mysqli, $token) {
    $stmt = $mysqli->prepare("SELECT player_id FROM players WHERE token = ?");
    $stmt->bind_param("s", $token);
    $stmt->execute();
    $res = $stmt->get_result();
    return $res->fetch_assoc()['player_id'] ?? null;
}

function get_card_rank($card) {
    return substr($card, 0, -1);
}

// 5. ROUTING LOGIC
$method = $_SERVER['REQUEST_METHOD'];
$request_uri = $_SERVER['REQUEST_URI'];
$token = get_bearer_token();
$current_player_id = $token ? get_player_id_by_token($mysqli, $token) : null;

// --- ENDPOINT: AUTH (Δημιουργία Παίκτη) ---
if (strpos($request_uri, 'auth') !== false && $method == 'POST') {
    $new_token = bin2hex(random_bytes(16));
    $stmt = $mysqli->prepare("INSERT INTO players (token) VALUES (?)");
    $stmt->bind_param("s", $new_token);
    $stmt->execute();
    echo json_encode(["status" => "success", "token" => $new_token, "player_id" => $mysqli->insert_id]);
    exit();
}

// --- ENDPOINT: CREATE GAME ---
if (preg_match('/\/games$/', $request_uri) && $method == 'POST') {
    if (!$current_player_id) die(json_encode(["status" => "error", "message" => "Unauthorized"]));

    $initial_state = initialize_game();
    $board_json = json_encode($initial_state);
    
    $stmt = $mysqli->prepare("INSERT INTO games (player1_id, status, current_turn, board_state) VALUES (?, 'waiting', ?, ?)");
    $stmt->bind_param("iis", $current_player_id, $current_player_id, $board_json);
    $stmt->execute();
    
    echo json_encode(["status" => "waiting", "game_id" => $mysqli->insert_id, "board_state" => $initial_state]);
    exit();
}

// --- ENDPOINT: JOIN GAME ---
if (preg_match('/\/games\/(\d+)\/join$/', $request_uri, $matches) && $method == 'POST') {
    $game_id = $matches[1];
    if (!$current_player_id) die(json_encode(["status" => "error", "message" => "Unauthorized"]));

    $stmt = $mysqli->prepare("UPDATE games SET player2_id = ?, status = 'active' WHERE game_id = ? AND player1_id != ? AND status = 'waiting'");
    $stmt->bind_param("iii", $current_player_id, $game_id, $current_player_id);
    $stmt->execute();

    if ($mysqli->affected_rows > 0) {
        echo json_encode(["status" => "active", "message" => "Joined successfully"]);
    } else {
        echo json_encode(["status" => "error", "message" => "Cannot join game"]);
    }
    exit();
}

// --- ENDPOINT: VIEW GAME ---
if (preg_match('/\/games\/(\d+)$/', $request_uri, $matches) && $method == 'GET') {
    $game_id = $matches[1];
    $stmt = $mysqli->prepare("SELECT * FROM games WHERE game_id = ?");
    $stmt->bind_param("i", $game_id);
    $stmt->execute();
    $game = $stmt->get_result()->fetch_assoc();

    if (!$game) die(json_encode(["status" => "error", "message" => "Game not found"]));
    
    echo json_encode([
        "status" => $game['status'],
        "current_turn" => $game['current_turn'],
        "board_state" => json_decode($game['board_state'], true),
        "player1_id" => $game['player1_id'],
        "player2_id" => $game['player2_id']
    ]);
    exit();
}

// --- ENDPOINT: MOVE ---
if (preg_match('/\/games\/(\d+)\/move$/', $request_uri, $matches) && $method == 'POST') {
    $game_id = $matches[1];
    $input = json_decode(file_get_contents('php://input'), true);
    
    $stmt = $mysqli->prepare("SELECT * FROM games WHERE game_id = ?");
    $stmt->bind_param("i", $game_id);
    $stmt->execute();
    $game = $stmt->get_result()->fetch_assoc();

    if (!$game || $game['current_turn'] != $current_player_id) {
        die(json_encode(["status" => "error", "message" => "Not your turn or game not found"]));
    }

    $game_data = [
        'player1_id' => $game['player1_id'],
        'player2_id' => $game['player2_id'],
        'board_state' => json_decode($game['board_state'], true)
    ];

    $move_res = apply_move_and_check_rules($game_data, $current_player_id, $input['player_card'], $input['table_cards']);
    
    if ($move_res['error']) die(json_encode(["status" => "error", "message" => $move_res['error']]));

    $opponent_id = ($current_player_id == $game['player1_id']) ? $game['player2_id'] : $game['player1_id'];
    $round_check = check_end_of_round_or_game($move_res['board_state'], $current_player_id, $opponent_id);
    
    $new_status = $round_check['status'];
    $final_state = $round_check['board_state'];
    $next_turn = $opponent_id;
    $final_scores = ($new_status == 'ended') ? calculate_final_score($final_state) : null;

    $stmt = $mysqli->prepare("UPDATE games SET board_state = ?, current_turn = ?, status = ? WHERE game_id = ?");
    $state_json = json_encode($final_state);
    $stmt->bind_param("sisi", $state_json, $next_turn, $new_status, $game_id);
    $stmt->execute();

    echo json_encode([
        "status" => $new_status,
        "current_turn" => $next_turn,
        "board_state" => $final_state,
        "final_scores" => $final_scores
    ]);
    exit();
}

// 6. DEFAULT RESPONSE
echo json_encode([
    "status" => "online",
    "database" => "connected",
    "environment" => ($is_server ? "IEE Server" : "Local Presentation"),
    "info" => "ADISE25 Xeri API Operational"
]);

$mysqli->close();
?>