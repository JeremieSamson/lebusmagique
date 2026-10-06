<?php

declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Migration one-shot : crée la page « Notre péniche à Lille », page cible de la
 * requête « péniche Lille » (la CI ne déploie que le code).
 */

const MKWVS_PENICHE_PAGE_SLUG = 'peniche-lille';
const MKWVS_PENICHE_PAGE_TEMPLATE = 'templates/peniche-lille.php';

add_action('init', 'mkwvs_migrate_peniche_page');

function mkwvs_migrate_peniche_page(): void
{
    if ((int) get_option('mkwvs_peniche_page_migrated', 0) >= 3) {
        return;
    }

    $page = get_page_by_path(MKWVS_PENICHE_PAGE_SLUG);

    if ($page instanceof WP_Post) {
        if (!mkwvs_peniche_page_is_outdated($page->post_content)) {
            update_option('mkwvs_peniche_page_migrated', 3);

            return;
        }

        $hero_id = mkwvs_peniche_hero_id();
        $photos = mkwvs_peniche_photos();

        // Un import d'image peut échouer (requête interrompue, écriture refusée) :
        // mieux vaut retenter au prochain chargement que figer une page sans visuel.
        if (!$hero_id || !$photos) {
            return;
        }

        wp_update_post([
            'ID' => $page->ID,
            'post_content' => mkwvs_peniche_page_content($photos),
        ]);

        set_post_thumbnail($page->ID, $hero_id);

        update_option('mkwvs_peniche_page_migrated', 3);

        return;
    }

    $hero_id = mkwvs_peniche_hero_id();

    $photos = mkwvs_peniche_photos();

    if (!$hero_id || !$photos) {
        return;
    }

    $page_id = wp_insert_post([
        'post_type' => 'page',
        'post_status' => 'publish',
        'post_title' => 'Notre péniche à Lille',
        'post_name' => MKWVS_PENICHE_PAGE_SLUG,
        'post_content' => mkwvs_peniche_page_content($photos),
    ]);

    if (is_wp_error($page_id) || !$page_id) {
        return;
    }

    update_post_meta($page_id, '_wp_page_template', MKWVS_PENICHE_PAGE_TEMPLATE);

    set_post_thumbnail($page_id, $hero_id);

    $icon_id = mkwvs_peniche_hublot_icon_id();
    if ($icon_id && function_exists('update_field')) {
        update_field('page_head_hublot_icon', $icon_id, $page_id);
    }

    update_post_meta($page_id, '_seopress_titles_title', 'Péniche à Lille : bar, restaurant, concerts | Le Bus Magique');
    update_post_meta(
        $page_id,
        '_seopress_titles_desc',
        "Bar, restaurant, concerts et coworking à bord d'une péniche de 1954 amarrée à l'entrée de la Citadelle de Lille. Accès, horaires et location."
    );

    flush_rewrite_rules(false);

    update_option('mkwvs_peniche_page_migrated', 3);
}

function mkwvs_peniche_page_is_outdated(string $content): bool
{
    // Une page servie sans aucune image vient d'un import qui a échoué.
    if (!str_contains($content, '<img')) {
        return true;
    }

    foreach (['une chambre pour passer la nuit à bord', "à l'avant du bateau", 'Rihour', 'studio.jpg'] as $marker) {
        if (str_contains($content, $marker)) {
            return true;
        }
    }

    return false;
}

function mkwvs_peniche_hero_id(): int
{
    return mkwvs_peniche_photo_id(
        'peniche-exterieur.jpg',
        "La péniche du Bus Magique amarrée sur la Deûle, au pied des remparts de la Citadelle de Lille"
    );
}

