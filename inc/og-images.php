<?php

declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Open Graph & Twitter Card images — 1200×630 pour les partages sociaux.
 *
 * Stratégie :
 * - Chaque page/post : prend la featured image (thumbnail) au format 'og-image' (1200×630 cropé)
 * - Fallback si pas de featured image : image par défaut du thème (/img/og-default.jpg) puis logo
 * - Override SEOPress via ses filtres pour qu'il serve le bon format
 */

/**
 * Retourne l'URL de l'OG image pour la page courante.
 */
function mkwvs_og_get_image_url(): string
{
    // 1) Featured image de la page
    if (is_singular() && has_post_thumbnail()) {
        $url = get_the_post_thumbnail_url(get_queried_object_id(), 'og-image');
        if ($url) {
            return $url;
        }
    }

    // 2) Première image de la galerie ACF page_head_gallery (nombreuses pages du thème utilisent ce pattern)
    if (is_singular() && function_exists('get_field')) {
        $gallery = get_field('page_head_gallery', get_queried_object_id());
        if (is_array($gallery) && !empty($gallery)) {
            $first = $gallery[0];
            if (!empty($first['page_head_gallery_item_image']['ID'])) {
                $url = wp_get_attachment_image_url($first['page_head_gallery_item_image']['ID'], 'og-image');
                if ($url) {
                    return $url;
                }
            } elseif (!empty($first['page_head_gallery_item_image']) && is_numeric($first['page_head_gallery_item_image'])) {
                $url = wp_get_attachment_image_url((int) $first['page_head_gallery_item_image'], 'og-image');
                if ($url) {
                    return $url;
                }
            }
        }
    }

    // 3) Fallback : image par défaut dans /img/og-default.jpg si elle existe
    $default_path = get_stylesheet_directory() . '/img/og-default.jpg';
    if (file_exists($default_path)) {
        return get_stylesheet_directory_uri() . '/img/og-default.jpg';
    }

    // 3) Fallback ultime : image de la home (featured image de la page d'accueil)
    $home = get_page_by_path('accueil');
    if ($home) {
        $url = get_the_post_thumbnail_url($home->ID, 'og-image');
        if ($url) {
            return $url;
        }
    }

    // 4) Dernier recours : favicon (pas idéal mais pas de 404)
    return get_stylesheet_directory_uri() . '/images/favicon/apple-touch-icon.png';
}

/**
 * Strip les og:image et twitter:image existants (SEOPress ou autre) puis
 * réinjecte nos tags au bon format 1200×630. Utilise un output buffer sur wp_head.
 */
function mkwvs_og_start_buffer(): void
{
    if (is_admin() || is_feed()) {
        return;
    }
    ob_start(static function (string $html): string {
        $html = preg_replace('#<meta\s+property=["\']og:image(?::(width|height|type|secure_url))?["\'][^>]*>\s*#i', '', $html);
        $html = preg_replace('#<meta\s+name=["\']twitter:(image|card)["\'][^>]*>\s*#i', '', $html);
        return (string) $html;
    });
}
add_action('wp_head', 'mkwvs_og_start_buffer', 1);

function mkwvs_og_end_buffer(): void
{
    if (is_admin() || is_feed()) {
        return;
    }
    if (ob_get_level() > 0) {
        ob_end_flush();
    }

    $url = mkwvs_og_get_image_url();
    if (!$url) {
        return;
    }

    echo "\n";
    echo '<meta property="og:image" content="' . esc_url($url) . '" />' . "\n";
    echo '<meta property="og:image:secure_url" content="' . esc_url($url) . '" />' . "\n";
    $size = mkwvs_og_image_size($url);
    if ($size) {
        echo '<meta property="og:image:width" content="' . $size[0] . '" />' . "\n";
        echo '<meta property="og:image:height" content="' . $size[1] . '" />' . "\n";
        echo '<meta property="og:image:type" content="' . esc_attr($size['mime']) . '" />' . "\n";
    }
    echo '<meta name="twitter:card" content="summary_large_image" />' . "\n";
    echo '<meta name="twitter:image" content="' . esc_url($url) . '" />' . "\n";
}
add_action('wp_head', 'mkwvs_og_end_buffer', 99);

/**
 * Dimensions réelles du fichier : une source plus petite que 1200×630 n'est pas
 * agrandie par WordPress, la taille 'og-image' peut donc sortir plus étroite.
 */
function mkwvs_og_image_size(string $url): array
{
    $uploads = wp_get_upload_dir();
    $path = str_replace(
        [$uploads['baseurl'], get_stylesheet_directory_uri()],
        [$uploads['basedir'], get_stylesheet_directory()],
        strtok($url, '?')
    );

    if ($path === $url || !is_file($path)) {
        return [];
    }

    $size = getimagesize($path);

    return $size ?: [];
}
