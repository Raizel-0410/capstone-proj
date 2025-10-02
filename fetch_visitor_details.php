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
<<<<<<< Updated upstream
<<<<<<< Updated upstream
<<<<<<< Updated upstream
<<<<<<< Updated upstream
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
=======
=======
>>>>>>> Stashed changes
=======
>>>>>>> Stashed changes
=======
>>>>>>> Stashed changes
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
        LEFT JOIN visitation_requests vr ON vr.visitor_name = CONCAT(v.first_name, ' ', v.last_name) AND vr.visit_date = v.date
        LEFT JOIN vehicles veh ON veh.visitation_id = vr.id
        WHERE v.id = :id
<<<<<<< Updated upstream
<<<<<<< Updated upstream
<<<<<<< Updated upstream
>>>>>>> Stashed changes
=======
>>>>>>> Stashed changes
=======
>>>>>>> Stashed changes
=======
>>>>>>> Stashed changes
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
