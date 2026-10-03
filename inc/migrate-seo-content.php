<?php

declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Migration one-shot : contenu SEO que la CI ne déploie pas (la prod n'a ni SSH ni wp-cli).
 * Meta descriptions SEOPress manquantes, lien mort vers le domaine de recette,
 * exclusion de la page conteneur /evenements/ et retrait de l'article par défaut.
 */

add_action('init', 'mkwvs_migrate_seo_content', 20);

function mkwvs_migrate_seo_content(): void
{
    if ((int) get_option('mkwvs_seo_content_migrated', 0) >= 4) {
        return;
    }

    mkwvs_migrate_meta_descriptions();
    mkwvs_migrate_seo_titles();
    mkwvs_migrate_dead_staging_link();
    mkwvs_migrate_noindex_evenements();
    mkwvs_migrate_trash_default_post();
    mkwvs_migrate_home_title_peniche();

    update_option('mkwvs_seo_content_migrated', 4);
}

/**
 * Chaque page porte une intention distincte : « péniche Lille » revient à la page
 * dédiée, la restauration au restaurant, l'accueil à la marque et au tiers-lieu.
 */
function mkwvs_seo_titles(): array
{
    return [
        'accueil' => 'Péniche Le Bus Magique : bar, concerts et tiers-lieu à Lille',
        'restauration' => "Bar et restaurant sur une péniche à Lille | Le Bus Magique",
        'coworking' => "Coworking sur une péniche à Lille | Le Bus Magique",
        'location' => "Privatiser une péniche à Lille | Le Bus Magique",
    ];
}

function mkwvs_migrate_seo_titles(): void
{
    foreach (mkwvs_seo_titles() as $path => $title) {
        $page = get_page_by_path($path);
        if (!$page instanceof WP_Post) {
            continue;
        }

        if (trim((string) get_post_meta($page->ID, '_seopress_titles_title', true)) !== '') {
            continue;
        }

        update_post_meta($page->ID, '_seopress_titles_title', $title);
    }
}

/**
 * L'ancien title de l'accueil ne disait pas « péniche » : on ne le remplace
 * que s'il n'a pas été retouché depuis, pour ne pas écraser une saisie cliente.
 */
function mkwvs_migrate_home_title_peniche(): void
{
    $page = get_page_by_path('accueil');
    if (!$page instanceof WP_Post) {
        return;
    }

    if (get_post_meta($page->ID, '_seopress_titles_title', true) !== "Le Bus Magique, tiers-lieu associatif sur l'eau à Lille") {
        return;
    }

    update_post_meta($page->ID, '_seopress_titles_title', mkwvs_seo_titles()['accueil']);
}

function mkwvs_seo_descriptions(): array
{
    return [
        'association' => "L'association Le Bus Magique fait vivre une péniche culturelle à Lille : bénévolat, projets solidaires et programmation ouverte à toutes et tous.",
        'contact' => "Contacter Le Bus Magique, péniche culturelle avenue Cuvier à Lille : formulaire, téléphone, adresse et horaires d'ouverture de la péniche.",
        'coworking' => "Travaillez au calme sur une péniche à Lille : espace de coworking convivial, wifi, café et vue sur l'eau, ouvert du mercredi au dimanche.",
        'location' => "Privatisez une péniche à Lille pour votre séminaire, anniversaire ou soirée d'entreprise : salle atypique au bord de l'eau, devis sur mesure.",
        'mentions-legales' => "Mentions légales du site du Bus Magique : éditeur, hébergeur, propriété intellectuelle et traitement des données personnelles.",
        'monter-a-bord' => "Rejoignez Le Bus Magique à Lille : adhésion à prix libre, bénévolat et propositions d'activités. Montez à bord de la péniche associative.",
        'notre-histoire' => "L'histoire du Bus Magique à Lille : d'une péniche à quai à un lieu culturel associatif porté par ses bénévoles et ses adhérents.",
        'programmation' => "La programmation du Bus Magique à Lille : concerts, ateliers, cafés des langues, drag shows et jams sur une péniche associative. Agenda du mois.",
        'restauration' => "Bar et restauration sur une péniche à Lille : cuisine maison, bières locales, boissons chaudes et brunch le dimanche au Bus Magique.",

        'programmation/ateliers-artistiques' => "Ateliers créatifs et artistiques à Lille : écriture, linogravure, punch needle, illustration et modelage, animés par des intervenants locaux.",
        'programmation/bien-etre' => "Rendez-vous bien-être à Lille sur une péniche : yoga, sophrologie, réflexologie et ateliers de santé au naturel, au fil de l'eau.",
        'programmation/culturels-festifs' => "Concerts, spectacles, impro, stand-up et soirées DJ à Lille : la programmation culturelle et festive de la péniche Le Bus Magique.",
        'programmation/engagee-inclusive' => "Programmation engagée et inclusive à Lille : drag shows, conférences gesticulées, cafés philo et projections sur la péniche du Bus Magique.",
        'programmation/jams-scenes-ouvertes' => "Jams et scènes ouvertes à Lille : jam sessions, scènes ouvertes, ukulélé et poésie. Le micro est à vous sur la péniche du Bus Magique.",
        'programmation/prog-conviviale' => "Soirées conviviales à Lille : blind test, karaoké, jeux de société, apéros et cafés des langues sur la péniche du Bus Magique.",

        'evenements/ateliers-lille' => "Ateliers créatifs à Lille sur une péniche : écriture, linogravure, modelage, encre de Chine. Des ateliers accessibles animés par des artistes locaux.",
        'evenements/blind-test-lille' => "Blind test à Lille sur une péniche : formez votre équipe pour notre blind test mensuel, dans une ambiance conviviale. Réservation conseillée.",
        'evenements/cafe-des-langues-lille' => "Café des langues à Lille : venez pratiquer l'anglais, l'espagnol ou l'italien autour d'un verre sur la péniche du Bus Magique. Entrée libre.",
        'evenements/cafe-italien-lille' => "Café italien à Lille : pratiquez l'italien autour d'un aperitivo convivial sur la péniche du Bus Magique, quel que soit votre niveau.",
        'evenements/cafe-philo-lille' => "Café philo à Lille : échangez et débattez d'un sujet de société sur la péniche du Bus Magique, dans un cadre bienveillant et ouvert à tous.",
        'evenements/drag-bingo-lille' => "Drag show et drag bingo à Lille sur une péniche : numéros drag animés par Stargirl, bingo musical et ambiance festive. Entrée à prix libre.",
        'evenements/jam-session-lille' => "Jam session à Lille sur une péniche : venez jouer, chanter ou simplement écouter. Scène ouverte aux musiciens de tous niveaux, entrée libre.",
        'evenements/scene-ouverte-lille' => "Scène ouverte à Lille sur une péniche : musique, poésie, humour. Un moment doux et sans pression pour monter sur scène ou venir écouter.",
    ];
}

function mkwvs_migrate_meta_descriptions(): void
{
    foreach (mkwvs_seo_descriptions() as $path => $description) {
        $page = get_page_by_path($path);
        if (!$page instanceof WP_Post) {
            continue;
        }

        if (trim((string) get_post_meta($page->ID, '_seopress_titles_desc', true)) !== '') {
            continue;
        }

        update_post_meta($page->ID, '_seopress_titles_desc', $description);
    }
}

function mkwvs_migrate_dead_staging_link(): void
{
    $page = get_page_by_path('notre-histoire');
    if (!$page instanceof WP_Post) {
        return;
    }

    if (!get_page_by_path('monter-a-bord') instanceof WP_Post) {
        return;
    }

    $host = (string) wp_parse_url(home_url(), PHP_URL_HOST);
    if ($host === '') {
        return;
    }

    $content = str_replace('busmagique.makewaves.fr', $host, $page->post_content);

    if ($content === $page->post_content) {
        return;
    }

    wp_update_post(['ID' => $page->ID, 'post_content' => $content]);
}

function mkwvs_migrate_noindex_evenements(): void
{
    $page = get_page_by_path('evenements');
    if (!$page instanceof WP_Post) {
        return;
    }

    update_post_meta($page->ID, '_seopress_robots_index', 'yes');
}

function mkwvs_migrate_trash_default_post(): void
{
    $post = get_page_by_path('bonjour-tout-le-monde', OBJECT, 'post');
    if (!$post instanceof WP_Post || $post->post_status !== 'publish') {
        return;
    }

    if (!str_contains($post->post_content, 'Bienvenue sur WordPress')) {
        return;
    }

    wp_trash_post($post->ID);
}
