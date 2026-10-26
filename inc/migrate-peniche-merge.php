<?php

declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Migration one-shot : la page « Notre péniche à Lille » est fusionnée dans
 * l'accueil, qui porte déjà « péniche lille » (la CI ne déploie que le code).
 * La page passe en brouillon, le lien de la page brunch pointe sur l'accueil.
 */

add_action('init', 'mkwvs_migrate_peniche_merge', 22);

function mkwvs_migrate_peniche_merge(): void
{
    if ((int) get_option('mkwvs_peniche_merge_migrated', 0) >= 1) {
        return;
    }

    $page = get_page_by_path(MKWVS_PENICHE_PAGE_SLUG);
    if ($page instanceof WP_Post && $page->post_status !== 'draft') {
        wp_update_post([
            'ID' => $page->ID,
            'post_status' => 'draft',
        ]);
    }

    $brunch = get_page_by_path('brunch-lille');
    $search = 'href="/peniche-lille/"';
    if ($brunch instanceof WP_Post && str_contains($brunch->post_content, $search)) {
        wp_update_post([
            'ID' => $brunch->ID,
            'post_content' => str_replace($search, 'href="/"', $brunch->post_content),
        ]);
    }

    update_option('mkwvs_peniche_merge_migrated', 1);
}

add_action('template_redirect', 'mkwvs_redirect_peniche_page', 1);

function mkwvs_redirect_peniche_page(): void
{
    if (!is_404() || trim((string) wp_parse_url($_SERVER['REQUEST_URI'] ?? '', PHP_URL_PATH), '/') !== MKWVS_PENICHE_PAGE_SLUG) {
        return;
    }

    wp_safe_redirect(home_url('/'), 301);
    exit;
}
