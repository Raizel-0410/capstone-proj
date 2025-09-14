<?php
require 'db_connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title   = $_POST['title'] ?? '';
    $linkUrl = $_POST['link_url'] ?? '#';
    $imagePath = null;

    if (!empty($_FILES['image']['name'])) {
        $targetDir = "uploads/vision/";
        if (!is_dir($targetDir)) mkdir($targetDir, 0777, true);

        $fileName = time() . "_" . basename($_FILES["image"]["name"]);
        $targetFile = $targetDir . $fileName;

        if (move_uploaded_file($_FILES["image"]["tmp_name"], $targetFile)) {
            $imagePath = $targetFile;
        }
    }

    $stmt = $pdo->prepare("INSERT INTO landing_vision_mission (title, image_path, link_url) VALUES (:title, :image, :link)");
    $stmt->execute([
        ':title' => $title,
        ':image' => $imagePath,
        ':link'  => $linkUrl
    ]);

    header("Location: customizelanding.php?msg=Vision/Mission updated");
    exit;
}
