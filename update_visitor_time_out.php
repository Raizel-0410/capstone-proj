<?php
require 'auth_check.php';
require 'db_connect.php';
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request']);
    exit;
}

$visitorId = $_POST['visitor_id'] ?? null;
$newTimeOut = $_POST['time_out'] ?? null;

if (!$visitorId || !$newTimeOut) {
    echo json_encode(['success' => false, 'message' => 'Missing required data']);
    exit;
}

try {
    // Fetch the visitor info
    $stmt = $pdo->prepare("SELECT * FROM visitation_requests WHERE id = :id LIMIT 1");
    $stmt->execute([':id' => $visitorId]);
    $visitor = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$visitor) {
        echo json_encode(['success' => false, 'message' => 'Visitor not found']);
        exit;
    }

    // Update time_out in visitation_requests
    $stmt = $pdo->prepare("UPDATE visitation_requests SET time_out = :time_out WHERE id = :id");
    $stmt->execute([
        ':time_out' => $newTimeOut,
        ':id' => $visitorId
    ]);

    // If visitor has a vehicle, sync exit_time in vehicles table
    if (!empty($visitor['plate_number'])) {
        $stmt = $pdo->prepare("UPDATE vehicles 
                               SET exit_time = :exit_time, status='Exited' 
                               WHERE plate_number = :plate AND status='Inside'");
        $stmt->execute([
            ':exit_time' => $newTimeOut,
            ':plate' => $visitor['plate_number']
        ]);
    }

    // Here, you can later add your SMS/email notifier logic

    echo json_encode(['success' => true, 'message' => 'Visitor time updated successfully']);

} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
