<?php
require_once __DIR__ . '/user-social-proof-data.php';
$socialProofMessages = user_dashboard_social_proof_messages();
?>
<div
  id="user-social-proof"
  class="user-social-proof pointer-events-none"
  aria-live="polite"
  aria-atomic="true"
  role="status"
>
  <div id="user-social-proof-toast" class="user-social-proof-toast opacity-0">
    <span class="user-social-proof-dot" aria-hidden="true"></span>
    <span id="user-social-proof-text" class="user-social-proof-text"></span>
  </div>
</div>
<script type="application/json" id="user-social-proof-data"><?php echo json_encode($socialProofMessages, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT); ?></script>
