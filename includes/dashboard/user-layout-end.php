</div>
</main>
<script>
document.addEventListener('DOMContentLoaded', function () {
  document.querySelectorAll('.bento-card, .glass-panel').forEach(function (card) {
    card.addEventListener('mousedown', function () { card.style.transform = 'scale(0.99)'; });
    card.addEventListener('mouseup', function () { card.style.transform = ''; });
    card.addEventListener('mouseleave', function () { card.style.transform = ''; });
  });
});
</script>
