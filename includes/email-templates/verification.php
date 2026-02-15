<?php
/**
 * Bloombit - Email Verification Template
 */
$config = $config ?? [];
$site_url = $site_url ?? '/';
$verify_url = $verify_url ?? $site_url . '/login';
$name = $name ?? 'User';
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8"/>
<meta name="viewport" content="width=device-width, initial-scale=1.0"/>
<title>Bloombit | Verify Your Email</title>
</head>
<body style="font-family:Arial,sans-serif;margin:0;padding:0;background:#f8f8f5;color:#1d180c;line-height:1.6">
<div style="max-width:600px;margin:0 auto;padding:24px">
<div style="background:#fff;border:1px solid #e5e5e0;border-radius:12px;overflow:hidden;box-shadow:0 4px 24px rgba(0,0,0,0.06)">
<div style="height:6px;width:100%;background:#ffc105"></div>
<div style="padding:32px 40px">
<span style="display:inline-block;padding:6px 12px;background:rgba(255,193,5,0.15);color:#b8860b;font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:0.05em;border-radius:9999px;margin-bottom:20px">Account Verification</span>
<h2 style="margin:0 0 24px;font-size:28px;font-weight:700;color:#1d180c;line-height:1.3">Verify your Bloombit account</h2>
<p style="margin:0 0 16px;color:#5c5c52;font-size:16px">Hi <strong style="color:#1d180c"><?= htmlspecialchars($name) ?></strong>,</p>
<p style="margin:0 0 24px;color:#5c5c52;font-size:16px">Thanks for signing up. Please verify your email address by clicking the button below.</p>
<div style="margin-top:32px;margin-bottom:32px;text-align:center">
<a href="<?= htmlspecialchars($verify_url) ?>" style="display:inline-block;padding:16px 32px;background:#ffc105;color:#1d180c;font-weight:700;font-size:16px;border-radius:8px;text-decoration:none;box-shadow:0 4px 14px rgba(255,193,5,0.35)">Verify Email →</a>
</div>
<hr style="border:none;border-top:1px solid #e5e5e0;margin:24px 0"/>
<div style="color:#8a8a7d;font-size:14px">
<p style="margin:0 0 4px">Best regards,</p>
<p style="margin:0;font-weight:700;color:#1d180c">The Bloombit Team</p>
</div>
</div>
</div>
</div>
</body>
</html>
