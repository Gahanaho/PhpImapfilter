<?php
session_start();
require_once 'includes/auth.php';
require_once 'includes/config.php';
require_once 'includes/layout.php';
requireLogin();

$accounts = readAccounts();
$rules    = readRules();
$configExists = file_exists(IMAPFILTER_CONFIG);
$logExists    = file_exists(IMAPFILTER_LOG);
$lastRun = $logExists ? date('Y-m-d H:i:s', filemtime(IMAPFILTER_LOG)) : 'never';

pageHeader('DASHBOARD', 'dashboard');
?>

<div class="stats-grid">
  <div class="stat-card">
    <div class="stat-icon">◎</div>
    <div class="stat-value"><?= count($accounts) ?></div>
    <div class="stat-label">ACCOUNTS</div>
  </div>
  <div class="stat-card">
    <div class="stat-icon">◆</div>
    <div class="stat-value"><?= count($rules) ?></div>
    <div class="stat-label">FILTER RULES</div>
  </div>
  <div class="stat-card">
    <div class="stat-icon">◉</div>
    <div class="stat-value"><?= $configExists ? 'OK' : 'MISSING' ?></div>
    <div class="stat-label">CONFIG.LUA</div>
    <div class="stat-badge <?= $configExists ? 'badge-ok' : 'badge-warn' ?>"><?= $configExists ? 'found' : 'not found' ?></div>
  </div>
  <div class="stat-card">
    <div class="stat-icon">▶</div>
    <div class="stat-value"><?= $logExists ? 'ACTIVE' : 'NO LOG' ?></div>
    <div class="stat-label">LAST RUN</div>
    <div class="stat-badge badge-ok"><?= $lastRun ?></div>
  </div>
</div>

<div class="dash-grid">
  <div class="panel">
    <div class="panel-header">
      <span class="panel-icon">◎</span> CONFIGURED ACCOUNTS
    </div>
    <div class="panel-body">
      <?php if (empty($accounts)): ?>
        <div class="empty-state">No accounts configured yet. <a href="accounts.php">Add one →</a></div>
      <?php else: ?>
        <table class="data-table">
          <thead><tr><th>NAME</th><th>HOST</th><th>USER</th><th>TLS</th></tr></thead>
          <tbody>
          <?php foreach ($accounts as $acc): ?>
            <tr>
              <td><span class="tag"><?= htmlspecialchars($acc['name']) ?></span></td>
              <td><?= htmlspecialchars($acc['host']) ?>:<?= htmlspecialchars($acc['port'] ?? '993') ?></td>
              <td><?= htmlspecialchars($acc['username']) ?></td>
              <td><?= !empty($acc['tls']) ? '<span class="badge-ok">TLS</span>' : '<span class="badge-warn">NO</span>' ?></td>
            </tr>
          <?php endforeach; ?>
          </tbody>
        </table>
      <?php endif; ?>
    </div>
  </div>

  <div class="panel">
    <div class="panel-header">
      <span class="panel-icon">◆</span> RECENT FILTER RULES
    </div>
    <div class="panel-body">
      <?php if (empty($rules)): ?>
        <div class="empty-state">No rules yet. <a href="rules.php">Create one →</a></div>
      <?php else: ?>
        <table class="data-table">
          <thead><tr><th>NAME</th><th>ACTION</th><th>ENABLED</th></tr></thead>
          <tbody>
          <?php foreach (array_slice($rules, 0, 6) as $rule): ?>
            <tr>
              <td><?= htmlspecialchars($rule['name']) ?></td>
              <td><span class="tag tag-action"><?= htmlspecialchars($rule['action']) ?></span></td>
              <td><?= !empty($rule['enabled']) ? '<span class="badge-ok">ON</span>' : '<span class="badge-off">OFF</span>' ?></td>
            </tr>
          <?php endforeach; ?>
          </tbody>
        </table>
      <?php endif; ?>
    </div>
  </div>
</div>

<div class="panel panel-quick">
  <div class="panel-header"><span class="panel-icon">▶</span> QUICK ACTIONS</div>
  <div class="panel-body quick-actions">
    <a href="runner.php" class="btn-action btn-run">▶ RUN IMAPFILTER</a>
    <a href="config.php" class="btn-action">◉ EDIT CONFIG.LUA</a>
    <a href="accounts.php" class="btn-action">◎ MANAGE ACCOUNTS</a>
    <a href="rules.php" class="btn-action">◆ EDIT RULES</a>
  </div>
</div>

<?php pageFooter(); ?>
