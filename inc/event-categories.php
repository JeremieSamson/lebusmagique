<?php

declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Configuration des 6 catégories thématiques d'événements.
 * Utilisée par le bloc pastilles (programmation) et le template de page catégorie.
 *
 * - slug   : terme de la taxonomie facebook_category (classement back-office)
 * - h1     : titre principal (H1) de la page catégorie
 * - page   : slug de la page enfant de /programmation/
 * - color  : couleur d'accent (anneau pastille + bandeau de la page catégorie)
 * - icon   : fichier image (dans /images/) affiché dans la pastille
 * - intro  : chapô éditorial de la page catégorie
 * - links  : liens curatés vers les pages événements dédiées [label, url]
 */
function mkwvs_event_categories(): array
{
    return [
        'prog-conviviale' => [
            'label' => "Prog' conviviale",
            'h1'    => "Nos soirées conviviales à Lille",
            'page'   => 'prog-conviviale',
            'color'  => '#ffd202',
            'hublot' => 'yellow',
            'icon'   => 'hublot-icon-prog-conviviale.png',
            'intro' => "Blind test, karaoké, jeux de société, apéros, cafés des langues : les rendez-vous chaleureux pour se retrouver à bord et passer un bon moment ensemble.",
            'links' => [
                ['Le blind test', '/evenements/blind-test-lille/', "Forme ton équipe et réserve vite ta table pour notre blind-test mensuel !"],
                ['Le café des langues', '/evenements/cafe-des-langues-lille/', "Venez converser en plusieurs langues dans la bonne humeur !"],
                ['Le café italien', '/evenements/cafe-italien-lille/', "Pratiquez l'italien autour d'un aperitivo convivial."],
            ],
        ],
        'bien-etre' => [
            'label' => 'Bien-être',
            'h1'    => "Nos rendez-vous bien-être à Lille",
            'page'   => 'bien-etre',
            'color'  => '#e6534e',
            'hublot' => 'red',
            'icon'   => 'hublot-icon-bien-etre.png',
            'intro' => "Yoga, sophrologie, réflexologie, ateliers santé au naturel : des parenthèses douces pour prendre soin de soi au fil de l'eau.",
            'links' => [],
        ],
        'prog-engagee-inclusive' => [
            'label' => 'Engagée & inclusive',
            'h1'    => "Notre programmation engagée et inclusive à Lille",
            'page'   => 'engagee-inclusive',
            'color'  => '#9bb909',
            'hublot' => 'green',
            'icon'   => 'hublot-icon-engagee-inclusive.png',
            'intro' => "Drag shows, conférences gesticulées, cafés philo, projections, fresques : une programmation qui prend position et inclut toustes.",
            'links' => [
                ['Les scènes ouvertes', '/evenements/scene-ouverte-lille/', "Un moment doux et sans pression pour se retrouver et vibrer ensemble."],
                ['Le drag show & bingo', '/evenements/drag-bingo-lille/', "Une soirée drag pour s'amuser, avec plein de lots à gagner !"],
                ['Le café philo', '/evenements/cafe-philo-lille/', "Venez échanger et apprendre à débattre d'un sujet de société !"],
            ],
        ],
        'culturels-festifs' => [
            'label' => 'Culturels & festifs',
            'h1'    => "Concerts, spectacles et soirées festives à Lille",
            'page'   => 'culturels-festifs',
            'color'  => '#36a585',
            'hublot' => 'blue',
            'icon'   => 'hublot-icon-culturels-festifs.png',
            'intro' => "Concerts, spectacles, impro, stand-up, soirées DJ : la culture vivante et la fête sur la péniche.",
            'links' => [],
        ],
        'ateliers-artistiques' => [
            'label' => 'Ateliers artistiques',
            'h1'    => "Ateliers créatifs et artistiques à Lille",
            'page'   => 'ateliers-artistiques',
            'color'  => '#ec6620',
            'hublot' => 'orange',
            'icon'   => 'hublot-icon-ateliers-artistiques.png',
            'intro' => "Écriture, linogravure, punch needle, illustration, modelage : des ateliers pour créer de ses mains, animés par des intervenant·es locaux.",
            'links' => [
                ['Tous les ateliers', '/evenements/ateliers-lille/', "Écriture, linogravure, modelage, encre de Chine : créez de vos mains !"],
            ],
        ],
        'jams-scenes-ouvertes' => [
            'label' => 'Jams & scènes ouvertes',
            'h1'    => "Jams et scènes ouvertes à Lille",
            'page'   => 'jams-scenes-ouvertes',
            'color'  => '#3f6fd1',
            'hublot' => 'navy',
            'icon'   => 'hublot-icon-jams-scenes-ouvertes.png',
            'intro' => "Jam sessions, scènes ouvertes, ukulélé, poésie : le micro et la scène sont à vous, que vous montiez sur le pont ou veniez écouter.",
            'links' => [
                ['La jam session', '/evenements/jam-session-lille/', "Venez jouer, chanter et bouger sur la scène ouverte !"],
                ['Les scènes ouvertes', '/evenements/scene-ouverte-lille/', "Un moment doux et sans pression pour se retrouver et vibrer ensemble."],
            ],
        ],
    ];
}

