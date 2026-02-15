<?php
/**
 * Bloombit - Session Bootstrap
 * Call on every page/API that uses session. Starts session and enforces 15-minute inactivity timeout for logged-in users.
 */
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (isset($_SESSION['user_id'])) {
    $timeout = 900; // 15 minutes
    $last = $_SESSION['last_activity'] ?? time();
    if (time() - $last > $timeout) {
        session_destroy();
        header('Location: /login?timeout=1');
        exit;
    }
    $_SESSION['last_activity'] = time();
}