function mkwvs_peniche_photos(): array
{
    $photos = [
        'photo_timonerie' => [
            'id' => mkwvs_peniche_photo_id('timonerie.jpg', "Timonerie de la péniche avec sa barre à roue d'origine et sa vue sur le canal"),
            'alt' => "Timonerie de la péniche avec sa barre à roue d'origine et sa vue sur le canal",
        ],
        'photo_studio' => [
            'id' => mkwvs_peniche_photo_id('studio-vue-ensemble.jpg', "Vue d'ensemble du studio : kitchenette, coin bar et espace nuit"),
            'alt' => "Vue d'ensemble du studio du Marinier : kitchenette, coin bar et espace nuit",
        ],
        'img_resto' => [
            'id' => mkwvs_peniche_theme_image_id('images/peniche-activite-restauration.jpg', "Le bar et le restaurant de la péniche du Bus Magique à Lille"),
            'alt' => "Le bar et le restaurant de la péniche du Bus Magique à Lille",
        ],
        'img_events' => [
            'id' => mkwvs_peniche_theme_image_id('images/peniche-activite-programmation.jpg', "Concerts et soirées à bord de la péniche à Lille"),
            'alt' => "Concerts et soirées à bord de la péniche à Lille",
        ],
        'img_cowork' => [
            'id' => mkwvs_peniche_theme_image_id('images/peniche-activite-coworking.jpg', "Espace de coworking à bord de la péniche à Lille"),
            'alt' => "Espace de coworking à bord de la péniche à Lille",
        ],
        'img_privatisation' => [
            'id' => mkwvs_peniche_theme_image_id('images/peniche-privatisation.jpg', "Location de la péniche pour un événement privé à Lille"),
            'alt' => "Location de la péniche pour un événement privé à Lille",
        ],
        'img_map' => [
            'id' => mkwvs_peniche_theme_image_id('images/peniche-plan-acces.jpg', "Plan d'accès à la péniche Le Bus Magique, avenue Cuvier à Lille"),
            'alt' => "Plan d'accès à la péniche Le Bus Magique, avenue Cuvier à Lille",
        ],
    ];

    foreach ($photos as $photo) {
        if (!$photo['id']) {
            return [];
        }
    }

    return $photos;
}

function mkwvs_peniche_page_url(): string
{
    $page = get_page_by_path(MKWVS_PENICHE_PAGE_SLUG);

    return $page instanceof WP_Post ? (string) get_permalink($page) : home_url('/' . MKWVS_PENICHE_PAGE_SLUG . '/');
}

/**
 * Réutilise les photos déjà importées par la page gîte, sinon les importe.
 */
function mkwvs_peniche_photo_id(string $file, string $alt): int
{
    $existing = get_posts([
        'post_type' => 'attachment',
        'post_status' => 'inherit',
        'posts_per_page' => 1,
        'fields' => 'ids',
        'meta_key' => '_mkwvs_hebergement_photo',
        'meta_value' => $file,
    ]);

    if (!empty($existing)) {
        return (int) $existing[0];
    }

    if (!function_exists('mkwvs_hebergement_import_photo')) {
        return 0;
    }

    return mkwvs_hebergement_import_photo($file, $alt);
}

/**
 * Importe une image livrée avec le thème, ou renvoie celle déjà importée.
 */
function mkwvs_peniche_theme_image_id(string $relative, string $alt): int
{
    $file = sanitize_file_name(basename($relative));

    $existing = get_posts([
        'post_type' => 'attachment',
        'post_status' => 'inherit',
        'posts_per_page' => 1,
        'fields' => 'ids',
        'meta_key' => '_mkwvs_peniche_image',
        'meta_value' => $file,
    ]);

    if (!empty($existing)) {
        return (int) $existing[0];
    }

    $source = get_template_directory() . '/' . ltrim($relative, '/');
    if (!file_exists($source)) {
        return 0;
    }

    require_once ABSPATH . 'wp-admin/includes/file.php';
    require_once ABSPATH . 'wp-admin/includes/media.php';
    require_once ABSPATH . 'wp-admin/includes/image.php';

    $upload = wp_upload_bits($file, null, (string) file_get_contents($source));
    if (!empty($upload['error'])) {
        return 0;
    }

    $type = wp_check_filetype($upload['file']);

    $attachment_id = wp_insert_attachment([
        'post_mime_type' => $type['type'] ?: 'image/jpeg',
        'post_title' => sanitize_file_name(pathinfo($file, PATHINFO_FILENAME)),
        'post_status' => 'inherit',
    ], $upload['file']);

    if (is_wp_error($attachment_id) || !$attachment_id) {
        return 0;
    }

    wp_update_attachment_metadata(
        $attachment_id,
        wp_generate_attachment_metadata($attachment_id, $upload['file'])
    );
    update_post_meta($attachment_id, '_wp_attachment_image_alt', $alt);
    update_post_meta($attachment_id, '_mkwvs_peniche_image', $file);

    return (int) $attachment_id;
}

/**
 * Hublot générique du thème, à défaut de quoi page-head.php pose son icône de repli.
 */
function mkwvs_peniche_hublot_icon_id(): int
{
    $found = get_posts([
        'post_type' => 'attachment',
        'post_status' => 'inherit',
        'posts_per_page' => 1,
        'fields' => 'ids',
        'meta_query' => [
            [
                'key' => '_wp_attached_file',
                'value' => 'hublot-icon.svg',
                'compare' => 'LIKE',
            ],
        ],
    ]);

    return empty($found) ? 0 : (int) $found[0];
}