/**
 * Configuration des pages "format d'événement" evergreen générées par le template
 * generic templates/evenement-format.php (clé = slug de la page sous /evenements/).
 */
function mkwvs_event_formats(): array
{
    return [
        'drag-bingo-lille' => [
            'icon'        => '👑',
            'h1'          => 'Drag Show & Drag Bingo à Lille',
            'hero'        => "Paillettes, punchlines et fous rires garantis ! Le Bus Magique accueille les drag artistes sur son pont : drag show flamboyant animé par Stargirl, et drag bingo music quizz pour jouer entre deux numéros. Un moment festif, queer et bienveillant, ouvert à toustes.",
            'schema_name' => 'Drag show et drag bingo au Bus Magique à Lille',
            'schema_desc' => "Drag show et drag bingo à Lille sur une péniche : numéros drag animés par Stargirl, bingo musical, ambiance festive et inclusive. Entrée à prix libre.",
            'start'       => '20:00',
            'category'    => 'prog-engagee-inclusive',
            'keywords'    => ['drag', 'bingo'],
            'how'         => [
                "Deux formats, une même énergie. Le drag show met en lumière des artistes drag, débutant·es ou confirmé·es, dans un cadre bienveillant : chaque édition réunit un casting inédit pour une soirée de performances uniques. Le drag bingo (ou music quizz) mêle parties de bingo déjantées et culture musicale, menées par nos hôtes drag.",
                "Glamour, humour, second degré et inclusivité : ici, on célèbre toutes les expressions de genre dans le respect de chacun·e.",
            ],
            'infos'       => [
                ['Quand', 'Une fois par mois, consultez la <a href="/programmation/">programmation</a>'],
                ['Heure', '19h30 pour les bingos et games<br>20h pour les shows (majoritairement debout)'],
                ['Tarif', 'Entrée à prix libre (cash, Lydia ou PayPal), adhésion à prix libre à partir de 1&nbsp;€'],
                ['Réservation', 'Conseillée pour les bingos et games (pas pour les shows) : voir la fiche de l\'événement'],
            ],
            'faq'         => [
                ["C'est quoi un drag show ?", "Un spectacle où des artistes drag performent en chanson, danse, lip-sync ou humour. Au Bus Magique, c'est animé par Stargirl, dans un cadre bienveillant ouvert à toustes."],
                ["Et le drag bingo, ça se passe comment ?", "Un bingo musical déjanté mené par nos hôtes drag : on coche ses cases entre deux numéros, dans la bonne humeur, avec quelques surprises à gagner."],
                ["Faut-il faire partie de la communauté LGBTQIA+ pour venir ?", "Pas du tout. La péniche est un endroit sauf et heureux, ouvert à toutes et tous. La seule règle : le respect de chacun·e, tolérance zéro pour toute discrimination."],
                ["C'est payant ?", "L'entrée est à prix libre. Le Bus Magique étant un café associatif, une adhésion à prix libre valable un an vous est proposée à la première visite, à partir de 1&nbsp;€."],
            ],
        ],
        'cafe-philo-lille' => [
            'icon'        => '💭',
            'h1'          => 'Café philo à Lille',
            'hero'        => "Et si on prenait le temps de penser ensemble ? Une fois par mois, le café philo du Bus Magique réunit curieux·ses et amateur·rices de débat autour d'une question, dans une discussion guidée et bienveillante. Pas besoin d'avoir lu Kant : juste l'envie d'échanger.",
            'schema_name' => 'Café philo au Bus Magique à Lille',
            'schema_desc' => "Café philo mensuel à Lille sur une péniche : discussion guidée autour d'une question, ouverte à tous niveaux. Entrée gratuite, ambiance conviviale.",
            'start'       => '19:00',
            'category'    => 'prog-engagee-inclusive',
            'keywords'    => ['philo'],
            'how'         => [
                "Chaque séance part d'une question (le bonheur, le temps, la liberté, le travail…) proposée et animée par un·e facilitateur·rice. La discussion est ouverte : on argumente, on s'écoute, on change d'avis, sans jargon ni esprit de compétition.",
                "L'objectif n'est pas d'avoir raison, mais de penser ensemble, à voix haute, dans un cadre détendu, un verre à la main.",
            ],
            'infos'       => [
                ['Quand', 'Une fois par mois, consultez la <a href="/programmation/">programmation</a>'],
                ['Heure', '19h'],
                ['Tarif', 'Entrée gratuite, adhésion à prix libre à partir de 1&nbsp;€'],
                ['Inscription', 'Sur place, pas de réservation'],
            ],
            'faq'         => [
                ["Faut-il connaître la philosophie pour participer ?", "Pas du tout. Le café philo est ouvert à tous les niveaux : on part d'une question de la vie quotidienne et l'animateur·rice guide la discussion. Aucun prérequis, juste l'envie d'échanger."],
                ["Comment se passe une séance ?", "On part d'une question, chacun·e prend la parole s'il ou elle le souhaite, l'animateur·rice fait le lien et relance. La séance dure environ 1h30 à 2h."],
                ["Peut-on venir juste pour écouter ?", "Bien sûr. Vous pouvez participer activement ou simplement écouter et réfléchir : les deux sont parfaitement bienvenus."],
                ["C'est payant ?", "L'entrée est gratuite. Le Bus Magique étant un café associatif, une adhésion à prix libre valable un an vous est proposée à la première visite, à partir de 1&nbsp;€."],
            ],
        ],
        'cafe-des-langues-lille' => [
            'icon'        => '🌍',
            'h1'          => 'Café des langues à Lille',
            'hero'        => "Holà, Ciao, Hallo, Hello ! Le café des langues du Bus Magique, c'est LA soirée multilingue pour pratiquer, progresser et rencontrer du monde. Anglais, espagnol, allemand, italien, suédois… ou le français si ce n'est pas ta langue : on blablate dans toutes les langues, dans la bonne humeur.",
            'schema_name' => 'Café des langues au Bus Magique à Lille',
            'schema_desc' => "Café des langues à Lille sur une péniche : soirée multilingue pour pratiquer anglais, espagnol, allemand, italien… Échange convivial, tous niveaux, entrée gratuite.",
            'start'       => '19:00',
            'category'    => 'prog-conviviale',
            'keywords'    => ['langue'],
            'how'         => [
                "Le principe est simple : des tables par langue, des participant·es de tous niveaux, et l'envie de discuter. Que tu sois bilingue ou grand·e débutant·e, tu trouves ta table et tu pratiques en t'amusant, sans pression et sans cours magistral.",
                "C'est aussi l'occasion idéale d'internationaliser son cercle d'ami·es et de rencontrer des Lillois·es comme des voyageur·ses de passage.",
            ],
            'infos'       => [
                ['Quand', 'Régulièrement, consultez la <a href="/programmation/">programmation</a>'],
                ['Heure', '19h'],
                ['Niveau', 'Tous niveaux, du grand débutant au bilingue'],
                ['Tarif', 'Entrée gratuite, adhésion à prix libre à partir de 1&nbsp;€'],
            ],
            'faq'         => [
                ["Faut-il être bilingue pour venir ?", "Pas du tout. Le café des langues accueille tous les niveaux : on apprend, on pratique et on s'amuse, sans jugement. Les débutant·es sont particulièrement les bienvenu·es."],
                ["Quelles langues peut-on pratiquer ?", "Anglais, espagnol, allemand, italien et d'autres selon les participant·es présent·es. Et bien sûr le français, pour celles et ceux dont ce n'est pas la langue maternelle."],
                ["Comment ça se passe ?", "On s'installe autour de tables par langue et on discute. Pas de programme rigide : on suit l'envie et le niveau de chacun·e."],
                ["C'est payant ?", "L'entrée est gratuite. Le Bus Magique étant un café associatif, une adhésion à prix libre valable un an vous est proposée à la première visite, à partir de 1&nbsp;€."],
            ],
        ],
        'cafe-italien-lille' => [
            'icon'        => '🇮🇹',
            'h1'          => 'Café italien à Lille',
            'hero'        => "Buongiorno ! Le café italien du Bus Magique, c'est le rendez-vous pour parler italien et goûter à la dolce vita lilloise. Conversation, aperitivo et bonne humeur transalpine : que tu sois bilingue ou que tu connaisses juste « ciao » et « pizza », tu es le·la bienvenu·e.",
            'schema_name' => 'Café italien au Bus Magique à Lille',
            'schema_desc' => "Café italien à Lille sur une péniche : soirée conversation et aperitivo pour pratiquer l'italien dans une ambiance conviviale. Tous niveaux, entrée gratuite.",
            'start'       => '19:00',
            'category'    => 'prog-conviviale',
            'keywords'    => ['italien'],
            'how'         => [
                "Une soirée dédiée à la langue et à la culture italiennes : on se retrouve autour de tables de conversation pour pratiquer l'italien, échanger et partager un moment à l'italienne. Tous les niveaux sont les bienvenus, des grand·es débutant·es aux madrelingua.",
                "Aperitivo, bonne humeur et envie de voyage : pas besoin de parler couramment, juste l'envie de se lancer.",
            ],
            'infos'       => [
                ['Quand', 'Régulièrement, consultez la <a href="/programmation/">programmation</a>'],
                ['Heure', '19h'],
                ['Niveau', 'Tous niveaux, du grand débutant au bilingue'],
                ['Tarif', 'Entrée gratuite, adhésion à prix libre à partir de 1&nbsp;€'],
            ],
            'faq'         => [
                ["Faut-il parler italien pour venir ?", "Pas du tout. Le café italien est ouvert à tous les niveaux : on vient pour pratiquer et progresser, dans une ambiance détendue. Les débutant·es sont les bienvenu·es."],
                ["Comment ça se passe ?", "On s'installe autour de tables de conversation, parfois avec un thème ou un petit aperitivo, et on échange en italien selon le niveau de chacun·e."],
                ["Peut-on venir seul·e ?", "Bien sûr, c'est même l'idéal pour rencontrer du monde. L'équipe veille à ce que chacun·e trouve sa place et sa table."],
                ["C'est payant ?", "L'entrée est gratuite. Le Bus Magique étant un café associatif, une adhésion à prix libre valable un an vous est proposée à la première visite, à partir de 1&nbsp;€."],
            ],
        ],
    ];
}

