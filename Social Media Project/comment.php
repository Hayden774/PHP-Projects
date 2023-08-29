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

$user_id = $_SESSION['user_id'];
$stmt = $pdo->prepare("SELECT username FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();

if (!$user) {
    // Handle user not found
    exit();
}

$username = $user['username'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $commentText = $_POST['comment_text'];

   /* $stmt = $pdo->prepare("INSERT INTO comments (user_id, comment_text, timestamp) VALUES (?, ?, NOW())");
    $stmt->execute([$user_id, $commentText]);*/

    // Handle image upload
    $uploadDir = 'uploads/';  // Specify the directory where images will be stored
    $uploadedFile = $_FILES['comment_image']['tmp_name'];
    $fileExtension = pathinfo($_FILES['comment_image']['name'], PATHINFO_EXTENSION);
    $newFilename = uniqid() . '.' . $fileExtension;
    $targetFilePath = $uploadDir . $newFilename;
    
    if (move_uploaded_file($uploadedFile, $targetFilePath)) {
        // Image uploaded successfully
        $stmt = $pdo->prepare("INSERT INTO comments (user_id, comment_text, image_filename, timestamp) VALUES (?, ?, ?, NOW())");
        $stmt->execute([$user_id, $commentText, $newFilename]);
    } else {
        // Image upload failed
        // Handle error or display a message to the user
    }

    header("Location: dashboard.php");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Comment</title>
    <!-- Add your CSS styles here -->
</head>
<body>
    <h1>Post a Comment</h1>
    
    <form method="post" enctype="multipart/form-data">
    <input type="text" value="<?= $username ?>" disabled>
    <textarea name="comment_text" placeholder="Leave a comment"></textarea>
    <input type="file" name="comment_image">
    <button type="submit">Submit</button>
</form>
</body>
</html>
