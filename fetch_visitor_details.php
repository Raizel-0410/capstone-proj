<?php
require 'db_connect.php';
header('Content-Type: application/json');

$id = $_GET['id'] ?? null;
if (!$id) {
    echo json_encode(['success' => false, 'message' => 'Missing ID']);
    exit;
}

$stmt = $pdo->prepare("SELECT * FROM visitation_requests WHERE id = :id");
$stmt->execute([':id' => $id]);
$visitor = $stmt->fetch(PDO::FETCH_ASSOC);

if ($visitor) {
    echo json_encode(['success' => true, 'data' => $visitor]);
} else {
    echo json_encode(['success' => false, 'message' => 'Visitor not found']);
}
