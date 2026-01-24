<?php
$host = 'localhost';
$user = 'iee2019131'; 
$pass = 'Kodikosieeihu4535*'; 
$db   = 'iee2019131';

$link = mysqli_connect($host, $user, $pass, $db);

if (!$link) {
    die("❌ ΑΠΟΤΥΧΙΑ ΣΥΝΔΕΣΗΣ: " . mysqli_connect_error());
}

echo "✅ Επιτυχής σύνδεση!<br>";

$sql_players = "CREATE TABLE IF NOT EXISTS players (
    player_id INT AUTO_INCREMENT PRIMARY KEY,
    token VARCHAR(32) NOT NULL UNIQUE,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";

mysqli_query($link, $sql_players);

$sql_games = "CREATE TABLE IF NOT EXISTS games (
    game_id INT AUTO_INCREMENT PRIMARY KEY,
    player1_id INT NOT NULL,
    player2_id INT,
    status ENUM('waiting', 'active', 'ended') NOT NULL DEFAULT 'waiting',
    current_turn INT,
    board_state TEXT NOT NULL,
    last_move DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (player1_id) REFERENCES players(player_id),
    FOREIGN KEY (player2_id) REFERENCES players(player_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";

if (mysqli_query($link, $sql_games)) {
    echo "✅ Η βάση είναι έτοιμη!";
} else {
    echo "❌ Σφάλμα: " . mysqli_error($link);
}
mysqli_close($link);
?>