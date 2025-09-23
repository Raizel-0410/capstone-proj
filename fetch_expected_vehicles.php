<?php
require 'auth_check.php';
require 'db_connect.php';

header('Content-Type: application/json');

try {
    // Vehicles waiting to enter (status = 'Scheduled' or no entry_time yet)
    $stmt = $pdo->prepare("
        SELECT id, vehicle_owner, vehicle_brand, vehicle_model, vehicle_color, plate_number
        FROM vehicles
        WHERE entry_time IS NULL AND (status IS NULL OR status != 'Inside')
        ORDER BY id DESC
    ");
    $stmt->execute();
    $vehicles = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Add "Scheduled" status to all
    foreach ($vehicles as &$vehicle) {
        $vehicle['status'] = 'Scheduled';
    }

    echo json_encode($vehicles);
} catch (Exception $e) {
    echo json_encode([]);
}
