<?php
/**
 * Bloombit - Password Reset Email
 * Based on the provided password reset design.
 */
$config = $config ?? [];
$site_url = $site_url ?? '/';
$reset_url = $reset_url ?? $site_url . '/login';
$name = $name ?? 'User';
$expiry_minutes = $expiry_minutes ?? 60;
require_once dirname(__DIR__) . '/helpers.php';
$siteName = get_site_name();
$logoParts = preg_match('/^(.+)bit$/i', $siteName, $m) ? [$m[1], 'bit'] : [$siteName, null];
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8"/>
<meta name="viewport" content="width=device-width, initial-scale=1.0"/>
<meta http-equiv="X-UA-Compatible" content="IE=edge"/>
<title>Bloombit | Password Reset</title>
</head>
<body style="font-family:Arial,sans-serif;margin:0;padding:0;background:#f8f8f5;color:#1d180c;line-height:1.6">
<div style="max-width:520px;margin:0 auto;padding:32px 24px">
<div style="background:#fff;border:1px solid #eae2cd;border-radius:12px;overflow:hidden;box-shadow:0 4px 24px rgba(0,0,0,0.06)">
<div style="height:6px;width:100%;background:#ffc105"></div>
<div style="padding:32px 40px 24px;background:#fff;border-bottom:1px solid #f0f0f0;text-align:center">
<span style="font-size:32px;font-weight:700;color:#1d180c;letter-spacing:-0.02em;line-height:1.2"><?= htmlspecialchars($logoParts[0]) ?><?php if ($logoParts[1]): ?><span style="color:#ffc105"><?= htmlspecialchars($logoParts[1]) ?></span><?php endif; ?></span>
</div>
<div style="padding:40px 40px 32px">
<div style="width:48px;height:48px;background:rgba(255,193,5,0.15);border-radius:8px;display:flex;align-items:center;justify-content:center;margin-bottom:24px">
<span style="font-size:24px">🔐</span>
</div>
<h2 style="margin:0 0 12px;font-size:24px;font-weight:700;color:#1d180c;line-height:1.3">Forgot Password?</h2>
<p style="margin:0 0 24px;color:#a18a45;font-size:14px;line-height:1.6">No worries, it happens. We received a request to reset the password for your Bloombit account.</p>
<p style="margin:0 0 24px;color:#5c5c52;font-size:16px">Hi <strong style="color:#1d180c"><?= htmlspecialchars($name) ?></strong>,</p>
<p style="margin:0 0 32px;color:#5c5c52;font-size:16px">Click the button below to create a new password. This link will expire in <strong><?= (int)$expiry_minutes ?> minutes</strong>.</p>
<div style="margin-bottom:32px;text-align:center">
<a href="<?= htmlspecialchars($reset_url) ?>" style="display:inline-block;padding:14px 28px;background:#ffc105;color:#1d180c;font-weight:700;font-size:16px;border-radius:8px;text-decoration:none;box-shadow:0 4px 14px rgba(255,193,5,0.25)">Reset My Password →</a>
</div>
<p style="margin:0;font-size:13px;color:#8a8a7d">If you didn't request this, you can safely ignore this email. Your password won't change.</p>
<hr style="border:none;border-top:1px solid #eae2cd;margin:32px 0 24px"/>
<div style="text-align:center">
<a href="<?= htmlspecialchars($site_url) ?>/login" style="font-size:14px;font-weight:500;color:#a18a45;text-decoration:underline">← Back to Login</a>
</div>
</div>
<div style="background:#f5f5f0;padding:24px;border-top:1px solid #eae2cd;text-align:center">
<p style="margin:0;font-size:11px;color:#8a8a7d;display:flex;align-items:center;justify-content:center;gap:6px">
<span>🔒</span> Secure Bank-Grade Encryption
</p>
<p style="margin:12px 0 0;font-size:12px;color:#8a8a7d">© <?= date('Y') ?> Bloombit. All rights reserved.</p>
</div>
</div>
</div>
</body>
</html>
