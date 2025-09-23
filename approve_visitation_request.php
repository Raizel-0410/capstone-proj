<?php
require 'auth_check.php';
require 'db_connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'] ?? null;

    if ($id) {
        // Mark visitation request as approved
        $stmt = $pdo->prepare("UPDATE visitation_requests SET status = 'Approved' WHERE id = :id");
        $stmt->execute([':id' => $id]);

        // Fetch the visitation request details
        $stmt = $pdo->prepare("SELECT * FROM visitation_requests WHERE id = :id");
        $stmt->execute([':id' => $id]);
        $request = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($request) {
            // Insert into vehicles table as EXPECTED (not yet inside)
            $stmt = $pdo->prepare("
                INSERT INTO vehicles 
                    (visitation_id, vehicle_owner, vehicle_brand, vehicle_model, vehicle_color, plate_number, vehicle_photo_path, entry_time, exit_time, status) 
                VALUES 
                    (:visitation_id, :vehicle_owner, :vehicle_brand, :vehicle_model, :vehicle_color, :plate_number, :vehicle_photo_path, NULL, NULL, 'Expected')
            ");
            $stmt->execute([
                ':visitation_id'     => $request['id'],
                ':vehicle_owner'     => $request['visitor_name'],
                ':vehicle_brand'     => $request['vehicle_brand'],
                ':vehicle_model'     => $request['vehicle_model'],
                ':vehicle_color'     => $request['vehicle_color'],
                ':plate_number'      => $request['plate_number'],
                ':vehicle_photo_path'=> $request['vehicle_photo'] ?? null
            ]);
        }

        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Missing request ID']);
    }
}
