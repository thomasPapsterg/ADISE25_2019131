<?php
/**
 * db_connect.php 
 
 */

$host = 'localhost';
$db   = 'iee2019131_db'; // Το όνομα της βάσης που φτιάξαμε στο PuTTY

require_once "db_upass.php";

$user = $DB_USER;
$pass = $DB_PASS;

if (gethostname() == 'users.iee.ihu.gr') {
    // ΣΥΝΔΕΣΗ ΣΤΟΝ SERVER (Μέσω Socket)
    // ΠΡΟΣΟΧΗ: Βάζουμε το ΔΙΚΟ ΣΟΥ path, όχι του asidirop
    $socket = '/home/student/iee/2019/iee2019131/mysql/run/mysql.sock';
    $mysqli = new mysqli($host, $user, $pass, $db, null, $socket);
} else {
    // ΣΥΝΔΕΣΗ ΣΤΟ PC (Localhost)
    $mysqli = new mysqli($host, $user, $pass, $db);
}

if ($mysqli->connect_errno) {
    echo "Failed to connect to MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
    exit();
}
?>