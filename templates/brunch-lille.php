<?php

/**
 * Template Name: Brunch Lille
 */
?>
<?php get_header(); ?>

<?php if (have_posts()) : ?>
  <?php while (have_posts()) : the_post(); ?>

    <section class="section-landing-standard">

      <?php include(locate_template('template-part/blocks/page-head.php')); ?>

    </section>

    <section class="o-wrapper o-landing">
      <div class="o-landing__content">
        <?php the_content(); ?>
      </div>
    </section>

  <?php endwhile; ?>
<?php endif; ?>

<?php get_footer();
