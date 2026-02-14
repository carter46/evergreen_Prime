<?php
/**
 * Bloombit - Email Template Renderer
 * Renders an email template with variables and returns HTML string.
 *
 * @param string $template Template filename (without path)
 * @param array $vars Variables to extract for the template
 * @return string HTML output
 */
function renderEmailTemplate($template, array $vars = []) {
    $config = include dirname(__DIR__, 2) . '/config.php';
    $vars['config'] = $config;
    extract($vars, EXTR_SKIP);
    ob_start();
    include dirname(__DIR__) . '/email-templates/' . $template;
    return ob_get_clean();
}
