<?php 
require_once('session.php');
require_once('dbconfig.php');
require_once('function.php');

if (isset($_SESSION['user'])) {
    header("Location: home.php");
    exit();
}

$email = '';
$login_success = null;

if (isset($_SESSION['registration_data'])) {
   $email = $_SESSION['registration_data']['email'];
   session_unset();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $email = trim(htmlspecialchars($_POST['email']));
    $password = trim(htmlspecialchars($_POST['password']));

    try {
        $pdo = getDB();

        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);

        // set the resulting array to associative
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if ($results && (count($results) == 1)) {
            $user = $results[0];

            $user_password_hash = $user['password'];

            if (password_verify($password, $user_password_hash)) { //successfull login
                $login_success = true;

                $_SESSION['user'] = $user; // create session data for logged in user

                if (isset($_POST['remember']) && $_POST['remember'] == 'on') {
                    $cookie_value = encrypt_cookie_value($user['id'], 'my-secret-key');
                    $cookie_value = urlencode($cookie_value); // encode the encrypted value for safe transmission in the URL
                    $cookie_expire = time() + 30 * 24 * 60 * 60; // 30 days
                    setcookie('userid', $cookie_value, $cookie_expire, "/");
                }
                header("Location: home.php"); // redirect to home page
                exit();
            }
        }

        $login_success = false;

    } catch(PDOException $e) {
        echo $e->getMessage();
    }
}

//check if cookie exists and decrypt it
if (isset($_COOKIE['userid'])) {
    $cookie_value = urldecode($_COOKIE['userid']); // decode the encrypted value from the URL
    $userid = decrypt_cookie_value($cookie_value, 'my-secret-key');

    if ($userid) {
        try {
            $pdo = getDB();

            $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
            $stmt->execute([$userid]);

            // set the resulting array to associative
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if ($results && (count($results) == 1)) {
                $user = $results[0];
                $_SESSION['user'] = $user;
                header("Location: home.php"); // redirect to home page
                exit();
            }

            $login_success = false;

        } catch(PDOException $e) {
            echo $e->getMessage();
        }
    } else {
        // cookie is invalid, delete it
        setcookie('userid', '', time() - 3600, "/");
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="container">
    <div class="row justify-content-center mt-5">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title text-center">Login</h4>
                </div>
                <div class="card-body">
                    <form action="<?php echo(htmlentities($_SERVER['PHP_SELF'])) ?>" method="POST">
                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" name="email" class="form-control" id="email" value="<?php echo $email; ?>" aria-describedby="emailHelp" placeholder="Your email">
                            <div id="emailHelp" class="form-text">Your email address.</div>
                        </div>
                        <div class="mb-3">
                            <label for="password" class="form-label">Password</label>
                            <input type="password" name="password" class="form-control <?php echo ($login_success === false) ? 'is-invalid' : '' ?>" id="password" placeholder="Passsword">
                            <div class="invalid-feedback">
                                Your credentials are invalid
                            </div>
                        </div>
                        <div class="mb-3 form-check">
                            <input type="checkbox" class="form-check-input" name="remember" id="remember-me">
                            <label class="form-check-label" for="remember-me">Remember me</label>
                        </div>
                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary">Login</button>
                        </div>
                    </form>
                </div>
                <div class="card-footer text-center">
                    <a href="register.php">Don't have an account? Register here</a>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Footer -->
<footer class="page-footer font-small">
    <div class="footer-copyright text-center py-3">© 2023 Copyright:
        <a href="https://github.com/briankod/My_Blog.git">My_Blog</a>
    </div>
</footer>
<!-- Footer -->
<!-- Bootstrap JS -->
<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>
</body>
</html>
