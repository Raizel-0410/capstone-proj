<?php
session_start();
require 'db_connect.php';
require 'audit_log.php';

// Read POST from form submission
$email = $_POST['email'] ?? '';
$password = $_POST['password'] ?? '';

// Lookup user
$stmt = $pdo->prepare("SELECT * FROM users WHERE email = :email LIMIT 1");
$stmt->execute([':email' => $email]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user || !password_verify($password, $user['password_hash'])) {
    log_admin_action($pdo, $email, "Failed login attempt"); // store attempted email
    $_SESSION['login_error'] = "Invalid email or password!";
    header('Location: loginpage.php');
    exit;
} else {
    log_admin_action($pdo, $user['id'], "User logged in");
}



// 🔑 Remove any old sessions for this user
$stmt = $pdo->prepare("DELETE FROM personnel_sessions WHERE user_id = :user_id");
$stmt->execute([':user_id' => $user['id']]);

// Create new session token
$token = bin2hex(random_bytes(32));
$sessionId = uniqid();

// Insert the new session
$stmt = $pdo->prepare("
    INSERT INTO personnel_sessions (id, user_id, token, expires_at)
    VALUES (:id, :user_id, :token, DATE_ADD(NOW(), INTERVAL 1 HOUR))
");
$stmt->execute([
  ':id'      => $sessionId,
  ':user_id' => $user['id'],
  ':token'   => $token
]);

// Secure PHP session
session_regenerate_id(true);
$_SESSION['token']   = $token;
$_SESSION['user_id'] = $user['id'];
$_SESSION['role']    = $user['role'];

// Route by role
if ($user['role'] === 'Admin') {
    header('Location: maindashboard.php');
} else {
    header('Location: maindashboard.php'); 
}
exit;
