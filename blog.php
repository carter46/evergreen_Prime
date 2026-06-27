<?php
require_once __DIR__ . '/includes/helpers.php';
require_once __DIR__ . '/includes/blog-posts.php';
$siteName = get_site_name();
$pageTitle = 'Insights & Articles | ' . $siteName;
$posts = array_values(get_blog_posts());
?>
<!DOCTYPE html>
<html class="light" lang="en">
<head>
<meta charset="utf-8"/>
<meta content="width=device-width, initial-scale=1.0" name="viewport"/>
<?php require_once __DIR__ . '/includes/marketing-head.php'; ?>
</head>
<body class="fidelity-subpage bg-background text-on-surface font-body-md antialiased overflow-x-hidden">
<?php $currentPage = 'blog'; require_once __DIR__ . '/includes/marketing-header.php'; ?>

<main class="max-w-[1152px] mx-auto px-margin-mobile md:px-margin-desktop py-xl">
<nav class="py-md flex items-center gap-xs text-body-sm text-outline mb-lg">
<a class="hover:text-primary" href="/">Home</a>
<span class="material-symbols-outlined text-[16px]">chevron_right</span>
<span class="text-on-surface">Blog</span>
</nav>
<div class="mb-xl max-w-2xl">
<h1 class="font-display-lg text-display-lg text-on-surface mb-sm">Insights &amp; articles</h1>
<p class="text-on-surface-variant font-body-lg">Practical guides on investing, retirement planning, wealth management, and building long-term financial security.</p>
</div>
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-lg">
<?php foreach ($posts as $post): render_blog_card($post); endforeach; ?>
</div>
</main>

<?php require_once __DIR__ . '/includes/marketing-footer.php'; ?>
</body>
</html>
