<?php

declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Migration one-shot : crée la page « Brunch sur une péniche à Lille », page cible
 * de la requête « brunch Lille » (la CI ne déploie que le code).
 * Réutilise les helpers d'import d'images de la page péniche.
 */

add_action('init', 'mkwvs_migrate_brunch_page', 11);

function mkwvs_migrate_brunch_page(): void
{
    if ((int) get_option('mkwvs_brunch_page_migrated', 0) >= 3) {
        return;
    }

    if (!function_exists('mkwvs_peniche_theme_image_id') || !function_exists('mkwvs_peniche_photo_id')) {
        return;
    }

    $hero_id = mkwvs_peniche_theme_image_id(
        'images/brunch-peniche-lille.jpg',
        "Brunch du dimanche sur la péniche du Bus Magique à Lille : tartine aux graines, salade de saison et energy bowl"
    );
    $photos = mkwvs_brunch_photos();

    // Ne rien persister tant qu'une image manque : le chargement suivant retentera.
    if (!$hero_id || !$photos) {
        return;
    }

    $page = get_page_by_path('brunch-lille');

    if ($page instanceof WP_Post) {
        if (!str_contains($page->post_content, '13h30')) {
            wp_update_post([
                'ID' => $page->ID,
                'post_content' => mkwvs_brunch_page_content($photos),
            ]);
        }

        update_option('mkwvs_brunch_page_migrated', 3);

        return;
    }

    $page_id = wp_insert_post([
        'post_type' => 'page',
        'post_status' => 'publish',
        'post_title' => 'Brunch sur une péniche à Lille',
        'post_name' => 'brunch-lille',
        'post_content' => mkwvs_brunch_page_content($photos),
    ]);

    if (is_wp_error($page_id) || !$page_id) {
        return;
    }

    update_post_meta($page_id, '_wp_page_template', 'templates/brunch-lille.php');

    set_post_thumbnail($page_id, $hero_id);

    $restauration = get_page_by_path('restauration');
    if ($restauration instanceof WP_Post && function_exists('get_field') && function_exists('update_field')) {
        $icon = get_field('page_head_hublot_icon', $restauration->ID, false);
        if ($icon) {
            update_field('page_head_hublot_icon', $icon, $page_id);
        }
        $color = get_field('page_head_hublot_color', $restauration->ID, false);
        if ($color) {
            update_field('page_head_hublot_color', $color, $page_id);
        }
    }

    update_post_meta($page_id, '_seopress_titles_title', 'Brunch à Lille sur une péniche, le dimanche | Le Bus Magique');
    update_post_meta(
        $page_id,
        '_seopress_titles_desc',
        "Brunch maison tous les dimanches de 11h à 15h sur une péniche à Lille, à l'entrée de la Citadelle : salé, sucré, jus bio. 23,50 €, sur réservation."
    );

    flush_rewrite_rules(false);

    update_option('mkwvs_brunch_page_migrated', 3);
}

