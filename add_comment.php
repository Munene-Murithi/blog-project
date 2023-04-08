<?php
require_once('session.php');
require_once('dbconfig.php');

if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get the comment from the form submission
    $comment = isset($_POST['comment']) ? $_POST['comment'] : null;
    $post_id = isset($_POST['post_id']) ? $_POST['post_id'] : null;

    try {
        $conn = getDB();

        // Get the ID of the currently logged in user
        $current_user_id = $_SESSION['user']['id'];
        $current_user_name = $_SESSION['user']['names'];

        // Get the post ID of the post being commented on
        $stmt = $conn->prepare("SELECT id FROM posts WHERE id = :post_id");
        $stmt->bindParam(':post_id', $post_id);
        $stmt->execute();

        $post = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($post) {
            // If the post exists, insert a new comment into the database
            $post_id = $post['id'];
            $stmt = $conn->prepare("INSERT INTO comments (post_id, user_id, names, comment) VALUES (:post_id, :user_id, :names, :comment)");
            $stmt->bindParam(':post_id', $post_id);
            $stmt->bindParam(':user_id', $current_user_id);
            $stmt->bindParam(':names', $current_user_name);
            $stmt->bindParam(':comment', $comment);
            $stmt->execute();

            // Redirect back to the post page
            header("Location: view_post.php?id=$post_id");
            exit();
        } else {
            // If the post doesn't exist, output an error message
            echo "Error: Post not found.";
            exit();
        }

    } catch(PDOException $e) {
        echo "Error: " . $e->getMessage();
        exit();
    }
}

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    try {
        $conn = getDB();

        // Prepare and execute the SQL statement to select the post with the given ID
        $stmt = $conn->prepare("SELECT * FROM posts WHERE id = :id");
        $stmt->bindParam(':id', $id);
        $stmt->execute();

        $post = $stmt->fetch(PDO::FETCH_ASSOC);
        
        // Prepare and execute the SQL statement to select all comments for the post with the given ID
        $stmt = $conn->prepare("SELECT comments.*, users.names FROM comments INNER JOIN users ON comments.user_id = users.id WHERE post_id = :post_id ORDER BY timestamp ASC");
        $stmt->bindParam(':post_id', $id);
        $stmt->execute();

        $comments = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch(PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}
?>
