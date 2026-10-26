<?php

declare(strict_types=1);

/**
 * Fiche événement Facebook (single-facebook_events).
 *
 * Le plugin Import Facebook Events injecte dans le contenu une meta brute
 * (« Date: juillet 3 », « Heure: 02:30 pm », carte Google Maps) et laisse la
 * description Facebook dans un unique <p> où les sauts de ligne se collapsent.
 * On nettoie tout ça côté thème, sans toucher au plugin :
 *  - date/heure reformatées en français via mkwvs_event_date_label()
 *  - carte Google Maps remplacée par un lien texte (perf + RGPD)
 *  - description restructurée en paragraphes, listes et encart réservation
 *
 * Chaque transformation échoue proprement si le markup change (contenu inchangé).
 */

// Emojis susceptibles d'introduire une ligne (puces visuelles de l'asso).
const MKWVS_FB_EMOJI = '\x{1F000}-\x{1FAFF}\x{2600}-\x{27BF}\x{2B00}-\x{2BFF}\x{2190}-\x{21FF}';
// Tirets/puces de début de ligne, y compris entités issues de wptexturize.
const MKWVS_FB_DASH = '(?:[\x{2013}\x{2014}\x{2022}\-]|&#8211;|&#8212;|&#8226;|&ndash;|&mdash;|&bull;)';

function mkwvs_fb_line_is_callout(string $line): bool
{
    if (preg_match('/^\s*\x{26A0}/u', $line)) {
        return true;
    }
    return (bool) preg_match('/r[e\x{00E9}]servation/iu', $line);
}

function mkwvs_fb_line_is_emoji(string $line): bool
{
    return (bool) preg_match('/^\s*[' . MKWVS_FB_EMOJI . ']/u', $line);
}

function mkwvs_fb_line_is_dash(string $line): bool
{
    return (bool) preg_match('/^\s*' . MKWVS_FB_DASH . '\s+/u', $line);
}

function mkwvs_fb_strip_dash(string $line): string
{
    return preg_replace('/^\s*' . MKWVS_FB_DASH . '\s+/u', '', $line);
}

// Ajoute une espace après l'emoji de tête s'il colle au texte (« 🥰Amélioration »).
function mkwvs_fb_space_after_emoji(string $line): string
{
    return preg_replace(
        '/^(\s*[' . MKWVS_FB_EMOJI . '][\x{FE0F}\x{200D}]*)([^\s\x{FE0F}\x{200D}])/u',
        '$1 $2',
        $line
    );
}

function mkwvs_fb_format_block(array $lines): string
{
    $out = '';
    $listBuffer = '';
    $flush = static function () use (&$out, &$listBuffer): void {
        if ($listBuffer !== '') {
            $out .= '<ul class="event-list">' . $listBuffer . '</ul>';
            $listBuffer = '';
        }
    };

    $n = count($lines);
    for ($i = 0; $i < $n;) {
        $line = $lines[$i];

        if (mkwvs_fb_line_is_callout($line)) {
            $flush();
            $out .= '<p class="event-callout">' . mkwvs_fb_space_after_emoji($line) . '</p>';
            $i++;
            continue;
        }

        if (mkwvs_fb_line_is_emoji($line)) {
            // Les tirets qui suivent une ligne emoji forment une sous-liste (ex. tarifs).
            $sub = '';
            $j = $i + 1;
            while ($j < $n && mkwvs_fb_line_is_dash($lines[$j])) {
                $sub .= '<li>' . mkwvs_fb_strip_dash($lines[$j]) . '</li>';
                $j++;
            }
            $li = '<li>' . mkwvs_fb_space_after_emoji($line);
            if ($sub !== '') {
                $li .= '<ul class="event-sublist">' . $sub . '</ul>';
            }
            $li .= '</li>';
            $listBuffer .= $li;
            $i = $j;
            continue;
        }

        if (mkwvs_fb_line_is_dash($line)) {
            $flush();
            $sub = '';
            while ($i < $n && mkwvs_fb_line_is_dash($lines[$i])) {
                $sub .= '<li>' . mkwvs_fb_strip_dash($lines[$i]) . '</li>';
                $i++;
            }
            $out .= '<ul class="event-sublist">' . $sub . '</ul>';
            continue;
        }

        // Ligne de texte : sous-titre si elle se termine par « : », sinon paragraphe.
        $flush();
        if (preg_match('/:\s*$/', $line)) {
            $out .= '<p class="event-subhead">' . $line . '</p>';
        } else {
            $out .= '<p>' . $line . '</p>';
        }
        $i++;
    }

    $flush();
    return $out;
}

