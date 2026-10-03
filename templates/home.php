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

            <?php $brunch = get_page_by_path('brunch-lille'); ?>
            <?php $brunch_image = function_exists('mkwvs_peniche_theme_image_id') ? mkwvs_peniche_theme_image_id('images/brunch-assiette-complete.jpg', 'Brunch du dimanche sur la péniche du Bus Magique à Lille') : 0; ?>
            <?php if ($brunch instanceof WP_Post && $brunch_image) : ?>
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

            <div class="bloc-fullwidth">
                <img src="<?php echo get_stylesheet_directory_uri() . '/images/hp-map@2x.jpg'; ?>" alt="Plan d'accès à la péniche Le Bus Magique, avenue Cuvier à Lille" loading="lazy" decoding="async">
            </div>

        </section>
    <?php endwhile; ?>
<?php endif; ?>

<?php include(locate_template('template-part/blocks/peniche-link.php')); ?>

<?php get_footer();
