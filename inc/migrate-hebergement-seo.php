<?php

declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Migration one-shot : cible « nuit insolite » et « logement insolite à Lille » sur
 * la page gîte (titres, FAQ, meta description) et varie l'ancre du lien depuis la
 * page péniche. Remplacements ciblés pour ne pas écraser une retouche de la cliente.
 */

const MKWVS_HEBERGEMENT_SEO_DESC = "Logement insolite à Lille : le studio du Marinier, sur une péniche amarrée à la Citadelle. Pour 2 à 3 personnes, terrasse sur le pont et vue sur la Deûle.";

add_action('init', 'mkwvs_migrate_hebergement_seo', 22);

function mkwvs_migrate_hebergement_seo(): void
{
    if ((int) get_option('mkwvs_hebergement_seo_migrated', 0) >= 1) {
        return;
    }

    $page = get_page_by_path('dormir-sur-une-peniche-a-lille');
    $peniche = get_page_by_path('peniche-lille');

    if (!$page instanceof WP_Post || !$peniche instanceof WP_Post) {
        return;
    }

    mkwvs_hebergement_seo_replace($page, [
        '<h2>Une nuit à bord, au fil de la Deûle</h2>' => '<h2>Une nuit insolite à Lille, au fil de la Deûle</h2>',
        '<h2 class="hebergement__title">Le logement</h2>' => '<h2 class="hebergement__title">Un logement insolite tout équipé</h2>',
        "    <details>\n      <summary>Comment réserver une nuit sur la péniche ?</summary>" => "    <details>\n"
            . "      <summary>Que faire autour de ce logement insolite à Lille ?</summary>\n"
            . "      <p>Le parc de la Citadelle et ses promenades au bord de la Deûle commencent au pied du bateau, le zoo de Lille se trouve dans le parc et le Vieux-Lille est à une dizaine de minutes à pied. Le dimanche, le brunch est servi à bord de 11h à 15h.</p>\n"
            . "    </details>\n"
            . "    <details>\n      <summary>Comment réserver une nuit sur la péniche ?</summary>",
    ], 'Que faire autour de ce logement insolite');

    mkwvs_hebergement_seo_replace($peniche, [
        "le logement du Marinier se loue à la nuit" => 'le logement du Marinier, un <a href="/dormir-sur-une-peniche-a-lille/" data-umami-event="hebergement-entree" data-umami-event-source="peniche-texte">logement insolite à Lille</a>, se loue à la nuit',
    ], 'peniche-texte');

    if (get_post_meta($page->ID, '_seopress_titles_desc', true) === "Louez le studio du Marinier sur une péniche amarrée à la Citadelle de Lille. Pour 2 à 3 personnes, terrasse et vue sur le canal. Disponibilités en ligne.") {
        update_post_meta($page->ID, '_seopress_titles_desc', MKWVS_HEBERGEMENT_SEO_DESC);
    }

    update_option('mkwvs_hebergement_seo_migrated', 1);
}

function mkwvs_hebergement_seo_replace(WP_Post $page, array $replacements, string $done_marker): void
{
    if (str_contains($page->post_content, $done_marker)) {
        return;
    }

    $content = str_replace(array_keys($replacements), array_values($replacements), $page->post_content);

    if ($content !== $page->post_content) {
        wp_update_post(['ID' => $page->ID, 'post_content' => $content]);
    }
}
