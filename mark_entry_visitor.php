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

    // Get visitation_id from visitor_id
    $visitationIdStmt = $pdo->prepare("SELECT id FROM visitation_requests WHERE visitor_name = (SELECT CONCAT(first_name, ' ', last_name) FROM visitors WHERE id = :vid) LIMIT 1");
    $visitationIdStmt->execute([':vid' => $visitorId]);
    $visitationId = $visitationIdStmt->fetchColumn();

    if (!$visitationId) {
        echo json_encode(['success' => false, 'message' => 'Visitation ID not found for visitor']);
        exit;
    }

    // Update vehicles table status to 'Inside' when visitor is inside also the time
    $stmt = $pdo->prepare("UPDATE vehicles SET entry_time = NOW(), status = 'Inside' WHERE visitation_id = :vid and entry_time IS NULL");
    $stmt->execute([':vid' => $visitationId]);

    // Insert into clearance_badges
    $badgeStmt = $pdo->prepare("INSERT INTO clearance_badges (visitor_id, clearance_level, key_card_number, validity_start, validity_end, status) VALUES (?, 'Visitor', ?, NOW(), DATE_ADD(NOW(), INTERVAL 1 DAY), 'active')");
    $badgeStmt->execute([$visitorId, $keyCardNumber]);

    // Removed transfer to inside_vehicles table as per user feedback
    // $transferStmt = $pdo->prepare("
    //     INSERT INTO inside_vehicles (visitation_id, vehicle_owner, vehicle_brand, vehicle_model, vehicle_color, plate_number, vehicle_photo_path, entry_time, status)
    //     SELECT visitation_id, vehicle_owner, vehicle_brand, vehicle_model, vehicle_color, plate_number, vehicle_photo_path, entry_time, status
    //     FROM vehicles
    //     WHERE visitation_id = :vid AND status = 'Inside'
    // ");
    // $transferStmt->execute([':vid' => $visitationId]);

    echo json_encode(['success' => true, 'message' => 'Visitor marked as Inside']);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
