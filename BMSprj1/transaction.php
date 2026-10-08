<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: home.html');
    exit;
}

$userId = (int)$_SESSION['user_id'];
$amount = (float)($_POST['amount'] ?? 0);
$type = $_POST['type'] ?? '';

if ($amount <= 0 || !in_array($type, ['deposit', 'withdraw'], true)) {
    die('Invalid transaction. <a href="dashboard.php">Go back</a>');
}

$conn->begin_transaction();

try {
    $stmt = $conn->prepare('SELECT balance FROM users WHERE id=? FOR UPDATE');
    $stmt->bind_param('i', $userId);
    $stmt->execute();
    $stmt->bind_result($balance);
    if (!$stmt->fetch()) {
        throw new Exception('User not found.');
    }
    $stmt->close();

    if ($type === 'withdraw' && $amount > (float)$balance) {
        throw new Exception('Insufficient balance.');
    }

    $newBalance = $type === 'deposit' ? (float)$balance + $amount : (float)$balance - $amount;

    $update = $conn->prepare('UPDATE users SET balance=? WHERE id=?');
    $update->bind_param('di', $newBalance, $userId);
    $update->execute();
    $update->close();

    $history = $conn->prepare('INSERT INTO transactions (user_id, amount, type) VALUES (?, ?, ?)');
    $history->bind_param('ids', $userId, $amount, $type);
    $history->execute();
    $history->close();

    if ($type === 'deposit') {
        $deposit = $conn->prepare("INSERT INTO deposits (user_id, deposit_date, amount, status) VALUES (?, CURDATE(), ?, 'Completed')");
        $deposit->bind_param('id', $userId, $amount);
        $deposit->execute();
        $deposit->close();
    }

    $conn->commit();
    $_SESSION['balance'] = $newBalance;
    header('Location: dashboard.php?success=' . urlencode(ucfirst($type) . ' successful'));
    exit;
} catch (Exception $e) {
    $conn->rollback();
    die($e->getMessage() . ' <a href="dashboard.php">Go back</a>');
}
?>
