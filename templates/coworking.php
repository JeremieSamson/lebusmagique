<?php

/**
 * Template Name: Coworking
 */
?>
<?php get_header(); ?>

<?php if (have_posts()) : ?>
  <?php while (have_posts()) : the_post(); ?>

    <section class="section-landing-standard">

      <?php include(locate_template('template-part/blocks/page-head.php')); ?>

    </section>

    <?php if (have_rows('page_services_list')) : ?>
      <p class="list-items-title">Tu viens, tu te poses et tu bosses :)</p>
      <ul class="list-items">
        <?php while (have_rows('page_services_list')) : the_row(); ?>
          <?php $icon = get_sub_field('page_services_item_icon'); ?>
          <?php $name = get_sub_field('page_services_item_name'); ?>
          <?php $descriptif = get_sub_field('page_services_item_descriptif'); ?>
          <li class="items"><img class="icon" src="<?php echo $icon['url']; ?>" alt="" aria-hidden="true">
            <h2><?php echo $name; ?></h2>
            <p><?php echo $descriptif; ?></p>
          </li>
        <?php endwhile; ?>
      </ul>
    <?php endif; ?>



    <section class="sections-price">
      <div class="sections-price__header">
      </div>
      <?php if (have_rows('page_prestation_list')) : ?>
        <div class="sections-price__list">
          <?php while (have_rows('page_prestation_list')) : the_row(); ?>
            <?php $color = get_sub_field('page_prestation_item_color'); ?>
            <?php $icon = get_sub_field('page_prestation_item_icon'); ?>
            <?php $toptitle = get_sub_field('page_prestation_item_toptitle'); ?>
            <?php $title = get_sub_field('page_prestation_item_title'); ?>
            <?php $details = get_sub_field('page_prestation_item_details'); ?>
            <?php $price = get_sub_field('page_prestation_item_price'); ?>
            <section class="section-price <?php echo $color; ?>">
              <div class="font-icon <?php echo $color; ?>">
                <img src="<?php echo $icon['url']; ?>" alt="" aria-hidden="true">
              </div>
              <p class="text-font category <?php echo $color; ?>"><?php echo $toptitle; ?></p>
              <h2 class="activity"><?php echo $title; ?></h2>
              <div class="price price-<?php echo $color; ?>">
                <p class="text-font <?php echo $color; ?>"><?php echo $price; ?></p>
              </div>
            </section>
          <?php endwhile; ?>
        </div>
      <?php endif; ?>
    </section>


    <section>
      <p class="list-items-title">
        Tu viens en grand groupe et tu veux t'assurer qu'il y a de la place pour tous vous recevoir ?
      </p>
      <?php
      $smiley = apply_filters('the_content', ';-)');
      $smiley = str_replace('<p>', '', $smiley);
      $smiley = str_replace('</p>', '', $smiley); ?>
      <p class="form-title">
        Si tu veux une réponse rapide pour le jour J, <br />appelle-nous sur le fixe pendant nos heures d'ouverture !<br />
        <br />
        Si tu t'y prends en avance tu peux remplir ce formulaire <?php echo $smiley; ?>
      </p>

      <?php $contact_form_page = get_page_by_path('ya-de-la-place', 'OBJECT', 'wpcf7_contact_form'); ?>
      <?php echo do_shortcode('[contact-form-7 id="' . $contact_form_page->ID . '" ]'); ?>
    </section>

  <?php endwhile; ?>
<?php endif; ?>



<?php get_footer();
