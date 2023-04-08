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
    $stmt->bindParam(':userid', $_SESSION['user']['id']);
    $stmt->execute();

    $stmt = $conn->prepare("SELECT * FROM users WHERE id = :userid");
    $stmt->bindParam(':userid', $_SESSION['user']['id']);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    // Check if the query was successful before trying to access the user data
    if ($user && is_array($user)) {
        $email = isset($user['email']) ? $user['email'] : "N/A";
        $lastLogin = isset($user['lastLogin']) ? $user['lastLogin'] : "N/A";
    } else {
        // Handle the case where the query failed
        $email = "N/A";
        $lastLogin = "N/A";
    }
    // Generate avatar image URL from user's name or email using third-party service
    $avatar_url = "https://www.gravatar.com/avatar/" . md5(strtolower(trim($email))) . "?s=200&d=identicon";

} catch(PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Profile</title>
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
            <li class="nav-item">
                <a class="nav-link" href="home.php">Home</a>
            </li>
            <li class="nav-item active">
                <a class="nav-link" href="#">Profile <span class="sr-only">(current)</span></a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="create_post.php">Create Post</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="logout.php">Logout</a>
            </li>
        </ul>
    </div>
</nav>
<div class="container mt-5">
    <div class="row">
        <div class="col-md-4">
            <img src="<?php echo $avatar_url ?>" alt="Avatar" class="img-thumbnail">
        </div>
        <div class="col-md-8">
            <h1><?php echo $user['names'] ?></h1>
            <p><strong>Email:</strong> <?php echo $user['email'] ?></p>
            <p><strong>Last Login:</strong> <?php echo date('F j, Y, g:i a', strtotime($user['lastLogin'])) ?></p>
        </div>
    </div>
</div>
<script src="https://code.jquery.com/jquery-3.2.1.slim.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"></script>
</body>
</html>