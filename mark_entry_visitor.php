<?php
require 'auth_check.php';
require 'db_connect.php';
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $visitorId = $_POST['visitor_id'] ?? null;

    if (!$visitorId) {
        echo json_encode(['success' => false, 'message' => 'Missing visitor ID.']);
        exit;
    }

    try {
        // Fetch visitor directly from visitors table
        $stmt = $pdo->prepare("SELECT * FROM visitors WHERE id = :id LIMIT 1");
        $stmt->execute([':id' => $visitorId]);
        $visitor = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$visitor) {
            echo json_encode(['success' => false, 'message' => 'Visitor not found']);
            exit;
        }

        // Update time_in and status
        $stmt = $pdo->prepare("
            UPDATE visitors 
            SET time_in = NOW(), status = 'Inside'
            WHERE id = :id
        ");
        $stmt->execute([':id' => $visitorId]);

        // Sync entry time to linked vehicles
        $stmt = $pdo->prepare("
            UPDATE vehicles 
            SET entry_time = NOW(), status = 'Inside'
            WHERE visitation_id = :vid AND entry_time IS NULL
        ");
        $stmt->execute([':vid' => $visitorId]);

        echo json_encode(['success' => true, 'message' => 'Visitor marked as Inside']);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request']);
<<<<<<< Updated upstream
=======
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

    // Fetch visit date for the visitor
    $stmt = $pdo->prepare("SELECT date FROM visitors WHERE id = :id");
    $stmt->execute([':id' => $visitorId]);
    $visitDate = $stmt->fetchColumn();

    if ($visitDate) {
        // Check if active badge exists for visitor
        $stmt = $pdo->prepare("SELECT id FROM clearance_badges WHERE visitor_id = :visitor_id AND status = 'active'");
        $stmt->execute([':visitor_id' => $visitorId]);
        $activeBadge = $stmt->fetchColumn();

        if (!$activeBadge) {
            // Generate key card number: KC- + padded visitor_id + - + timestamp
            $paddedVisitorId = str_pad($visitorId, 6, '0', STR_PAD_LEFT);
            $timestamp = time();
            $keyCardNumber = "KC-" . $paddedVisitorId . "-" . $timestamp;

            $validityStart = date('Y-m-d H:i:s');
            // Set validity end to 5 hours from now
            $validityEnd = date('Y-m-d H:i:s', strtotime('+5 hours'));

            // Insert new clearance badge
            $stmt = $pdo->prepare("INSERT INTO clearance_badges (visitor_id, key_card_number, validity_start, validity_end, status, issued_at, updated_at) VALUES (:visitor_id, :key_card_number, :validity_start, :validity_end, 'active', NOW(), NOW())");
            $stmt->execute([
                ':visitor_id' => $visitorId,
                ':key_card_number' => $keyCardNumber,
                ':validity_start' => $validityStart,
                ':validity_end' => $validityEnd
            ]);

            // Update visitors table with key card number
            $stmt = $pdo->prepare("UPDATE visitors SET key_card_number = :key_card_number WHERE id = :id");
            $stmt->execute([':key_card_number' => $keyCardNumber, ':id' => $visitorId]);
        }
    }

    echo json_encode(['success' => true, 'message' => 'Visitor marked as Inside']);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
>>>>>>> Stashed changes
}
