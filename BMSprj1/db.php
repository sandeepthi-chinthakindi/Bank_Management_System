<?php
$host = "localhost";
$dbname = "banking_system";
$user = "root"; // change if needed
$pass = "";     // change if needed

$conn = new mysqli($host, $user, $pass, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>