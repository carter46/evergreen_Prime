<?php
/**
 * Bloombit - OTP Email Template
 */
$config = $config ?? [];
$site_url = $site_url ?? '/';
$otp = $otp ?? '';
$name = $name ?? 'User';
$purpose_label = $purpose_label ?? 'verification';
require_once dirname(__DIR__) . '/helpers.php';
$siteName = get_site_name();
$logoParts = preg_match('/^(.+)bit$/i', $siteName, $m) ? [$m[1], 'bit'] : [$siteName, null];
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8"/>
<meta name="viewport" content="width=device-width, initial-scale=1.0"/>
<title><?= htmlspecialchars($siteName) ?> | <?= htmlspecialchars($purpose_label) ?></title>
</head>
<body style="font-family:Arial,sans-serif;margin:0;padding:0;background:#f8f8f5;color:#1d180c;line-height:1.6">
<div style="max-width:600px;margin:0 auto;padding:24px">
<div style="background:#fff;border:1px solid #e5e5e0;border-radius:12px;overflow:hidden;box-shadow:0 4px 24px rgba(0,0,0,0.06)">
<div style="height:6px;width:100%;background:#ffc105"></div>
<div style="padding:32px 40px 24px;background:#fff;border-bottom:1px solid #f0f0f0;text-align:center">
<div style="display:inline-flex;align-items:center;gap:12px;vertical-align:middle">
<div style="width:48px;height:48px;background:#ffc105;border-radius:4px;display:inline-flex;align-items:center;justify-content:center;flex-shrink:0;vertical-align:middle">
<span style="font-size:24px;color:#fff;line-height:1">✨</span>
</div>
<span style="font-size:24px;font-weight:700;color:#1d180c;letter-spacing:-0.02em;line-height:48px;vertical-align:middle"><?= htmlspecialchars($logoParts[0]) ?><?php if ($logoParts[1]): ?><span style="color:#ffc105"><?= htmlspecialchars($logoParts[1]) ?></span><?php endif; ?></span>
</div>
</div>
<div style="padding:32px 40px">
<span style="display:inline-block;padding:6px 12px;background:rgba(255,193,5,0.15);color:#b8860b;font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:0.05em;border-radius:9999px;margin-bottom:20px">Verification Code</span>
<h2 style="margin:0 0 24px;font-size:28px;font-weight:700;color:#1d180c;line-height:1.3">Your verification code</h2>
<p style="margin:0 0 16px;color:#5c5c52;font-size:16px">Hi <strong style="color:#1d180c"><?= htmlspecialchars($name) ?></strong>,</p>
<p style="margin:0 0 24px;color:#5c5c52;font-size:16px">Use the following 6-digit code to complete <?= htmlspecialchars($purpose_label) ?>:</p>
<div style="margin:24px 0;padding:20px;background:#f8f8f5;border-radius:8px;text-align:center">
<code style="font-size:32px;font-weight:700;letter-spacing:0.25em;color:#1d180c"><?= htmlspecialchars($otp) ?></code>
</div>
<p style="margin:0 0 8px;color:#8a8a7d;font-size:14px">This code expires in 10 minutes. Do not share it with anyone.</p>
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
