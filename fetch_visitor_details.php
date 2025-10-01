<?php
require 'db_connect.php';
header('Content-Type: application/json');

$id = $_GET['id'] ?? null;
if (!$id) {
    echo json_encode(['success' => false, 'message' => 'Missing ID']);
    exit;
}

try {
    $stmt = $pdo->prepare("
        SELECT
            id,
            full_name,
            contact_number,
            email,
            address,
            reason,
            id_photo_path,
            selfie_photo_path,
            date,
            time_in,
            time_out,
            status
        FROM visitors
        WHERE id = :id
    ");
    $stmt->execute([':id' => $id]);
    $visitor = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($visitor) {
        // Ensure paths are absolute or correct relative URLs for frontend
        $visitor['id_photo_path'] = $visitor['id_photo_path'] ? htmlspecialchars($visitor['id_photo_path'], ENT_QUOTES, 'UTF-8') : '';
        $visitor['selfie_photo_path'] = $visitor['selfie_photo_path'] ? htmlspecialchars($visitor['selfie_photo_path'], ENT_QUOTES, 'UTF-8') : '';
        echo json_encode(['success' => true, 'data' => $visitor]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Visitor not found']);
    }
} catch (Exception $e) {
    error_log("Query error in fetch_visitor_details.php: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Query error: ' . $e->getMessage()]);
}
