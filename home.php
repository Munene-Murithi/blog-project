<?php
require_once('session.php');
require_once('dbconfig.php');

if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}

try {
    $conn = getDB();

    // Prepare and execute the SQL statement
    $stmt = $conn->prepare("UPDATE users SET lastLogin = CURRENT_TIMESTAMP WHERE id = :userid");
    $stmt->bindParam(':userid', $userid);
    $stmt->execute();

    $sql = "SELECT * FROM posts WHERE 1=1";
    if (!empty($_GET['date'])) {
        $date = $_GET['date'];
        $sql .= " AND DATE(timestamp) = '$date'";
    }
    if (!empty($_GET['names'])) {
        $names = $_GET['names'];
        $sql .= " AND names LIKE '%$names%'";
    }
    if (!empty($_GET['keywords'])) {
        $keywords = $_GET['keywords'];
        $keywords = explode(" ", $keywords);
        foreach ($keywords as $keyword) {
            $sql .= " AND (title LIKE '%$keyword%' OR body LIKE '%$keyword%')";
        }
    }
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $posts = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $posts = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $posts = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Home</title>
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
    <h1>Recent Posts</h1>
    <form action="" method="GET">
    <div class="form-row">
        <div class="form-group col-md-4">
        <label for="inputDate">Date</label>
        <input type="date" class="form-control" name="date" id="inputDate">
        </div>
        <div class="form-group col-md-4">
        <label for="inputNames">Names</label>
        <input type="text" class="form-control" name="names" id="inputNames">
        </div>
        <div class="form-group col-md-4">
        <label for="inputKeyword">Keyword/phrase</label>
        <input type="text" class="form-control" name="keywords" id="inputKeyword">
        </div>
    </div>
    <button type="submit" class="btn btn-primary">Filter</button>
    </form>
    <?php
    if ($posts) {
    foreach ($posts as $row) {
        echo "<div class='card mb-3'>";
        echo "<div class='card-body'>";
        echo "<h5 class='card-title'>" . $row['title'] . "</h5>";
        echo "<p class='card-text'>" . $row['body'] . "</p>";
        echo "<p class='card-text'><small class='text-muted'>By " . $row['names'] . " on " . date('F j, Y', strtotime($row['timestamp'])) . "</small></p>";
        echo "<a href='view_post.php?id=" . $row['id'] . "' class='btn btn-primary'>Read More</a>";
        echo "</div></div>";
    }
    } else {
    echo "<p>No posts found.</p>";
    }
    ?>
</div>
<!-- Footer -->
<footer class="page-footer font-small">
    <div class="footer-copyright text-center py-3">© 2023 Copyright:
        <a href="https://github.com/briankod/My_Blog.git">My_Blog</a>
    </div>
</footer>
<!-- Footer -->
<script src="https://code.jquery.com/jquery-3.2.1.slim.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"></script>
</body>
</html>
