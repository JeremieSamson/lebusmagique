<?php
/**
 * Template Name: Programmation
 */
?>
<?php get_header(); ?>

<?php if (have_posts()) : ?>
    <?php while (have_posts()) : the_post(); ?>

        <section class="section-landing-standard">

            <?php include(locate_template('template-part/blocks/page-head.php')); ?>

        </section>

        <?php include(locate_template('template-part/blocks/categories-thematiques.php')); ?>

        <?php if (have_rows('page_programmation_list')) : ?>
            <section>
                <h2 class="has-text-align-center">Événements à venir</h2>

                <div class="events">
                    <?php echo do_shortcode('[facebook_events col="2" posts_per_page="40"]'); ?>
                </div>

            </section>
        <?php endif; ?>

    <?php endwhile; ?>
<?php endif; ?>


<?php get_footer();
