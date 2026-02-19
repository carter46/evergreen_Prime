<?php
/**
 * Bloombit - Broadcast Email Template (to users)
 */
$config = $config ?? [];
$site_url = $site_url ?? '/';
require_once dirname(__DIR__) . '/helpers.php';
$siteName = get_site_name();
$logoParts = preg_match('/^(.+)bit$/i', $siteName, $m) ? [$m[1], 'bit'] : [$siteName, null];
$subject = $subject ?? '';
$body = $body ?? '';
$body_escaped = nl2br(htmlspecialchars($body));
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8"/>
<meta name="viewport" content="width=device-width, initial-scale=1.0"/>
<title><?= htmlspecialchars($subject ?: $siteName) ?></title>
</head>
<body style="font-family:Arial,sans-serif;margin:0;padding:0;background:#f8f8f5;color:#1d180c;line-height:1.6">
<div style="max-width:600px;margin:0 auto;padding:24px">
<div style="background:#fff;border:1px solid #e5e5e0;border-radius:12px;overflow:hidden;box-shadow:0 4px 24px rgba(0,0,0,0.06)">
<div style="height:6px;width:100%;background:#ffc105"></div>
<div style="padding:32px 40px 24px;background:#fff;border-bottom:1px solid #f0f0f0;text-align:center">
<span style="font-size:32px;font-weight:700;color:#1d180c;letter-spacing:-0.02em;line-height:1.2"><?= htmlspecialchars($logoParts[0]) ?><?php if ($logoParts[1]): ?><span style="color:#ffc105"><?= htmlspecialchars($logoParts[1]) ?></span><?php endif; ?></span>
</div>
<div style="padding:32px 40px">
<h2 style="margin:0 0 24px;font-size:22px;font-weight:700;color:#1d180c"><?= htmlspecialchars($subject) ?></h2>
<div style="color:#1d180c;line-height:1.7"><?= $body_escaped ?></div>
<p style="margin-top:24px">
<a href="<?= htmlspecialchars($site_url) ?>/dashboard" style="display:inline-block;padding:14px 28px;background:#ffc105;color:#1d180c;font-weight:700;text-decoration:none;border-radius:8px">Access Your Dashboard</a>
</p>
</div>
<div style="padding:16px 24px;background:#f8f8f5;border-top:1px solid #e5e5e0;font-size:12px;color:#8a8a7d;text-align:center">
<p style="margin:0"><?= htmlspecialchars($siteName) ?></p>
</div>
</div>
</div>
</body>
</html>
