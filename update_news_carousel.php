<?php
require 'db_connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $imagePath = null;

    if (!empty($_FILES['image']['name'])) {
        $targetDir = "uploads/news_carousel/";
        if (!is_dir($targetDir)) mkdir($targetDir, 0777, true);

        $fileName = time() . "_" . basename($_FILES["image"]["name"]);
        $targetFile = $targetDir . $fileName;

        if (move_uploaded_file($_FILES["image"]["tmp_name"], $targetFile)) {
            $imagePath = $targetFile;
        }
    }

    $stmt = $pdo->prepare("INSERT INTO news_carousel (image_path) VALUES (:image)");
    $stmt->execute([':image' => $imagePath]);

    header("Location: customizelanding.php?msg=News Carousel updated");
    exit;
}
