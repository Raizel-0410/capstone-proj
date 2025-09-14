<?php
require 'db_connect.php';

// Only fetch accounts that are NOT deleted
$stmt = $pdo->prepare("SELECT * FROM users WHERE deleted_account = 0 ORDER BY joined_date DESC");
$stmt->execute();
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode($users);
?>
