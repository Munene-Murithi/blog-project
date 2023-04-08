<?php
require_once('session.php');

// unset session and cookie
session_unset();
if (isset($_COOKIE['userid'])) {
    setcookie('userid', '', time() - 3600, "/");
}

// redirect to login page
header('Location: login.php');
exit();
?>
