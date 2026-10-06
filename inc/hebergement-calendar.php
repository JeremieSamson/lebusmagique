<?php

declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit;
}

const MKWVS_HEBERGEMENT_ICAL_OPTION = 'mkwvs_hebergement_ical_url';
const MKWVS_HEBERGEMENT_ICAL_TTL = 3 * HOUR_IN_SECONDS;
const MKWVS_HEBERGEMENT_LISTING_URL = 'https://www.airbnb.fr/rooms/1049733716164120046';

const MKWVS_HEBERGEMENT_PAGE_SLUG = 'dormir-sur-une-peniche-a-lille';

function mkwvs_hebergement_page_url(): string
{
    $page = get_page_by_path(MKWVS_HEBERGEMENT_PAGE_SLUG);

    return $page instanceof WP_Post ? (string) get_permalink($page) : MKWVS_HEBERGEMENT_LISTING_URL;
}

function mkwvs_hebergement_ical_url(): string
{
    $url = defined('MKWVS_HEBERGEMENT_ICAL')
        ? (string) MKWVS_HEBERGEMENT_ICAL
        : (string) get_option(MKWVS_HEBERGEMENT_ICAL_OPTION, '');

    return (string) apply_filters('mkwvs_hebergement_ical_url', $url);
}

function mkwvs_hebergement_busy_dates(): ?array
{
    $url = mkwvs_hebergement_ical_url();
    if ($url === '') {
        return null;
    }

    $cache_key = 'mkwvs_hebergement_busy_' . md5($url);
    $cached = get_transient($cache_key);
    if (is_array($cached)) {
        return $cached;
    }

    $response = wp_remote_get($url, ['timeout' => 8]);
    if (is_wp_error($response) || wp_remote_retrieve_response_code($response) !== 200) {
        return null;
    }

    if (!str_contains(wp_remote_retrieve_body($response), 'BEGIN:VCALENDAR')) {
        return null;
    }

    $dates = mkwvs_hebergement_parse_ical(wp_remote_retrieve_body($response));
    set_transient($cache_key, $dates, MKWVS_HEBERGEMENT_ICAL_TTL);

    return $dates;
}

function mkwvs_hebergement_parse_ical(string $ics): array
{
    $dates = [];
    preg_match_all('/BEGIN:VEVENT(.*?)END:VEVENT/s', $ics, $events);

    foreach ($events[1] as $event) {
        if (!preg_match('/DTSTART[^:]*:(\d{8})/', $event, $start)) {
            continue;
        }
        if (!preg_match('/DTEND[^:]*:(\d{8})/', $event, $end)) {
            continue;
        }

        $cursor = DateTimeImmutable::createFromFormat('Ymd', $start[1]);
        $stop = DateTimeImmutable::createFromFormat('Ymd', $end[1]);
        if (!$cursor instanceof DateTimeImmutable || !$stop instanceof DateTimeImmutable) {
            continue;
        }

        while ($cursor < $stop) {
            $dates[$cursor->format('Y-m-d')] = true;
            $cursor = $cursor->modify('+1 day');
        }
    }

    return array_keys($dates);
}

function mkwvs_hebergement_calendar(int $months = 12): string
{
    global $a_months;

    $dates = mkwvs_hebergement_busy_dates();
    if ($dates === null) {
        return '';
    }

    $busy = array_flip($dates);
    $today = new DateTimeImmutable('today');
    $month = $today->modify('first day of this month');
    $labels = ['Lun', 'Mar', 'Mer', 'Jeu', 'Ven', 'Sam', 'Dim'];

    $out = '<div class="hebergement__calendar">';

    for ($i = 0; $i < $months; $i++) {
        $first = $month->modify("+$i month");
        $days = (int) $first->format('t');
        $offset = ((int) $first->format('N')) - 1;

        $out .= '<div class="hebergement__month" data-month-index="' . $i . '">';
        $out .= '<p class="hebergement__month-title">'
            . esc_html($a_months[$first->format('m')] . ' ' . $first->format('Y')) . '</p>';
        $out .= '<div class="hebergement__grid">';

        foreach ($labels as $label) {
            $out .= '<span class="hebergement__dow">' . esc_html($label) . '</span>';
        }
        $out .= str_repeat('<span class="hebergement__day is-empty"></span>', $offset);

        for ($d = 1; $d <= $days; $d++) {
            $date = $first->setDate((int) $first->format('Y'), (int) $first->format('m'), $d);
            $key = $date->format('Y-m-d');

            $class = 'hebergement__day';
            $label = 'Libre';
            if ($date < $today) {
                $class .= ' is-past';
                $label = 'Passé';
            } elseif (isset($busy[$key])) {
                $class .= ' is-busy';
                $label = 'Occupé';
            }

            $readable = $d . ' ' . mb_strtolower($a_months[$date->format('m')]) . ' ' . $date->format('Y');

            if ($date < $today || isset($busy[$key])) {
                $out .= sprintf(
                    '<span class="%s"><abbr title="%s : %s">%d</abbr></span>',
                    esc_attr($class),
                    esc_attr($readable),
                    esc_attr($label),
                    $d
                );
                continue;
            }

            $out .= sprintf(
                '<button type="button" class="%s" data-date="%s" aria-label="%s, libre">%d</button>',
                esc_attr($class),
                esc_attr($key),
                esc_attr($readable),
                $d
            );
        }

        $out .= '</div></div>';
    }

    $out .= '</div>';
    $out .= '<p class="hebergement__summary" data-empty="Choisissez vos dates d\'arrivée et de départ.">'
        . 'Choisissez vos dates d\'arrivée et de départ.</p>';
    $out .= '<p class="hebergement__legend">';
    $out .= '<span class="hebergement__key hebergement__key--free"></span> Libre';
    $out .= '<span class="hebergement__key hebergement__key--busy"></span> Déjà réservé';
    $out .= '</p>';
    $out .= '<div class="hebergement__booking" data-listing="' . esc_attr(MKWVS_HEBERGEMENT_LISTING_URL) . '">'
        . '<a class="cta cta-decoration" href="' . esc_url(MKWVS_HEBERGEMENT_LISTING_URL) . '"'
        . ' target="_blank" rel="noopener"'
        . ' data-umami-event="hebergement-airbnb" data-umami-event-source="page-hebergement">'
        . 'Voir les disponibilités et réserver</a></div>';

    return $out;
}

add_shortcode('hebergement_calendrier', static function ($atts): string {
    $atts = shortcode_atts(['mois' => 12], (array) $atts);

    return mkwvs_hebergement_calendar(max(1, (int) $atts['mois']));
});

add_filter('nav_menu_link_attributes', static function (array $atts, $item): array {
    if ('post_type' === $item->type && 'page' === $item->object && get_page_by_path(MKWVS_HEBERGEMENT_PAGE_SLUG)?->ID === (int) $item->object_id) {
        $atts['data-umami-event'] = 'hebergement-entree';
        $atts['data-umami-event-source'] = 'nav';
    }

    return $atts;
}, 10, 2);
