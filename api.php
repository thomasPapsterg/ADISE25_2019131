<?php


// 1. ΕΜΦΑΝΙΣΗ ΣΦΑΛΜΑΤΩΝ (Debugging)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// 2. ΣΥΝΔΕΣΗ ΜΕ ΤΗ ΒΑΣΗ (Χρήση του αρχείου της σχολής)
// Αντί να γράφουμε εδώ τη σύνδεση, καλούμε το έτοιμο αρχείο
require_once "db_connect.php";
// Πλέον έχουμε διαθέσιμη τη μεταβλητή $mysqli από το db_connect.php

// 3. ΡΥΘΜΙΣΕΙΣ API HEADERS
header('Content-Type: application/json');
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

// 4. ΕΝΣΩΜΑΤΩΣΗ ΛΟΓΙΚΗΣ ΠΑΙΧΝΙΔΙΟΥ
if (file_exists('game_logic.php')) {
    require_once 'game_logic.php';
}

// 5. ROUTING LOGIC
$request_uri = $_SERVER['REQUEST_URI'];
$method = $_SERVER['REQUEST_METHOD'];

// Παράδειγμα: Αυθεντικοποίηση (/auth)
if (strpos($request_uri, 'auth') !== false && $method == 'POST') {
    $token = bin2hex(random_bytes(16));
    $stmt = $mysqli->prepare("INSERT INTO players (token) VALUES (?)");
    $stmt->bind_param("s", $token);
    
    if($stmt->execute()) {
        echo json_encode([
            "status" => "success", 
            "token" => $token, 
            "player_id" => $mysqli->insert_id
        ]);
    } else {
        http_response_code(500);
        echo json_encode(["status" => "error", "message" => $mysqli->error]);
    }
    exit();
}

// 6. DEFAULT RESPONSE (Έλεγχος αν δουλεύουν όλα)
// Αν κάποιος καλέσει το api.php χωρίς παραμέτρους
echo json_encode([
    "status" => "online",
    "database" => "connected",
    "message" => "Το API της Ξερής λειτουργεί κανονικά!",
    "environment" => (gethostname() == 'users.iee.ihu.gr' ? "Περιβάλλον Σχολής" : "Τοπικό Περιβάλλον")
]);

// Κλείσιμο σύνδεσης στο τέλος
$mysqli->close();
?>