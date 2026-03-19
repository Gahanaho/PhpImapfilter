<?php
session_start();
require_once 'includes/auth.php';
require_once 'includes/config.php';
require_once 'includes/layout.php';
requireLogin();

$accounts = readAccounts();
$msg = '';
$err = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'add') {
        $new = [
            'id'       => uniqid('acc_'),
            'name'     => trim($_POST['name'] ?? ''),
            'host'     => trim($_POST['host'] ?? ''),
            'port'     => (int)($_POST['port'] ?? 993),
            'username' => trim($_POST['username'] ?? ''),
            'password' => trim($_POST['password'] ?? ''),
            'tls'      => !empty($_POST['tls']),
        ];
        if ($new['name'] && $new['host'] && $new['username']) {
            $accounts[] = $new;
            writeAccounts($accounts);
            $msg = 'Account "' . htmlspecialchars($new['name']) . '" added.';
        } else {
            $err = 'Name, Host and Username are required.';
        }
    } elseif ($action === 'delete') {
        $id = $_POST['id'] ?? '';
        $accounts = array_values(array_filter($accounts, fn($a) => $a['id'] !== $id));
        writeAccounts($accounts);
        $msg = 'Account removed.';
    } elseif ($action === 'toggle') {
        $id = $_POST['id'] ?? '';
        foreach ($accounts as &$acc) {
            if ($acc['id'] === $id) $acc['enabled'] = empty($acc['enabled']);
        }
        writeAccounts($accounts);
        $msg = 'Account updated.';
    }
    $accounts = readAccounts();
}

pageHeader('ACCOUNTS', 'accounts');
?>

<?php if ($msg): ?><div class="alert alert-ok"><?= $msg ?></div><?php endif; ?>
<?php if ($err): ?><div class="alert alert-error"><?= $err ?></div><?php endif; ?>

<div class="panel">
  <div class="panel-header">◎ ADD ACCOUNT</div>
  <div class="panel-body">
    <form method="POST" class="form-grid">
      <input type="hidden" name="action" value="add">
      <div class="field-group">
        <label class="field-label">ACCOUNT NAME</label>
        <input type="text" name="name" class="field-input" placeholder="work_mail" required>
      </div>
      <div class="field-group">
        <label class="field-label">IMAP HOST</label>
        <input type="text" name="host" class="field-input" placeholder="imap.example.com" required>
      </div>
      <div class="field-group">
        <label class="field-label">PORT</label>
        <input type="number" name="port" class="field-input" value="993" min="1" max="65535">
      </div>
      <div class="field-group">
        <label class="field-label">USERNAME</label>
        <input type="text" name="username" class="field-input" placeholder="user@example.com" required>
      </div>
      <div class="field-group">
        <label class="field-label">PASSWORD</label>
        <input type="password" name="password" class="field-input" placeholder="stored in data/accounts.json">
      </div>
      <div class="field-group field-check">
        <label class="check-label">
          <input type="checkbox" name="tls" value="1" checked> <span>USE TLS / SSL</span>
        </label>
      </div>
      <div class="field-group field-submit">
        <button type="submit" class="btn-primary">+ ADD ACCOUNT</button>
      </div>
    </form>
  </div>
</div>

<div class="panel">
  <div class="panel-header">◎ CONFIGURED ACCOUNTS (<?= count($accounts) ?>)</div>
  <div class="panel-body">
    <?php if (empty($accounts)): ?>
      <div class="empty-state">No accounts configured yet.</div>
    <?php else: ?>
    <table class="data-table">
      <thead>
        <tr>
          <th>NAME</th><th>HOST</th><th>PORT</th><th>USER</th><th>TLS</th><th>ACTIONS</th>
        </tr>
      </thead>
      <tbody>
      <?php foreach ($accounts as $acc): ?>
        <tr>
          <td><span class="tag"><?= htmlspecialchars($acc['name']) ?></span></td>
          <td><?= htmlspecialchars($acc['host']) ?></td>
          <td><?= htmlspecialchars($acc['port'] ?? 993) ?></td>
          <td><?= htmlspecialchars($acc['username']) ?></td>
          <td><?= !empty($acc['tls']) ? '<span class="badge-ok">TLS</span>' : '<span class="badge-warn">NO</span>' ?></td>
          <td class="actions-cell">
            <form method="POST" style="display:inline">
              <input type="hidden" name="action" value="delete">
              <input type="hidden" name="id" value="<?= htmlspecialchars($acc['id']) ?>">
              <button type="submit" class="btn-danger" onclick="return confirm('Remove account?')">✕ REMOVE</button>
            </form>
          </td>
        </tr>
      <?php endforeach; ?>
      </tbody>
    </table>
    <?php endif; ?>
  </div>
</div>

<?php pageFooter(); ?>
