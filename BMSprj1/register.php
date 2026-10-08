<?php
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: register.html');
    exit;
}

$username = trim($_POST['username'] ?? '');
$password = $_POST['password'] ?? '';

if ($username === '' || $password === '') {
    die('Username and password are required. <a href="register.html">Go back</a>');
}

$check = $conn->prepare('SELECT id FROM users WHERE username=?');
$check->bind_param('s', $username);
$check->execute();
$check->store_result();

if ($check->num_rows > 0) {
    die('Username already exists. <a href="register.html">Try another username</a>');
}

$hashedPassword = password_hash($password, PASSWORD_DEFAULT);
$stmt = $conn->prepare('INSERT INTO users (username, password, balance) VALUES (?, ?, 0.00)');
$stmt->bind_param('ss', $username, $hashedPassword);

if ($stmt->execute()) {
    header('Location: home.html?registered=1');
    exit;
}

die('Registration failed. <a href="register.html">Go back</a>');
?>
