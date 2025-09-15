<?php
require 'db_connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';

    // Find user
    $stmt = $pdo->prepare("SELECT id FROM users WHERE email = :email LIMIT 1");
    $stmt->execute([':email' => $email]);
    $user = $stmt->fetch();

    if ($user) {
        // Generate reset token
        $token = bin2hex(random_bytes(32));
        
        // Store in password_resets table
        $stmt = $pdo->prepare("INSERT INTO password_resets (user_id, reset_token, expires_at)
                               VALUES (:user_id, :reset_token, DATE_ADD(NOW(), INTERVAL 15 MINUTE))");
        $stmt->execute([
            ':user_id' => $user['id'],
            ':reset_token' => $token
        ]);

        // Show reset link directly (for testing only!)
        echo "<p><b>Reset Link:</b> <a href='reset_password.php?token=$token'>Click here</a></p>";
    } else {
        echo "<p>Email not found. Try again.</p>";
    }
}
