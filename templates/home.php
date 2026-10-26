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
            transform: rotate(-8deg);
        }

        .section-content--brunch {
            padding-top: 60px;
        }

        @media screen and (min-width: 1024px) {
            .bloc-media-text--brunch .bloc-media-text__media img {
                width: 340px;
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
                <a class="cta" href="<?php echo get_permalink(get_page_by_path("programmation")); ?>">Voir la
                    programmation en détails !</a>
            <?php elseif (have_rows('page_home_programmation_list')) : ?>
                <a class="cta" href="<?php echo get_permalink(get_page_by_path("programmation")); ?>">Voir la
                    programmation</a>
            <?php endif; ?>
        </section>

        <section class="section-content section-content--beige">

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
                    <a href="<?php echo $link; ?>" class="cta cta--jungle-green"><?php echo $libelle; ?></a>
                </div>
            </div>


            <?php $image = get_field('page_home_encart_2_image', $hp->ID); ?>
            <?php $titre = get_field('page_home_encart_2_titre', $hp->ID); ?>
            <?php $description = get_field('page_home_encart_2_description', $hp->ID); ?>
            <?php $libelle = get_field('page_home_encart_2_libelle', $hp->ID); ?>
            <?php $link = get_field('page_home_encart_2_link', $hp->ID); ?>
            <div class="bloc-media-text bloc-media-text--right">
                <div class="bloc-media-text__media">
                    <?php mkwvs_the_image($image, 'large', ['sizes' => '(max-width: 900px) 100vw, 600px'], (string) $titre); ?>
                </div>
                <div class="bloc-media-text__content">
                    <h3><?php echo $titre; ?></h3>
                    <?php echo apply_filters('the_content', $description) ?>
                    <a href="<?php echo $link; ?>" class="cta cta--red"><?php echo $libelle; ?></a>
                </div>
            </div>

            <?php $image = get_field('page_home_encart_3_image', $hp->ID); ?>
            <?php $titre = get_field('page_home_encart_3_titre', $hp->ID); ?>
            <?php $description = get_field('page_home_encart_3_description', $hp->ID); ?>
            <?php $libelle = get_field('page_home_encart_3_libelle', $hp->ID); ?>
            <?php $link = get_field('page_home_encart_3_link', $hp->ID); ?>
            <div class="bloc-media-text">
                <div class="bloc-media-text__media">
                    <?php mkwvs_the_image($image, 'large', ['sizes' => '(max-width: 900px) 100vw, 600px'], (string) $titre); ?>
                </div>
                <div class="bloc-media-text__content">
                    <h3><?php echo $titre; ?></h3>
                    <?php echo apply_filters('the_content', $description) ?>
                    <a href="<?php echo $link; ?>" class="cta cta--green"><?php echo $libelle; ?></a>
                </div>
            </div>

            <div class="peniche__access peniche__access--home">
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
                    <a class="cta cta--tomato" href="https://www.google.com/maps/dir/?api=1&amp;destination=Le+Bus+Magique%2C+avenue+Cuvier%2C+59800+Lille" target="_blank" rel="noopener" data-umami-event="peniche-itineraire" data-umami-event-source="accueil">Calculer mon itinéraire</a>
                </div>
                <a class="peniche__access-map" href="https://www.google.com/maps/search/?api=1&amp;query=Le+Bus+Magique%2C+avenue+Cuvier%2C+59800+Lille" target="_blank" rel="noopener" data-umami-event="peniche-carte" data-umami-event-source="accueil" aria-label="Ouvrir le plan d'accès dans Google Maps">
                    <img src="<?php echo get_stylesheet_directory_uri() . '/images/hp-map@2x.jpg'; ?>" alt="Plan d'accès à la péniche Le Bus Magique, avenue Cuvier à Lille" loading="lazy" decoding="async">
                </a>
            </div>

        </section>

        <?php $brunch = get_page_by_path('brunch-lille'); ?>
        <?php $brunch_image = function_exists('mkwvs_peniche_theme_image_id') ? mkwvs_peniche_theme_image_id('images/brunch-assiette-complete.jpg', 'Brunch du dimanche sur la péniche du Bus Magique à Lille') : 0; ?>
        <?php $has_brunch = $brunch instanceof WP_Post && $brunch_image; ?>
        <?php if ($has_brunch) : ?>
            <section class="section-content section-content--brunch">
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
            </section>
        <?php endif; ?>

        <?php $timonerie_id = mkwvs_peniche_photo_id('timonerie.jpg', "Timonerie de la péniche avec sa barre à roue d'origine et sa vue sur le canal"); ?>
        <?php $studio_id = mkwvs_peniche_photo_id('studio-vue-ensemble.jpg', "Vue d'ensemble du studio du Marinier : kitchenette, coin bar et espace nuit"); ?>
        <section class="section-content section-content--peniche">
            <div class="peniche">

                <div class="peniche__split">
                    <div class="peniche__split-text">
                        <h2>Une péniche citerne de 1954</h2>
                        <p>Le bateau s'appelait l'Île. C'est une péniche citerne construite en 1954, une vieille dame que l'association a choisie en février 2019, après avoir obtenu l'été précédent l'autorisation de stationner devant le Champ de Mars.</p>
                        <p>De février 2019 à octobre 2020, des chantiers participatifs l'ont transformée en lieu de vie. C'est aujourd'hui la péniche associative de Lille, ouverte à toutes et à tous. <a href="/notre-histoire/" data-umami-event="peniche-activite" data-umami-event-cible="histoire">Lire l'histoire du bateau</a>.</p>
                    </div>
                    <?php if ($timonerie_id) : ?>
                        <figure class="peniche__split-media">
                            <?php mkwvs_the_image($timonerie_id, 'large', ['sizes' => '(max-width: 900px) 100vw, 520px'], "Timonerie de la péniche avec sa barre à roue d'origine et sa vue sur le canal"); ?>
                        </figure>
                    <?php endif; ?>
                </div>

                <div class="peniche__split peniche__split--reverse">
                    <div class="peniche__split-text">
                        <h2>Dormir sur la péniche</h2>
                        <p>À l'arrière du bateau, le logement du Marinier, un <a href="/dormir-sur-une-peniche-a-lille/" data-umami-event="hebergement-entree" data-umami-event-source="accueil-texte">logement insolite à Lille</a>, se loue à la nuit pour deux à trois personnes, avec sa terrasse privée sur le pont et sa vue sur le canal. C'est un hébergement indépendant du bar et du restaurant.</p>
                        <p class="peniche__split-cta"><a class="cta cta--tomato" href="/dormir-sur-une-peniche-a-lille/" data-umami-event="hebergement-entree" data-umami-event-source="accueil">Voir les disponibilités</a></p>
                    </div>
                    <?php if ($studio_id) : ?>
                        <figure class="peniche__split-media">
                            <?php mkwvs_the_image($studio_id, 'large', ['sizes' => '(max-width: 900px) 100vw, 520px'], "Vue d'ensemble du studio du Marinier : kitchenette, coin bar et espace nuit"); ?>
                        </figure>
                    <?php endif; ?>
                </div>

                <h2 class="peniche__title">Questions fréquentes</h2>
                <div class="peniche__faq">
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
                </div>

            </div>
        </section>
    <?php endwhile; ?>
<?php endif; ?>

<?php get_footer();
