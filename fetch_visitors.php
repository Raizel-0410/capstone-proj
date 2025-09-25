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
            address AS home_address,     -- ✅ clearer mapping
            reason,
            id_photo_path AS valid_id,   -- ✅ alias for consistency
            selfie_photo_path AS selfie, -- ✅ alias for consistency
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
