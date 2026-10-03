<?php

/**
 * Template Name: Restauration
 */
?>
<?php get_header(); ?>

<?php if (have_posts()) : ?>
  <?php while (have_posts()) : the_post(); ?>

    <section class="section-landing-standard">

      <?php include(locate_template('template-part/blocks/page-head.php')); ?>

    </section>

    <a href="#glou-glou" class="cta cta-glouglou ">aller au glouglou !</a>

    <section id="carte" class="carte">

      <div class="accroche">
        Psst ! Pour les jeudis et vendredis midis, et pour les brunchs du dimanche : <a class="cta" style="margin-bottom: 50px;margin-top:45px" href="https://uniiti.com/shop/le-bus-magique" target="_blank" rel="noopener" data-umami-event="reservation-resto" data-umami-event-source="restauration">Réservez maintenant !</a>
        <p class="form-title">S'il n'y a plus de place en ligne, appelez-nous pendant nos horaires d'ouverture ou envoyez-nous un p'tit mail ;)
        </p>

      </div>


      <?php $args = array('taxonomy' => 'typologie', 'hide_empty' => false, 'parent' => 0, 'orderby' => 'term_id', 'order' => 'ASC'); ?>
      <?php $top_typologies = get_terms($args); ?>
      <?php foreach ($top_typologies as $top_typologie) : ?>

        <?php if ($top_typologie->slug == 'le-miam-miam') : ?>
          <div class="miam-brunch">
            <!-- Template Carte Miam Miam -->
            <div class="carte--miam">
              <?php $icon_typologie = get_field('carte_typologie_icon', $top_typologie->taxonomy . '_' . $top_typologie->term_id); ?>
              <img class="icon-miam" src="<?php echo $icon_typologie['url']; ?>" alt="" aria-hidden="true">
              <h2><?php echo $top_typologie->name; ?></h2>
              <?php $subtitle = get_field('carte_typologie_subtitle', $top_typologie->taxonomy . '_' . $top_typologie->term_id); ?>
              <?php if (!empty($subtitle)) : ?>
                <p class="day"><?php echo $top_typologie->name; ?></p>
              <?php endif; ?>

              <?php if (!empty($top_typologie->description)) : ?>
                <?php echo apply_filters('the_content', $top_typologie->description); ?>
              <?php endif; ?>

              <?php $sub_topologies = get_terms(array('taxonomy' => 'typologie', 'hide_empty' => false, 'child_of' => $top_typologie->term_id, 'orderby' => 'term_id', 'order' => 'ASC')); ?>
              <?php foreach ($sub_topologies as $sub_topologie) : ?>
                <p class="column text-font category red">
                  <span><?php echo $sub_topologie->name; ?></span>
                  <span><?php echo get_field('carte_typologie_price', $sub_topologie->taxonomy . '_' . $sub_topologie->term_id); ?></span>
                </p>
              <?php endforeach; ?>

              <p class="daily-ardoise"><a href="https://www.instagram.com/le_bus_magique_lille" target="_blank">
                  Retrouvez l’ardoise du jour sur Instagram !<br>
                  <img class="link-social" src="<?php echo get_stylesheet_directory_uri() . '/images/instagram.svg'; ?>" alt="Instagram">
                </a></p>
            </div>
          <?php endif; ?>

          <?php if ($top_typologie->slug == 'le-brunch-brunch') : ?>
            <!-- Template Carte Burnch Brunch -->
            <div class="carte--brunch">
              <?php $icon_typologie = get_field('carte_typologie_icon', $top_typologie->taxonomy . '_' . $top_typologie->term_id); ?>
              <img class="icon-brunch" src="<?php echo $icon_typologie['url']; ?>" alt="" aria-hidden="true">
              <h2><?php echo $top_typologie->name; ?></h2>
              <?php $subtitle = get_field('carte_typologie_subtitle', $top_typologie->taxonomy . '_' . $top_typologie->term_id); ?>
              <?php if (!empty($subtitle)) : ?>
                <p class="day"><?php echo $subtitle; ?></p>
              <?php endif; ?>

              <?php if (!empty($top_typologie->description)) : ?>
                <?php echo apply_filters('the_content', $top_typologie->description); ?>
              <?php endif; ?>

              <?php $sub_topologies = get_terms(array('taxonomy' => 'typologie', 'hide_empty' => false, 'child_of' => $top_typologie->term_id, 'orderby' => 'term_id', 'order' => 'ASC')); ?>
              <?php foreach ($sub_topologies as $sub_topologie) : ?>
                <p class="column text-font category green">
                  <span><?php echo $sub_topologie->name; ?></span>
                  <span><?php echo get_field('carte_typologie_price', $sub_topologie->taxonomy . '_' . $sub_topologie->term_id); ?></span>
                  <?php $details = get_field('carte_typologie_details', $sub_topologie->taxonomy . '_' . $sub_topologie->term_id); ?>
                </p>
                <?php if (!empty($details)) : ?>
                  <?php echo apply_filters('the_content', $details); ?>
                <?php endif; ?>
              <?php endforeach; ?>

              <?php if (function_exists('mkwvs_brunch_page_url')) : ?>
                <a class="cta cta--yellow" style="margin: 24px auto 8px;" href="<?php echo esc_url(mkwvs_brunch_page_url()); ?>" data-umami-event="brunch-entree" data-umami-event-source="restauration">Tout savoir sur le brunch</a>
              <?php endif; ?>

            </div>
          </div>
        <?php endif; ?>
        <?php if ($top_typologie->slug == 'le-glou-glou') : ?>
          <div id="glou-glou"></div>
          <div class="carte--glou">
            <?php $icon_typologie = get_field('carte_typologie_icon', $top_typologie->taxonomy . '_' . $top_typologie->term_id); ?>
            <img class="icon-glou" src="<?php echo $icon_typologie['url']; ?>" alt="" aria-hidden="true">
            <h2><?php echo $top_typologie->name; ?></h2>
            <?php $subtitle = get_field('carte_typologie_subtitle', $top_typologie->taxonomy . '_' . $top_typologie->term_id); ?>
            <?php if (!empty($subtitle)) : ?>
              <p class="day"><?php echo $top_typologie->name; ?></p>
            <?php endif; ?>

            <?php if (!empty($top_typologie->description)) : ?>
              <?php echo apply_filters('the_content', $top_typologie->description); ?>
            <?php endif; ?>


            <div class="carte--glou__block">

              <div class="left">

                <?php $args = array('taxonomy' => 'typologie', 'hide_empty' => false, 'parent' => $top_typologie->term_id, 'orderby' => 'term_id', 'order' => 'ASC', 'exclude' => array(13)); ?>
                <?php $sub_typologies = get_terms($args); ?>
                <?php foreach ($sub_typologies as $sub_typologie) : ?>

                  <?php $typologie_ctx = $sub_typologie->taxonomy . '_' . $sub_typologie->term_id; ?>
                  <?php $volume_1 = get_field('carte_typologie_volume_1', $typologie_ctx); ?>
                  <?php $volume_2 = get_field('carte_typologie_volume_2', $typologie_ctx); ?>
                  <?php $volume_3 = get_field('carte_typologie_volume_3', $typologie_ctx); ?>

                  <p class="text-font category jungle-green column">

                    <span><?php echo $sub_typologie->name; ?></span>
                    <span>
                      <?php if (!empty($volume_3)) : ?>
                        <span class="volume-left"><?php echo $volume_1; ?></span>
                        <span class="volume-mid"><?php echo $volume_2; ?></span>
                        <span class="volume-right"><?php echo $volume_3; ?></span>
                      <?php elseif (!empty($volume_2)) : ?>
                        <span class="volume-left"><?php echo $volume_1; ?></span>
                        <span class="volume-right"><?php echo $volume_2; ?></span>
                      <?php else : ?>
                        <span class="volume-right"><?php echo $volume_1; ?></span>
                      <?php endif; ?>
                    </span>
                  </p>

                  <?php $post_args = array('post_type' => 'carte', 'posts_per_page' => '-1'); ?>
                  <?php $tax_query = array(); ?>
                  <?php $tax_query[] = array('taxonomy' => 'typologie', 'field' => 'term_id', 'terms' => array($sub_typologie->term_id), 'operator' => 'IN'); ?>
                  <?php $post_args['tax_query'] = $tax_query; ?>
                  <?php $carte_items = new WP_Query($post_args); ?>
                  <?php if ($carte_items->have_posts()) : ?>
                    <?php while ($carte_items->have_posts()) : $carte_items->the_post(); ?>
                      <?php $carte_post_id = get_the_ID(); ?>
                      <p class="column ">
                        <?php $legend_drink = get_field('carte_infos_legend_drink', $carte_post_id);  ?>
                        <?php $s_class = (!empty($legend_drink) ? 'class="' . $legend_drink . '"' : ''); ?>
                        <span <?php echo $s_class; ?>><?php echo get_the_title(); ?></span>
                        <span class="prix">
                          <?php $price1 = get_field('carte_infos_price_1', $carte_post_id); ?>
                          <?php $price2 = get_field('carte_infos_price_2', $carte_post_id); ?>
                          <?php $price3 = get_field('carte_infos_price_3', $carte_post_id); ?>

                          <?php if (!empty($price3)) : ?>
                            <span class="volume-left content"><?php echo $price1; ?></span>
                            <span class="volume-mid content"><?php echo $price2; ?></span>
                            <span class="volume-right content"><?php echo $price3; ?></span>
                          <?php elseif (!empty($price1) && !empty($price2)) : ?>
                            <span class="volume-left content"><?php echo $price1; ?></span>
                            <span class="volume-right content"><?php echo $price2; ?></span>
                          <?php elseif (!empty($price1)) : ?>
                            <span class="volume-right content"><?php echo $price1; ?></span>
                          <?php endif; ?>
                        </span>
                      </p>
                    <?php endwhile; ?>
                    <?php wp_reset_postdata(); ?>
                  <?php endif; ?>

                <?php endforeach; ?>

              </div>

              <div class="right">
                <?php $hot_drinks_id = 13; ?>
                <?php $right_typologies = array_merge(
                  get_terms(array('taxonomy' => 'typologie', 'hide_empty' => false, 'include' => array($hot_drinks_id))),
                  get_terms(array('taxonomy' => 'typologie', 'hide_empty' => false, 'parent' => $hot_drinks_id))
                ); ?>
                <?php foreach ($right_typologies as $right_typologie) : ?>
                  <p class="text-font category <?php echo ($right_typologie->term_id === $hot_drinks_id ? 'jungle-green ' : ''); ?>column">
                    <span><?php echo $right_typologie->name; ?></span>
                    <?php $typologie_price = get_field('carte_typologie_price', $right_typologie->taxonomy . '_' . $right_typologie->term_id); ?>
                    <?php if (!empty($typologie_price)) : ?>
                      <span><?php echo $typologie_price; ?></span>
                    <?php endif; ?>
                  </p>
                  <?php $post_args = array('post_type' => 'carte', 'posts_per_page' => '-1'); ?>
                  <?php $tax_query = array(); ?>
                  <?php $tax_query[] = array('taxonomy' => 'typologie', 'field' => 'term_id', 'terms' => array($right_typologie->term_id), 'operator' => 'IN', 'include_children' => false); ?>
                  <?php $post_args['tax_query'] = $tax_query; ?>
                  <?php $carte_items = new WP_Query($post_args); ?>
                  <?php if ($carte_items->have_posts()) : ?>
                    <?php while ($carte_items->have_posts()) : $carte_items->the_post(); ?>
                      <?php $carte_post_id = get_the_ID(); ?>
                      <p class="column ">
                        <?php $legend_drink = get_field('carte_infos_legend_drink', $carte_post_id);  ?>
                        <?php $s_class = (!empty($legend_drink) ? 'class="' . $legend_drink . '"' : ''); ?>
                        <span <?php echo $s_class; ?>><?php echo get_the_title(); ?></span>
                        <?php $price1 = get_field('carte_infos_price_1', $carte_post_id); ?>
                        <?php if (!empty($price1)) : ?>
                          <span>
                            <span><?php echo $price1; ?>
                            </span>
                          </span>
                        <?php endif; ?>
                        <?php $description = get_field('carte_infos_description', $carte_post_id); ?>
                        <?php if (!empty($description)) : ?>
                          <span class="price-description">
                            <?php echo strip_tags(apply_filters('the_content', $description), '<br><strong><a>'); ?>
                          </span>
                        <?php endif; ?>
                      </p>
                    <?php endwhile; ?>
                    <?php wp_reset_postdata(); ?>
                  <?php endif; ?>
                <?php endforeach; ?>

                <!--
                  <br>
                  <p class="column">
                    <span>Thé, infusion<br>(lait d'avoine : + 0,50€) <br>(matcha : + 0,50€) </span>
                    <span>
                  <span>3.50€</span>
              </span>
                  </p>
                  <br>

                  <p class="column">
                    <span><img class="icon-legende" src= "<?php echo get_stylesheet_directory_uri() . '/images/black-icon-legende.svg'; ?>" alt="" aria-hidden="true">Assam Indes (nature)</span>
                  </p>

                  <p class="column">
                    <span><img class="icon-legende" src= "<?php echo get_stylesheet_directory_uri() . '/images/black-icon-legende.svg'; ?>" alt="" aria-hidden="true">Earl grey</span>
                  </p>

                  <p class="column">
                    <span><img class="icon-legende" src= "<?php echo get_stylesheet_directory_uri() . '/images/black-icon-legende.svg'; ?>" alt="" aria-hidden="true">Poire gourmande (amandes, fève tonka)</span>
                  </p>

                  <p class="column">
                    <span><img class="icon-legende" src= "<?php echo get_stylesheet_directory_uri() . '/images/green-icon-legende.svg'; ?>" alt="" aria-hidden="true">Nanjeor Tejn Corée (nature)</span>
                  </p>

                  <p class="column">
                    <span><img class="icon-legende" src= "<?php echo get_stylesheet_directory_uri() . '/images/green-icon-legende.svg'; ?>" alt="" aria-hidden="true">T'eu verras chez bon (abricot, pêche, passion)</span>
                  </p>

                  <p class="column">
                    <span><img class="icon-legende" src= "<?php echo get_stylesheet_directory_uri() . '/images/green-icon-legende.svg'; ?>" alt="" aria-hidden="true">Détox des louloutes (thé vert, matcha, maté, gingembre, citron)</span>
                  </p>

                  <p class="column">
                    <span><img class="icon-legende" src= "<?php echo get_stylesheet_directory_uri() . '/images/blue-icon-legende.svg'; ?>" alt="" aria-hidden="true">Tri doska</span>
                  </p>

                  <p class="column">
                    <span><img class="icon-legende" src= "<?php echo get_stylesheet_directory_uri() . '/images/blue-icon-legende.svg'; ?>" alt="" aria-hidden="true">Elixir du Bos (pomme framboise, mûre verveine, passiflore)</span>
                  </p>
                  <br>-->
              </div>

            </div>

            <div class="legende">

              <p class="column">
                <span><img class="icon-legende" src="<?php echo get_stylesheet_directory_uri() . '/images/red-icon-legende.svg'; ?>" alt="" aria-hidden="true">Vin rouge</span>
              </p>
              <p class="column">
                <span><img class="icon-legende" src="<?php echo get_stylesheet_directory_uri() . '/images/orange-icon-legende.svg'; ?>" alt="" aria-hidden="true">Vin rosé</span>
              </p>

              <p class="column">
                <span><img class="icon-legende" src="<?php echo get_stylesheet_directory_uri() . '/images/yellow-icon-legende.svg'; ?>" alt="" aria-hidden="true">Vin blanc</span>
              </p>

              <p class="column">
                <span><img class="icon-legende" src="<?php echo get_stylesheet_directory_uri() . '/images/Groupe 154.svg'; ?>" alt="" aria-hidden="true">Pétillant</span>
              </p>

              <p class="column">
                <span><img class="icon-legende" src="<?php echo get_stylesheet_directory_uri() . '/images/black-icon-legende.svg'; ?>" alt="" aria-hidden="true">Thé noir</span>
              </p>

              <p class="column">
                <span><img class="icon-legende" src="<?php echo get_stylesheet_directory_uri() . '/images/green-icon-legende.svg'; ?>" alt="" aria-hidden="true">Thé vert</span>
              </p>

              <p class="column">
                <span><img class="icon-legende" src="<?php echo get_stylesheet_directory_uri() . '/images/blue-icon-legende.svg'; ?>" alt="" aria-hidden="true">Infusion</span>
              </p>
            </div>

          </div>
        <?php endif; ?>
      <?php endforeach; ?>










    </section>

  <?php endwhile; ?>
<?php endif; ?>


<?php include(locate_template('template-part/blocks/peniche-link.php')); ?>

<?php get_footer();
