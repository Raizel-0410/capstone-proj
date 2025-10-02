<?php
require 'db_connect.php';
header('Content-Type: application/json');

try {
    $stmt = $pdo->prepare("
        SELECT
            id,
            first_name,
            last_name,
            contact_number,
            key_card_number,
            time_in,
            time_out,
            status
        FROM visitors
        WHERE time_in IS NOT NULL AND time_out IS NULL
    ");
    $stmt->execute();
    $visitors = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($visitors);
} catch (Exception $e) {
    echo json_encode([]);
}
