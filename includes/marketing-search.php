<?php
/**
 * Search index for public marketing pages and blog posts.
 */

function get_marketing_search_index(): array
{
    $items = [
        [
            'type' => 'Page',
            'title' => 'Home',
            'url' => '/',
            'text' => 'home homepage invest today plan tomorrow brokerage retirement wealth',
        ],
        [
            'type' => 'Page',
            'title' => 'Investing',
            'url' => '/investing',
            'text' => 'investing stocks etfs brokerage trading markets commission-free research tools',
        ],
        [
            'type' => 'Page',
            'title' => 'Retirement',
            'url' => '/planning',
            'text' => 'retirement planning ira 401k savings goals calculators income',
        ],
        [
            'type' => 'Page',
            'title' => 'Wealth Management',
            'url' => '/wealth-management',
            'text' => 'wealth management advisor portfolio estate planning insights',
        ],
        [
            'type' => 'Page',
            'title' => 'Blog',
            'url' => '/blog',
            'text' => 'blog articles insights retirement investing wealth planning research',
        ],
        [
            'type' => 'Page',
            'title' => 'Legal Center',
            'url' => '/legal_centre',
            'text' => 'legal terms privacy policy risk disclosure compliance regulatory',
        ],
        [
            'type' => 'Page',
            'title' => 'Help Centre',
            'url' => '/help_centre',
            'text' => 'help support customer service faq contact assistance',
        ],
        [
            'type' => 'Page',
            'title' => 'About Us',
            'url' => '/about_us',
            'text' => 'about company mission team story',
        ],
    ];

    if (!function_exists('get_blog_posts')) {
        require_once __DIR__ . '/blog-posts.php';
    }

    foreach (get_blog_posts() as $post) {
        $text = $post['title'] . ' ' . $post['excerpt'] . ' ' . $post['category'];
        foreach ($post['points'] as $point) {
            $text .= ' ' . $point['title'] . ' ' . $point['body'];
        }
        $items[] = [
            'type' => 'Blog',
            'title' => $post['title'],
            'url' => '/blog/' . $post['slug'],
            'text' => $text,
            'subtitle' => $post['category'],
        ];
    }

    return $items;
}
