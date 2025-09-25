<?php
require 'db_connect.php';

define('ENC_KEY', 'your-32-character-secret-key');

function decryptData($encrypted) {
    if (!$encrypted) return '';
    $data = base64_decode($encrypted, true);
    if ($data === false || strlen($data) < 16) return '';
    $iv = substr($data, 0, 16);
    $ciphertext = substr($data, 16);
    $decrypted = openssl_decrypt($ciphertext, 'AES-256-CBC', ENC_KEY, OPENSSL_RAW_DATA, $iv);
    return $decrypted ?: '';
}

header('Content-Type: application/json');

try {
    // Join with users table to get encrypted full_name
    $stmt = $pdo->prepare("
        SELECT a.action, a.created_at, u.full_name AS encrypted_name
        FROM admin_audit_logs a
        LEFT JOIN users u ON u.id = a.user_id
        ORDER BY a.created_at DESC
        LIMIT 5
    ");
    $stmt->execute();
    $logs = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Decrypt user full_name
    foreach ($logs as &$log) {
        $log['full_name'] = decryptData($log['encrypted_name']) ?: 'Unknown';
        unset($log['encrypted_name']); // remove raw encrypted data
    }

    echo json_encode($logs);
} catch (Exception $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
