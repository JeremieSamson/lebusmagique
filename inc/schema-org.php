<?php

declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Données communes utilisées par tous les schemas (adresse postale, coordonnées, contact).
 * Source : ACF Options Theme (Infos Générales).
 */
function mkwvs_schema_get_common_data(): array
{
    $address = function_exists('get_field') ? get_field('option_contact_address', 'option') : '';
    $city = function_exists('get_field') ? get_field('option_contact_city', 'option') : '';
    $zipcode = function_exists('get_field') ? get_field('option_contact_zipcode', 'option') : '';
    $country = function_exists('get_field') ? get_field('option_contact_country', 'option') : '';
    $phone = function_exists('get_field') ? get_field('option_contact_phone', 'option') : '';
    $email = function_exists('get_field') ? get_field('option_contact_email', 'option') : '';
    $open_hours = function_exists('get_field') ? get_field('option_open_hours', 'option') : '';

    $socials = [];
    if (function_exists('have_rows') && have_rows('option_social_network_list', 'option')) {
        while (have_rows('option_social_network_list', 'option')) {
            the_row();
            $url = get_sub_field('item_social_network_url');
            if (!empty($url)) {
                $socials[] = $url;
            }
        }
    }

    return [
        'name' => get_bloginfo('name'),
        'url' => home_url('/'),
        'address' => trim((string) $address),
        'city' => trim((string) $city),
        'zipcode' => trim((string) $zipcode),
        'country' => trim((string) $country),
        'phone' => trim((string) $phone),
        'email' => trim((string) $email),
        'hours' => trim((string) $open_hours),
        'socials' => $socials,
        'logo' => get_stylesheet_directory_uri() . '/img/favicon.png',
    ];
}

/**
 * Parse le champ texte "Jeudi : 11h - 23h\nVendredi : 11h - 0h..." en openingHoursSpecification.
 */
function mkwvs_schema_parse_opening_hours(string $raw): array
{
    $map = [
        'lundi' => 'Monday',
        'mardi' => 'Tuesday',
        'mercredi' => 'Wednesday',
        'jeudi' => 'Thursday',
        'vendredi' => 'Friday',
        'samedi' => 'Saturday',
        'dimanche' => 'Sunday',
    ];
    $spec = [];
    $lines = preg_split('/[\n\r]+/', $raw);
    foreach ($lines as $line) {
        $line = trim($line);
        if ($line === '') {
            continue;
        }
        if (!preg_match('/^([a-zà-ü]+)\s*:\s*(\d{1,2})h?\s*[-–]\s*(\d{1,2})h?/iu', $line, $m)) {
            continue;
        }
        $day_fr = mb_strtolower($m[1]);
        if (!isset($map[$day_fr])) {
            continue;
        }
        $open = str_pad($m[2], 2, '0', STR_PAD_LEFT) . ':00';
        $close_hour = (int) $m[3];
        if ($close_hour === 0) {
            $close_hour = 24;
        }
        $close = str_pad((string) $close_hour, 2, '0', STR_PAD_LEFT) . ':00';
        $spec[] = [
            '@type' => 'OpeningHoursSpecification',
            'dayOfWeek' => $map[$day_fr],
            'opens' => $open,
            'closes' => $close,
        ];
    }
    return $spec;
}

/**
 * Construit le bloc address + contact commun.
 */
function mkwvs_schema_build_base(array $data, string|array $type = 'Organization'): array
{
    $schema = [
        '@context' => 'https://schema.org',
        '@type' => $type,
        'name' => $data['name'],
        'alternateName' => 'Péniche Le Bus Magique',
        'url' => $data['url'],
        'logo' => $data['logo'],
        'image' => $data['logo'],
    ];

    if ($data['address'] || $data['city']) {
        $schema['address'] = array_filter([
            '@type' => 'PostalAddress',
            'streetAddress' => $data['address'],
            'addressLocality' => $data['city'],
            'postalCode' => $data['zipcode'],
            'addressCountry' => $data['country'] ?: 'FR',
        ]);
    }
    if ($data['phone']) {
        $schema['telephone'] = $data['phone'];
    }
    if (!empty($data['socials'])) {
        $schema['sameAs'] = $data['socials'];
    }
    return $schema;
}

