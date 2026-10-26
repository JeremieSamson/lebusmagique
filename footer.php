

<footer class="footer-adhesion">
  <h2 class="accroche">Envie de monter à bord ?</h2>
  <a href="<?php echo get_permalink(get_page_by_path('monter-a-bord')) ?>" class="cta cta-footer cta-decoration" data-umami-event="adhesion-entree" data-umami-event-source="footer">J'adhère !
    <img class="cta-responsive-decoration" src="<?php echo get_stylesheet_directory_uri() . '/images/Groupe 396.svg'; ?>" alt="" aria-hidden="true">
  </a>
  <!-- <img class="cta-decoration" src="<?php echo get_stylesheet_directory_uri() . '/images/footer-cta-decoration.png'; ?>"> -->

</footer>
<?php if (!is_page_template('templates/hebergement.php')) : ?>
<footer class="footer-location">
  <h2 class="accroche has-text-align-center">Psst ! La péniche a aussi son gîte !</h2>
  <a href="<?php echo esc_url(mkwvs_hebergement_page_url()); ?>" class="cta cta-footer cta-decoration" data-umami-event="hebergement-entree" data-umami-event-source="footer">Je passe la nuit sur la péniche
  </a>
</footer>
<?php endif; ?>

<footer class="footer-network-news">
  <img class="footer-logo" src="<?php echo get_stylesheet_directory_uri() . '/images/Groupe 77.svg'; ?>" alt="Le Bus Magique">

  <div class="rubric menu">

    <h2>Menu</h2>
<?php $footer_menu_items = wp_get_nav_menu_items("navigation"); ?>
<?php foreach ($footer_menu_items as $footer_menu_item): ?>
<?php   if($footer_menu_item->type != "custom" && $footer_menu_item->title != "Contact"): ?>
    <p class="menu-links"><a href="<?php echo $footer_menu_item->url; ?>"><?php echo $footer_menu_item->title; ?></a></p>
<?php   endif; ?>
<?php endforeach; ?>

  </div>

  <div class="rubric contact">
    <h2>Contactez-nous</h2>
    <?php $contact_email = get_field('option_contact_email', 'option'); ?>
    <?php if($contact_email != ""): ?>
    <p><a href="mailto:<?php echo antispambot($contact_email, 1); ?>"><?php echo antispambot($contact_email); ?></a></p>
    <?php endif; ?>
    <?php if (have_rows('option_contact_list', 'option')) : ?>
      <?php while (have_rows('option_contact_list', 'option')) : the_row(); ?>
        <!--<?php $contact_name = get_sub_field('option_contact_item_name', 'option'); ?>-->
        <?php $contact_phone = get_sub_field('option_contact_item_phone', 'option'); ?>
        <p><!--<?php echo $contact_name ?>--> <a href="tel:<?php echo $contact_phone; ?>"><?php echo $contact_phone; ?></a></p>
      <?php endwhile; ?>
    <?php endif; ?>
    <p class="address">
        <?php $contact_address = get_field('option_contact_address', 'option'); ?>
        <?php if($contact_address != ""): ?>
          <?php echo $contact_address; ?>
        <?php endif; ?>
        <?php $contact_zipcode = get_field('option_contact_zipcode', 'option'); ?>
        <?php if($contact_zipcode != ""): ?>
          <br/><?php echo $contact_zipcode; ?>
        <?php endif; ?>
        <?php $contact_city = get_field('option_contact_city', 'option'); ?>
        <?php if($contact_city != ""): ?>
           <?php echo $contact_city; ?>
        <?php endif; ?>
    </p>

    <div class="reseaux-sociaux">
      <?php if (have_rows('option_social_network_list', 'option')) : ?>
        <?php while (have_rows('option_social_network_list', 'option')) : the_row(); ?>
          <?php $social_network_name = get_sub_field('item_social_network_name', 'option'); ?>
          <?php $social_network_icon = get_sub_field('item_social_network_icon', 'option'); ?>
          <?php $social_network_url  = get_sub_field('item_social_network_url', 'option'); ?>
          <a href="<?php echo $social_network_url; ?>" target="_blank"><img class="link-social-responsive border-white-responsive" src="<?php echo $social_network_icon['url']; ?>" alt="<?php echo esc_attr($social_network_name); ?>"></a>
        <?php endwhile; ?>
      <?php endif; ?>
    </div>

    <div class="horaires">
      <p>
        Horaires
        <div class="horaires__list">
          <?php $contact_open_hours = get_field('option_open_hours', 'option'); ?>
          <?php if(!empty($contact_open_hours)) : ?>
          <?php echo apply_filters('the_content',$contact_open_hours); ?>
          <?php endif; ?>
        </div>
      </p>
    </div>

  </div>


  <div class="rubric newsletter">
    <h2>Newsletter</h2>
    <p>1 mail chaque semaine, garanti sans spam et plein d'anecdotes !</p>
    <form class="newsletter-form" novalidate>
      <p class="email-wrap">
        <label for="newsletter-email" class="screen-reader-text">Adresse e-mail</label>
        <input type="email" id="newsletter-email" name="email" required placeholder="Ici ton email, et hop !">
      </p>
      <p class="consent">
        <label>
          <input type="checkbox" name="consent" value="1" required>
          <span>J'accepte de recevoir la newsletter hebdomadaire. Désabonnement à tout moment via le lien dans chaque email.</span>
        </label>
      </p>
      <p class="newsletter-honeypot" aria-hidden="true">
        <label>Site web<input type="text" name="website" tabindex="-1" autocomplete="off"></label>
      </p>
      <p class="submit-wrap">
        <input type="submit" value="S'inscrire">
      </p>
      <p class="newsletter-message" role="status" aria-live="polite"></p>
    </form>

    <div class="reseaux-sociaux">
      <?php if (have_rows('option_social_network_list', 'option')) : ?>
        <?php while (have_rows('option_social_network_list', 'option')) : the_row(); ?>
          <?php $social_network_name = get_sub_field('item_social_network_name', 'option'); ?>
          <?php $social_network_icon = get_sub_field('item_social_network_icon', 'option'); ?>
          <?php $social_network_url  = get_sub_field('item_social_network_url', 'option'); ?>
          <a href="<?php echo $social_network_url; ?>" target="_blank"><img class="link-social border-white" src="<?php echo $social_network_icon['url']; ?>" alt="<?php echo esc_attr($social_network_name); ?>"></a>
        <?php endwhile; ?>
      <?php endif; ?>
    </div>

  </div>

</footer>

<div class="footer__misc">
  <ul>
<?php foreach ([['mentions-legales', 'Mentions légales'], ['confidentialite', 'Confidentialité']] as [$legal_slug, $legal_label]) : ?>
  <?php $legal_page = get_page_by_path($legal_slug); ?>
  <?php if ($legal_page instanceof WP_Post) : ?>
    <li><a href="<?php echo esc_url(get_permalink($legal_page)); ?>"><?php echo $legal_label; ?></a></li>
  <?php endif; ?>
<?php endforeach; ?>
    <li>Site par <a href="https://manon-verbeke.com/" target="_blank">Manon Verbeke</a> &times; <a href="https://makewaves.fr/" target="_blank">Makewaves</a> &times; <a href="https://symfolidity.com/" target="_blank">Symfolidity</a></li>
  </ul>
</div>


<script>
				var site_url = '<?php echo get_site_url(); ?>';
			</script>
        <?php wp_footer(); ?>

    </body>

</html>
