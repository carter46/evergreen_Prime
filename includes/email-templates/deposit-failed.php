<?php
/**
 * Bloombit - Deposit Failed Email Template
 */
$site_url = $site_url ?? '/';
require_once dirname(__DIR__) . '/helpers.php';
$siteName = get_site_name();
$logoParts = preg_match('/^(.+)bit$/i', $siteName, $m) ? [$m[1], 'bit'] : [$siteName, null];

$name = $name ?? 'User';
$amount = $amount ?? '0';
$amountUsd = $amountUsd ?? null;
$currency = $currency ?? 'USD';
$reference = $reference ?? '';
$expires_at = $expires_at ?? '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title><?= htmlspecialchars($siteName) ?> | Deposit Failed</title>
</head>
<body style="font-family:Arial,sans-serif;margin:0;padding:0;background:#f8f8f5;color:#1d180c;line-height:1.6">
  <div style="max-width:600px;margin:0 auto;padding:24px">
    <div style="background:#fff;border:1px solid #e5e5e0;border-radius:12px;overflow:hidden;box-shadow:0 4px 24px rgba(0,0,0,0.06)">
      <div style="height:6px;width:100%;background:#ffc105"></div>
      <div style="padding:32px 40px 24px;background:#fff;border-bottom:1px solid #f0f0f0;text-align:center">
        <span style="font-size:32px;font-weight:700;color:#1d180c;letter-spacing:-0.02em;line-height:1.2">
          <?= htmlspecialchars($logoParts[0]) ?><?php if ($logoParts[1]): ?><span style="color:#ffc105"><?= htmlspecialchars($logoParts[1]) ?></span><?php endif; ?>
        </span>
      </div>
      <div style="padding:32px 40px">
        <span style="display:inline-block;padding:6px 12px;background:rgba(239,68,68,0.15);color:#dc2626;font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:0.05em;border-radius:9999px;margin-bottom:20px">Deposit Failed</span>
        <h2 style="margin:0 0 18px;font-size:28px;font-weight:700;color:#1d180c;line-height:1.3">Your deposit request expired</h2>
        <p style="margin:0 0 16px;color:#5c5c52;font-size:16px">Hi <strong style="color:#1d180c"><?= htmlspecialchars($name) ?></strong>,</p>
        <p style="margin:0 0 24px;color:#5c5c52;font-size:16px">
          Your deposit request was not confirmed before the countdown ended, so it has been marked as <strong>failed</strong>.
        </p>

        <div style="margin:24px 0;padding:20px;background:#f8f8f5;border-radius:8px;border-left:4px solid #dc2626">
          <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:8px">
            <span style="color:#5c5c52;font-size:14px;font-weight:600">Amount:</span>
            <span style="font-size:22px;font-weight:800;color:#1d180c"><?= htmlspecialchars($currency) ?> <?= number_format((float)$amount, 8, '.', ',') ?></span>
          </div>
          <?php if ($amountUsd !== null && $amountUsd !== '' && (float)$amountUsd > 0): ?>
            <div style="display:flex;justify-content:space-between;align-items:center;margin-top:8px;padding-top:8px;border-top:1px solid #e5e5e0">
              <span style="color:#5c5c52;font-size:14px">USD Equivalent:</span>
              <span style="font-size:16px;font-weight:700;color:#1d180c">$<?= number_format((float)$amountUsd, 2, '.', ',') ?></span>
            </div>
          <?php endif; ?>
          <?php if (!empty($reference)): ?>
            <div style="margin-top:12px;padding-top:12px;border-top:1px solid #e5e5e0">
              <span style="color:#5c5c52;font-size:12px">Reference:</span>
              <span style="color:#1d180c;font-size:12px;font-family:monospace;word-break:break-all"><?= htmlspecialchars($reference) ?></span>
            </div>
          <?php endif; ?>
          <?php if (!empty($expires_at)): ?>
            <div style="margin-top:12px;padding-top:12px;border-top:1px solid #e5e5e0">
              <span style="color:#5c5c52;font-size:12px">Expired at:</span>
              <span style="color:#1d180c;font-size:12px"><?= htmlspecialchars($expires_at) ?></span>
            </div>
          <?php endif; ?>
        </div>

        <p style="margin:0 0 24px;color:#5c5c52;font-size:16px">
          If you still want to deposit, please create a new deposit request from your wallet page.
        </p>

        <div style="margin-top:28px;margin-bottom:28px;text-align:center">
          <a href="<?= htmlspecialchars($site_url) ?>/dashboard/user/wallet" style="display:inline-block;padding:16px 32px;background:#ffc105;color:#1d180c;font-weight:800;font-size:16px;border-radius:8px;text-decoration:none;box-shadow:0 4px 14px rgba(255,193,5,0.35)">Open Wallet →</a>
        </div>

        <hr style="border:none;border-top:1px solid #e5e5e0;margin:24px 0"/>
        <div style="color:#8a8a7d;font-size:14px">
          <p style="margin:0 0 4px">Best regards,</p>
          <p style="margin:0;font-weight:800;color:#1d180c">The <?= htmlspecialchars($siteName) ?> Team</p>
        </div>
      </div>
    </div>
  </div>
</body>
</html>

