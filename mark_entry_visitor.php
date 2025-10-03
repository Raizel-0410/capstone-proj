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
    // Assign key card
    $keyCardNumber = 'KC-' . strtoupper(substr(md5(uniqid()), 0, 8));

    // Update visitor with key card
    $updateStmt = $pdo->prepare("UPDATE visitors SET time_in = NOW(), status = 'Inside', key_card_number = ? WHERE id = ?");
    $updateStmt->execute([$keyCardNumber, $visitorId]);

    // Insert into clearance_badges
    $badgeStmt = $pdo->prepare("INSERT INTO clearance_badges (visitor_id, clearance_level, key_card_number, validity_start, validity_end, status) VALUES (?, 'Visitor', ?, NOW(), DATE_ADD(NOW(), INTERVAL 1 DAY), 'active')");
    $badgeStmt->execute([$visitorId, $keyCardNumber]);

    // Sync entry time to linked vehicles
    $stmt = $pdo->prepare("UPDATE vehicles SET entry_time = NOW(), status = 'Inside' WHERE visitation_id = :vid AND entry_time IS NULL");
    $stmt->execute([':vid' => $visitorId]);

    echo json_encode(['success' => true, 'message' => 'Visitor marked as Inside']);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
