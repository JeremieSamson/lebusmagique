<?php
/**
 * Template Name: Location
 */
?>
<?php get_header(); ?>

<?php if (have_posts()) : ?>
  <?php while (have_posts()) : the_post(); ?>

    <section class="section-landing-standard section-landing-standard--privatisation">

        <?php include(locate_template('template-part/blocks/page-head.php')); ?>

        <div class="text-yellow-background bottom priv-intro">
          <h2>Privatiser une péniche à Lille</h2>
          <p>Envie d'un lieu unique pour votre prochain événement ? Le Bus Magique, péniche amarrée au cœur de Lille, vous ouvre ses portes pour vos privatisations : anniversaire, séminaire, soirée d'entreprise, mariage ou simple fête entre amis. Profitez d'un cadre atypique en bord de Deûle avec salle intérieure, terrasse et prestations sur mesure. Sélectionnez une date ci-dessous pour obtenir votre devis en ligne.</p>
        </div>

    </section>

    <?php if( have_rows('page_services_list')) : ?>
      <ul class="list-items">
        <?php while(have_rows('page_services_list')) : the_row(); ?>
          <?php $icon = get_sub_field('page_services_item_icon'); ?>
          <?php $name = get_sub_field('page_services_item_name'); ?>
          <?php $descriptif = get_sub_field('page_services_item_descriptif'); ?>
        <li class="items">
          <span class="icon"><img src="<?php echo $icon['url']; ?>" alt="" aria-hidden="true"></span>
          <h2><?php echo $name; ?></h2>
          <p><?php echo $descriptif; ?></p>
        </li>
        <?php endwhile; ?>
      </ul>
    <?php endif; ?>




    <section class="sections-price sections-price--privatisation">
        <div class="sections-price__header">
          <h2>Disponibilités pour privatiser la péniche</h2>
          <p>– les lundi, mardi et mercredi en journée et soirée<br>
          – les dimanches à partir de 19h<br>
          – les samedis de 9h à 16h</p>
          <p>Les autres créneaux sont étudiés spécifiquement</p>
        </div>
    </section>

    <section class="section-privatisation">

        <?php include(locate_template('template-part/privatisation/calendar.php')); ?>
        <?php include(locate_template('template-part/privatisation/form-wizard.php')); ?>
    </section>

  <?php endwhile; ?>
<?php endif; ?>



<?php get_footer();
