<?php
// Ρυθμίσεις Βάσης Δεδομένων
define('DB_SERVER', 'localhost');
define('DB_USERNAME', 'root'); // Προεπιλεγμένο XAMPP username
define('DB_PASSWORD', '');     // Προεπιλεγμένο XAMPP password
define('DB_NAME', 'xeri_api'); // Το όνομα της βάσης που δημιουργήσατε

// Δημιουργία σύνδεσης
$link = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);

// Έλεγχος σύνδεσης
if ($link === false) {
    // Αν υπάρχει σφάλμα, το API πρέπει να επιστρέψει JSON σφάλματος
    header('Content-Type: application/json');
    http_response_code(500);
    echo json_encode(["status" => "error", "message" => "Could not connect to database."]);
    exit();
}

// Ρύθμιση κωδικοποίησης χαρακτήρων σε UTF-8
$link->set_charset("utf8mb4");

// Ρύθμιση κεφαλίδων (Headers) για JSON
header('Content-Type: application/json');
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");

// --- HELPER FUNCTIONS ---

// Συνάρτηση για αναγνώριση παίκτη από το token
function get_player_id_from_token($link, $token) {
    $stmt = $link->prepare("SELECT player_id FROM players WHERE token = ?");
    $stmt->bind_param("s", $token);
    $stmt->execute();
    $result = $stmt->get_result();
    $player_id = null;
    if ($row = $result->fetch_assoc()) {
        $player_id = $row['player_id'];
    }
    $stmt->close();
    return $player_id;
}