function mkwvs_brunch_photos(): array
{
    $photos = [
        'photo_peniche' => [
            'id' => mkwvs_peniche_photo_id('peniche-exterieur.jpg', "La péniche du Bus Magique amarrée sur la Deûle, au pied des remparts de la Citadelle de Lille"),
            'alt' => "La péniche du Bus Magique amarrée sur la Deûle, à l'entrée de la Citadelle de Lille",
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

function mkwvs_brunch_page_url(): string
{
    $page = get_page_by_path('brunch-lille');

    return $page instanceof WP_Post ? (string) get_permalink($page) : home_url('/brunch-lille/');
}

function mkwvs_brunch_page_content(array $photos): string
{
    $content = <<<'HTML'
<!-- wp:html -->
<div class="peniche brunch">

  <p class="peniche__chapo">Tous les dimanches, la péniche du Bus Magique sert son brunch, amarrée sur la Deûle à l'entrée de la Citadelle de Lille. Une cuisine maison, bio et de saison, du salé, du sucré, et du thé et du café à volonté, <strong>à bord d'un bateau de 1954&nbsp;!</strong></p>

  <ul class="brunch__infos">
    <li class="brunch__info brunch__info--yellow">Tous les dimanches</li>
    <li class="brunch__info brunch__info--jungle-green">Arrivée entre 11h et 13h30</li>
    <li class="brunch__info brunch__info--tomato">Dès 23,50 €</li>
  </ul>

  <div class="brunch__menu">
    <img class="brunch__icon" src="/wp-content/uploads/2021/05/icon-brunch-brunch.svg" alt="" aria-hidden="true" width="80" height="40">
    <h2 class="peniche__title">Ce qu'il y a dans l'assiette</h2>
    <p class="brunch__lead">Le brunch magique, c'est du salé, du sucré et de quoi boire à volonté. La proposition salée change selon le jour et la saison.</p>
    <ul class="brunch__plates">
      <li class="brunch__plate brunch__plate--green">
        <span class="brunch__label">Pour commencer</span>
        <strong>L'energy bowl</strong>
        <p>Fromage blanc végétal aux graines de chia, fruits frais, granola et fruits secs.</p>
      </li>
      <li class="brunch__plate brunch__plate--tomato">
        <span class="brunch__label">Le salé</span>
        <strong>La proposition du jour</strong>
        <p>Tarte salée, frittata, crumble de légumes, velouté, petite salade… à découvrir sur place.</p>
      </li>
      <li class="brunch__plate brunch__plate--red">
        <span class="brunch__label">Le sucré</span>
        <strong>Une part de douceur</strong>
        <p>À choisir parmi deux douceurs sucrées.</p>
      </li>
      <li class="brunch__plate brunch__plate--jungle-green">
        <span class="brunch__label">À boire</span>
        <strong>Un jus de fruits bio</strong>
        <p>Pour accompagner le salé comme le sucré.</p>
      </li>
      <li class="brunch__plate brunch__plate--yellow">
        <span class="brunch__label">À volonté</span>
        <strong>Thé du jour et café</strong>
        <p>Resservez-vous autant que vous voulez.</p>
      </li>
    </ul>
  </div>

  <h2 class="peniche__title">Les formules et les prix</h2>
  <ul class="brunch__offers">
    <li class="brunch__offer">
      <span class="brunch__label">Le brunch magique</span>
      <strong class="brunch__price">23,50 €</strong>
      <p>La formule complète : bowl, salé du jour, douceur, jus bio, thé et café à volonté.</p>
    </li>
    <li class="brunch__offer brunch__offer--featured">
      <span class="brunch__label">Le brunch gourmand</span>
      <strong class="brunch__price">26,50 €</strong>
      <p>La formule complète, avec une boisson gourmande en plus (+&nbsp;3&nbsp;€).</p>
    </li>
    <li class="brunch__offer">
      <span class="brunch__label">Les p'tits moussaillons</span>
      <strong class="brunch__price">12 €</strong>
      <p>La formule des enfants, pour bruncher en famille.</p>
    </li>
  </ul>

  <div class="brunch__book">
    <h2>Réserver son brunch du dimanche</h2>
    <p>Le brunch est servi uniquement le dimanche, de 11h à 15h, et on vous conseille de réserver. Les réservations sont prises pour une arrivée entre 11h et 13h30&nbsp;: en arrivant à 13h30, on a encore le temps de bruncher tranquillement jusqu'à 15h. S'il n'y a plus de place en ligne, appelez-nous pendant nos horaires d'ouverture ou envoyez-nous un petit mail.</p>
    <a class="cta cta--tomato" href="https://uniiti.com/shop/le-bus-magique" target="_blank" rel="noopener" data-umami-event="reservation-resto" data-umami-event-source="brunch">Réserver mon brunch</a>
  </div>

  <div class="peniche__split">
    <div class="peniche__split-text">
      <h2>Un brunch sur l'eau, au cœur de Lille</h2>
      <p>Le Bus Magique est une péniche citerne de 1954 devenue un tiers-lieu associatif&nbsp;: bar, restaurant, concerts, ateliers et coworking. Le dimanche, on y brunche au calme, au bord de l'eau, à l'entrée de la Citadelle et à dix minutes à pied du Vieux-Lille.</p>
      <p>Certains dimanches, le brunch prend des airs de fête&nbsp;: brunch musical avec concert, brunch solidaire… Ces rendez-vous sont annoncés dans <a href="/programmation/" data-umami-event="brunch-lien" data-umami-event-cible="programmation">la programmation</a>.</p>
      <p class="peniche__split-cta"><a class="cta cta--jungle-green" href="/peniche-lille/" data-umami-event="brunch-lien" data-umami-event-cible="peniche">Découvrir la péniche</a></p>
    </div>
    <figure class="peniche__split-media">
      {{photo_peniche}}
    </figure>
  </div>

  <div class="peniche__access">
    <div class="peniche__access-text">
      <h2>Où bruncher : l'accès à la péniche</h2>
      <p>La péniche est amarrée au cœur de Lille, le long de la Deûle, juste à l'entrée de la Citadelle.</p>
      <ul>
        <li><strong>Adresse :</strong> péniche Le Bus Magique, avenue Cuvier, 59800 Lille, à l'entrée de la Citadelle</li>
        <li><strong>Métro :</strong> station République Beaux-Arts</li>
        <li><strong>Bus :</strong> arrêt Champ de Mars</li>
        <li><strong>V'Lille :</strong> station à moins de 5 minutes à pied</li>
        <li><strong>Voiture :</strong> parking du Champ de Mars, juste à côté de la péniche</li>
      </ul>
      <a class="cta cta--tomato" href="https://www.google.com/maps/dir/?api=1&destination=Le+Bus+Magique%2C+avenue+Cuvier%2C+59800+Lille" target="_blank" rel="noopener" data-umami-event="brunch-itineraire">Calculer mon itinéraire</a>
    </div>
    <a class="peniche__access-map" href="https://www.google.com/maps/search/?api=1&query=Le+Bus+Magique%2C+avenue+Cuvier%2C+59800+Lille" target="_blank" rel="noopener" data-umami-event="brunch-carte" aria-label="Ouvrir le plan d'accès dans Google Maps">
      {{img_map}}
    </a>
  </div>

  <h2 class="peniche__title">Questions fréquentes</h2>
  <div class="peniche__faq">
    <details open>
      <summary>Quand a lieu le brunch sur la péniche ?</summary>
      <p>Tous les dimanches, de 11h à 15h, avec une arrivée entre 11h et 13h30&nbsp;: en arrivant à 13h30, on a encore le temps de bruncher jusqu'à 15h. Le brunch n'est servi que le dimanche&nbsp;; les jeudi et vendredi midi, la péniche propose des plats du jour.</p>
    </details>
    <details>
      <summary>Combien coûte le brunch ?</summary>
      <p>Le brunch magique simple coûte 23,50 €. La version gourmande ajoute une boisson gourmande pour 3 € de plus, et la formule enfant, celle des p'tits moussaillons, est à 12 €.</p>
    </details>
    <details>
      <summary>Faut-il réserver pour bruncher ?</summary>
      <p>C'est conseillé. Les réservations sont prises pour une arrivée entre 11h et 13h30, en ligne&nbsp;; s'il n'y a plus de place, appelez-nous pendant nos horaires d'ouverture ou envoyez-nous un mail.</p>
    </details>
    <details>
      <summary>Faut-il adhérer à l'association pour bruncher à bord ?</summary>
      <p>Oui. Le Bus Magique est une association, l'adhésion est donc nécessaire. Son montant est libre, c'est vous qui décidez, et elle se prend directement à bord.</p>
    </details>
    <details>
      <summary>Où se trouve la péniche ?</summary>
      <p>Avenue Cuvier, 59800 Lille, à l'entrée de la Citadelle, le long de la Deûle. Métro République Beaux-Arts, arrêt de bus Champ de Mars, et le parking du Champ de Mars juste à côté.</p>
    </details>
  </div>

  <p class="peniche__more">Envie de revenir en semaine&nbsp;? <a href="/restauration/" data-umami-event="brunch-lien" data-umami-event-cible="restauration">Voir toute la carte du bar et du restaurant</a>.</p>

</div>
<!-- /wp:html -->
HTML;

    foreach ($photos as $key => $photo) {
        $tag = wp_get_attachment_image($photo['id'], 'large', false, [
            'alt' => $photo['alt'],
            'loading' => 'lazy',
            'decoding' => 'async',
            'class' => 'wp-image-' . (int) $photo['id'],
        ]);
        $content = str_replace('{{' . $key . '}}', $tag, $content);
    }

    return $content;
}