/**
 * Retourne la config d'une catégorie à partir du slug de page (ex. 'engagee-inclusive').
 */
function mkwvs_event_category_by_page(string $page_slug): ?array
{
    foreach (mkwvs_event_categories() as $term_slug => $cat) {
        if ($cat['page'] === $page_slug) {
            return ['term' => $term_slug] + $cat;
        }
    }
    return null;
}

/**
 * Événements à venir d'une catégorie donnée (taxonomie facebook_category).
 */
function mkwvs_event_category_query(string $term_slug, int $limit = 12): WP_Query
{
    // Événements À VENIR de la catégorie, du plus proche au plus lointain.
    // (On n'affiche pas les events passés : ils se font rediriger en 301.)
    return new WP_Query([
        'post_type'      => 'facebook_events',
        'post_status'    => 'publish',
        'posts_per_page' => $limit,
        'no_found_rows'  => true,
        'meta_query'     => [
            'start' => [
                'key'     => 'start_ts',
                'value'   => time(),
                'compare' => '>=',
                'type'    => 'NUMERIC',
            ],
        ],
        'orderby'        => ['start' => 'ASC'],
        'tax_query'      => [
            [
                'taxonomy' => 'facebook_category',
                'field'    => 'slug',
                'terms'    => $term_slug,
            ],
        ],
    ]);
}

