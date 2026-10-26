<?php
/**
 * Template Name: Accueil
 */
?>
<?php get_header(); ?>

    <style>
        .zoomable {
            cursor: zoom-in;
            transition: transform 0.3s ease;
        }

        .zoomable.zoomed {
            cursor: zoom-out;
            transform: scale(2);
        }

        .bloc-media-text--brunch .bloc-media-text__media {
            flex: 0 0 48%;
            display: flex;
            justify-content: center;
        }

        .bloc-media-text--brunch .bloc-media-text__media img {
            width: min(85%, 380px);
            height: auto;
            aspect-ratio: 4 / 5;
            object-fit: cover;
            border: 16px solid #ffd202;
            border-radius: 50%;
        }

        @media screen and (min-width: 1024px) {
            .bloc-media-text--brunch .bloc-media-text__media img {
                width: 340px;
            }
        }

        .bloc-media-text--oval .bloc-media-text__media {
            flex: 0 0 48%;
            display: flex;
            justify-content: center;
        }

        .bloc-media-text--oval .bloc-media-text__media img {
            width: min(85%, 380px);
            height: auto;
            aspect-ratio: 4 / 5;
            object-fit: cover;
            border: 16px solid #9bb909;
            border-radius: 50%;
        }

        .bloc-media-text--tomato .bloc-media-text__media img {
            border-color: #ec6620;
        }

        .section-content--peniche {
            padding-top: 20px;
        }

        @media screen and (min-width: 1024px) {
            .bloc-media-text--oval .bloc-media-text__media img {
                width: 340px;
            }
        }

        .bloc-fullwidth a {
            display: block;
        }

        .home-access {
            max-width: 640px;
            margin: 0 auto;
            padding: 60px 20px;
            text-align: center;
        }

        .home-access ul {
            list-style: none;
            margin: 0 0 30px;
            padding: 0;
        }

        .home-access li {
            margin-bottom: 6px;
        }

        .home-access .cta {
            display: inline-flex;
            margin: 0;
            background-color: transparent;
        }

        .home-faq {
            max-width: 760px;
            margin: 0 auto;
            padding: 20px 20px 80px;
        }

        .home-faq details {
            border-bottom: 2px dashed #000;
        }

        .home-faq summary {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
            padding: 18px 0;
            font-weight: 600;
            cursor: pointer;
            list-style: none;
        }

        .home-faq summary::-webkit-details-marker {
            display: none;
        }

        .home-faq summary::after {
            content: '+';
            flex: none;
            font-size: 24px;
            line-height: 1;
        }

        .home-faq details[open] summary::after {
            content: '\2212';
        }

        .home-faq p {
            margin: 0 0 18px;
        }

        .section-content--beige {
            position: relative;
        }

        body.home .section-content--beige:before {
            display: none;
        }

        .home-thread {
            display: none;
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            overflow: visible;
            pointer-events: none;
            z-index: 0;
        }

        .home-thread path {
            fill: none;
            stroke: #161615;
            stroke-linecap: round;
            stroke-linejoin: round;
        }

        @media screen and (min-width: 1024px) {
            .home-thread {
                display: block;
            }
        }

        .section-content--beige > *:not(.home-thread) {
            position: relative;
            z-index: 1;
        }

        .home-access li,
        .home-faq summary {
            font-size: 12px;
        }

        .bloc-media-text--oval p a,
        .home-faq p a {
            color: inherit;
            text-decoration: underline;
        }

        @media screen and (min-width: 550px) {
            .home-access li,
            .home-faq summary {
                font-size: 16px;
            }
        }
    </style>

<?php $hp = get_page_by_path('accueil'); ?>

