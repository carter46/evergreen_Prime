</div>
</main>
<script>
document.addEventListener('DOMContentLoaded', function () {
  document.querySelectorAll('.bento-card, .glass-panel').forEach(function (card) {
    card.addEventListener('mousedown', function () { card.style.transform = 'scale(0.99)'; });
    card.addEventListener('mouseup', function () { card.style.transform = ''; });
    card.addEventListener('mouseleave', function () { card.style.transform = ''; });
  });

  var dataEl = document.getElementById('user-social-proof-data');
  var toast = document.getElementById('user-social-proof-toast');
  var textEl = document.getElementById('user-social-proof-text');
  if (dataEl && toast && textEl) {
  var messages = [];
  try {
    messages = JSON.parse(dataEl.textContent || '[]');
  } catch (e) {
    messages = [];
  }
  if (messages.length) {
  var idx = 0;
  function highlightAmounts(msg) {
    return String(msg).replace(/\$[\d,]+(?:\.\d+)?/g, function (amount) {
      return '<span class="user-social-proof-amount">' + amount + '</span>';
    });
  }
  function showNext() {
    toast.classList.remove('is-visible');
    setTimeout(function () {
      textEl.innerHTML = highlightAmounts(messages[idx % messages.length]);
      idx += 1;
      toast.classList.add('is-visible');
    }, 500);
  }
  showNext();
  setInterval(showNext, 5000);
  }
  }
});
</script>
