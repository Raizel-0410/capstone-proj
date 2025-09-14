<?php
require 'db_connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Read JSON body instead of just $_POST
    $data = json_decode(file_get_contents("php://input"), true);
    $id = $data['id'] ?? null;

    if ($id) {
        $stmt = $pdo->prepare("UPDATE users SET deleted_account = 1 WHERE id = :id");
        $stmt->execute([':id' => $id]);

        echo json_encode(['success' => true, 'message' => 'User has been deleted.']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Invalid ID']);
    }
}
?>