<?php if (have_posts()) : ?>

    <?php while (have_posts()) : the_post(); ?>

        <section class="section-landing-standard">

            <div class="section-landing-top">
                <div class="swiper-container top-image">
                    <?php if (have_rows('page_head_gallery')) : ?>
                        <div class="swiper-wrapper">
                            <?php $image_count = 0; ?>
                            <?php while (have_rows('page_head_gallery')) : the_row(); ?>
                                <?php $image = get_sub_field('page_head_gallery_item_image'); ?>
                                <?php $image_count++; ?>
                                <?php $description = get_sub_field('page_head_gallery_item_descriptif'); ?>
                                <?php
                                $slide_attr = ['class' => 'swiper-slide', 'sizes' => '100vw'];
                                if (!empty($description)) {
                                    $slide_attr['alt'] = $description;
                                }
                                if ($image_count === 1) {
                                    $slide_attr['loading']       = 'eager';
                                    $slide_attr['fetchpriority'] = 'high';
                                }
                                mkwvs_the_image($image, 'full', $slide_attr, 'La péniche Le Bus Magique à Lille');
                                ?>
                            <?php endwhile; ?>

                        </div>


                    <?php endif; ?>
                </div>

                <div class="hublot<?php if ($image_count > 1) echo " has-slider"; ?>">
                    <?php $color_hublot = get_field('page_head_hublot_color'); ?>
                    <img src="<?php echo get_stylesheet_directory_uri() . '/images/' . $color_hublot . '-hublot.svg'; ?>" alt="" aria-hidden="true">
                    <?php $icon_hublot = get_field('page_head_hublot_icon'); ?>
                    <h1 class="big-title"><img class="icon" src="<?php echo $icon_hublot['url']; ?>" alt="" aria-hidden="true">Bienvenu·e·s<br> à
                        bord du <br>bus magique !</h1>
                    <?php if ($image_count > 1): ?>
                        <div class="o-slider__navigation">
                            <button class="o-slider__prev"><?php locate_template('images/fleche.svg', true, false); ?></button>
                            <button class="o-slider__next"><?php locate_template('images/fleche.svg', true, false); ?></button>
                        </div>
                    <?php endif; ?>
                </div>

                <div class="text-yellow-background top">
                    <?php $icon_scroll = get_field('page_head_icon_scroll'); ?>
                    <?php $accroche_scroll = get_field('page_head_accroche_scroll'); ?>
                    <img class="icon-top-landing" src="<?php echo $icon_scroll['url']; ?>" alt="" aria-hidden="true">
                    <p class="to_show"><?php echo $accroche_scroll; ?></p>
                </div>
            </div>

            <div class="text-yellow-background bottom">
                <?php $subtitle = get_field('page_head_subtitle'); ?>
                <?php if (!empty($subtitle)) : ?>
                    <h2><?php echo $subtitle; ?></h2>
                <?php endif; ?>
                <?php $description = get_field('page_head_description'); ?>
                <?php if (!empty($description)) : ?>
                    <?php echo apply_filters('the_content', $description); ?>
                <?php endif; ?>
            </div>
        </section>

        <section class="section-content section-content--prog">
            <h2>Prog' du mois</h2>
            <?php
            $image = get_field('programmation_du_mois', $hp->ID);
            if (isset($image) && is_array($image)) : ?>
                <?php mkwvs_the_image($image, 'full', [
                    'class'   => 'zoomable',
                    'sizes'   => '(max-width: 900px) 100vw, 900px',
                    'onclick' => "this.classList.toggle('zoomed')",
                ], 'Programmation du mois du Bus Magique'); ?>
            <?php elseif (have_rows('page_home_programmation_list')) : ?>
                <div class="events">
                    <?php echo do_shortcode('[facebook_events col="2" posts_per_page="4"]'); ?>
                </div>
            <?php endif; ?>
        </section>

        <section class="section-bggreen mt75">
            <?php if ($image) : ?>
                <a class="cta" href="<?php echo get_permalink(get_page_by_path("programmation")); ?>" data-umami-event="programmation-entree" data-umami-event-source="accueil">Voir la
                    programmation en détails !</a>
            <?php elseif (have_rows('page_home_programmation_list')) : ?>
                <a class="cta" href="<?php echo get_permalink(get_page_by_path("programmation")); ?>" data-umami-event="programmation-entree" data-umami-event-source="accueil">Voir la
                    programmation</a>
            <?php endif; ?>
        </section>

        <?php $brunch = get_page_by_path('brunch-lille'); ?>
        <?php $brunch_image = function_exists('mkwvs_peniche_theme_image_id') ? mkwvs_peniche_theme_image_id('images/brunch-assiette-complete.jpg', 'Brunch du dimanche sur la péniche du Bus Magique à Lille') : 0; ?>
        <?php $has_brunch = $brunch instanceof WP_Post && $brunch_image; ?>
        <?php $timonerie_id = mkwvs_peniche_photo_id('timonerie.jpg', "Timonerie de la péniche avec sa barre à roue d'origine et sa vue sur le canal"); ?>
        <?php $studio_id = mkwvs_peniche_photo_id('studio-vue-ensemble.jpg', "Vue d'ensemble du studio du Marinier : kitchenette, coin bar et espace nuit"); ?>
        <section class="section-content section-content--beige">

            <svg class="home-thread" aria-hidden="true" focusable="false"><path d=""/></svg>

            <h2>Que faire à bord Capitaine ?</h2>

            <?php $image = get_field('page_home_encart_1_image', $hp->ID); ?>
            <?php $titre = get_field('page_home_encart_1_titre', $hp->ID); ?>
            <?php $description = get_field('page_home_encart_1_description', $hp->ID); ?>
            <?php $libelle = get_field('page_home_encart_1_libelle', $hp->ID); ?>
            <?php $link = get_field('page_home_encart_1_link', $hp->ID); ?>
            <div class="bloc-media-text">
                <div class="bloc-media-text__media">
                    <?php mkwvs_the_image($image, 'large', ['sizes' => '(max-width: 900px) 100vw, 600px'], (string) $titre); ?>
                </div>
                <div class="bloc-media-text__content">
                    <h3><?php echo $titre; ?></h3>
                    <?php echo apply_filters('the_content', $description) ?>
                    <a href="<?php echo $link; ?>" class="cta cta--jungle-green" data-umami-event="restauration-entree" data-umami-event-source="accueil"><?php echo $libelle; ?></a>
                </div>
            </div>

            <?php if ($has_brunch) : ?>
                <div class="bloc-media-text bloc-media-text--right bloc-media-text--brunch">
                    <div class="bloc-media-text__media">
                        <?php mkwvs_the_image($brunch_image, 'large', ['sizes' => '(max-width: 900px) 80vw, 420px'], 'Brunch du dimanche sur la péniche du Bus Magique à Lille'); ?>
                    </div>
                    <div class="bloc-media-text__content">
                        <h3>Brunch du dimanche</h3>
                        <p>Tous les dimanches de 11h à 15h, un brunch maison, bio et de saison sur la péniche : energy bowl, plat du jour, pâtisserie, jus bio, thé et café à volonté.</p>
                        <a href="<?php echo esc_url(get_permalink($brunch)); ?>" class="cta cta--yellow" data-umami-event="brunch-entree" data-umami-event-source="accueil">Je viens bruncher !</a>
                    </div>
                </div>
            <?php endif; ?>

            <?php $image = get_field('page_home_encart_2_image', $hp->ID); ?>
            <?php $titre = get_field('page_home_encart_2_titre', $hp->ID); ?>
            <?php $description = get_field('page_home_encart_2_description', $hp->ID); ?>
            <?php $libelle = get_field('page_home_encart_2_libelle', $hp->ID); ?>
            <?php $link = get_field('page_home_encart_2_link', $hp->ID); ?>
            <div class="bloc-media-text">
                <div class="bloc-media-text__media">
                    <?php mkwvs_the_image($image, 'large', ['sizes' => '(max-width: 900px) 100vw, 600px'], (string) $titre); ?>
                </div>
                <div class="bloc-media-text__content">
                    <h3><?php echo $titre; ?></h3>
                    <?php echo apply_filters('the_content', $description) ?>
                    <a href="<?php echo $link; ?>" class="cta cta--red" data-umami-event="coworking-entree" data-umami-event-source="accueil"><?php echo $libelle; ?></a>
                </div>
            </div>

            <?php $image = get_field('page_home_encart_3_image', $hp->ID); ?>
            <?php $titre = get_field('page_home_encart_3_titre', $hp->ID); ?>
            <?php $description = get_field('page_home_encart_3_description', $hp->ID); ?>
            <?php $libelle = get_field('page_home_encart_3_libelle', $hp->ID); ?>
            <?php $link = get_field('page_home_encart_3_link', $hp->ID); ?>
            <div class="bloc-media-text bloc-media-text--right">
                <div class="bloc-media-text__media">
                    <?php mkwvs_the_image($image, 'large', ['sizes' => '(max-width: 900px) 100vw, 600px'], (string) $titre); ?>
                </div>
                <div class="bloc-media-text__content">
                    <h3><?php echo $titre; ?></h3>
                    <?php echo apply_filters('the_content', $description) ?>
                    <a href="<?php echo $link; ?>" class="cta cta--green" data-umami-event="location-entree" data-umami-event-source="accueil"><?php echo $libelle; ?></a>
                </div>
            </div>

            <?php if ($studio_id) : ?>
                <div class="bloc-media-text bloc-media-text--oval bloc-media-text--tomato">
                    <div class="bloc-media-text__media">
                        <?php mkwvs_the_image($studio_id, 'large', ['sizes' => '(max-width: 900px) 80vw, 420px'], "Vue d'ensemble du studio du Marinier : kitchenette, coin bar et espace nuit"); ?>
                    </div>
                    <div class="bloc-media-text__content">
                        <h3>Dormir à bord</h3>
                        <p>À l'arrière du bateau, le logement du Marinier, un <a href="/dormir-sur-une-peniche-a-lille/" data-umami-event="hebergement-entree" data-umami-event-source="accueil-texte">logement insolite à Lille</a>, se loue à la nuit pour deux à trois personnes, avec sa terrasse privée sur le pont et sa vue sur le canal. C'est un hébergement indépendant du bar et du restaurant.</p>
                        <a href="/dormir-sur-une-peniche-a-lille/" class="cta cta--tomato" data-umami-event="hebergement-entree" data-umami-event-source="accueil">Je regarde les dates</a>
                    </div>
                </div>
            <?php endif; ?>

            <div class="bloc-fullwidth">
                <a href="https://www.google.com/maps/search/?api=1&amp;query=Le+Bus+Magique%2C+avenue+Cuvier%2C+59800+Lille" target="_blank" rel="noopener" data-umami-event="peniche-carte" data-umami-event-source="accueil" aria-label="Ouvrir le plan d'accès dans Google Maps">
                    <img src="<?php echo get_stylesheet_directory_uri() . '/images/hp-map@2x.jpg'; ?>" alt="Plan d'accès à la péniche Le Bus Magique, avenue Cuvier à Lille" loading="lazy" decoding="async">
                </a>
            </div>

        </section>

        <script>
            (function () {
                var section = document.querySelector('.section-content--beige');
                var prog = document.querySelector('.section-content--prog');
                var svg = section && section.querySelector('.home-thread');
                if (!svg || !prog) {
                    return;
                }

                // Tracé de images/zigzag.svg, le fond de la section programmation qui
                // déborde jusqu'ici. Au-delà du bandeau vert, on le redessine pour
                // l'arrêter derrière la première photo et enchaîner sur le fil.
                var ZIGZAG = {
                    width: 2173.689,
                    height: 2244.023,
                    d: 'M-1525.246-1018.092s508.292-57.274 675.206 297.847 367.21 235.9 528.56 273.949c76.066 17.937 106.449 102.629 139.361 158.819 52.742 90.048 124.638 161.183 234.357 192.246 80.683 22.843 162.516 11.278 236.992 53.237 63.955 36.031 117.559 82.66 148.587 146.136 62.853 128.584 25.172 298.7-73.06 404.976-142.942 154.644-394.3 131.7-578.255 69.055-65.572-22.331-222.9-84.276-290.6-98.541-124.307-26.192-161.039 7.881-289.4 32.638-145.856 28.131-300.278 100.189-353.561 235.772-44.864 106.965-20.654 216.788 48.052 278.915 114.378 76.912 304.322-57.528 426.795-100.611 144.9-12.677 123.55 2.347 290.465 37.859',
                    rotate: [5.13, -12382.947, 19127.147]
                };

                var ns = 'http://www.w3.org/2000/svg';
                var path = svg.querySelector('path');
                var zigzag = document.createElementNS(ns, 'path');
                zigzag.setAttribute('d', ZIGZAG.d);
                zigzag.setAttribute('transform', 'rotate(' + ZIGZAG.rotate.join(' ') + ')');
                zigzag.style.display = 'none';
                svg.appendChild(zigzag);

                var clipStyle = document.createElement('style');
                document.head.appendChild(clipStyle);
                var desktop = window.matchMedia('(min-width: 1024px)');

                function zigzagPoints(box, scale) {
                    var origin = prog.getBoundingClientRect();
                    var width = document.documentElement.clientWidth;
                    var x0 = (width - ZIGZAG.width * scale) / 2;
                    var y0 = origin.top + origin.height * 1.3 - ZIGZAG.height * scale;
                    var matrix = zigzag.transform.baseVal.consolidate().matrix;
                    var length = zigzag.getTotalLength();
                    var step = 2 / scale;
                    var points = [];

                    for (var l = 0; l <= length; l += step) {
                        var p = zigzag.getPointAtLength(l).matrixTransform(matrix);
                        points.push([x0 + p.x * scale - box.left, y0 + p.y * scale - box.top]);
                    }

                    return points;
                }

                function update() {
                    if (!desktop.matches) {
                        clipStyle.textContent = '';
                        return;
                    }

                    var box = section.getBoundingClientRect();
                    var origin = prog.getBoundingClientRect();
                    var scale = Math.min(document.documentElement.clientWidth / ZIGZAG.width, origin.height * 1.3 / ZIGZAG.height);

                    var centers = [];
                    section.querySelectorAll('.bloc-media-text__media').forEach(function (media) {
                        var r = media.getBoundingClientRect();
                        centers.push([r.left - box.left + r.width / 2, r.top - box.top + r.height / 2, r.width / 2, r.height / 2]);
                    });
                    if (!centers.length) {
                        return;
                    }

                    // Partie visible du zigzag : tout ce qui passe sous le bandeau vert,
                    // jusqu'à son entrée dans la première photo (ou son point le plus proche).
                    var first = centers[0];
                    var all = zigzagPoints(box, scale);
                    var end = -1;
                    var closest = -1;
                    var best = Infinity;
                    for (var i = 0; i < all.length; i++) {
                        if (all[i][1] < 0) {
                            continue;
                        }
                        var dx = (all[i][0] - first[0]) / first[2];
                        var dy = (all[i][1] - first[1]) / first[3];
                        var dist = dx * dx + dy * dy;
                        if (dist <= 1) {
                            end = i;
                            break;
                        }
                        if (dist < best) {
                            best = dist;
                            closest = i;
                        }
                    }
                    if (end < 0) {
                        end = closest;
                    }

                    var d = '';
                    var drawing = false;
                    for (var j = 0; j <= end; j++) {
                        if (all[j][1] < 0) {
                            drawing = false;
                            continue;
                        }
                        d += (drawing ? ' L' : ' M') + all[j][0].toFixed(1) + ' ' + all[j][1].toFixed(1);
                        drawing = true;
                    }
                    d += (drawing ? ' L' : ' M') + first[0] + ' ' + first[1];

                    for (var k = 1; k < centers.length; k++) {
                        var mid = (centers[k - 1][1] + centers[k][1]) / 2;
                        d += ' C' + centers[k - 1][0] + ' ' + mid + ' ' + centers[k][0] + ' ' + mid + ' ' + centers[k][0] + ' ' + centers[k][1];
                    }

                    var map = section.querySelector('.bloc-fullwidth');
                    if (map) {
                        var m = map.getBoundingClientRect();
                        var last = centers[centers.length - 1];
                        var end2 = [m.left - box.left + m.width / 2, m.top - box.top + 20];
                        var mid2 = (last[1] + end2[1]) / 2;
                        d += ' C' + last[0] + ' ' + mid2 + ' ' + end2[0] + ' ' + mid2 + ' ' + end2[0] + ' ' + end2[1];
                    }

                    svg.setAttribute('viewBox', '0 0 ' + box.width + ' ' + box.height);
                    path.setAttribute('d', d);
                    path.setAttribute('stroke-width', (6 * scale).toFixed(2));
                    path.setAttribute('stroke-dasharray', (30 * scale).toFixed(2));

                    var hidden = Math.max(0, origin.top + origin.height * 1.3 - box.top);
                    clipStyle.textContent = 'body.home .section-content--prog:before{clip-path:inset(0 0 ' + hidden + 'px 0)}';
                }

                update();
                window.addEventListener('load', update);
                desktop.addEventListener('change', update);
                if ('ResizeObserver' in window) {
                    new ResizeObserver(update).observe(section);
                } else {
                    window.addEventListener('resize', update);
                }
            })();
        </script>

        <section class="section-content home-access">
            <h2>Où est amarrée la péniche ?</h2>
            <p>Au cœur de Lille, le long de la Deûle, juste à l'entrée de la Citadelle.</p>
            <ul>
                <li><strong>Adresse :</strong> péniche Le Bus Magique, avenue Cuvier, 59800 Lille</li>
                <li><strong>Métro :</strong> République Beaux-Arts</li>
                <li><strong>Bus :</strong> arrêt Champ de Mars</li>
                <li><strong>V'Lille :</strong> une station à moins de 5 minutes à pied</li>
                <li><strong>Voiture :</strong> parking du Champ de Mars, juste à côté</li>
            </ul>
            <a class="cta cta--tomato" href="https://www.google.com/maps/dir/?api=1&amp;destination=Le+Bus+Magique%2C+avenue+Cuvier%2C+59800+Lille" target="_blank" rel="noopener" data-umami-event="peniche-itineraire" data-umami-event-source="accueil">Je calcule mon itinéraire</a>
        </section>

        <?php if ($timonerie_id) : ?>
            <section class="section-content section-content--peniche">
                <h2>Une péniche citerne de 1954</h2>
                <div class="bloc-media-text bloc-media-text--right bloc-media-text--oval">
                    <div class="bloc-media-text__media">
                        <?php mkwvs_the_image($timonerie_id, 'large', ['sizes' => '(max-width: 900px) 80vw, 420px'], "Timonerie de la péniche avec sa barre à roue d'origine et sa vue sur le canal"); ?>
                    </div>
                    <div class="bloc-media-text__content">
                        <p>Le bateau s'appelait l'Île. Cette péniche citerne construite en 1954, l'association l'a choisie en février 2019, après avoir obtenu l'été précédent l'autorisation de stationner devant le Champ de Mars. De février 2019 à octobre 2020, des chantiers participatifs l'ont transformée en lieu de vie : c'est aujourd'hui la péniche associative de Lille, ouverte à toutes et à tous.</p>
                        <a href="/notre-histoire/" class="cta cta--green" data-umami-event="peniche-activite" data-umami-event-cible="histoire">Je lis son histoire</a>
                    </div>
                </div>
            </section>
        <?php endif; ?>

        <section class="section-content home-faq">
            <h2>Questions fréquentes</h2>
            <details open>
                <summary>Où se trouve la péniche Le Bus Magique à Lille ?</summary>
                <p>La péniche est amarrée avenue Cuvier, 59800 Lille, à l'entrée de la Citadelle, le long de la Deûle. On y accède par le métro République Beaux-Arts ou l'arrêt de bus Champ de Mars, et le parking du Champ de Mars se trouve juste à côté.</p>
            </details>
            <details>
                <summary>Peut-on manger et boire un verre sur la péniche ?</summary>
                <p>Oui. La péniche sert des plats du jour les jeudi et vendredi midi et un <a href="/brunch-lille/" data-umami-event="brunch-entree" data-umami-event-source="accueil-faq">brunch le dimanche</a>, avec une cuisine maison, bio et de saison. Le bar propose des bières locales, des vins et des boissons chaudes, à toute heure du jeudi au dimanche (et même le mercredi à la belle saison)&nbsp;!</p>
            </details>
            <details>
                <summary>Faut-il adhérer à l'association pour monter à bord ?</summary>
                <p>Oui. Le Bus Magique est une association loi 1901 née au printemps 2018, qui vit grâce à ses bénévoles et à ses adhérents&nbsp;: l'adhésion est donc nécessaire. Son montant est libre, c'est vous qui décidez, et elle se prend directement à bord auprès d'un bénévole ou d'un serveur. <a href="/monter-a-bord/" data-umami-event="peniche-activite" data-umami-event-cible="adhesion">Adhérer et monter à bord</a>.</p>
            </details>
            <details>
                <summary>Peut-on privatiser la péniche pour un événement ?</summary>
                <p>Oui, pour des événements privés comme un anniversaire, un séminaire, une soirée d'entreprise ou un mariage. La salle accueille 60 personnes assises et 100 en cocktail, la terrasse 40 assises et 60 en cocktail. Les disponibilités se consultent directement sur <a href="/location/" data-umami-event="peniche-activite" data-umami-event-cible="location">notre page de privatisation</a>.</p>
            </details>
            <details>
                <summary>Peut-on dormir sur la péniche ?</summary>
                <p>Oui. Le <a href="/dormir-sur-une-peniche-a-lille/" data-umami-event="hebergement-entree" data-umami-event-source="accueil-faq">logement du Marinier</a>, à l'arrière du bateau, se loue à la nuit pour deux à trois personnes, avec sa terrasse privée et sa salle de bain.</p>
            </details>
        </section>
    <?php endwhile; ?>
<?php endif; ?>

<?php get_footer();
