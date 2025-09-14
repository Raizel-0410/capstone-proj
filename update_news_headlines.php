<?php
require 'db_connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title       = $_POST['title'] ?? '';
    $description = $_POST['description'] ?? '';
    $imagePath   = null;

    if (!empty($_FILES['image']['name'])) {
        $targetDir = "uploads/news_headlines/";
        if (!is_dir($targetDir)) mkdir($targetDir, 0777, true);

        $fileName = time() . "_" . basename($_FILES["image"]["name"]);
        $targetFile = $targetDir . $fileName;

        if (move_uploaded_file($_FILES["image"]["tmp_name"], $targetFile)) {
            $imagePath = $targetFile;
        }
    }

    $stmt = $pdo->prepare("INSERT INTO news_headlines (title, description, image_path) VALUES (:title, :desc, :image)");
    $stmt->execute([
        ':title' => $title,
        ':desc'  => $description,
        ':image' => $imagePath
    ]);

    header("Location: customizelanding.php?msg=Headline added");
    exit;
}
