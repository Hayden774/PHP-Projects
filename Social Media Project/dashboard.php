<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

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
?>

<!DOCTYPE html>
<html>
<head>
    <title>Dashboard</title>
    <style>
        /* Add your CSS styles here */
    </style>
</head>
<body>
    <h1>Welcome, <?= $username ?>!</h1>
    
    <div id="comments">
        <!-- Comments will be dynamically added here -->
    </div>
    
  <!---  <form id="comment-form">
        <input type="text" id="username" value="" disabled>
        <textarea id="comment" placeholder="Leave a comment"></textarea>
        <button type="submit">Submit</button>
    </form> -->

    <a href="comment.php"><button>Post a Comment</button></a>
    
    <script>
        const commentsContainer = document.getElementById('comments');
        const commentForm = document.getElementById('comment-form');
        const usernameInput = document.getElementById('username');
        const commentInput = document.getElementById('comment');
        
        // Function to fetch and display comments
        function fetchComments() {
    fetch('get_comments.php')
        .then(response => response.json())
        .then(data => {
            commentsContainer.innerHTML = '';
            data.forEach(comment => {
                const commentElement = document.createElement('div');
                commentElement.classList.add('comment');
                commentElement.innerHTML = `
                    <p><strong>${comment.username}</strong></p>
                    <p>${comment.comment_text}</p>
                    ${comment.image_filename ? `<img src="uploads/${comment.image_filename}" alt="Comment Image" width="200">` : ''}
                    <p>${comment.timestamp}</p>
                `;
                commentsContainer.appendChild(commentElement);
            });
        });
}
        
        // Fetch comments on page load
        fetchComments();
        
        // Submit comment using AJAX
        commentForm.addEventListener('submit', function (event) {
            event.preventDefault();
            
            const formData = new FormData(commentForm);
            
            fetch('post_comment.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Clear form fields
                    usernameInput.value = '';
                    commentInput.value = '';
                    
                    // Fetch and display updated comments
                    fetchComments();
                } else {
                    alert('Failed to post comment.');
                }
            });
        });
    </script>
</body>
</html>
