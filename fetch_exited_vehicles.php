<?php
require 'auth_check.php';
require 'db_connect.php';
header('Content-Type: application/json');

try {
    $stmt = $pdo->prepare("
        SELECT id, vehicle_owner, vehicle_brand, vehicle_model, vehicle_color, plate_number, entry_time, exit_time, status
        FROM vehicles
        WHERE LOWER(status) = 'exited'
        ORDER BY exit_time DESC
    ");
    $stmt->execute();
    $vehicles = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($vehicles);
} catch (Exception $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
