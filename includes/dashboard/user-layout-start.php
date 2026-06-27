<?php
/**
 * Opens user dashboard shell: html, body, sidebar, topbar, main.
 * Requires $pageTitle (optional), $currentPage for sidebar.
 */
$siteName = $siteName ?? get_site_name();
?>
<!DOCTYPE html>
<html class="light" lang="en">
<head>
<?php require __DIR__ . '/user-head.php'; ?>
</head>
<body class="user-dashboard bg-background text-on-surface font-body-md text-body-md min-h-screen">
<?php include __DIR__ . '/user-sidebar.php'; ?>
<?php include __DIR__ . '/user-header.php'; ?>
<main class="user-dash-main relative z-10 min-h-screen w-full lg:ml-64 lg:w-[calc(100%-16rem)] px-4 lg:px-lg pb-6 lg:pb-10">
<div class="user-dash-content space-y-lg">
