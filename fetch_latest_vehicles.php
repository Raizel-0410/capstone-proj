<?php
require 'auth_check.php';
require 'db_connect.php';

header('Content-Type: application/json');

try {
    $stmt = $pdo->prepare("
        SELECT 
            vehicle_brand,
            vehicle_model,
            vehicle_owner AS owner_name,
            plate_number,
            entry_time
        FROM vehicles
        WHERE status = 'Inside'
        ORDER BY entry_time DESC
        LIMIT 4
    ");
    $stmt->execute();
    $vehicles = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($vehicles);
} catch (Exception $e) {
    echo json_encode([]);
}
