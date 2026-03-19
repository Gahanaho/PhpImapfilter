<?php
session_start();
require_once 'includes/auth.php';
require_once 'includes/config.php';
require_once 'includes/layout.php';
requireLogin();

$output = '';
$status = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    if ($action === 'run') {
        if (!file_exists(IMAPFILTER_BIN)) {
            $output = '[ERROR] imapfilter binary not found at: ' . IMAPFILTER_BIN;
            $status = 'error';
        } elseif (!file_exists(IMAPFILTER_CONFIG)) {
            $output = '[ERROR] config.lua not found at: ' . IMAPFILTER_CONFIG;
            $status = 'error';
        } else {
            $cmd = escapeshellcmd(IMAPFILTER_BIN) . ' -c ' . escapeshellarg(IMAPFILTER_CONFIG) . ' 2>&1';
            exec($cmd, $lines, $retcode);
            $output = implode("\n", $lines);
            $status = $retcode === 0 ? 'ok' : 'error';
            // Append to log
            $logEntry = "[" . date('Y-m-d H:i:s') . "] EXIT:$retcode\n$output\n" . str_repeat('-', 60) . "\n";
            file_put_contents(IMAPFILTER_LOG, $logEntry, FILE_APPEND);
        }
    } elseif ($action === 'clear_log') {
        if (file_exists(IMAPFILTER_LOG)) {
            file_put_contents(IMAPFILTER_LOG, '');
        }
        $output = '[INFO] Log cleared.';
        $status = 'ok';
    }
}

// Read log tail
$logContent = '';
if (file_exists(IMAPFILTER_LOG)) {
    $lines = file(IMAPFILTER_LOG);
    $logContent = implode('', array_slice($lines, -200));
}

pageHeader('RUN / LOG', 'runner');
?>

<div class="runner-grid">
  <div class="panel panel-run">
    <div class="panel-header">▶ EXECUTE IMAPFILTER</div>
    <div class="panel-body">
      <div class="run-info">
        <div class="run-detail"><span>BINARY</span><code><?= htmlspecialchars(IMAPFILTER_BIN) ?></code></div>
        <div class="run-detail"><span>CONFIG</span><code><?= htmlspecialchars(IMAPFILTER_CONFIG) ?></code></div>
        <div class="run-detail">
          <span>STATUS</span>
          <?php if (file_exists(IMAPFILTER_BIN)): ?>
            <span class="badge-ok">binary found</span>
          <?php else: ?>
            <span class="badge-warn">not found</span>
          <?php endif; ?>
          &nbsp;
          <?php if (file_exists(IMAPFILTER_CONFIG)): ?>
            <span class="badge-ok">config found</span>
          <?php else: ?>
            <span class="badge-warn">config missing</span>
          <?php endif; ?>
        </div>
      </div>

      <form method="POST">
        <input type="hidden" name="action" value="run">
        <button type="submit" class="btn-run-big" id="run-btn">
          <span class="run-icon">▶</span> RUN NOW
        </button>
      </form>

      <?php if ($output): ?>
      <div class="output-block output-<?= $status ?>">
        <div class="output-header"><?= $status === 'ok' ? '✓ SUCCESS' : '✗ ERROR / OUTPUT' ?></div>
        <pre class="output-pre"><?= htmlspecialchars($output) ?></pre>
      </div>
      <?php endif; ?>
    </div>
  </div>

  <div class="panel panel-log">
    <div class="panel-header">
      ◈ IMAPFILTER LOG
      <form method="POST" style="display:inline;float:right">
        <input type="hidden" name="action" value="clear_log">
        <button type="submit" class="btn-sm btn-danger" onclick="return confirm('Clear log?')">✕ CLEAR</button>
      </form>
    </div>
    <div class="panel-body no-pad">
      <?php if ($logContent): ?>
        <pre class="log-pre" id="log-pre"><?= htmlspecialchars($logContent) ?></pre>
      <?php else: ?>
        <div class="empty-state" style="padding:1.5rem">No log entries yet.</div>
      <?php endif; ?>
    </div>
  </div>
</div>

<script>
// Auto-scroll log to bottom
const logEl = document.getElementById('log-pre');
if (logEl) logEl.scrollTop = logEl.scrollHeight;

// Run button feedback
document.getElementById('run-btn')?.addEventListener('click', function() {
  this.innerHTML = '<span class="run-icon spin">⟳</span> RUNNING…';
  this.disabled = true;
});
</script>

<?php pageFooter(); ?>
