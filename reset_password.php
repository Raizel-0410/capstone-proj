<?php
require 'db_connect.php';

$token = $_GET['token'] ?? '';

$stmt = $pdo->prepare("SELECT * FROM password_resets WHERE reset_token = :reset_token AND expires_at > NOW()");
$stmt->execute([':reset_token' => $token]);
$reset = $stmt->fetch();

if (!$reset) {
    die("Invalid or expired reset link.");
}
?>

<form action="reset_password_submit.php" method="POST">
  <input type="hidden" name="token" value="<?= htmlspecialchars($token) ?>">
  <label>New Password: <input type="password" name="password" required></label>
  <button type="submit">Reset Password </button>
</form>
