<?php

declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Migration one-shot : maillage interne vers la page brunch (la CI ne déploie que
 * le code). Sous-menu sous « Restauration », liens depuis la péniche et le gîte.
 */

add_action('init', 'mkwvs_migrate_brunch_links', 21);

function mkwvs_migrate_brunch_links(): void
{
    if ((int) get_option('mkwvs_brunch_links_migrated', 0) >= 1) {
        return;
    }

    if (!get_page_by_path('brunch-lille') instanceof WP_Post) {
        return;
    }

    if (!mkwvs_migrate_brunch_menu()) {
        return;
    }

    mkwvs_brunch_link_page_content(
        MKWVS_PENICHE_PAGE_SLUG,
        'un brunch le dimanche',
        'un <a href="/brunch-lille/" data-umami-event="brunch-entree" data-umami-event-source="peniche-lille">brunch le dimanche</a>'
    );

    mkwvs_brunch_link_page_content(
        MKWVS_HEBERGEMENT_PAGE_SLUG,
        "la Citadelle commence de l'autre côté du quai.</p>",
        "la Citadelle commence de l'autre côté du quai.</p>\n      <p>Le dimanche, le <a href=\"/brunch-lille/\" data-umami-event=\"brunch-entree\" data-umami-event-source=\"gite\">brunch de la péniche</a> est servi à bord de 11h à 15h&nbsp;: de quoi bien commencer la journée sans quitter le bateau.</p>"
    );

    update_option('mkwvs_brunch_links_migrated', 1);
}

function mkwvs_migrate_brunch_menu(): bool
{
    $menu = wp_get_nav_menu_object('navigation');
    $restauration = get_page_by_path('restauration');
    $brunch = get_page_by_path('brunch-lille');

    if (!$menu instanceof WP_Term || !$restauration instanceof WP_Post) {
        return false;
    }

    $items = wp_get_nav_menu_items($menu->term_id);

    if (!is_array($items)) {
        return false;
    }

    $parent = null;
    foreach ($items as $item) {
        if ('post_type' === $item->type && (int) $item->object_id === $brunch->ID) {
            return true;
        }
        if (0 === (int) $item->menu_item_parent && (int) $item->object_id === $restauration->ID) {
            $parent = $item;
        }
    }

    if (!$parent instanceof WP_Post) {
        return false;
    }

    $position = (int) $parent->menu_order + 1;

    foreach ($items as $item) {
        if ((int) $item->menu_order >= $position) {
            wp_update_post([
                'ID' => $item->ID,
                'menu_order' => (int) $item->menu_order + 2,
            ]);
        }
    }

    $children = [
        ['Bar et restaurant', $restauration->ID],
        ['Brunch du dimanche', $brunch->ID],
    ];

    foreach ($children as $offset => [$label, $page_id]) {
        $item_id = wp_update_nav_menu_item($menu->term_id, 0, [
            'menu-item-title' => $label,
            'menu-item-object' => 'page',
            'menu-item-object-id' => $page_id,
            'menu-item-type' => 'post_type',
            'menu-item-status' => 'publish',
            'menu-item-parent-id' => $parent->ID,
            'menu-item-position' => $position + $offset,
        ]);

        if (is_wp_error($item_id)) {
            return false;
        }
    }

    return true;
}

function mkwvs_brunch_link_page_content(string $slug, string $search, string $replace): void
{
    $page = get_page_by_path($slug);

    if (!$page instanceof WP_Post || str_contains($page->post_content, '/brunch-lille/') || !str_contains($page->post_content, $search)) {
        return;
    }

    wp_update_post([
        'ID' => $page->ID,
        'post_content' => str_replace($search, $replace, $page->post_content),
    ]);
}
