<?php
    require_once('session.php');
    require_once('dbconfig.php');
    require_once('function.php');

    if (isset($_SESSION['user'])) {
        header("Location: home.php");
        exit();
    }

    $success_message = null;
    $error_messages = [];

    if ($_SERVER['REQUEST_METHOD'] == 'POST' && $_POST['terms'] == 'on') {
        $names = trim(htmlspecialchars($_POST['names']));
        $email = trim(htmlspecialchars($_POST['email']));
        $password = trim(htmlspecialchars($_POST['password']));
        $phone = trim(htmlspecialchars($_POST['phone']));
        $password_confirm = trim(htmlspecialchars($_POST['password_confirm']));

        if ($password !== $password_confirm) {
            $error_messages[] = 'Passwords do not match.';
        } elseif (strlen($password) < 8) {
            $error_messages[] = 'Password must be at least 8 characters long.';
        } else {
           
            $password_hash = password_hash($password, PASSWORD_DEFAULT);
        }

        $formatted_phone = preg_replace('/^0/', '+254', $phone);

        // Check if email and phone number already exist in the database
        $email_exists = checkIfExists($email, null);
        $phone_exists = checkIfExists(null, $formatted_phone);

        if ($email_exists) {
            $error_messages[] = 'Email already exists. Please use a different email address.';
        }

        if ($phone_exists) {
            $error_messages[] = 'Phone number already exists. Please use a different phone number.';
        }
        

        if (count($error_messages) == 0) {
            try {
                $conn = getDB();
                $sql = "INSERT INTO users (names, email, phone, password) VALUES ('$names', '$email', '$formatted_phone', '$password_hash')";
                $conn->exec($sql);
                $success_message = "Account created successfully!";

               

                $_SESSION['registration_data'] = [
                    'email' => $email,
                ];
                header("Location: login.php");
            } catch(PDOException $e) {
                echo $e->getMessage();
            }

            $conn = null;
            ?>
            <div class="alert alert-success" role="alert">
            <?php echo $success_message; ?>
        </div> 
        <?php
        }
    }
        ?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
    <?php foreach($error_messages as $error_message): ?>
            <div class="alert alert-danger" role="alert">
                <?php echo $error_message; ?>
            </div> 
        <?php endforeach;?>
        <div class="container">
            <div class="row justify-content-center mt-5">
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title text-center">Register</h4>
                        </div>
                        <div class="card-body">
                            <form action="<?php echo(htmlentities($_SERVER['PHP_SELF'])) ?>" method="POST">
                                <div class="mb-3">
                                    <label for="names" class="form-label">Your names</label>
                                    <input name="names" type="text" class="form-control" id="names" aria-describedby="namesHelp" placeholder="Your names">
                                    <div id="namesHelp" class="form-text">All your names</div>
                                </div>
                                <div class="mb-3">
                                    <label for="email" class="form-label">Email</label>
                                    <input type="email" name="email" class="form-control" id="email" aria-describedby="emailHelp" placeholder="Your email">
                                    <div id="emailHelp" class="form-text">Your email address.</div>
                                </div>
                                <div class="mb-3">
                                    <label for="phone" class="form-label">Phone</label>
                                    <input type="phone" name="phone" class="form-control" id="phone" aria-describedby="phoneHelp" placeholder="Your phone">
                                    <div id="phoneHelp" class="form-text">Your phone number.</div>
                                </div>
                                <div class="mb-3">
                                    <label for="password" class="form-label">Password</label>
                                    <input type="password" name="password" class="form-control" id="password" placeholder="Passsword">
                                </div>
                                <div class="mb-3">
                                    <label for="password" class="form-label">Confirm Password</label>
                                    <input type="password" name="password_confirm" class="form-control" id="password_confirm" placeholder="Passsword">
                                </div>
                                <div class="mb-3 form-check">
                                    <input type="checkbox" class="form-check-input" id="terms" name="terms" checked>
                                    <label class="form-check-label" for="terms">I agree to the <a href="terms-and-conditions.php">terms and conditions</a></label>
                                </div>
                                <div class="d-grid">
                                    <button type="submit" class="btn btn-primary">Register</button>
                                </div>
                            </form>
                        </div>
                        <div class="card-footer text-center">
                            <a href="login.php">Already registered? Login</a>
                        </div>
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