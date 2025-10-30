<?php
// includes/auth.php
session_start();

function require_login() {
    if (empty($_SESSION['admin_logged_in'])) {
        header('Location: /admin/login.php');
        exit;
    }
}

function login($userId, $username) {
    session_regenerate_id(true);
    $_SESSION['admin_logged_in'] = true;
    $_SESSION['admin_id'] = $userId;
    $_SESSION['admin_username'] = $username;
}

function logout() {
    $_SESSION = [];
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $params["path"], $params["domain"],
            $params["secure"], $params["httponly"]
        );
    }
    session_destroy();
}
