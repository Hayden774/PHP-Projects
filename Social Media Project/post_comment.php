<?php
session_start();

$host = 'localhost';
$db   = 'users_db';
$user = 'root';
$pass = '';

$dsn = "mysql:host=$host;dbname=$db;charset=utf8mb4";
$options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES => false,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (\PDOException $e) {
    throw new \PDOException($e->getMessage(), (int)$e->getCode());
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_SESSION['user_id'];
    $commentText = $_POST['comment_text']; // Corrected line

    // Handle image upload
    $imageFilename = null;
    if (!empty($_FILES['comment_image']['name'])) {
        $uploadDir = 'uploads/';
        $uploadedFile = $_FILES['comment_image']['tmp_name'];
        $fileExtension = pathinfo($_FILES['comment_image']['name'], PATHINFO_EXTENSION);
        $newFilename = uniqid() . '.' . $fileExtension;
        $targetFilePath = $uploadDir . $newFilename;

        if (move_uploaded_file($uploadedFile, $targetFilePath)) {
            $imageFilename = $newFilename;
        }
    }

    $stmt = $pdo->prepare("INSERT INTO comments (user_id, comment_text, image_filename, timestamp) VALUES (?, ?, ?, NOW())");
    $stmt->execute([$user_id, $commentText, $imageFilename]);

    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false]);
}
?>