/**
 * Dates concrètes (startDate/endDate ISO 8601) du prochain événement d'une liste
 * de facebook_events à venir. Google exige startDate sur Event : sans occurrence
 * datée, ne pas émettre de schema Event.
 */
function mkwvs_schema_event_occurrence(array $events): array
{
    if (empty($events) || !$events[0] instanceof WP_Post) {
        return [];
    }
    $start_ts = mkwvs_event_start_timestamp($events[0]->ID);
    if (!$start_ts) {
        $start_ts = (int) get_post_meta($events[0]->ID, 'start_ts', true);
    }
    if (!$start_ts) {
        return [];
    }

    return [
        'startDate' => wp_date('Y-m-d\TH:i:sP', $start_ts),
        'endDate' => wp_date('Y-m-d\TH:i:sP', $start_ts + 3 * HOUR_IN_SECONDS),
    ];
}

/**
 * Schema.org global : injecté dans <head> sur toutes les pages.
 * - Organization sur toutes les pages
 * - LocalBusiness + openingHours sur la home (signal fort d'entité locale)
 */
function mkwvs_schema_inject_global(): void
{
    if (is_admin() || is_feed()) {
        return;
    }

    $data = mkwvs_schema_get_common_data();

    if (is_front_page() || is_home()) {
        $schema = mkwvs_schema_build_base($data, 'LocalBusiness');
        $schema['description'] = get_bloginfo('description')
            ?: "Péniche associative à Lille : restaurant, bar, programmation culturelle, coworking et privatisation.";
        $schema['priceRange'] = '€€';
        if ($data['hours']) {
            $hours = mkwvs_schema_parse_opening_hours($data['hours']);
            if (!empty($hours)) {
                $schema['openingHoursSpecification'] = $hours;
            }
        }
    } else {
        $schema = mkwvs_schema_build_base($data, 'Organization');
    }

    echo "\n" . '<script type="application/ld+json">'
        . wp_json_encode($schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE)
        . '</script>' . "\n";
}
add_action('wp_head', 'mkwvs_schema_inject_global', 99);

/**
 * SEOPress nomme le WebSite d'après le title de l'accueil (« Le Bus Magique - Montez à bord ! »).
 * Google en tire le nom de site affiché en SERP : on lui donne la marque, et le nom
 * de la fiche Google Business en alternatif pour lever l'ambiguïté avec le dessin animé.
 */
function mkwvs_schema_website_name(array $schema): array
{
    $schema['name'] = get_bloginfo('name');
    $schema['alternateName'] = 'Péniche Le Bus Magique';

    return $schema;
}
add_filter('seopress_schemas_website', 'mkwvs_schema_website_name');

/**
 * Schema.org spécifique : injecté sur les pages /restauration/ et /coworking/.
 */
