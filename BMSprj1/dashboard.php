<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: home.html');
    exit;
}

$userId = (int)$_SESSION['user_id'];
$stmt = $conn->prepare('SELECT username, balance FROM users WHERE id=?');
$stmt->bind_param('i', $userId);
$stmt->execute();
$stmt->bind_result($username, $balance);
$stmt->fetch();
$stmt->close();
$_SESSION['username'] = $username;
$_SESSION['balance'] = $balance;
?>
<!DOCTYPE html>
<html lang="en"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Bank Dashboard</title>
<style>body{font-family:Arial,sans-serif;background:#f2f2f2;margin:0;padding:30px}.box{max-width:500px;margin:40px auto;background:#fff;padding:30px;border-radius:10px;box-shadow:0 2px 10px #ccc}input,select{width:100%;padding:10px;margin:8px 0 15px;box-sizing:border-box}button{padding:11px 20px;border:0;background:#007bff;color:#fff;border-radius:5px;cursor:pointer}.balance{font-size:24px;font-weight:bold;margin:20px 0}.msg{color:green}</style></head>
<body><div class="box"><h2>Welcome, <?=htmlspecialchars($username)?>!</h2><div class="balance">Balance: ₹<?=number_format((float)$balance,2)?></div>
<?php if(isset($_GET['success'])): ?><p class="msg"><?=htmlspecialchars($_GET['success'])?></p><?php endif; ?>
<form action="transaction.php" method="post"><label>Amount</label><input type="number" name="amount" min="0.01" step="0.01" placeholder="Enter amount" required><label>Transaction Type</label><select name="type"><option value="deposit">Deposit</option><option value="withdraw">Withdraw</option></select><button type="submit">Submit Transaction</button></form>
<p><a href="transactions.php">View My Transactions</a></p><p><a href="logout.php">Logout</a></p></div></body></html>
