<?php
// includes/layout.php
function pageHeader(string $title, string $active = ''): void {
    $nav = [
        'dashboard' => ['href' => 'dashboard.php',  'icon' => '◈', 'label' => 'DASHBOARD'],
        'accounts'  => ['href' => 'accounts.php',   'icon' => '◎', 'label' => 'ACCOUNTS'],
        'rules'     => ['href' => 'rules.php',       'icon' => '◆', 'label' => 'FILTER RULES'],
        'config'    => ['href' => 'config.php',      'icon' => '◉', 'label' => 'CONFIG.LUA'],
        'runner'    => ['href' => 'runner.php',      'icon' => '▶', 'label' => 'RUN / LOG'],
    ];
    ?>
<!DOCTYPE html>
<html lang="de">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>IMAPFilter · <?= htmlspecialchars($title) ?></title>
<link rel="stylesheet" href="assets/css/main.css">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=JetBrains+Mono:wght@300;400;600;700&family=Syne:wght@400;700;800&display=swap" rel="stylesheet">
</head>
<body>
<div class="scanlines"></div>
<nav class="sidebar">
  <div class="sidebar-logo">
    <span class="logo-icon">⬡</span>
    <div>
      <div class="logo-text">IMAPFILTER</div>
      <div class="logo-sub">WEB CONTROL</div>
    </div>
  </div>
  <ul class="nav-list">
    <?php foreach ($nav as $key => $item): ?>
    <li class="nav-item <?= $active === $key ? 'active' : '' ?>">
      <a href="<?= $item['href'] ?>" class="nav-link">
        <span class="nav-icon"><?= $item['icon'] ?></span>
        <span class="nav-label"><?= $item['label'] ?></span>
      </a>
    </li>
    <?php endforeach; ?>
  </ul>
  <div class="sidebar-footer">
    <span class="session-user">● <?= htmlspecialchars($_SESSION['user'] ?? 'user') ?></span>
    <a href="logout.php" class="btn-logout">LOGOUT</a>
  </div>
</nav>
<main class="main-content">
  <div class="page-header">
    <h1 class="page-title"><?= htmlspecialchars($title) ?></h1>
    <div class="page-meta">
      <span class="timestamp" id="ts"></span>
    </div>
  </div>
  <div class="page-body">
    <?php
}

function pageFooter(): void {
    ?>
  </div><!-- .page-body -->
</main>
<script src="assets/js/main.js"></script>
</body>
</html>
    <?php
}
