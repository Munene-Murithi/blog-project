<?php
require_once('session.php');
require_once('dbconfig.php');

if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}

// Check if the form was submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    try {
        $conn = getDB();

        // Delete the comment from the comments table
        $stmt = $conn->prepare("DELETE FROM comments WHERE comment = :comment AND user_id = :user_id");
        $stmt->bindParam(':comment', $_POST['comment']);
        $stmt->bindParam(':user_id', $_SESSION['user']['id']);
        $stmt->execute();

        // Redirect back to the home.php page
        header("Location: home.php");
        exit();
    } catch(PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}
?>
