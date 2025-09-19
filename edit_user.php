<?php
require 'auth_check.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method']);
    exit;
}

// Get POST data and sanitize
$id = $_POST['id'] ?? '';
$full_name = $_POST['full_name'] ?? '';
$email = $_POST['email'] ?? '';
$rank = $_POST['rank'] ?? '';
$role = $_POST['role'] ?? '';
$status = $_POST['status'] ?? '';

if (empty($id) || empty($full_name) || empty($email)) {
    echo json_encode(['status' => 'error', 'message' => 'Required fields missing']);
    exit;
}

// Update user in DB
$stmt = $pdo->prepare("UPDATE users SET full_name = :full_name, email = :email, rank = :rank, role = :role, status = :status WHERE id = :id");
$success = $stmt->execute([
    ':full_name' => $full_name,
    ':email' => $email,
    ':rank' => $rank,
    ':role' => $role,
    ':status' => $status,
    ':id' => $id
]);

if ($success) {
    echo json_encode(['status' => 'success', 'message' => 'User updated successfully']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Failed to update user']);
}
?>
