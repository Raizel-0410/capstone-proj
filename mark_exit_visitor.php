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
    // Update time_out and status
    $stmt = $pdo->prepare("UPDATE visitors SET time_out = NOW(), status = 'Exited' WHERE id = :id");
    $stmt->execute([':id' => $visitorId]);

    // If visitor has a vehicle, update vehicles table
    $stmt = $pdo->prepare("UPDATE vehicles SET exit_time = NOW(), status = 'Exited' WHERE visitation_id = :vid AND exit_time IS NULL");
    $stmt->execute([':vid' => $visitorId]);

    // Update clearance_badges status to terminated for the visitor's key card
    $stmt = $pdo->prepare("UPDATE clearance_badges SET status = 'terminated' WHERE visitor_id = :vid AND status = 'active'");
    $stmt->execute([':vid' => $visitorId]);

} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
