<?php
<<<<<<< HEAD
/**
 * ====================================================================
 * WEB API FOR XERI GAME - ADISE25 (FINAL INTEGRATED VERSION)
 * ====================================================================
 */
=======
// Ρυθμίσεις Βάσης Δεδομένων
define('DB_SERVER', 'localhost');
define('DB_USERNAME', 'iee2019131'); // Προεπιλεγμένο XAMPP username
define('DB_PASSWORD', 'Kodikosieeihu4535*');     // Προεπιλεγμένο XAMPP password
define('DB_NAME', 'iee2019131'); // Το όνομα της βάσης που δημιουργήσατε
>>>>>>> 26a59c7774768a54040fbed7f56e002502693112

// Ενεργοποίηση αναφοράς σφαλμάτων για εντοπισμό προβλημάτων (μόνο για ανάπτυξη)
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

// 1. ΡΥΘΜΙΣΕΙΣ ΣΥΝΔΕΣΗΣ ΒΑΣΗΣ ΔΕΔΟΜΕΝΩΝ
// Βελτιωμένος έλεγχος περιβάλλοντος (Laptop vs Server)
$is_localhost = ($_SERVER['REMOTE_ADDR'] == '127.0.0.1' || $_SERVER['REMOTE_ADDR'] == '::1' || $_SERVER['SERVER_NAME'] == 'localhost');

if ($is_localhost) {
    // Ρυθμίσεις για LAPTOP (XAMPP)
    define('DB_SERVER', 'localhost');
    define('DB_USERNAME', 'root');
    define('DB_PASSWORD', ''); 
    define('DB_NAME', 'iee2019131'); // ΠΡΟΣΟΧΗ: Αν στο XAMPP την ονόμασες xeri_db, άλλαξέ το εδώ σε 'xeri_db'
} else {
    // Ρυθμίσεις για SERVER (IEE IHU)
    define('DB_SERVER', 'localhost');
    define('DB_USERNAME', 'iee2019131');
    define('DB_PASSWORD', 'Kodikosieeihu4535*');
    define('DB_NAME', 'iee2019131');
}

try {
    // Δημιουργία σύνδεσης
    $link = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);
    $link->set_charset("utf8mb4");
} catch (mysqli_sql_exception $e) {
    // Αν αποτύχει η σύνδεση, επιστρέφουμε JSON με το ακριβές σφάλμα
    header('Content-Type: application/json');
    http_response_code(500);
    echo json_encode([
        "status" => "error", 
        "message" => "DB Connection Failed: " . $e->getMessage(),
        "debug_info" => [
            "host" => DB_SERVER,
            "user" => DB_USERNAME,
            "database" => DB_NAME,
            "env" => ($is_localhost ? "Local/Laptop" : "University Server")
        ],
        "hint" => "Βεβαιωθείτε ότι η βάση δεδομένων υπάρχει και τα στοιχεία σύνδεσης είναι σωστά."
    ]);
    exit();
}

// 2. HEADERS ΓΙΑ JSON & CORS
header('Content-Type: application/json');
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// --- HELPER FUNCTIONS ---

function get_player_id_from_token($link) {
    // Fallback για headers σε περίπτωση που δεν είναι διαθέσιμη η apache_request_headers
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
    header('Content-Type: application/json');
    echo json_encode(["status" => "error", "message" => "game_logic.php is missing from server."]);
    exit();
}

// --- ΡΟΗ ROUTING ---
$method = $_SERVER['REQUEST_METHOD'];
$request_uri = $_SERVER['REQUEST_URI'];
$script_name = $_SERVER['SCRIPT_NAME']; 

$base_path = str_replace('api.php', '', $script_name);
$path = str_replace($base_path, '', explode('?', $request_uri)[0]);
$path = str_replace(['api.php/', 'api/v1/'], '', trim($path, '/'));
$parts = explode('/', $path);

$resource = $parts[0] ?? ''; 
$id = $parts[1] ?? null;     
$action = $parts[2] ?? null; 

// --- API ENDPOINTS ---

switch ($resource) {
    case 'auth':
        if ($method === 'POST') {
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
        
        if ($method === 'POST' && !$id) {
            if (!$player_id) { http_response_code(401); exit; }
            $initial_state = initialize_game();
            $state_json = json_encode($initial_state);
            $stmt = $link->prepare("INSERT INTO games (player1_id, current_turn, board_state, status) VALUES (?, ?, ?, 'waiting')");
            $stmt->bind_param("iis", $player_id, $player_id, $state_json);
            if ($stmt->execute()) {
                echo json_encode(["status" => "waiting", "game_id" => $stmt->insert_id, "board_state" => $initial_state]);
            }
        }
        elseif ($method === 'POST' && $id && $action === 'join') {
            if (!$player_id) { http_response_code(401); exit; }
            $stmt = $link->prepare("UPDATE games SET player2_id = ?, status = 'active' WHERE game_id = ? AND player1_id != ? AND player2_id IS NULL");
            $stmt->bind_param("iii", $player_id, $id, $player_id);
            if ($stmt->execute() && $link->affected_rows > 0) {
                echo json_encode(["status" => "active", "message" => "Player 2 joined."]);
            } else {
                echo json_encode(["status" => "error", "message" => "Cannot join game. Check if game exists or if you are already in it."]);
            }
        }
        elseif ($method === 'GET' && $id) {
            $game = load_game_state($link, $id);
            if ($game) {
                unset($game['board_state']['deck']); 
                echo json_encode($game);
            } else {
                http_response_code(404);
                echo json_encode(["status" => "error", "message" => "Game not found."]);
            }
        }
        elseif ($method === 'POST' && $id && $action === 'move') {
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
<<<<<<< HEAD
?>
=======


?>
>>>>>>> 26a59c7774768a54040fbed7f56e002502693112
