<?php
require_once('session.php');
require_once('dbconfig.php');

if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}

// Check if the form was submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $names = isset($_POST['names']) ? trim($_POST['names']) : null;
    $title = isset($_POST['title']) ? trim($_POST['title']) : null;
    $body = isset($_POST['body']) ? trim($_POST['body']) : null;

    try {
        $conn = getDB();

        // Check if the names matches names in the users table
        $stmt = $conn->prepare("SELECT id FROM users WHERE TRIM(names) = :names");
        $stmt->bindParam(':names', $names);
        $stmt->execute();

        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user) {
            $user_id = $user['id'];

            // Insert the new post into the posts table
            $stmt = $conn->prepare("INSERT INTO posts (user_id, names, title, body) VALUES (:user_id, :names, :title, :body)");
            $stmt->bindParam(':user_id', $user_id);
            $stmt->bindParam(':title', $title);
            $stmt->bindParam(':body', $body);
            $stmt->bindParam(':names', $names);
            $stmt->execute();

            // Redirect back to the view_posts.php page
            header("Location: view_posts.php");
            exit();
        } else {
            // If the user doesn't exist, output an error message
            echo "Error: User not found.";
        }
    } catch(PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}

try {
    $conn = getDB();

    // Prepare and execute the SQL statement to select the post by ID
    $stmt = $conn->prepare("SELECT posts.*, users.names FROM posts INNER JOIN users ON posts.user_id = users.id WHERE posts.id = :id");
    $stmt->bindParam(':id', $_GET['id'], PDO::PARAM_INT);
    $stmt->execute();

    $post = $stmt->fetch(PDO::FETCH_ASSOC);

    // Prepare and execute the SQL statement to select all comments for the post
    $stmt = $conn->prepare("SELECT comments.*, users.names FROM comments INNER JOIN users ON comments.user_id = users.id WHERE post_id = :post_id ORDER BY timestamp DESC");
    $stmt->bindParam(':post_id', $_GET['id'], PDO::PARAM_INT);
    $stmt->execute();

    $comments = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>View Post</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="style.css">
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-light bg-light">
    <a class="navbar-brand" href="#">My Blog</a>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav"
            aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav">
            <li class="nav-item active">
                <a class="nav-link" href="#">Home <span class="sr-only">(current)</span></a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="profile.php">Profile</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="create_post.php">Create Post</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="logout.php">logout</a>
            </li>
        </ul>
    </div>
</nav>
<div class="container mt-5">
    <a href="home.php" class="btn btn-secondary mb-3">Back to Home</a>
    <div class="card mb-3">
        <div class="card-body">
            <h5 class="card-title"><?php echo ($post['title'] ?? ''); ?></h5>
            <p class="card-text"><?php echo ($post['body'] ?? ''); ?></p>
            <?php
            ?>
            <?php if (!empty($post)): ?>
            <p class="card-text"><small class="text-muted">Posted by <?php echo ($post['names'] ?? ''); ?> on <?php echo date('F j, Y', strtotime($post['timestamp']) ?? ''); ?></small></p>
            <?php endif; ?>
            <?php if (!empty($post) && isset($_SESSION['user']['id']) && $_SESSION['user']['id'] == $post['user_id']) { ?>
            <form method="post" action="delete_post.php">
                <input type="hidden" name="post_id" value="<?php echo $post['id']; ?>">
                <button type="submit" class="btn btn-danger">Delete</button>
            </form>
<?php } ?>
        </div>
    </div>
    <h3>Comments</h3>
    <?php if (count($comments) > 0) { ?>
        <?php foreach($comments as $comment) { ?>
            <div class="card my-3">
            <div class="card-body">
                <p class="card-text"><?php echo ($comment['comment'] ?? ''); ?></p>
                <?php if (!empty($comment)): ?>
                <p class="card-subtitle"><small class="text-muted">By <?php echo ($comment['names'] ?? ''); ?> on <?php echo date('F j, Y', strtotime($comment['timestamp']) ?? ''); ?></small></p>
                <?php endif; ?>
                <?php if (!empty($comment) && isset($_SESSION['user']['id']) && $_SESSION['user']['id'] == $comment['user_id']) { ?>
                <form method="post" action="delete_comment.php">
                    <input type="hidden" name="comment" value="<?php echo $comment['comment']; ?>">
                    <button type="submit" class="btn btn-danger">Delete</button>
                </form>
            <?php } ?>
            </div>
            </div>
        <?php } ?>
        <?php } else { ?>
        <div class="card my-3">
            <div class="card-body">
            <p class="card-text">No comments yet.</p>
            </div>
        </div>
    <?php } ?>

        <!-- Add comment form -->
    <div class="card">
        <div class="card-body">
            <?php if (isset($_SESSION['user'])) { ?>
                <h3>Add a Comment</h3>
                <form method="post" action="add_comment.php">
                    <div class="form-group">
                        <label for="comment">Comment:</label>
                        <textarea name="comment" class="form-control" required></textarea>
                    </div>
                    <input type="hidden" name="post_id" value="<?php echo $post['id']; ?>">
                    <button type="submit" class="btn btn-primary">Submit</button>
                </form>
                <?php } else { ?>
                    <p>Please <a href="login.php">login</a> to add a comment.</p>
            <?php } ?>
        </div>
    </div>
</div> <!-- End of container div -->
<!-- Footer -->
<footer class="page-footer font-small">
    <div class="footer-copyright text-center py-3">© 2023 Copyright:
        <a href="https://github.com/briankod/My_Blog.git">My_Blog</a>
    </div>
</footer>
<!-- Footer -->
</body>
</html>
