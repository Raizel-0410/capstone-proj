<?php
require 'auth_check.php';
require 'db_connect.php';
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fullName = trim($_POST['full_name'] ?? '');
    $contactNumber = trim($_POST['contact_number'] ?? '');
    $reason = trim($_POST['reason'] ?? '');

    if (empty($fullName) || empty($reason)) {
        echo json_encode(['success' => false, 'message' => 'Full name and reason are required.']);
        exit;
    }

    try {
        $stmt = $pdo->prepare("INSERT INTO visitors 
                               (full_name, contact_number, reason, date, time_in, status) 
                               VALUES (:full_name, :contact_number, :reason, CURDATE(), NOW(), 'Inside')");
        $stmt->execute([
            ':full_name' => $fullName,
            ':contact_number' => $contactNumber,
            ':reason' => $reason
        ]);

        echo json_encode(['success' => true, 'message' => 'Visitor added successfully']);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}
