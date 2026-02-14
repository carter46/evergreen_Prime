<?php
/**
 * Bloombit - Contact Form Notification Email (to support team)
 */
$config = $config ?? [];
$site_url = $config['site']['url'] ?? 'https://bloombit.com';
$name = $name ?? '';
$email = $email ?? '';
$subject = $subject ?? '';
$message = $message ?? '';
$message_escaped = htmlspecialchars($message);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8"/>
<meta name="viewport" content="width=device-width, initial-scale=1.0"/>
<title>Bloombit | Contact Form</title>
</head>
<body style="font-family:Arial,sans-serif;margin:0;padding:0;background:#f8f8f5;color:#1d180c;line-height:1.6">
<div style="max-width:600px;margin:0 auto;padding:24px">
<div style="background:#fff;border:1px solid #e5e5e0;border-radius:12px;overflow:hidden;box-shadow:0 4px 24px rgba(0,0,0,0.06)">
<div style="height:6px;width:100%;background:#ffc105"></div>
<div style="padding:32px 40px">
<span style="display:inline-block;padding:6px 12px;background:rgba(255,193,5,0.15);color:#b8860b;font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:0.05em;border-radius:9999px;margin-bottom:20px">Contact Form</span>
<h2 style="margin:0 0 24px;font-size:24px;font-weight:700;color:#1d180c">New message from Bloombit website</h2>
<table style="width:100%;border-collapse:collapse;margin-bottom:24px">
<tr><td style="padding:8px 0;color:#5c5c52;font-weight:600;width:120px">Name</td><td style="padding:8px 0;color:#1d180c"><?= htmlspecialchars($name) ?></td></tr>
<tr><td style="padding:8px 0;color:#5c5c52;font-weight:600">Email</td><td style="padding:8px 0"><a href="mailto:<?= htmlspecialchars($email) ?>" style="color:#ffc105"><?= htmlspecialchars($email) ?></a></td></tr>
<tr><td style="padding:8px 0;color:#5c5c52;font-weight:600">Subject</td><td style="padding:8px 0;color:#1d180c"><?= htmlspecialchars($subject) ?></td></tr>
</table>
<div style="padding:16px;background:#f5f5f0;border-radius:8px;border-left:4px solid #ffc105">
<div style="margin:0;color:#1d180c;line-height:1.7"><?= nl2br($message_escaped) ?></div>
</div>
<p style="margin-top:24px;font-size:13px;color:#8a8a7d">Reply directly to this email to respond to <?= htmlspecialchars($name) ?>.</p>
</div>
</div>
</div>
</body>
</html>
