<?php
require_once __DIR__ . '/includes/helpers.php';
require_once __DIR__ . '/includes/blog-posts.php';

$slug = trim((string) ($_GET['slug'] ?? ''));
$post = $slug !== '' ? get_blog_post($slug) : null;
if (!$post) {
    http_response_code(404);
    header('Location: /blog');
    exit;
}

$siteName = get_site_name();
$pageTitle = $post['title'] . ' | ' . $siteName;
$url = '/blog/' . rawurlencode($post['slug']);
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

<main class="max-w-[800px] mx-auto px-margin-mobile md:px-margin-desktop py-xl">
<nav class="py-md flex items-center gap-xs text-body-sm text-outline mb-lg flex-wrap">
<a class="hover:text-primary" href="/">Home</a>
<span class="material-symbols-outlined text-[16px]">chevron_right</span>
<a class="hover:text-primary" href="/blog">Blog</a>
<span class="material-symbols-outlined text-[16px]">chevron_right</span>
<span class="text-on-surface line-clamp-1"><?php echo htmlspecialchars($post['title']); ?></span>
</nav>
<article>
<div class="flex items-center gap-xs text-on-surface-variant font-label-md text-label-md mb-sm">
<span class="uppercase tracking-widest text-[10px] text-fidelity-green font-bold"><?php echo htmlspecialchars($post['category']); ?></span>
<span class="w-1 h-1 bg-outline rounded-full"></span>
<span><?php echo htmlspecialchars($post['read_time']); ?></span>
</div>
<h1 class="font-display-lg text-display-lg text-on-surface mb-md leading-tight"><?php echo htmlspecialchars($post['title']); ?></h1>
<p class="text-on-surface-variant font-body-lg mb-lg"><?php echo htmlspecialchars($post['excerpt']); ?></p>
<div class="aspect-video rounded-xl overflow-hidden mb-xl border border-surface-gray">
<img class="w-full h-full object-cover" alt="<?php echo htmlspecialchars($post['title']); ?>" src="<?php echo htmlspecialchars($post['image']); ?>">
</div>
<ol class="space-y-md">
<?php foreach ($post['points'] as $i => $point): ?>
<li class="bg-white border border-surface-gray rounded-xl p-md">
<div class="flex gap-sm">
<span class="shrink-0 w-8 h-8 rounded-full bg-fidelity-green text-white text-sm font-bold flex items-center justify-center"><?php echo $i + 1; ?></span>
<div>
<h2 class="font-headline-md text-[18px] text-on-surface mb-xs"><?php echo htmlspecialchars($point['title']); ?></h2>
<p class="text-body-sm text-on-surface-variant leading-relaxed"><?php echo htmlspecialchars($point['body']); ?></p>
</div>
</div>
</li>
<?php endforeach; ?>
</ol>
<div class="mt-xl pt-lg border-t border-surface-gray flex flex-wrap gap-md">
<a href="/blog" class="text-institutional-blue font-label-md hover:underline">← Back to all articles</a>
<a href="/register" class="bg-fidelity-green text-white px-lg py-sm rounded-lg font-label-md hover:opacity-90 transition-all">Open an account</a>
</div>
</article>
</main>

<?php require_once __DIR__ . '/includes/marketing-footer.php'; ?>
</body>
</html>