function mkwvs_schema_inject_page_specific(): void
{
    if (is_admin() || is_feed()) {
        return;
    }

    $template = get_page_template_slug();
    $data = mkwvs_schema_get_common_data();
    $hours = $data['hours'] ? mkwvs_schema_parse_opening_hours($data['hours']) : [];

    $schema = null;

    if ($template === 'templates/restauration.php') {
        $schema = mkwvs_schema_build_base($data, 'Restaurant');
        $schema['description'] = "Restaurant et bar sur péniche à Lille. Saveurs locales, cuisine saine et conviviale, options végétariennes, brunch dominical.";
        $schema['servesCuisine'] = ['Française', 'Bistro', 'Végétarienne'];
        $schema['priceRange'] = '€€';
        $schema['acceptsReservations'] = 'True';
        if (!empty($hours)) {
            $schema['openingHoursSpecification'] = $hours;
        }
        $schema['hasMenu'] = get_permalink();
    } elseif ($template === 'templates/coworking.php') {
        $schema = mkwvs_schema_build_base($data, ['LocalBusiness', 'CoworkingSpace']);
        $schema['description'] = "Espace de coworking atypique sur péniche à Lille, au bord de la Deûle. Wifi, café, ambiance chaleureuse et cadre unique pour travailler autrement.";
        $schema['priceRange'] = 'Gratuit';
        $schema['isAccessibleForFree'] = true;
        if (!empty($hours)) {
            $schema['openingHoursSpecification'] = $hours;
        }
        $schema['amenityFeature'] = [
            ['@type' => 'LocationFeatureSpecification', 'name' => 'Wifi', 'value' => true],
            ['@type' => 'LocationFeatureSpecification', 'name' => 'Bar / Café', 'value' => true],
            ['@type' => 'LocationFeatureSpecification', 'name' => 'Terrasse', 'value' => true],
            ['@type' => 'LocationFeatureSpecification', 'name' => 'Accès gratuit', 'value' => true],
        ];
    } elseif ($template === 'templates/hebergement.php') {
        $schema = mkwvs_schema_build_base($data, 'LodgingBusiness');
        $schema['description'] = "Studio insolite à louer à la nuit sur une péniche à Lille, aux portes de la Citadelle, pour deux à trois personnes.";
        $schema['numberOfRooms'] = 1;
        $schema['petsAllowed'] = false;
        $schema['maximumAttendeeCapacity'] = 3;
        $schema['amenityFeature'] = [
            ['@type' => 'LocationFeatureSpecification', 'name' => 'Wifi', 'value' => true],
            ['@type' => 'LocationFeatureSpecification', 'name' => 'Kitchenette', 'value' => true],
            ['@type' => 'LocationFeatureSpecification', 'name' => 'Terrasse privée', 'value' => true],
            ['@type' => 'LocationFeatureSpecification', 'name' => 'Lave-vaisselle', 'value' => true],
            ['@type' => 'LocationFeatureSpecification', 'name' => 'Climatisation', 'value' => true],
        ];
    } elseif ($template === 'templates/peniche-lille.php') {
        $schema = mkwvs_schema_build_base($data, ['LocalBusiness', 'TouristAttraction']);
        $schema['description'] = "Péniche associative amarrée à l'entrée de la Citadelle de Lille : bar, restauration, programmation culturelle, coworking, privatisation et gîte à bord d'un bateau de 1954.";
        $schema['priceRange'] = '€€';
        $schema['isAccessibleForFree'] = false;
        $schema['publicAccess'] = true;
        $schema['touristType'] = ['Familles', 'Groupes', 'Visiteurs de Lille'];
        $schema['hasMap'] = 'https://www.google.com/maps/search/?api=1&query=Le+Bus+Magique%2C+avenue+Cuvier%2C+59800+Lille';
        if (!empty($hours)) {
            $schema['openingHoursSpecification'] = $hours;
        }
    } elseif ($template === 'templates/location.php') {
        $schema = mkwvs_schema_build_base($data, ['LocalBusiness', 'EventVenue']);
        $schema['description'] = "Privatisation d'une péniche à Lille pour un anniversaire, un séminaire, une soirée d'entreprise ou un mariage, à l'entrée de la Citadelle.";
        $schema['maximumAttendeeCapacity'] = 100;
        $schema['priceRange'] = '€€';
        if (!empty($hours)) {
            $schema['openingHoursSpecification'] = $hours;
        }
    }

    if ($template === 'templates/peniche-lille.php') {
        $faq = [
            '@context' => 'https://schema.org',
            '@type' => 'FAQPage',
            'mainEntity' => [
                [
                    '@type' => 'Question',
                    'name' => "Où se trouve la péniche Le Bus Magique à Lille ?",
                    'acceptedAnswer' => [
                        '@type' => 'Answer',
                        'text' => "La péniche est amarrée avenue Cuvier, 59800 Lille, à l'entrée de la Citadelle, le long de la Deûle. On y accède par le métro République Beaux-Arts ou l'arrêt de bus Champ de Mars, et le parking du Champ de Mars se trouve juste à côté.",
                    ],
                ],
                [
                    '@type' => 'Question',
                    'name' => "Peut-on manger et boire un verre sur la péniche ?",
                    'acceptedAnswer' => [
                        '@type' => 'Answer',
                        'text' => "Oui. La péniche sert des plats du jour les jeudi et vendredi midi et un brunch le dimanche, avec une cuisine maison, bio et de saison. Le bar propose des bières locales, des vins et des boissons chaudes, à toute heure du jeudi au dimanche (et même le mercredi à la belle saison) !",
                    ],
                ],
                [
                    '@type' => 'Question',
                    'name' => "Faut-il adhérer à l'association pour monter à bord ?",
                    'acceptedAnswer' => [
                        '@type' => 'Answer',
                        'text' => "Oui. Le Bus Magique est une association, l'adhésion est donc nécessaire. Son montant est libre et elle se prend directement à bord auprès d'un bénévole ou d'un serveur.",
                    ],
                ],
                [
                    '@type' => 'Question',
                    'name' => "Peut-on privatiser la péniche pour un événement ?",
                    'acceptedAnswer' => [
                        '@type' => 'Answer',
                        'text' => "Oui, pour des événements privés comme un anniversaire, un séminaire, une soirée d'entreprise ou un mariage. La salle accueille 60 personnes assises et 100 en cocktail, la terrasse 40 assises et 60 en cocktail. Les disponibilités se consultent directement sur notre page de privatisation.",
                    ],
                ],
                [
                    '@type' => 'Question',
                    'name' => "Peut-on dormir sur la péniche ?",
                    'acceptedAnswer' => [
                        '@type' => 'Answer',
                        'text' => "Oui. Le logement du Marinier, à l'arrière du bateau, se loue à la nuit pour deux à trois personnes, avec sa terrasse privée et sa salle de bain.",
                    ],
                ],
            ],
        ];

        echo "\n" . '<script type="application/ld+json">'
            . wp_json_encode($faq, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE)
            . '</script>' . "\n";
    }

    if ($template === 'templates/hebergement.php') {
        $faq = [
            '@context' => 'https://schema.org',
            '@type' => 'FAQPage',
            'mainEntity' => [
                [
                    '@type' => 'Question',
                    'name' => "Combien de personnes peut accueillir le studio de la péniche ?",
                    'acceptedAnswer' => [
                        '@type' => 'Answer',
                        'text' => "Le studio accueille deux à trois personnes. Il dispose de deux lits et d'une salle de bain privative.",
                    ],
                ],
                [
                    '@type' => 'Question',
                    'name' => "Où est amarrée la péniche à Lille ?",
                    'acceptedAnswer' => [
                        '@type' => 'Answer',
                        'text' => "La péniche est amarrée avenue Cuvier, à l'entrée de la Citadelle de Lille, à une dizaine de minutes à pied du Vieux-Lille.",
                    ],
                ],
                [
                    '@type' => 'Question',
                    'name' => "Le logement est-il indépendant du bar et du restaurant ?",
                    'acceptedAnswer' => [
                        '@type' => 'Answer',
                        'text' => "Oui. Le studio occupe le logement du Marinier, à l'arrière du bateau, avec son entrée et sa terrasse privée.",
                    ],
                ],
                [
                    '@type' => 'Question',
                    'name' => "Comment réserver une nuit sur la péniche ?",
                    'acceptedAnswer' => [
                        '@type' => 'Answer',
                        'text' => "Les disponibilités sont affichées sur cette page et la réservation se fait en ligne sur notre annonce.",
                    ],
                ],
            ],
        ];

        echo "\n" . '<script type="application/ld+json">'
            . wp_json_encode($faq, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE)
            . '</script>' . "\n";
    }

    if ($schema !== null) {
        echo "\n" . '<script type="application/ld+json">'
            . wp_json_encode($schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE)
            . '</script>' . "\n";
    }
}
add_action('wp_head', 'mkwvs_schema_inject_page_specific', 100);
