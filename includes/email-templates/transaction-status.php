<?php
/**
 * Bloombit - Transaction Status Update Email Template
 */
$config = $config ?? [];
$site_url = $site_url ?? '/';
require_once dirname(__DIR__) . '/helpers.php';
$siteName = get_site_name();
[$brandBase, $brandAccent] = get_site_brand_parts($siteName);
$name = $name ?? 'User';
$status = $status ?? 'approved'; // approved or rejected
$type = $type ?? 'deposit'; // deposit or withdrawal
$amount = $amount ?? '0';
$currency = $currency ?? 'USD';
$amountUsd = $amountUsd ?? $amount;
$reference = $reference ?? '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8"/>
<meta name="viewport" content="width=device-width, initial-scale=1.0"/>
<title><?= htmlspecialchars($siteName) ?> | <?= ucfirst($type) ?> <?= ucfirst($status) ?></title>
</head>
<body style="font-family:Arial,sans-serif;margin:0;padding:0;background:#f8f8f5;color:#1d180c;line-height:1.6">
<div style="max-width:600px;margin:0 auto;padding:24px">
<div style="background:#fff;border:1px solid #e5e5e0;border-radius:12px;overflow:hidden;box-shadow:0 4px 24px rgba(0,0,0,0.06)">
<div style="height:6px;width:100%;background:#ffc105"></div>
<div style="padding:32px 40px 24px;background:#fff;border-bottom:1px solid #f0f0f0;text-align:center">
<span style="font-size:32px;font-weight:700;color:#1d180c;letter-spacing:-0.02em;line-height:1.2"><?= htmlspecialchars($brandBase) ?><?php if ($brandAccent !== ''): ?><span style="color:#ffc105"><?= htmlspecialchars($brandAccent) ?></span><?php endif; ?></span>
</div>
<div style="padding:32px 40px">
<span style="display:inline-block;padding:6px 12px;background:<?= $status === 'approved' ? 'rgba(34,197,94,0.15)' : 'rgba(239,68,68,0.15)' ?>;color:<?= $status === 'approved' ? '#16a34a' : '#dc2626' ?>;font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:0.05em;border-radius:9999px;margin-bottom:20px"><?= ucfirst($type) ?> <?= ucfirst($status) ?></span>
<h2 style="margin:0 0 24px;font-size:28px;font-weight:700;color:#1d180c;line-height:1.3">Your <?= htmlspecialchars($type) ?> has been <?= htmlspecialchars($status) ?></h2>
<p style="margin:0 0 16px;color:#5c5c52;font-size:16px">Hi <strong style="color:#1d180c"><?= htmlspecialchars($name) ?></strong>,</p>
<p style="margin:0 0 24px;color:#5c5c52;font-size:16px">Your <?= htmlspecialchars($type) ?> request has been <?= htmlspecialchars($status) ?> by our team.</p>
<div style="margin:24px 0;padding:20px;background:#f8f8f5;border-radius:8px;border-left:4px solid <?= $status === 'approved' ? '#16a34a' : '#dc2626' ?>">
<div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:8px">
<span style="color:#5c5c52;font-size:14px;font-weight:600">Amount:</span>
<span style="font-size:24px;font-weight:700;color:#1d180c"><?= htmlspecialchars($currency) ?> <?= number_format((float)$amount, 8, '.', ',') ?></span>
</div>
<?php if ($amountUsd != $amount): ?>
<div style="display:flex;justify-content:space-between;align-items:center;margin-top:8px;padding-top:8px;border-top:1px solid #e5e5e0">
<span style="color:#5c5c52;font-size:14px">USD Equivalent:</span>
<span style="font-size:18px;font-weight:600;color:#1d180c">$<?= format_usd_amount($amountUsd) ?></span>
</div>
<?php endif; ?>
<?php if (!empty($reference)): ?>
<div style="margin-top:12px;padding-top:12px;border-top:1px solid #e5e5e0">
<span style="color:#5c5c52;font-size:12px">Reference:</span>
<span style="color:#1d180c;font-size:12px;font-family:monospace;word-break:break-all"><?= htmlspecialchars($reference) ?></span>
</div>
<?php endif; ?>
</div>
<?php if ($status === 'approved'): ?>
<p style="margin:24px 0;color:#5c5c52;font-size:16px"><?= $type === 'deposit' ? 'Your funds have been credited to your account and are now available for use.' : 'Your withdrawal has been processed and funds have been sent to your specified address.' ?></p>
<?php else: ?>
<p style="margin:24px 0;color:#5c5c52;font-size:16px">If you believe this is an error, please contact our support team for assistance.</p>
<?php endif; ?>
<div style="margin-top:32px;margin-bottom:32px;text-align:center">
<a href="<?= htmlspecialchars($site_url) ?>/dashboard/user/wallet" style="display:inline-block;padding:16px 32px;background:#ffc105;color:#1d180c;font-weight:700;font-size:16px;border-radius:8px;text-decoration:none;box-shadow:0 4px 14px rgba(255,193,5,0.35)">View Transactions →</a>
</div>
<hr style="border:none;border-top:1px solid #e5e5e0;margin:24px 0"/>
<div style="color:#8a8a7d;font-size:14px">
<p style="margin:0 0 4px">Best regards,</p>
<p style="margin:0;font-weight:700;color:#1d180c">The <?= htmlspecialchars($siteName) ?> Team</p>
</div>
</div>
</div>
</div>
</body>
</html>
