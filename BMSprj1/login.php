<?php
include 'db.php';

$username = $_POST['username'];
$password = $_POST['password'];

$stmt = $conn->prepare("SELECT id, password, balance FROM users WHERE username=?");
$stmt->bind_param("s", $username);
$stmt->execute();
$stmt->store_result();
$stmt->bind_result($id, $hashed_pw, $balance);

if ($stmt->fetch() && password_verify($password, $hashed_pw)) {
    session_start();
    $_SESSION['user_id'] = $id;
    $_SESSION['username'] = $username;
    $_SESSION['balance'] = $balance;
    header("Location: dashboard.php");
} else {
    echo "Invalid login.";
}
?>