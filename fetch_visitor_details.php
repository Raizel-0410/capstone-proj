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
            v.id,
            CONCAT(v.first_name, ' ', v.last_name) AS full_name,
            v.first_name,
            v.last_name,
            v.contact_number,
            v.email,
            v.address,
            v.reason,
            v.id_photo_path,
            v.selfie_photo_path,
            v.date,
            v.time_in,
            v.time_out,
            v.status,
            vr.personnel_related,
            vr.driver_name,
            vr.driver_id,
            veh.vehicle_owner,
            veh.vehicle_brand,
            veh.vehicle_model,
            veh.vehicle_color,
            veh.plate_number,
            veh.vehicle_photo_path
        FROM visitors v
        LEFT JOIN visitation_requests vr 
            ON vr.visitor_name = CONCAT(v.first_name, ' ', v.last_name) 
            AND vr.visit_date = v.date
        LEFT JOIN vehicles veh 
            ON veh.visitation_id = vr.id
        WHERE v.id = :id
    ");
    $stmt->execute([':id' => $id]);
    $visitor = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($visitor) {
        // sanitize paths
        $visitor['id_photo_path'] = $visitor['id_photo_path'] ? htmlspecialchars($visitor['id_photo_path'], ENT_QUOTES, 'UTF-8') : '';
        $visitor['selfie_photo_path'] = $visitor['selfie_photo_path'] ? htmlspecialchars($visitor['selfie_photo_path'], ENT_QUOTES, 'UTF-8') : '';
        $visitor['vehicle_photo_path'] = $visitor['vehicle_photo_path'] ? htmlspecialchars($visitor['vehicle_photo_path'], ENT_QUOTES, 'UTF-8') : '';
        $visitor['driver_id'] = $visitor['driver_id'] ? htmlspecialchars($visitor['driver_id'], ENT_QUOTES, 'UTF-8') : '';

        echo json_encode(['success' => true, 'data' => $visitor]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Visitor not found']);
    }
} catch (Exception $e) {
    error_log("Query error in fetch_visitor_details.php: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Query error: ' . $e->getMessage()]);
}