function mkwvs_fb_format_description(string $inner): string
{
    // Retours « durs » de Facebook (souvent en plein mot) : on les fond en espace.
    $text = preg_replace('#<br\s*/?>#i', ' ', $inner);
    // Flèches ASCII lisibles (wptexturize encode « > » en « &gt; »).
    $text = preg_replace('/=(?:&gt;|>)/', "\u{2192}", $text);

    $blocks = preg_split('/\R[ \t]*\R/u', $text) ?: [];

    $html = '';
    foreach ($blocks as $block) {
        $lines = preg_split('/\R/u', $block) ?: [];
        $lines = array_map(static fn(string $l): string => preg_replace('/\s{2,}/u', ' ', trim($l)), $lines);
        $lines = array_values(array_filter($lines, static fn(string $l): bool => $l !== ''));
        if (!$lines) {
            continue;
        }

        if (count($lines) === 1) {
            $line = $lines[0];
            $class = mkwvs_fb_line_is_callout($line) ? ' class="event-callout"' : '';
            $html .= '<p' . $class . '>' . mkwvs_fb_space_after_emoji($line) . '</p>';
            continue;
        }

        $html .= mkwvs_fb_format_block($lines);
    }

    return $html !== '' ? $html : '<p>' . $inner . '</p>';
}

// Le plugin importe le titre Facebook suffixé du lieu (« … / Péniche Le Bus
// magique »). Redondant sur le site de la péniche : on retire le suffixe.
function mkwvs_fb_strip_venue_from_title(string $title, int $post_id = 0): string
{
    if ($post_id !== 0 && get_post_type($post_id) !== 'facebook_events') {
        return $title;
    }

    return preg_replace('#\s*[/|@\x{2013}\x{2014}\x{239F}-]\s*P[e\x{00E9}]niche\s+le\s+bus\s+magique\s*$#iu', '', $title);
}
add_filter('the_title', 'mkwvs_fb_strip_venue_from_title', 10, 2);

function mkwvs_fb_single_clean_meta(string $content): string
{
    if (!is_singular('facebook_events') || !is_main_query() || !in_the_loop()) {
        return $content;
    }

    if (strpos($content, 'ife_eventmeta') === false) {
        return $content;
    }

    $post_id = get_the_ID();

    // Date + Heure du plugin -> libellé français (« Vendredi 3 juillet · 14h30 »).
    $label = function_exists('mkwvs_event_date_label') ? mkwvs_event_date_label($post_id) : '';
    if ($label !== '') {
        $content = preg_replace(
            '#<strong>\s*Date\s*:\s*</strong>.*?<strong>\s*Heure\s*:\s*</strong>\s*<p>.*?</p>#is',
            '<p class="event-when">' . esc_html($label) . '</p>',
            $content,
            1
        );
    }

    // Carte Google Maps -> lien texte à partir de l'adresse du lieu.
    if (preg_match('#<div class="venue">.*?<p>(.*?)</p>#is', $content, $m)) {
        $address = trim(wp_strip_all_tags(html_entity_decode($m[1], ENT_QUOTES, 'UTF-8')));
        if ($address !== '') {
            $maps_url = 'https://www.google.com/maps/search/?api=1&query=' . rawurlencode($address);
            $link = '<a class="event-map-link" href="' . esc_url($maps_url) . '" target="_blank" rel="noopener">Voir sur Google Maps</a>';
            $content = preg_replace('#<div class="map">.*?</div>#is', $link, $content, 1);
        }
    }

    // Description Facebook -> paragraphes / listes / encart.
    $content = preg_replace_callback(
        '#<p class="wp-block-paragraph">(.*?)</p>#is',
        static fn(array $m): string => mkwvs_fb_format_description($m[1]),
        $content
    );

    return $content;
}
add_filter('the_content', 'mkwvs_fb_single_clean_meta', 20);
