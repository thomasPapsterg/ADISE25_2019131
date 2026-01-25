<?php
$host = 'localhost'; 
$user = 'iee2019131'; 
$pass = 'Kodikosieeihu4535*'; 
$db   = 'iee2019131';

$link = mysqli_connect($host, $user, $pass, $db);
if (!$link) { die(" Σφάλμα Σύνδεσης: " . mysqli_connect_error()); }

echo " Σύνδεση Επιτυχής!<br>";

// Πίνακας Παικτών
$q1 = "CREATE TABLE IF NOT EXISTS players (
    player_id INT AUTO_INCREMENT PRIMARY KEY, 
    token VARCHAR(32) NOT NULL UNIQUE, 
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";

// Πίνακας Παιχνιδιών
$q2 = "CREATE TABLE IF NOT EXISTS games (
    game_id INT AUTO_INCREMENT PRIMARY KEY, 
    player1_id INT NOT NULL, 
    player2_id INT, 
    status ENUM('waiting','active','ended') DEFAULT 'waiting', 
    board_state TEXT, 
    FOREIGN KEY (player1_id) REFERENCES players(player_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";

mysqli_query($link, $q1);
if (mysqli_query($link, $q2)) { 
    echo " Η Βάση Δεδομένων είναι έτοιμη για χρήση!"; 
} else { 
    echo " Σφάλμα: " . mysqli_error($link); 
}
mysqli_close($link);
?>