function mkwvs_peniche_page_content(array $photos): string
{
    $content = <<<'HTML'
<!-- wp:html -->
<div class="peniche">

  <p class="peniche__chapo">Le Bus Magique est une péniche amarrée avenue Cuvier, à l'entrée de la Citadelle de Lille, le long de la Deûle. Un bateau de 1954 devenu un tiers-lieu associatif : un bar et un restaurant flottants, des concerts et des ateliers, un espace de coworking, et même <strong>un studio pour passer un séjour insolite à bord&nbsp;!</strong></p>

  <div class="peniche__split">
    <div class="peniche__split-text">
      <h2>Une péniche citerne de 1954</h2>
      <p>Le bateau s'appelait l'Île. C'est une péniche citerne construite en 1954, une vieille dame que l'association a choisie en février 2019, après avoir obtenu l'été précédent l'autorisation de stationner devant le Champ de Mars.</p>
      <p>De février 2019 à octobre 2020, des chantiers participatifs l'ont transformée en lieu de vie. C'est aujourd'hui la péniche associative de Lille, ouverte à toutes et à tous. <a href="/notre-histoire/" data-umami-event="peniche-activite" data-umami-event-cible="histoire">Lire l'histoire du bateau</a>.</p>
    </div>
    <figure class="peniche__split-media">
      {{photo_timonerie}}
    </figure>
  </div>

  <div class="peniche__split">
    <div class="peniche__split-text">
      <h2>Dormir sur la péniche</h2>
      <p>À l'arrière du bateau, le logement du Marinier, un <a href="/dormir-sur-une-peniche-a-lille/" data-umami-event="hebergement-entree" data-umami-event-source="peniche-texte">logement insolite à Lille</a>, se loue à la nuit pour deux à trois personnes, avec sa terrasse privée sur le pont et sa vue sur le canal. C'est un hébergement indépendant du bar et du restaurant.</p>
      <p class="peniche__split-cta"><a class="cta cta--tomato" href="/dormir-sur-une-peniche-a-lille/" data-umami-event="hebergement-entree" data-umami-event-source="peniche">Voir les disponibilités</a></p>
    </div>
    <figure class="peniche__split-media">
      {{photo_studio}}
    </figure>
  </div>

  <h2 class="peniche__title">Ce que l'on fait à bord</h2>
  <ul class="peniche__usages">
    <li class="peniche__usage">
      <a class="peniche__usage-media" href="/restauration/" data-umami-event="peniche-activite" data-umami-event-cible="restauration">
        {{img_resto}}
      </a>
      <div class="peniche__usage-body">
        <strong>Manger et boire un verre</strong>
        <p>Le bar et le restaurant de la péniche servent des plats du jour les jeudi et vendredi midi, et un <a href="/brunch-lille/" data-umami-event="brunch-entree" data-umami-event-source="peniche-lille">brunch le dimanche</a>. Bières locales, vins et boissons chaudes, cuisine maison, bio et de saison.</p>
        <a class="cta cta--jungle-green" href="/restauration/" data-umami-event="peniche-activite" data-umami-event-cible="restauration">Voir la carte</a>
      </div>
    </li>
    <li class="peniche__usage">
      <a class="peniche__usage-media" href="/programmation/" data-umami-event="peniche-activite" data-umami-event-cible="programmation">
        {{img_events}}
      </a>
      <div class="peniche__usage-body">
        <strong>Sortir et assister aux événements</strong>
        <p>Concerts, scènes ouvertes, jam sessions, blind tests, drag bingo, café philo, café des langues et ateliers créatifs.</p>
        <a class="cta cta--red" href="/programmation/" data-umami-event="peniche-activite" data-umami-event-cible="programmation">Voir la programmation</a>
      </div>
    </li>
    <li class="peniche__usage">
      <a class="peniche__usage-media" href="/coworking/" data-umami-event="peniche-activite" data-umami-event-cible="coworking">
        {{img_cowork}}
      </a>
      <div class="peniche__usage-body">
        <strong>Travailler au bord de l'eau</strong>
        <p>Espace de coworking ouvert les jeudi et vendredi, de 9h à 12h et de 14h à 17h. Wifi haut débit, réseau Ethernet, café et pâtisseries.</p>
        <a class="cta cta--yellow" href="/coworking/" data-umami-event="peniche-activite" data-umami-event-cible="coworking">Découvrir le coworking</a>
      </div>
    </li>
    <li class="peniche__usage">
      <a class="peniche__usage-media" href="/location/" data-umami-event="peniche-activite" data-umami-event-cible="location">
        {{img_privatisation}}
      </a>
      <div class="peniche__usage-body">
        <strong>Privatiser le bateau</strong>
        <p>Anniversaire, séminaire, soirée d'entreprise ou mariage : la location de la péniche accueille 60 personnes assises en salle, 100 en cocktail, plus une terrasse sur le pont.</p>
        <a class="cta cta--green" href="/location/" data-umami-event="peniche-activite" data-umami-event-cible="location">Demander un devis</a>
      </div>
    </li>
  </ul>

  <div class="peniche__access">
    <div class="peniche__access-text">
      <h2>Où est amarrée la péniche</h2>
      <p>La péniche est amarrée au cœur de Lille, le long de la Deûle, juste à l'entrée de la Citadelle.</p>
      <ul>
        <li><strong>Adresse :</strong> péniche Le Bus Magique, avenue Cuvier, 59800 Lille, à l'entrée de la Citadelle</li>
        <li><strong>Métro :</strong> station République Beaux-Arts</li>
        <li><strong>Bus :</strong> arrêt Champ de Mars</li>
        <li><strong>V'Lille :</strong> station à moins de 5 minutes à pied</li>
        <li><strong>Voiture :</strong> parking du Champ de Mars, juste à côté de la péniche</li>
      </ul>
      <a class="cta cta--tomato" href="https://www.google.com/maps/dir/?api=1&destination=Le+Bus+Magique%2C+avenue+Cuvier%2C+59800+Lille" target="_blank" rel="noopener" data-umami-event="peniche-itineraire">Calculer mon itinéraire</a>
    </div>
    <a class="peniche__access-map" href="https://www.google.com/maps/search/?api=1&query=Le+Bus+Magique%2C+avenue+Cuvier%2C+59800+Lille" target="_blank" rel="noopener" data-umami-event="peniche-carte" aria-label="Ouvrir le plan d'accès dans Google Maps">
      {{img_map}}
    </a>
  </div>

  <div class="peniche__assoc">
    <h2>Une péniche portée par une association</h2>
    <p>Le Bus Magique est une association loi 1901 née au printemps 2018. Le lieu vit grâce à ses bénévoles et à ses adhérents, autour de quelques valeurs simples : le bien-être, le lien social, le respect de l'environnement et le soutien à l'économie locale.</p>
    <p><a class="cta cta--red" href="/monter-a-bord/" data-umami-event="peniche-activite" data-umami-event-cible="adhesion">Adhérer et monter à bord</a></p>
  </div>

  <h2 class="peniche__title">Questions fréquentes</h2>
  <div class="peniche__faq">
    <details open>
      <summary>Où se trouve la péniche Le Bus Magique à Lille ?</summary>
      <p>La péniche est amarrée avenue Cuvier, 59800 Lille, à l'entrée de la Citadelle, le long de la Deûle. On y accède par le métro République Beaux-Arts ou l'arrêt de bus Champ de Mars, et le parking du Champ de Mars se trouve juste à côté.</p>
    </details>
    <details>
      <summary>Peut-on manger et boire un verre sur la péniche ?</summary>
      <p>Oui. La péniche sert des plats du jour les jeudi et vendredi midi et un <a href="/brunch-lille/" data-umami-event="brunch-entree" data-umami-event-source="peniche-lille">brunch le dimanche</a>, avec une cuisine maison, bio et de saison. Le bar propose des bières locales, des vins et des boissons chaudes, à toute heure du jeudi au dimanche (et même le mercredi à la belle saison)&nbsp;!</p>
    </details>
    <details>
      <summary>Faut-il adhérer à l'association pour monter à bord ?</summary>
      <p>Oui. Le Bus Magique est une association, l'adhésion est donc nécessaire. Son montant est libre, c'est vous qui décidez, et elle se prend directement à bord auprès d'un bénévole ou d'un serveur.</p>
    </details>
    <details>
      <summary>Peut-on privatiser la péniche pour un événement ?</summary>
      <p>Oui, pour des événements privés comme un anniversaire, un séminaire, une soirée d'entreprise ou un mariage. La salle accueille 60 personnes assises et 100 en cocktail, la terrasse 40 assises et 60 en cocktail. Les disponibilités se consultent directement sur <a href="/location/" data-umami-event="peniche-activite" data-umami-event-cible="location">notre page de privatisation</a>.</p>
    </details>
    <details>
      <summary>Peut-on dormir sur la péniche ?</summary>
      <p>Oui. Le logement du Marinier, à l'arrière du bateau, se loue à la nuit pour deux à trois personnes, avec sa terrasse privée et sa salle de bain.</p>
    </details>
  </div>

</div>
<!-- /wp:html -->
HTML;

    foreach ($photos as $key => $photo) {
        $tag = '';
        if (!empty($photo['id'])) {
            $tag = wp_get_attachment_image($photo['id'], 'large', false, [
                'alt' => $photo['alt'],
                'loading' => 'lazy',
                'decoding' => 'async',
                'class' => 'wp-image-' . (int) $photo['id'],
            ]);
        }
        $content = str_replace('{{' . $key . '}}', $tag, $content);
    }

    return $content;
}
