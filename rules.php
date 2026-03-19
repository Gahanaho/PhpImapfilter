<?php
session_start();
require_once 'includes/auth.php';
require_once 'includes/config.php';
require_once 'includes/layout.php';
requireLogin();

$rules    = readRules();
$accounts = readAccounts();
$msg = ''; $err = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'add') {
        $new = [
            'id'          => uniqid('rule_'),
            'name'        => trim($_POST['name'] ?? ''),
            'account'     => trim($_POST['account'] ?? ''),
            'mailbox'     => trim($_POST['mailbox'] ?? 'INBOX'),
            'criteria'    => trim($_POST['criteria'] ?? ''),
            'criteria_val'=> trim($_POST['criteria_val'] ?? ''),
            'action'      => trim($_POST['rule_action'] ?? 'move'),
            'target'      => trim($_POST['target'] ?? ''),
            'enabled'     => true,
        ];
        if ($new['name'] && $new['account'] && $new['criteria'] && $new['action']) {
            $rules[] = $new;
            writeRules($rules);
            $msg = 'Rule "' . htmlspecialchars($new['name']) . '" added.';
        } else {
            $err = 'Name, Account, Criteria and Action are required.';
        }
    } elseif ($action === 'delete') {
        $id = $_POST['id'] ?? '';
        $rules = array_values(array_filter($rules, fn($r) => $r['id'] !== $id));
        writeRules($rules);
        $msg = 'Rule removed.';
    } elseif ($action === 'toggle') {
        $id = $_POST['id'] ?? '';
        foreach ($rules as &$r) {
            if ($r['id'] === $id) $r['enabled'] = !($r['enabled'] ?? true);
        }
        writeRules($rules);
        $msg = 'Rule updated.';
    }
    $rules = readRules();
}

$criteria_options = [
    'from'    => 'FROM',
    'to'      => 'TO',
    'subject' => 'SUBJECT',
    'cc'      => 'CC',
    'body'    => 'BODY contains',
    'seen'    => 'IS SEEN',
    'unseen'  => 'IS UNSEEN',
    'flagged' => 'IS FLAGGED',
    'older'   => 'OLDER THAN (days)',
];
$action_options = [
    'move'   => 'MOVE to folder',
    'copy'   => 'COPY to folder',
    'delete' => 'DELETE',
    'flag'   => 'FLAG',
    'mark_seen' => 'MARK AS SEEN',
];

pageHeader('FILTER RULES', 'rules');
?>

<?php if ($msg): ?><div class="alert alert-ok"><?= $msg ?></div><?php endif; ?>
<?php if ($err): ?><div class="alert alert-error"><?= $err ?></div><?php endif; ?>

<div class="panel">
  <div class="panel-header">◆ NEW FILTER RULE</div>
  <div class="panel-body">
    <form method="POST" class="form-grid">
      <input type="hidden" name="action" value="add">
      <div class="field-group">
        <label class="field-label">RULE NAME</label>
        <input type="text" name="name" class="field-input" placeholder="e.g. move_newsletters" required>
      </div>
      <div class="field-group">
        <label class="field-label">ACCOUNT</label>
        <select name="account" class="field-input field-select" required>
          <option value="">— select —</option>
          <?php foreach ($accounts as $acc): ?>
            <option value="<?= htmlspecialchars($acc['name']) ?>"><?= htmlspecialchars($acc['name']) ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="field-group">
        <label class="field-label">SOURCE MAILBOX</label>
        <input type="text" name="mailbox" class="field-input" value="INBOX" placeholder="INBOX">
      </div>
      <div class="field-group">
        <label class="field-label">CRITERIA</label>
        <select name="criteria" class="field-input field-select">
          <?php foreach ($criteria_options as $val => $label): ?>
            <option value="<?= $val ?>"><?= $label ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="field-group">
        <label class="field-label">CRITERIA VALUE</label>
        <input type="text" name="criteria_val" class="field-input" placeholder="e.g. newsletter@example.com">
      </div>
      <div class="field-group">
        <label class="field-label">ACTION</label>
        <select name="rule_action" class="field-input field-select">
          <?php foreach ($action_options as $val => $label): ?>
            <option value="<?= $val ?>"><?= $label ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="field-group">
        <label class="field-label">TARGET FOLDER</label>
        <input type="text" name="target" class="field-input" placeholder="e.g. Newsletters">
      </div>
      <div class="field-group field-submit">
        <button type="submit" class="btn-primary">+ ADD RULE</button>
      </div>
    </form>
  </div>
</div>

<div class="panel">
  <div class="panel-header">◆ ALL RULES (<?= count($rules) ?>)</div>
  <div class="panel-body">
    <?php if (empty($rules)): ?>
      <div class="empty-state">No filter rules yet. Create one above.</div>
    <?php else: ?>
    <table class="data-table">
      <thead>
        <tr><th>NAME</th><th>ACCOUNT</th><th>MAILBOX</th><th>CRITERIA</th><th>ACTION</th><th>TARGET</th><th>STATUS</th><th>OPS</th></tr>
      </thead>
      <tbody>
      <?php foreach ($rules as $rule): ?>
        <tr class="<?= empty($rule['enabled']) ? 'row-disabled' : '' ?>">
          <td><?= htmlspecialchars($rule['name']) ?></td>
          <td><span class="tag"><?= htmlspecialchars($rule['account']) ?></span></td>
          <td><?= htmlspecialchars($rule['mailbox']) ?></td>
          <td>
            <span class="tag tag-criteria"><?= htmlspecialchars($criteria_options[$rule['criteria']] ?? $rule['criteria']) ?></span>
            <?= $rule['criteria_val'] ? '<br><small>' . htmlspecialchars($rule['criteria_val']) . '</small>' : '' ?>
          </td>
          <td><span class="tag tag-action"><?= htmlspecialchars($action_options[$rule['action']] ?? $rule['action']) ?></span></td>
          <td><?= htmlspecialchars($rule['target'] ?? '—') ?></td>
          <td><?= !empty($rule['enabled']) ? '<span class="badge-ok">ON</span>' : '<span class="badge-off">OFF</span>' ?></td>
          <td class="actions-cell">
            <form method="POST" style="display:inline">
              <input type="hidden" name="action" value="toggle">
              <input type="hidden" name="id" value="<?= htmlspecialchars($rule['id']) ?>">
              <button type="submit" class="btn-sm btn-toggle"><?= !empty($rule['enabled']) ? 'PAUSE' : 'ENABLE' ?></button>
            </form>
            <form method="POST" style="display:inline">
              <input type="hidden" name="action" value="delete">
              <input type="hidden" name="id" value="<?= htmlspecialchars($rule['id']) ?>">
              <button type="submit" class="btn-sm btn-danger" onclick="return confirm('Delete rule?')">✕</button>
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
