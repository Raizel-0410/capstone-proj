<?php
require 'auth_check.php';
require 'db_connect.php';
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request']);
    exit;
}

$visitorId = $_POST['visitor_id'] ?? null;
if (!$visitorId) {
    echo json_encode(['success' => false, 'message' => 'Missing visitor ID.']);
    exit;
}

try {
    // Update time_in and status
    $stmt = $pdo->prepare("UPDATE visitors SET time_in = NOW(), status = 'Inside' WHERE id = :id");
    $stmt->execute([':id' => $visitorId]);

    // Sync entry time to linked vehicles
    $stmt = $pdo->prepare("UPDATE vehicles SET entry_time = NOW(), status = 'Inside' WHERE visitation_id = :vid AND entry_time IS NULL");
    $stmt->execute([':vid' => $visitorId]);

    echo json_encode(['success' => true, 'message' => 'Visitor marked as Inside']);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