function load_game_state($link, $game_id) {
    // Επιλέγουμε όλα τα πεδία του παιχνιδιού
    $stmt = $link->prepare("SELECT game_id, player1_id, player2_id, status, current_turn, board_state FROM games WHERE game_id = ?");
    $stmt->bind_param("i", $game_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if (!$game_data = $result->fetch_assoc()) {
        $stmt->close();
        return null; // Το παιχνίδι δεν βρέθηκε
    }
    
    $stmt->close();

    // ΑΠΟΚΑΤΑΣΤΑΣΗ: Μετατροπή του JSON string του board_state σε PHP array
    $game_data['board_state'] = json_decode($game_data['board_state'], true);
    
    // Ενοποίηση του player ID στο board state
    $game_data['board_state']['player1_id'] = $game_data['player1_id'];
    $game_data['board_state']['player2_id'] = $game_data['player2_id'];

    return $game_data;
}


/** Εξάγει την αξία/rank του φύλλου (π.χ. 'H8' -> '8', 'SK' -> 'K') */
function get_card_rank($card_string) {
    // Η αξία είναι ό,τι μένει αν αφαιρέσουμε τον τελευταίο χαρακτήρα (το χρώμα)
    return substr($card_string, 0, -1);
}

function update_game_state_and_turn($link, $game_id, $current_player_id, $new_state_container) {
    // 1. Βρίσκουμε τον επόμενο παίκτη
    $player1_id = $new_state_container['board_state']['player1_id'];
    $player2_id = $new_state_container['board_state']['player2_id'];
    
    // Αλλαγή σειράς:
    $next_player_id = ($current_player_id == $player1_id) ? $player2_id : $player1_id;

    // Ετοιμάζουμε τα δεδομένα για τη βάση
    $board_state_json = json_encode($new_state_container['board_state']);
    
    // 2. Ενημέρωση της βάσης δεδομένων
    $stmt = $link->prepare("UPDATE games SET board_state = ?, current_turn = ?, last_move = NOW() WHERE game_id = ?");
    $stmt->bind_param("sii", $board_state_json, $next_player_id, $game_id);

    // Ελέγχουμε για σφάλματα (προαιρετικό, αλλά καλό)
    if (!$stmt->execute()) {
        error_log("Failed to update game state: " . $stmt->error);
        return false;
    }
    $stmt->close();
    return true;
}


// --- ΣΥΜΠΕΡΙΛΗΨΗ ΛΟΓΙΚΗΣ ΠΑΙΧΝΙΔΙΟΥ ---
require_once 'game_logic.php';

// --- ROUTING ---

$request_method = $_SERVER['REQUEST_METHOD'];
$request_uri = $_SERVER['REQUEST_URI'];

// 2. Καθαρισμός Διαδρομής (Path Cleaning)
// Αφαιρεί το /xeri_api/
$base_path = str_replace('/api.php', '', $_SERVER['SCRIPT_NAME']);
$path = trim(str_replace($base_path, '', $request_uri), '/');

if (strpos($path, 'api.php/') === 0) {
    $path = substr($path, 8); 
}



$parts = explode('/', $path);

// 0. Χειρισμός OPTIONS requests (CORS Preflight)
if ($request_method === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// 1. POST /api/v1/auth (Δημιουργία Παίκτη)
if ($request_method === 'POST' && count($parts) === 3 && $parts[0] === 'api' && $parts[2] === 'auth') {
    $token = bin2hex(random_bytes(16)); 
    $stmt = $link->prepare("INSERT INTO players (token) VALUES (?)");
    $stmt->bind_param("s", $token);

    if ($stmt->execute()) {
        $player_id = $stmt->insert_id;
        http_response_code(201);
        echo json_encode([
            "status" => "success",
            "message" => "Player account created.",
            "player_id" => $player_id,
            "token" => $token
        ]);
    } else {
        http_response_code(500);
        echo json_encode(["status" => "error", "message" => "Database error: " . $stmt->error]);
    }
    $stmt->close();
}

// 2. POST /api/v1/games (Έναρξη Παιχνιδιού)
elseif ($request_method === 'POST' && count($parts) === 3 && $parts[0] === 'api' && $parts[2] === 'games') {
   $headers = getallheaders();
    $auth_header = $headers['Authorization'] ?? '';
    $token = str_replace('Bearer ', '', $auth_header);
    
    $player1_id = get_player_id_from_token($link, $token);
    
    if (!$player1_id) {
        http_response_code(401);
        echo json_encode(["status" => "error", "message" => "Invalid or missing token."]);
        $link->close(); exit();
    }

    // 1. Αρχικοποίηση παιχνιδιού από το game_logic.php
    $initial_board_state = initialize_game(); // <--- Καλεί τη συνάρτηση από το game_logic.php
    $board_state_json = json_encode($initial_board_state);
    
    // 2. Εισαγωγή στη βάση (current_turn = Player 1)
    $stmt = $link->prepare("INSERT INTO games (player1_id, status, current_turn, board_state) VALUES (?, 'waiting', ?, ?)");
    $stmt->bind_param("iis", $player1_id, $player1_id, $board_state_json);

    if ($stmt->execute()) {
        $game_id = $stmt->insert_id;
        http_response_code(201);
        echo json_encode([
            "status" => "waiting",
            "message" => "Game created. Waiting for opponent.",
            "game_id" => $game_id,
            "player1_id" => $player1_id,
            "board_state" => $initial_board_state 
        ]);
    } else {
        http_response_code(500);
        echo json_encode(["status" => "error", "message" => "Could not create game: " . $stmt->error]);
    }
    $stmt->close();
}


// 3. POST /api/v1/games/{game_id}/join (Συμμετοχή Player 2)
elseif ($request_method === 'POST' && count($parts) === 5 && $parts[0] === 'api' && $parts[4] === 'join') {
    $game_id = $parts[3]; 

    // Authorization: Παίρνουμε το token του παίκτη (P2)
    $headers = getallheaders();
    $auth_header = $headers['Authorization'] ?? '';
    $token = str_replace('Bearer ', '', $auth_header);
    
    $player2_id = get_player_id_from_token($link, $token);

    if (!$player2_id) {
        http_response_code(401); 
        echo json_encode(["status" => "error", "message" => "Invalid or missing token in Authorization header."]);
        $link->close(); exit();
    }

    // Έλεγχος κατάστασης παιχνιδιού και παίκτη
    $stmt = $link->prepare("SELECT player1_id, player2_id, status FROM games WHERE game_id = ?");
    $stmt->bind_param("i", $game_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $game_info = $result->fetch_assoc();
    $stmt->close();

    if (!$game_info) {
        http_response_code(404);
        echo json_encode(["status" => "error", "message" => "Game not found."]);
        $link->close(); exit();
    }
    
    // Έλεγχος 1: Είναι ήδη ο ίδιος ο παίκτης ο Player 1;
    if ($game_info['player1_id'] == $player2_id) {
        http_response_code(400);
        echo json_encode(["status" => "error", "message" => "You are already Player 1 in this game."]);
        $link->close(); exit();
    }
    
    // Έλεγχος 2: Είναι το παιχνίδι ήδη πλήρες;
    if ($game_info['player2_id'] !== null) {
        http_response_code(400);
        echo json_encode(["status" => "error", "message" => "Game is already full or active."]);
        $link->close(); exit();
    }
    
    // Έλεγχος 3: Είναι το παιχνίδι σε αναμονή;
    if ($game_info['status'] !== 'waiting') {
        http_response_code(400);
        echo json_encode(["status" => "error", "message" => "Game is not waiting for an opponent."]);
        $link->close(); exit();
    }
    
    // Ενημέρωση Βάσης Δεδομένων: Προσθήκη P2, Αλλαγή status σε 'active'
    $stmt = $link->prepare("UPDATE games SET player2_id = ?, status = 'active' WHERE game_id = ?");
    $stmt->bind_param("ii", $player2_id, $game_id);

    if ($stmt->execute()) {
        http_response_code(200);
        echo json_encode([
            "status" => "active",
            "message" => "Successfully joined game. Player 1 starts.",
            "game_id" => $game_id,
            "player2_id" => $player2_id
        ]);
    } else {
        http_response_code(500);
        echo json_encode(["status" => "error", "message" => "Database update failed."]);
    }
    $stmt->close();
}

// 4. POST /api/v1/games/{game_id}/move (Εκτέλεση Κίνησης)
elseif ($request_method === 'POST' && count($parts) === 5 && $parts[0] === 'api' && $parts[4] === 'move') { // Διόρθωση: Το 'move' είναι το $parts[3]
    $game_id = $parts[3]; // Το ID του παιχνιδιού

    // 1. Authorization & Βασικοί Έλεγχοι
    $headers = getallheaders();
    $auth_header = $headers['Authorization'] ?? '';
    $token = str_replace('Bearer ', '', $auth_header);
    $player_id = get_player_id_from_token($link, $token);

    if (!$player_id) { http_response_code(401); echo json_encode(["status" => "error", "message" => "Invalid token."]); $link->close(); exit(); }
    
    $game_data = load_game_state($link, $game_id);
    
    // Έλεγχος: Βρέθηκε το παιχνίδι, είναι ενεργό και έχει 2 παίκτες
    if (!$game_data || $game_data['status'] !== 'active' || $game_data['player2_id'] === null) { 
        http_response_code(400);
        echo json_encode(["status" => "error", "message" => "Game not found or not ready to play."]);
        $link->close(); exit();
    }
    
    // 2. Έλεγχος Σειράς Παιξιάς (current_turn)
    if ($game_data['current_turn'] != $player_id) {
        http_response_code(403);
        echo json_encode(["status" => "error", "message" => "It's not your turn."]);
        $link->close(); exit();
    }

    // 3. Λήψη δεδομένων κίνησης (JSON body)
    $input = json_decode(file_get_contents('php://input'), true);
    $player_card = $input['player_card'] ?? null;
    $table_cards_to_collect = $input['table_cards'] ?? [];

    if (!$player_card) { http_response_code(400); echo json_encode(["status" => "error", "message" => "Missing player_card in request body."]); $link->close(); exit(); }

    // 4. Εφαρμογή και έλεγχος κανόνων
    $move_result = apply_move_and_check_rules($game_data, $player_id, $player_card, $table_cards_to_collect);

    if ($move_result['error']) { http_response_code(400); echo json_encode(["status" => "error", "message" => $move_result['error']]); $link->close(); exit(); }
    
    
    // --- 5. ΕΛΕΓΧΟΣ DEADLOCK / ΤΕΛΟΥΣ ΓΥΡΟΥ ---
    
    $opponent_id = ($player_id == $game_data['player1_id']) ? $game_data['player2_id'] : $game_data['player1_id'];
    $end_check = check_end_of_round_or_game($move_result['board_state'], $player_id, $opponent_id);

    $final_game_status = $end_check['status'];
    $final_scores = null;
    $update_success = false;

    if ($final_game_status === 'ended') {
        // Τέλος Παιχνιδιού: Υπολογισμός τελικού σκορ και ενημέρωση status
        $final_scores = calculate_final_score($end_check['board_state']);
        
        $stmt = $link->prepare("UPDATE games SET board_state = ?, status = 'ended' WHERE game_id = ?");
        $board_state_json = json_encode($end_check['board_state']);
        $stmt->bind_param("si", $board_state_json, $game_id);
        $update_success = $stmt->execute();
        $stmt->close();

    } else {
        // Συνεχίζουμε: Αποθήκευση νέας κατάστασης και αλλαγή σειράς (η update_game_state_and_turn κάνει τα πάντα)
        $update_success = update_game_state_and_turn($link, $game_id, $player_id, $end_check);
    }

    // 6. Τελικός Έλεγχος Αποθήκευσης και Επιστροφή
    if (!$update_success) {
        http_response_code(500);
        echo json_encode(["status" => "error", "message" => "Failed to update game state after move."]);
        $link->close(); exit();
    }

    http_response_code(200);
    $response = [
        "status" => $final_game_status, 
        "game_id" => $game_id, 
        "board_state" => $end_check['board_state']
    ];

    if ($final_game_status === 'ended' && $final_scores) {
        $response['final_scores'] = $final_scores;
    }

    echo json_encode($response);
}

// 5. GET /api/v1/games/{game_id} (Εμφάνιση Κατάστασης Παιχνιδιού)
elseif ($request_method === 'GET' && count($parts) === 4 && $parts[0] === 'api' && $parts[2] === 'games') {
    $game_id = $parts[3];

    $game_data = load_game_state($link, $game_id);

    if (!$game_data) {
        http_response_code(404);
        echo json_encode(["status" => "error", "message" => "Game not found."]);
        $link->close(); exit();
    }
    
    // Ασφάλεια: Αφαίρεση της τράπουλας (deck) από την εμφάνιση
    unset($game_data['board_state']['deck']);

    http_response_code(200);
    echo json_encode([
        "status" => $game_data['status'],
        "current_turn" => $game_data['current_turn'],
        "board_state" => $game_data['board_state']
    ]);
}
else {
    http_response_code(404);
    echo json_encode(["status" => "error", "message" => "Endpoint not found."]);
}

$link->close();


?>