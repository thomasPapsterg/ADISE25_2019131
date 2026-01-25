<?php
/**
 * ====================================================================
 * WEB API FOR XERI GAME - ADISE25 (FINAL CONNECTION SETUP)
 * ====================================================================
 */

// 1. ΕΜΦΑΝΙΣΗ ΣΦΑΛΜΑΤΩΝ
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// 2. ΡΥΘΜΙΣΕΙΣ ΣΥΝΔΕΣΗΣ (Προσαρμοσμένες στη νέα σου βάση)
$host = 'localhost';
$db   = 'iee2019131_db'; // Το όνομα που φτιάξαμε στο PuTTY

// 3. ΑΝΙΧΝΕΥΣΗ ΠΕΡΙΒΑΛΛΟΝΤΟΣ & CREDENTIALS
$is_server = (gethostname() == 'users.iee.ihu.gr' || strpos($_SERVER['HTTP_HOST'], 'iee.ihu.gr') !== false);

if ($is_server) {
    // ΡΥΘΜΙΣΕΙΣ ΓΙΑ ΤΟΝ SERVER ΤΗΣ ΣΧΟΛΗΣ
    $user = 'root'; 
    $pass = 'Kodikosmysql123!'; // Ο κωδικός που έβαλες στο mysqladmin
    $socket = '/home/student/iee/2019/iee2019131/mysql/mysql.sock';
    
    // Σύνδεση με το socket (Υποχρεωτικό για τη δική σου MySQL)
    $link = new mysqli($host, $user, $pass, $db, null, $socket);
} else {
    // ΡΥΘΜΙΣΕΙΣ ΓΙΑ ΤΟ LAPTOP (XAMPP)
    $user = 'root';
    $pass = ''; 
    $link = new mysqli($host, $user, $pass, $db);
}

// 4. ΕΛΕΓΧΟΣ ΣΥΝΔΕΣΗΣ
if ($link->connect_errno) {
    header('Content-Type: application/json');
    die(json_encode([
        "status" => "error",
        "message" => "Connection failed: " . $link->connect_error,
        "debug" => [
            "db" => $db,
            "user" => $user,
            "socket" => $socket ?? 'none'
        ]
    ]));
}

// 5. ΑΠΟΚΡΙΣΗ ΕΠΙΤΥΧΙΑΣ
header('Content-Type: application/json');
header("Access-Control-Allow-Origin: *");

echo json_encode([
    "status" => "online",
    "database" => "connected",
    "db_name" => $db,
    "message" => "🚀 Η σύνδεση με τη νέα βάση έγινε επιτυχώς!"
]);

$link->close();
?>