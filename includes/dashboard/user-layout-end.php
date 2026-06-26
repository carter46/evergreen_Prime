</div>
</main>
<script>
document.addEventListener('DOMContentLoaded', function () {
  document.querySelectorAll('.glass-panel').forEach(function (card) {
    card.addEventListener('mouseenter', function () { card.style.borderColor = 'rgba(255, 195, 92, 0.2)'; });
    card.addEventListener('mouseleave', function () { card.style.borderColor = 'rgba(255, 255, 255, 0.08)'; });
  });
});
</script>
