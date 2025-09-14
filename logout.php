<?php
session_start();
require 'db_connect.php';

if (!empty($_SESSION['token'])) {
    $stmt = $pdo->prepare("DELETE FROM personnel_sessions WHERE token = :token");
    $stmt->execute([':token' => $_SESSION['token']]);
}

// Clear session completely
$_SESSION = [];
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

session_destroy();

// Redirect to login page
header("Location: loginpage.php?logged_out=1");
exit;
