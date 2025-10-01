<?php
require 'db_connect.php';
header('Content-Type: application/json');

try {
    $stmt = $pdo->query("
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
        ORDER BY date DESC, time_in DESC
    ");
    $visitors = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($visitors);
} catch (Exception $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
