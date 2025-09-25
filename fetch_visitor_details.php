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
        echo json_encode(['success' => true, 'data' => $visitor]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Visitor not found']);
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Query error']);
}
