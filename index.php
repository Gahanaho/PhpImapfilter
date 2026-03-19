<?php
session_start();
require_once 'includes/auth.php';
require_once 'includes/config.php';

if (isLoggedIn()) {
    header('Location: dashboard.php');
    exit;
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    if (login($username, $password)) {
        header('Location: dashboard.php');
        exit;
    } else {
        $error = 'Ungültige Zugangsdaten.';
    }
}
?>
<!DOCTYPE html>
<html lang="de">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>IMAPFilter — Login</title>
<link rel="stylesheet" href="assets/css/main.css">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=JetBrains+Mono:wght@300;400;600;700&family=Syne:wght@400;700;800&display=swap" rel="stylesheet">
</head>
<body class="login-body">
<div class="login-bg">
  <div class="grid-lines"></div>
  <div class="scanlines"></div>
</div>
<div class="login-container">
  <div class="login-logo">
    <span class="logo-icon">⬡</span>
    <span class="logo-text">IMAPFILTER</span>
    <span class="logo-version">v2.8 · WEB UI</span>
  </div>
  <?php if ($error): ?>
  <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
  <?php endif; ?>
  <form method="POST" class="login-form">
    <div class="field-group">
      <label class="field-label">USER_</label>
      <input type="text" name="username" class="field-input" autocomplete="username" autofocus placeholder="admin">
    </div>
    <div class="field-group">
      <label class="field-label">PASS_</label>
      <input type="password" name="password" class="field-input" autocomplete="current-password" placeholder="••••••••">
    </div>
    <button type="submit" class="btn-primary">
      <span class="btn-arrow">→</span> AUTHENTICATE
    </button>
  </form>
  <div class="login-footer">imapfilter web interface · session-based auth</div>
</div>
</body>
</html>
