<?php
$siteName = $siteName ?? get_site_name();
$pageTitle = $pageTitle ?? ($siteName . ' | Command Center');
?>
<!DOCTYPE html>
<html class="dark" lang="en">
<head>
<?php require __DIR__ . '/admin-head.php'; ?>
</head>
<body class="admin-dashboard font-body-md text-body-md min-h-screen overflow-x-hidden custom-scrollbar">
<?php include __DIR__ . '/admin-sidebar.php'; ?>
<?php include __DIR__ . '/admin-header.php'; ?>
<main class="admin-dash-main relative z-10 min-h-screen w-full lg:ml-64 lg:w-[calc(100%-16rem)] px-4 md:px-margin-desktop pb-6 md:pb-10">
<div class="admin-dash-content">
