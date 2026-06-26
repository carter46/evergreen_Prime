</div>
</main>
<script>
document.addEventListener('DOMContentLoaded', function () {
  document.querySelectorAll('.glass-panel').forEach(function (card) {
    card.addEventListener('mouseenter', function () {
      card.style.transform = 'translateY(-2px)';
      card.style.transition = 'transform 0.2s ease-out';
    });
    card.addEventListener('mouseleave', function () {
      card.style.transform = 'translateY(0)';
    });
  });
});
</script>
