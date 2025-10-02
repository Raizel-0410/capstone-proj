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
            date,
            status
        FROM visitors
        WHERE time_in IS NULL
        ORDER BY date DESC
    ");
    $stmt->execute();
    $visitors = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Add "Expected" status to all
    foreach ($visitors as &$visitor) {
        $visitor['status'] = 'Expected';
    }

    echo json_encode($visitors);
} catch (Exception $e) {
    echo json_encode([]);
}
