<?php
session_start();
session_destroy();
header("Location: ../views/login.html");
exit;

if (password_verify($password, $user['password'])) {
    // success
}
