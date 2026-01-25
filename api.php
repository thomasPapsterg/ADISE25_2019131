<?php
/**
 * ====================================================================
 * WEB API FOR XERI GAME - ADISE25 (SMART CONFIG VERSION)
 * ====================================================================
 */

// 1. ΡΥΘΜΙΣΕΙΣ ΠΕΡΙΒΑΛΛΟΝΤΟΣ
// Ανιχνεύουμε αν τρέχουμε στο Laptop (localhost) ή στον Server (iee.ihu.gr)
$is_localhost = ($_SERVER['REMOTE_ADDR'] == '127.0.0.1' || $_SERVER['REMOTE_ADDR'] == '::1' || $_SERVER['SERVER_NAME'] == 'localhost');

if ($is_localhost) {
    // --- ΡΥΘΜΙΣΕΙΣ ΓΙΑ ΤΟ LAPTOP (XAMPP) ---
    // Εδώ συνήθως δεν χρειάζεται κωδικός
    $db_host = 'localhost';
    $db_user = 'root';
    $db_pass = ''; 
    $db_name = 'iee2019131'; 
} else {
    // --- ΡΥΘΜΙΣΕΙΣ ΓΙΑ ΤΟΝ SERVER ΤΗΣ ΣΧΟΛΗΣ ---
    // Εδώ ο κωδικός είναι υποχρεωτικός. Τον βάζουμε ΜΙΑ ΦΟΡΑ και τον ξεχνάμε.
    $db_host = 'localhost';
    $db_user = 'iee2019131';
    $db_pass = 'Kodikosieeihu4535*'; // Ο κωδικός σου από το portal.iee.ihu.gr
    $db_name = 'iee2019131';
}

// 2. ΣΥΝΔΕΣΗ ΜΕ ΤΗ ΒΑΣΗ
try {
    $link = new mysqli($db_host, $db_user, $db_pass, $db_name);
    $link->set_charset("utf8mb4");
} catch (Exception $e) {
    header('Content-Type: application/json');
    http_response_code(500);
    echo json_encode([
        "status" => "error",
        "message" => "Αποτυχία Σύνδεσης: " . $e->getMessage(),
        "env" => ($is_localhost ? "Laptop" : "Server")
    ]);
    exit();
}

// 3. ΒΑΣΙΚΟΙ HEADERS
header('Content-Type: application/json');
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

// 4. ROUTING LOGIC
$request_uri = $_SERVER['REQUEST_URI'];

// Τεστ για να δούμε αν δουλεύει
if (strpos($request_uri, 'test') !== false) {
    echo json_encode([
        "status" => "success",
        "message" => "Το API λειτουργεί!",
        "database" => "Συνδεδεμένη",
        "mode" => ($is_localhost ? "Τοπικό (Laptop)" : "Απομακρυσμένο (Server)")
    ]);
    exit();
}

// --- ΣΥΜΠΕΡΙΛΗΨΗ ΛΟΓΙΚΗΣ ΠΑΙΧΝΙΔΙΟΥ ---
if (file_exists('game_logic.php')) {
    require_once 'game_logic.php';
}

// Επιστροφή βασικής κατάστασης αν δεν ζητηθεί κάτι άλλο
echo json_encode([
    "status" => "online",
    "db" => "connected"
]);

$link->close();
?>