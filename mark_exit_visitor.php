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
    // Fetch visitor from visitors table
    $stmt = $pdo->prepare("SELECT * FROM visitors WHERE id = :id LIMIT 1");
    $stmt->execute([':id' => $visitorId]);
    $visitor = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$visitor) {
        echo json_encode(['success' => false, 'message' => 'Visitor not found']);
        exit;
    }

    // Update time_out and status
    $stmt = $pdo->prepare("UPDATE visitors SET time_out = NOW(), status = 'Exited' WHERE id = :id");
    $stmt->execute([':id' => $visitorId]);

    // If visitor has a vehicle, update vehicles table
    if (!empty($visitor['plate_number'])) {
        $stmt = $pdo->prepare("
            UPDATE vehicles 
            SET exit_time = NOW(), status = 'Exited' 
            WHERE plate_number = :plate AND status = 'Inside'
        ");
        $stmt->execute([':plate' => $visitor['plate_number']]);
    }

    echo json_encode(['success' => true, 'message' => 'Visitor exit marked successfully']);

} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
