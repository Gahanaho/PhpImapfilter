// IMAPFilter Web UI — main.js

// Live clock
function updateTimestamp() {
  const el = document.getElementById('ts');
  if (el) {
    const now = new Date();
    el.textContent = now.toISOString().replace('T', ' ').substring(0, 19) + ' UTC';
  }
}
updateTimestamp();
setInterval(updateTimestamp, 1000);

// Auto-dismiss alerts after 5s
document.querySelectorAll('.alert').forEach(el => {
  setTimeout(() => {
    el.style.transition = 'opacity .5s';
    el.style.opacity = '0';
    setTimeout(() => el.remove(), 500);
  }, 5000);
});

// Confirm on all delete buttons (already handled inline but as fallback)
document.querySelectorAll('[data-confirm]').forEach(btn => {
  btn.addEventListener('click', e => {
    if (!confirm(btn.dataset.confirm)) e.preventDefault();
  });
});
