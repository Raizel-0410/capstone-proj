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
        // Fetch visitor info first
        $stmt = $pdo->prepare("SELECT * FROM visitation_requests WHERE id = :id LIMIT 1");
        $stmt->execute([':id' => $visitorId]);
        $visitor = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$visitor) {
            echo json_encode(['success' => false, 'message' => 'Visitor not found']);
            exit;
        }

        // Insert into exited_visitors
        $stmt = $pdo->prepare("INSERT INTO exited_visitors 
            (visitation_id, full_name, contact_number, email, home_address, reason, visit_date, time_in, time_out, valid_id_path, selfie_photo_path)
            VALUES (:visitation_id, :full_name, :contact_number, :email, :home_address, :reason, :visit_date, :time_in, :time_out, :valid_id_path, :selfie_photo_path)");
        $stmt->execute([
            ':visitation_id' => $visitor['id'],
            ':full_name' => $visitor['visitor_name'],
            ':contact_number' => $visitor['contact_number'],
            ':email' => $visitor['email'],
            ':home_address' => $visitor['home_address'],
            ':reason' => $visitor['reason'],
            ':visit_date' => $visitor['visit_date'],
            ':time_in' => $visitor['visit_time'],
            ':time_out' => date('H:i:s'),
            ':valid_id_path' => $visitor['valid_id_path'],
            ':selfie_photo_path' => $visitor['selfie_photo_path']
        ]);

        // Update visitation_requests status
        $stmt = $pdo->prepare("UPDATE visitation_requests SET status = 'Exited' WHERE id = :id");
        $stmt->execute([':id' => $visitorId]);

        // --- Sync exit time to linked vehicles ---
        $stmt = $pdo->prepare("UPDATE vehicles 
                               SET exit_time = NOW(), status = 'Exited' 
                               WHERE visitation_id = :vid AND status != 'Exited'");
        $stmt->execute([':vid' => $visitorId]);

        echo json_encode(['success' => true]);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request']);
}
