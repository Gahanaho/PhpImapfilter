<?php
// includes/auth.php
// Change these credentials or move them to a .env / external config
define('ADMIN_USER', 'admin');
define('ADMIN_PASS_HASH', password_hash('changeme', PASSWORD_BCRYPT)); // replace hash in production

function isLoggedIn(): bool {
    return !empty($_SESSION['authenticated']) && $_SESSION['authenticated'] === true;
}

function login(string $user, string $pass): bool {
    if ($user === ADMIN_USER && password_verify($pass, ADMIN_PASS_HASH)) {
        $_SESSION['authenticated'] = true;
        $_SESSION['user'] = $user;
        $_SESSION['login_time'] = time();
        return true;
    }
    return false;
}

function requireLogin(): void {
    if (!isLoggedIn()) {
        header('Location: index.php');
        exit;
    }
}

function logout(): void {
    session_destroy();
    header('Location: index.php');
    exit;
}
