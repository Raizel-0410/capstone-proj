<?php
require 'db_connect.php';
header('Content-Type: application/json');

try {
    $stmt = $pdo->query("
        SELECT 
            vr.id,
            vr.visitor_name AS full_name,
            vr.contact_number,
            vr.reason,
            vr.visit_date AS date,
            v.entry_time AS time_in,
            v.exit_time AS time_out,
            vr.status
        FROM visitation_requests vr
        LEFT JOIN vehicles v ON v.visitation_id = vr.id
        WHERE vr.status != 'Pending'
        ORDER BY vr.visit_date DESC, v.entry_time DESC
    ");
    $visitors = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($visitors);
} catch (Exception $e) {
    echo json_encode([]);
}
