<?php
require 'db_connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title   = $_POST['title'] ?? '';
    $content = $_POST['content'] ?? '';

    $stmt = $pdo->prepare("UPDATE landing_about_us SET title = :title, content = :content WHERE id = 1");
    $stmt->execute([
        ':title'   => $title,
        ':content' => $content
    ]);

    header("Location: customizelanding.php?msg=About Us updated");
    exit;
}
