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

$comments = [];

$stmt = $pdo->prepare("SELECT c.comment_text, c.timestamp, u.username, c.image_filename
                      FROM comments c
                      JOIN users u ON c.user_id = u.id
                      ORDER BY c.timestamp DESC");
$stmt->execute();

while ($row = $stmt->fetch()) {
    $comments[] = [
        'username' => $row['username'],
        'comment_text' => $row['comment_text'],
        'timestamp' => $row['timestamp'],
        'image_filename' => $row['image_filename']
    ];
}

echo json_encode($comments);
?>