/**
 * Prochains événements à venir (start_ts >= maintenant), filtrés par mots-clés
 * dans le titre et/ou par catégorie. Triés du plus proche au plus lointain.
 *
 * @param array $opts ['keywords' => string[], 'category' => string]
 * @return WP_Post[]
 */
function mkwvs_upcoming_events(array $opts = [], int $limit = 3): array
{
    global $wpdb;

    $where_cb = null;
    if (!empty($opts['keywords'])) {
        $likes = [];
        foreach ((array) $opts['keywords'] as $kw) {
            $likes[] = $wpdb->prepare($wpdb->posts . '.post_title LIKE %s', '%' . $wpdb->esc_like($kw) . '%');
        }
        $clause = ' AND (' . implode(' OR ', $likes) . ') ';
        $where_cb = static function ($where) use ($clause) {
            return $where . $clause;
        };
        add_filter('posts_where', $where_cb);
    }

    $args = [
        'post_type'      => 'facebook_events',
        'post_status'    => 'publish',
        'posts_per_page' => $limit,
        'no_found_rows'  => true,
        'meta_query'     => [
            'start' => [
                'key'     => 'start_ts',
                'value'   => time(),
                'compare' => '>=',
                'type'    => 'NUMERIC',
            ],
        ],
        'orderby'        => ['start' => 'ASC'],
    ];
    if (!empty($opts['category'])) {
        $args['tax_query'] = [
            [
                'taxonomy' => 'facebook_category',
                'field'    => 'slug',
                'terms'    => $opts['category'],
            ],
        ];
    }

    $q = new WP_Query($args);

    if ($where_cb) {
        remove_filter('posts_where', $where_cb);
    }

    return $q->posts;
}

