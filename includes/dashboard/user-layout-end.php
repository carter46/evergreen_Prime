</div>
</main>
<script>
document.addEventListener('DOMContentLoaded', function () {
  document.querySelectorAll('.glass-panel').forEach(function (card) {
    card.addEventListener('mouseenter', function () { card.style.borderColor = 'rgba(255, 195, 92, 0.2)'; });
    card.addEventListener('mouseleave', function () { card.style.borderColor = 'rgba(255, 255, 255, 0.08)'; });
  });

  (function initUserSocialProof() {
    var dataEl = document.getElementById('user-social-proof-data');
    var toast = document.getElementById('user-social-proof-toast');
    var textEl = document.getElementById('user-social-proof-text');
    if (!dataEl || !toast || !textEl) return;

    var messages;
    try { messages = JSON.parse(dataEl.textContent || '[]'); } catch (e) { return; }
    if (!messages.length) return;

    var idx = 0;
    var fadeMs = 500;
    var holdMs = 4000;
    var cycleMs = fadeMs + holdMs + fadeMs;

    function highlightAmount(msg) {
      return String(msg).replace(/(\$[\d,]+(?:\.\d{2})?)/, '<strong>$1</strong>');
    }

    function setMessage() {
      textEl.innerHTML = highlightAmount(messages[idx % messages.length]);
      idx += 1;
    }

    function showToast() {
      setMessage();
      requestAnimationFrame(function () { toast.classList.add('is-visible'); });
    }

    function hideThenShow() {
      toast.classList.remove('is-visible');
      setTimeout(showToast, fadeMs);
    }

    setTimeout(showToast, 400);
    setInterval(hideThenShow, cycleMs);
  })();
});
</script>
