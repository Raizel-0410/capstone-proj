<?php
require 'db_connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $caption_title = $_POST['caption_title'] ?? null;
    $caption_text  = $_POST['caption_text'] ?? null;
    $imagePath = null;

    // Handle image upload if provided
    if (!empty($_FILES['image']['name'])) {
        $targetDir = "uploads/carousel/";
        if (!is_dir($targetDir)) mkdir($targetDir, 0777, true);

        $fileName = time() . "_" . basename($_FILES["image"]["name"]);
        $targetFile = $targetDir . $fileName;

        if (move_uploaded_file($_FILES["image"]["tmp_name"], $targetFile)) {
            $imagePath = $targetFile;
        }
    }

    $stmt = $pdo->prepare("INSERT INTO landing_carousel (image_path, caption_title, caption_text) VALUES (:image, :title, :text)");
    $stmt->execute([
        ':image' => $imagePath,
        ':title' => $caption_title,
        ':text'  => $caption_text
    ]);

    header("Location: customizelanding.php?msg=Carousel updated");
    exit;
}
