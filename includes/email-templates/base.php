<?php
/**
 * Bloombit - Base HTML Email Template
 * Uses inline styles for email client compatibility.
 * Variables: $badge, $heading, $content_html, $cta_text, $cta_url
 */
$badge = $badge ?? 'Account Notification';
$heading = $heading ?? ('Your ' . get_site_name() . ' update');
$content_html = $content_html ?? '<p>Your message content here.</p>';
$cta_text = $cta_text ?? null;
$cta_url = $cta_url ?? '#';
$site_url = $site_url ?? '/';
$siteName = $siteName ?? get_site_name();
$siteLogo = get_site_setting('site_logo', '');
[$brandBase, $brandAccent] = get_site_brand_parts($siteName);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8"/>
<meta name="viewport" content="width=device-width, initial-scale=1.0"/>
<meta http-equiv="X-UA-Compatible" content="IE=edge"/>
<title><?= htmlspecialchars($siteName) ?> | Transactional Email</title>
<style>
body{font-family:'Space Grotesk',Arial,sans-serif;margin:0;padding:0;background-color:#f8f8f5;color:#1d180c}
a{color:#ffc105;text-decoration:none}
</style>
</head>
<body style="font-family:'Space Grotesk',Arial,sans-serif;margin:0;padding:0;background-color:#f8f8f5;color:#1d180c;line-height:1.6">
<div style="max-width:600px;margin:0 auto;padding:24px">
<div style="background:#fff;border:1px solid #e5e5e0;border-radius:12px;overflow:hidden;box-shadow:0 4px 24px rgba(0,0,0,0.06)">
<div style="height:6px;width:100%;background:#ffc105"></div>
<div style="padding:32px 40px 24px;background:#fff;border-bottom:1px solid #f0f0f0;text-align:center">
<span style="font-size:32px;font-weight:700;color:#1d180c;letter-spacing:-0.02em;line-height:1.2"><?= htmlspecialchars($brandBase) ?><?php if ($brandAccent !== ''): ?><span style="color:#ffc105"><?= htmlspecialchars($brandAccent) ?></span><?php endif; ?></span>
</div>
<div style="padding:32px 40px">
<div style="margin-bottom:24px;text-align:center">
<span style="display:inline-block;padding:6px 12px;background:rgba(255,193,5,0.15);color:#b8860b;font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:0.05em;border-radius:9999px"><?= htmlspecialchars($badge) ?></span>
</div>
<h2 style="margin:0 0 24px;font-size:28px;font-weight:700;color:#1d180c;line-height:1.3"><?= htmlspecialchars($heading) ?></h2>
<div style="color:#5c5c52;font-size:16px;line-height:1.7">
<?= $content_html ?>
</div>
<?php if ($cta_text): ?>
<div style="margin-top:32px;margin-bottom:32px;text-align:center">
<a href="<?= htmlspecialchars($cta_url) ?>" style="display:inline-block;padding:16px 32px;background:#ffc105;color:#1d180c;font-weight:700;font-size:16px;border-radius:8px;text-decoration:none;box-shadow:0 4px 14px rgba(255,193,5,0.35)"><?= htmlspecialchars($cta_text) ?> →</a>
</div>
<?php endif; ?>
<hr style="border:none;border-top:1px solid #e5e5e0;margin:24px 0"/>
<div style="color:#8a8a7d;font-size:14px">
<p style="margin:0 0 4px">Best regards,</p>
<p style="margin:0;font-weight:700;color:#1d180c">The <?= htmlspecialchars($siteName) ?> Team</p>
</div>
</div>
<div style="background:#f5f5f0;padding:24px 40px;border-top:1px solid #e5e5e0">
<div style="text-align:center;margin-bottom:16px">
<a href="#" style="display:inline-flex;align-items:center;justify-content:center;width:40px;height:40px;margin:0 4px;background:#fff;border:1px solid #e5e5e0;border-radius:50%;color:#5c5c52;text-decoration:none">𝕏</a>
<a href="#" style="display:inline-flex;align-items:center;justify-content:center;width:40px;height:40px;margin:0 4px;background:#fff;border:1px solid #e5e5e0;border-radius:50%;color:#5c5c52;text-decoration:none">in</a>
</div>
<p style="margin:0 0 4px;font-size:11px;color:#8a8a7d;text-transform:uppercase;letter-spacing:0.1em;font-weight:700"><?= strtoupper(htmlspecialchars($siteName)) ?> Technologies Inc.</p>
<p style="margin:0;font-size:11px;color:#8a8a7d">123 Innovation Drive, Suite 500, San Francisco, CA 94103</p>
<div style="margin-top:20px;padding-top:20px;border-top:1px solid #e5e5e0;text-align:center">
<p style="margin:0;font-size:11px;color:#8a8a7d">You're receiving this because you're a <?= htmlspecialchars($siteName) ?> customer. <a href="<?= htmlspecialchars($site_url) ?>/legal_centre" style="color:#5c5c52;text-decoration:underline">Manage Preferences</a> · <a href="<?= htmlspecialchars($site_url) ?>/legal_centre" style="color:#5c5c52;text-decoration:underline">Unsubscribe</a></p>
</div>
</div>
</div>
<div style="margin-top:24px;text-align:center">
<p style="margin:0;font-size:13px;color:#8a8a7d">Questions? Visit our <a href="<?= htmlspecialchars($site_url) ?>/help_centre" style="color:#5c5c52;text-decoration:underline">Help Center</a> or reply to this email.</p>
<p style="margin:8px 0 0;font-size:12px;color:#8a8a7d">© <?= date('Y') ?> <?= htmlspecialchars($siteName) ?>. All rights reserved.</p>
</div>
</div>
</body>
</html>