/**
 * Timestamp de début d'un événement Facebook depuis ses meta de date locale.
 * Le meta start_ts n'est pas fiable : selon l'install, il encode l'heure
 * locale comme si elle était UTC (décalage de 2h constaté en prod).
 */
function mkwvs_event_start_timestamp(int $post_id): int
{
    $date     = get_post_meta($post_id, 'event_start_date', true);
    $hour     = get_post_meta($post_id, 'event_start_hour', true);
    $minute   = get_post_meta($post_id, 'event_start_minute', true);
    $meridian = get_post_meta($post_id, 'event_start_meridian', true);

    $tz = wp_timezone();

    if ($date && $hour !== '') {
        $str = sprintf('%s %d:%02d %s', $date, (int) $hour, (int) $minute, strtolower((string) $meridian));
        $dt = DateTime::createFromFormat('Y-m-d g:i a', $str, $tz);
        if ($dt instanceof DateTime) {
            return $dt->getTimestamp();
        }
    }
    if ($date) {
        $dt = DateTime::createFromFormat('Y-m-d', $date, $tz);
        if ($dt instanceof DateTime) {
            return $dt->setTime(0, 0)->getTimestamp();
        }
    }

    return 0;
}

/**
 * Libellé de date FR d'un événement Facebook à partir de ses meta.
 * Ex. "Jeudi 25 juin · 20h30".
 */
function mkwvs_event_date_label(int $post_id): string
{
    $date     = get_post_meta($post_id, 'event_start_date', true);
    $hour     = get_post_meta($post_id, 'event_start_hour', true);
    $minute   = get_post_meta($post_id, 'event_start_minute', true);
    $meridian = get_post_meta($post_id, 'event_start_meridian', true);

    $tz = wp_timezone();
    $ts = 0;
    $has_time = false;

    // Heure locale stockée par le plugin : on la parse dans le fuseau du site.
    if ($date && $hour !== '') {
        $str = sprintf('%s %d:%02d %s', $date, (int) $hour, (int) $minute, strtolower((string) $meridian));
        $dt = DateTime::createFromFormat('Y-m-d g:i a', $str, $tz);
        if ($dt instanceof DateTime) {
            $ts = $dt->getTimestamp();
            $has_time = true;
        }
    }
    if (!$ts && $date) {
        $dt = DateTime::createFromFormat('Y-m-d', $date, $tz);
        if ($dt instanceof DateTime) {
            $ts = $dt->setTime(0, 0)->getTimestamp();
        }
    }
    if (!$ts) {
        return '';
    }

    $label = ucfirst((string) wp_date('l j F', $ts));
    if ($has_time) {
        $label .= ' · ' . wp_date('H\hi', $ts);
    }
    return $label;
}
