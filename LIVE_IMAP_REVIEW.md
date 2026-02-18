# Live IMAP Implementation – Deep Review & Handoff Summary

**Date:** 2025-02-14  
**Status:** Implementation verified; ready for QA on Hostinger

---

## Plan vs Implementation Comparison

| Plan Item | Implementation | Status |
|-----------|----------------|--------|
| Use live IMAP for Inbox and Sent UI | `imap-list.php` + `imap-message.php`; Inbox/Sent tables in `communication_hub.php` | OK |
| DB only for audit / optional Archive | `admin_mailbox` inserts for broadcasts and replies; Archive to DB calls `mail-sync.php` | OK |
| IMAP APPEND after broadcast send | `broadcast.php` calls `bloombit_imap_append_to_sent($mime)` per successful send | OK |
| IMAP APPEND after reply send | `imap-reply.php` calls `bloombit_imap_append_to_sent($mime, $config['sent_folder'])` | OK |
| SMTP for sending, IMAP for receiving | PHPMailer for send; IMAP for list/fetch/append | OK |

---

## Component Verification

### 1. `includes/imap.php`
- **bloombit_imap_config()**: Reads `mail_imap_host`, `mail_imap_username`, `mail_imap_password`, `mail_imap_port`, `mail_imap_encryption`, `mail_imap_sent_folder` from `site_settings`.
- **bloombit_imap_append_to_sent($mime, ?$sentFolder)**: Normalizes line endings to `\r\n`, uses config `sent_folder` when `$sentFolder` is null.
- **Early return**: Returns early if `imap_open` does not exist; callers check before requiring.

### 2. `api/admin/imap-list.php`
- GET: `folder`, `limit`, `offset`, `unread_only`.
- Allowed folders: `INBOX`, `SPAM`, `config['sent_folder']`.
- Uses `bloombit_imap_list_uids`, `bloombit_imap_extract_best_body` for preview.

### 3. `api/admin/imap-message.php`
- GET: `folder`, `uid`, `mark_read` (default true).
- Returns full message with `reply_to`, `in_reply_to`, `references` for threading.
- Marks message read when `mark_read` is true.

### 4. `api/admin/imap-reply.php`
- POST JSON: `folder`, `uid`, `body`, `subject` (optional).
- Sends via PHPMailer, then `bloombit_imap_append_to_sent($mime, $config['sent_folder'])`.
- Logs to `admin_mailbox` with columns: direction, source, from_email, from_name, to_emails, subject, body_text, status, in_reply_to, references, created_at.
- INSERT column count and order: correct.

### 5. `api/admin/broadcast.php`
- Requires `imap.php` only when `imap_open` exists.
- Calls `bloombit_imap_append_to_sent($mime)` inside the send loop when `!$testOnly`.
- Does not pass `$sentFolder`; uses config default (correct).
- Test sends do not append to Sent (correct).

### 6. `dashboard/admin/communication_hub.php`
- `$imapSentFolder`: Uses `get_site_setting('mail_imap_sent_folder','') ?: 'Sent'`; edge case guarded with `trim()` fallback.
- **loadInbox()**: `GET /api/admin/imap-list.php?folder=INBOX&limit=20`.
- **loadSent()**: `GET /api/admin/imap-list.php?folder={sentFolder}&limit=20`.
- **openMessage(folder, uid)**: `GET /api/admin/imap-message.php?folder=...&uid=...`.
- Reply form: Only for `folder === 'INBOX'`.
- **#reply-send**: Event delegation; sends `{ folder, uid, body }` to `imap-reply.php`.
- **mail-refresh-all**: Triggers `loadInbox()` and `loadSent()`.
- **inbox-refresh** / **sent-refresh**: Individual refresh per table.

### 7. `api/admin/mail-sync.php`
- Archive to DB: Imports from INBOX and Sent into `admin_mailbox` with `mailbox_folder` and `imap_uid`.
- Response: `{ success, data: { folders, imported, skipped } }` – matches UI expectations.

---

## PHPMailer Integration
- `getSentMIMEMessage()` is used in `broadcast.php` and `imap-reply.php` after `$mail->send()`.
- Returns full RFC 2822 MIME; suitable for IMAP APPEND.

---

## Known Limitations

1. **Nested multipart**: `bloombit_imap_extract_best_body` only handles top-level parts. Messages with `multipart/mixed` containing `multipart/alternative` may have empty body in preview/detail. Common structures (e.g. `multipart/alternative` with text/plain and text/html) work.
2. **Hostinger folder names**: Plan assumes `Sent` (not `INBOX.Sent`). Config should be set in Admin Settings.
3. **Layout**: `w-full min-w-0` on mailbox grid; no 42px collapse observed.

---

## Fix Applied
- **communication_hub.php**: Added `trim()` around `get_site_setting('mail_imap_sent_folder','')` so whitespace-only values fall back to `'Sent'`.

---

## Recommended QA on Hostinger

1. Configure IMAP: host, port (993), user, pass, encryption (ssl), sent folder (`Sent`).
2. Verify Inbox loads and messages open.
3. Verify Sent loads; send a broadcast and reply; confirm new items appear in Sent after refresh.
4. Archive to DB and confirm `admin_mailbox` inserts.
5. Check layout on various viewport widths.

---

## Files Touched in This Review
- `dashboard/admin/communication_hub.php` – minor `$imapSentFolder` safeguard.
