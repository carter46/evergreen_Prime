<?php
/**
 * Bloombit - Newsletter Welcome Email (to subscriber)
 */
$config = $config ?? [];
$site_url = $site_url ?? '/';
require_once dirname(__DIR__) . '/helpers.php';
$siteName = get_site_name();
$logoParts = preg_match('/^(.+)bit$/i', $siteName, $m) ? [$m[1], 'bit'] : [$siteName, null];
$email = $email ?? '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8"/>
<meta name="viewport" content="width=device-width, initial-scale=1.0"/>
<title>Bloombit | Welcome</title>
</head>
<body style="font-family:Arial,sans-serif;margin:0;padding:0;background:#f8f8f5;color:#1d180c;line-height:1.6">
<div style="max-width:600px;margin:0 auto;padding:24px">
<div style="background:#fff;border:1px solid #e5e5e0;border-radius:12px;overflow:hidden;box-shadow:0 4px 24px rgba(0,0,0,0.06)">
<div style="height:6px;width:100%;background:#ffc105"></div>
<div style="padding:24px 40px 16px;background:#fff;border-bottom:1px solid #f0f0f0">
<div style="display:flex;align-items:center;gap:8px;justify-content:center">
<div style="width:40px;height:40px;background:#ffc105;border-radius:4px;display:flex;align-items:center;justify-content:center;flex-shrink:0">
<span style="font-size:20px;color:#fff">✨</span>
</div>
<span style="font-size:20px;font-weight:700;color:#1d180c;letter-spacing:-0.02em"><?= htmlspecialchars($logoParts[0]) ?><?php if ($logoParts[1]): ?><span style="color:#ffc105"><?= htmlspecialchars($logoParts[1]) ?></span><?php endif; ?></span>
</div>
</div>
<div style="padding:32px 40px">
<span style="display:inline-block;padding:6px 12px;background:rgba(255,193,5,0.15);color:#b8860b;font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:0.05em;border-radius:9999px;margin-bottom:20px">Welcome</span>
<h2 style="margin:0 0 24px;font-size:28px;font-weight:700;color:#1d180c;line-height:1.3">Thanks for subscribing to Bloombit</h2>
<p style="margin:0 0 16px;color:#5c5c52;font-size:16px">You're now part of a community of forward-thinking investors who use AI to optimize their crypto portfolios.</p>
<p style="margin:0 0 24px;color:#5c5c52;font-size:16px">Expect exclusive insights, market updates, and platform news delivered to your inbox.</p>
<div style="margin-top:32px;margin-bottom:32px;text-align:center">
<a href="<?= htmlspecialchars($site_url) ?>" style="display:inline-block;padding:16px 32px;background:#ffc105;color:#1d180c;font-weight:700;font-size:16px;border-radius:8px;text-decoration:none;box-shadow:0 4px 14px rgba(255,193,5,0.35)">Visit Bloombit →</a>
</div>
<hr style="border:none;border-top:1px solid #e5e5e0;margin:24px 0"/>
<div style="color:#8a8a7d;font-size:14px">
<p style="margin:0 0 4px">Best regards,</p>
<p style="margin:0;font-weight:700;color:#1d180c">The Bloombit Team</p>
</div>
</div>
<div style="background:#f5f5f0;padding:24px 40px;border-top:1px solid #e5e5e0">
<p style="margin:0;font-size:11px;color:#8a8a7d;text-align:center">You're receiving this because you subscribed at bloombit.com. <a href="<?= htmlspecialchars($site_url) ?>/legal_centre" style="color:#5c5c52;text-decoration:underline">Unsubscribe</a></p>
<p style="margin:8px 0 0;font-size:12px;color:#8a8a7d;text-align:center">© <?= date('Y') ?> Bloombit. All rights reserved.</p>
</div>
</div>
</div>
</body>
</html>
