<?php
// Exit if trying to access directly
if ( ! defined( 'ABSPATH' ) ) exit;

// Display Errors ?
ini_set('display_errors',FALSE);

/**
 * Global Variables
 */
require get_template_directory() . '/inc/globals.php';
/**
 * Fleet security hardening (pentest 2026-07-31).
 */
require get_template_directory() . '/inc/fleet-security.php';
/**
 * Utils functions.
 */
require get_template_directory() . '/inc/utils-functions.php';
require get_template_directory() . '/inc/image-functions.php';
/**
 * ACF Block Functions.
 */
require get_template_directory() . '/inc/acf-blocks-functions.php';
/**
 * Custom Menu Walker Functions.
 */
require get_template_directory() . '/inc/custom-walker.php';
require get_template_directory() . '/inc/privatisation-functions.php';
require get_template_directory() . '/inc/privatisation-ajax.php';
require get_template_directory() . '/inc/privatisation-pdf.php';
require get_template_directory() . '/inc/privatisation-emails.php';
require get_template_directory() . '/inc/privatisation-client-response.php';
require get_template_directory() . '/inc/brevo-smtp.php';
require get_template_directory() . '/inc/brevo-newsletter.php';
require get_template_directory() . '/inc/event-categories.php';
require get_template_directory() . '/inc/migrate-event-pages.php';
require get_template_directory() . '/inc/legal-pages.php';
require get_template_directory() . '/inc/migrate-legal-pages.php';
require get_template_directory() . '/inc/facebook-events-redirect.php';
require get_template_directory() . '/inc/facebook-events-single.php';
require get_template_directory() . '/inc/schema-org.php';
require get_template_directory() . '/inc/og-images.php';
require get_template_directory() . '/inc/facebook-events-categories.php';
require get_template_directory() . '/inc/facebook-events-archive.php';
require get_template_directory() . '/inc/links-social-media-functions.php';
require get_template_directory() . '/inc/analytics.php';
require get_template_directory() . '/inc/hebergement-calendar.php';
require get_template_directory() . '/inc/migrate-hebergement-page.php';
require get_template_directory() . '/inc/migrate-menu-hebergement.php';
require get_template_directory() . '/inc/migrate-peniche-page.php';
require get_template_directory() . '/inc/seo-fixes.php';
require get_template_directory() . '/inc/migrate-seo-content.php';

// Local dev: route emails to Mailpit (configured via WPMS_* env vars in docker-compose.yml)
if (defined('WPMS_ON') && WPMS_ON) {
    add_action('phpmailer_init', function ($phpmailer) {
        $phpmailer->isSMTP();
        $phpmailer->Host = WPMS_SMTP_HOST;
        $phpmailer->Port = WPMS_SMTP_PORT;
        $phpmailer->SMTPAuth = false;
        $phpmailer->SMTPSecure = '';
    });
}

// WP SPECIFICS
////////////////////////////////////////////////////////////////////////////////

// Disable Admin Email Check - WP 5.3
add_filter('admin_email_check_interval', '__return_false');

// Disable automatic WordPress plugin updates  - WP 5.5
add_filter( 'auto_update_plugin', '__return_false' );

// Disable automatic WordPress theme updates - WP 5.5
add_filter( 'auto_update_theme', '__return_false' );

// THEME OPTIONS
////////////////////////////////////////////////////////////////////////////////

// ACF Option Page
if( function_exists('acf_add_options_page') ) {

  acf_add_options_page(array(
    'page_title'    => 'Options générale du thème',
    'menu_title'    => 'Options Thème',
    'menu_slug'     => 'options',
    'capability'    => 'edit_posts',
    'position'      => 35,
    'icon_url'      => 'dashicons-list-view'
  ));

  acf_add_options_sub_page(array(
    'page_title'    => 'Infos Générales',
    'menu_title'    => 'Infos Générales',
    'parent_slug'   => 'options',
  ));

}


if (class_exists('ACF')){
  function mkwvs_json_save_acf_groups( $path ) {
    $path = get_stylesheet_directory() . '/acf-json';
    return $path;
  }
  add_filter( 'acf/settings/save_json', 'mkwvs_json_save_acf_groups' );
}

// Add Items To Theme Support
if (function_exists('add_theme_support')){
  // Add Menu Support
  add_theme_support('menus');
  // Add Title Tag Support
  add_theme_support('title-tag');
  // Add Thumbnail Theme Support
  add_theme_support('post-thumbnails');
  // Add Excerpt On Page Post Type
  add_post_type_support( 'page', 'excerpt' );
}

// Initialize Widgets Area
add_action( 'widgets_init', 'mkwvs_widgets_init' );
function mkwvs_widgets_init() {
    register_sidebar( array(
        'name' =>'Main Sidebar',
        'id' => 'main-sidebar',
        'description'   => '',
        'before_widget' => '<li id="%1$s" class="widget %2$s">',
        'after_widget'  => '</li>',
        'before_title'  => '<h2 class="widgettitle">',
        'after_title'   => '</h2>',
    ) );
}

// Add Mimes Types : SVG Support Upload
add_filter('upload_mimes', 'mkwvs_add_mime_types');
function mkwvs_add_mime_types($mimes) {
  $mimes['svg'] = 'image/svg+xml';
  return $mimes;
}

// Basic Load Custom Post Types
////////////////////////////////////////////////////////////////////////////////
//require_once 'cpt/cpt-post.php';
//require_once 'cpt/cpt-page.php';
require_once 'cpt/cpt-carte.php';
require_once 'cpt/cpt-privatisation.php';




// BACKEND
////////////////////////////////////////////////////////////////////////////////

// Admin Load Fonts, Styles & Scripts
function mkwvs_admin_scripts_styles(){

    // // Load : Custom Admin Scripts JS
    // wp_register_script('admin-scripts-js', get_template_directory_uri(). '/js/admin-scripts.js' , array('jquery'), '', true);
    // wp_enqueue_script('admin-scripts-js'); // Enqueue it!
    //
    // // Admin CSS
    // wp_register_style('admin-styles', get_template_directory_uri() . '/admin-style.css', array(), '', 'all');
    // wp_enqueue_style('admin-styles'); // Enqueue it!
}

// Remove Help Tab
add_filter( 'contextual_help', 'mkwvs_remove_help', 9999, 3 );
function mkwvs_remove_help($old_help, $screen_id, $screen){
    $screen->remove_help_tabs();
    return $old_help;
}

// Load Admin Custom Dashboard Widget
if (current_user_can('manage_options') && is_admin() ){
    //require_once 'dashboxes/dashbox-social.php';
    //require_once 'dashboxes/dashbox-identity.php';
}


// FRONT END
////////////////////////////////////////////////////////////////////////////////

// Optimize Head Section
if (!function_exists('mkwvs_clean_head')){
    add_action('init', 'mkwvs_clean_head');
    function mkwvs_clean_head(){
        // Hide Admin Bar On Front Office
        add_filter('show_admin_bar', '__return_false');
        // Add Scripts & Styles : Front End
        add_action('wp_enqueue_scripts', 'mkwvs_scripts_styles', 20 );
        // Add Scripts & Styles : Back End
        add_action('admin_enqueue_scripts', 'mkwvs_admin_scripts_styles', 21 );
        // REMOVE WP EMOJI
        /*remove_action('wp_head', 'print_emoji_detection_script', 7);
        remove_action('wp_print_styles', 'print_emoji_styles');
        remove_action('admin_print_scripts', 'print_emoji_detection_script' );
        remove_action('admin_print_styles', 'print_emoji_styles' );*/
        // DISABLED EMBED
        //add_action( 'wp_footer', 'mkwvs_deregister_embed' );
        // Remove Post Tags Default Post Taxonomy
        unregister_taxonomy_for_object_type('post_tag', 'post');
    }
}

add_filter('wp_resource_hints', 'mkwvs_font_resource_hints', 10, 2);
function mkwvs_font_resource_hints(array $urls, string $relation_type): array
{
    if ('preconnect' !== $relation_type || is_admin()) {
        return $urls;
    }

    $urls[] = 'https://fonts.googleapis.com';
    $urls[] = ['href' => 'https://fonts.gstatic.com', 'crossorigin'];

    return $urls;
}

// Deregister Embed Js
function mkwvs_deregister_embed(){
    wp_deregister_script( 'wp-embed' );
}

// Load Fonts, Styles & Scripts
function mkwvs_scripts_styles(){

    // FONTS
    wp_enqueue_style('google-fonts', 'https://fonts.googleapis.com/css2?family=Barlow+Condensed:wght@500;600&family=Barlow:ital,wght@0,500;0,600;0,800;1,500;1,600;1,800&display=swap', array(), null);

    // STYLES

    // Swiper CSS
    wp_register_style('swiper-style', get_template_directory_uri(). '/js/swiper/swiper.min.css',array(), '', 'all' );
    wp_enqueue_style('swiper-style'); // Enqueue it!

    // Dropkick CSS
    wp_register_style('dropkick-style', get_template_directory_uri(). '/js/dropkick/dropkick.css',array(), '', 'all' );
    wp_enqueue_style('dropkick-style'); // Enqueue it!

    // Modal Video CSS
    wp_register_style('modal-video-style', get_template_directory_uri(). '/js/modal-video/modal-video.min.css',array(), '', 'all' );
    wp_enqueue_style('modal-video-style'); // Enqueue it!

    // Theme CSS
    wp_register_style('styles', get_template_directory_uri() . '/css/main.css', array(), filemtime(get_template_directory() . '/css/main.css'), 'all');
    wp_enqueue_style('styles'); // Enqueue it!


    // SCRIPTS

    // Load : Last Version Of jQuery
    wp_deregister_script('jquery');
    wp_register_script('jquery','https://ajax.googleapis.com/ajax/libs/jquery/1/jquery.min.js', array(), '', true);
    wp_enqueue_script('jquery');

    // Load : Underscore JS
    wp_register_script('underscore-js', get_template_directory_uri(). '/js/underscore-min.js',array('jquery'), '', true );
    wp_enqueue_script('underscore-js'); // Enqueue it!

    // Load : Swiper JS
    wp_register_script('swiper-js', get_template_directory_uri(). '/js/swiper/swiper.min.js',array('jquery'), '', true );
    wp_enqueue_script('swiper-js'); // Enqueue it!

    // Load : Dropkick JS
    wp_register_script('dropkick-js', get_template_directory_uri(). '/js/dropkick/dropkick.min.js',array('jquery'), '', true );
    wp_enqueue_script('dropkick-js'); // Enqueue it!

    // Load : Modal Video JS
    wp_register_script('modal-video-js', get_template_directory_uri(). '/js/modal-video/jquery-modal-video.min.js',array('jquery'), '', true );
    wp_enqueue_script('modal-video-js'); // Enqueue it!

    if (is_home() || is_single()){
        // Load : PARRALAX
        // wp_register_script('parallax-js', get_template_directory_uri(). '/js/parallax/jquery.parallax-1.1.3.js',array('jquery'), '', true );
        // wp_enqueue_script('parallax-js'); // Enqueue it!
        // Load : jQuery-UI
        // wp_register_script('jquery-ui', get_template_directory_uri(). '/js/jquery-ui.js',array('jquery'), '', true );
        // wp_enqueue_script('jquery-ui'); // Enqueue it!

        // Load : Waypoints
        // wp_register_script('waypoints-js', get_template_directory_uri(). '/js/waypoints/jquery.waypoints.min.js',array('jquery','typed-js'), '', true );
        // wp_enqueue_script('waypoints-js'); // Enqueue it!


    }


    // Load : Custom Scripts JS
    wp_register_script('scripts-js', get_template_directory_uri(). '/js/main.js' , array('jquery', 'swiper-js'), '', true);
    wp_enqueue_script('scripts-js'); // Enqueue it!

    // Make AjaxUrl visible in scripts (WP 5.7+ requires wp_localize_script $l10n to be an array)
    wp_add_inline_script('scripts-js', 'var ajaxurl = "' . esc_js(admin_url('admin-ajax.php')) . '";', 'before');

    // Newsletter JS (formulaire footer Brevo)
    wp_register_script('newsletter-js', get_template_directory_uri() . '/js/src/newsletter.js', ['jquery'], filemtime(get_template_directory() . '/js/src/newsletter.js'), true);
    wp_enqueue_script('newsletter-js');
    wp_localize_script('newsletter-js', 'mkwvsNewsletter', [
        'ajaxUrl' => admin_url('admin-ajax.php'),
        'nonce'   => wp_create_nonce('brevo_subscribe'),
    ]);

    // Privatisation JS (pages utilisant le calendrier de privatisation)
    $priv_templates = [
        'templates/location.php',
        'templates/privatisation-seminaires.php',
        'templates/privatisation-evenements-prives.php',
    ];
    if (array_filter($priv_templates, 'is_page_template')) {
        wp_register_script('privatisation-js', get_template_directory_uri() . '/js/src/privatisation.js', ['jquery'], filemtime(get_template_directory() . '/js/src/privatisation.js'), true);
        wp_enqueue_script('privatisation-js');
        wp_localize_script('privatisation-js', 'privData', [
            'ajaxurl' => admin_url('admin-ajax.php'),
            'nonce'   => wp_create_nonce('priv_submit_nonce'),
            'tarifs'  => mkwvs_priv_get_tarifs_for_js(),
        ]);
    }

    // CSS dédié aux pages événements récurrents (design des maquettes client)
    $event_templates = [
        'templates/evenement-blind-test-lille.php',
        'templates/evenement-jam-session-lille.php',
        'templates/evenement-scene-ouverte-lille.php',
        'templates/evenement-ateliers-lille.php',
        'templates/evenement-format.php',
    ];
    if (array_filter($event_templates, 'is_page_template')) {
        wp_register_style('event-recurrent-style', get_template_directory_uri() . '/css/event-recurrent.css', ['styles'], filemtime(get_template_directory() . '/css/event-recurrent.css'), 'all');
        wp_enqueue_style('event-recurrent-style');
    }

    // CSS des pastilles thématiques (programmation) + pages catégories
    $cat_templates = ['templates/programmation.php', 'templates/categorie-evenements.php'];
    if (array_filter($cat_templates, 'is_page_template')) {
        wp_register_style('prog-categories-style', get_template_directory_uri() . '/css/programmation-categories.css', ['styles'], filemtime(get_template_directory() . '/css/programmation-categories.css'), 'all');
        wp_enqueue_style('prog-categories-style');
    }

    // CSS de la page péniche
    if (is_page_template('templates/peniche-lille.php')) {
        wp_register_style('peniche-style', get_template_directory_uri() . '/css/peniche.css', ['styles'], filemtime(get_template_directory() . '/css/peniche.css'), 'all');
        wp_enqueue_style('peniche-style');
    }

    // CSS de la page hébergement
    if (is_page_template('templates/hebergement.php')) {
        wp_register_style('hebergement-style', get_template_directory_uri() . '/css/hebergement.css', ['styles'], filemtime(get_template_directory() . '/css/hebergement.css'), 'all');
        wp_enqueue_style('hebergement-style');
        wp_enqueue_script('hebergement-script', get_template_directory_uri() . '/js/hebergement.js', [], filemtime(get_template_directory() . '/js/hebergement.js'), true);
    }

}

// Custom Excerpt Length
add_filter( 'excerpt_length', 'mkwvs_excerpt_length', 999 );
function mkwvs_excerpt_length( $length ) {
	return 30;
}

// Theme Pagination Fonction From GEEKPRESS.FR
// www.geekpress.fr/pagination-wordpress-sans-plugin/
if( !function_exists( 'theme_pagination' ) ) {

    function theme_pagination($custom_query, $custom_args = '') {

	global $wp_query, $wp_rewrite;
	$custom_query->query_vars['paged'] > 1 ? $current = $custom_query->query_vars['paged'] : $current = 1;

	$pagination = array(
		'base' => @add_query_arg('page','%#%'),
		'format' => '',
		'total' => $custom_query->max_num_pages,
		'current' => $current,
	        'show_all' => false,
	        'end_size'     => 1,
	        'mid_size'     => 2,
		'type' => 'list',
		'next_text' => '»',
		'prev_text' => '«'
	);
	if (!empty($custom_args)){
            if (is_array($custom_args)){
                $a_args = array();
                foreach ($custom_args as $custom_arg) {
                    $a_args[$custom_arg] = $_POST[$custom_arg];
                }

                $pagination['add_args'] = $a_args;
            }
        }else{
            if( $wp_rewrite->using_permalinks() )
                    $pagination['base'] = user_trailingslashit( trailingslashit( remove_query_arg( 's', get_pagenum_link( 1 ) ) ) . 'page/%#%/', 'paged' );

            if( !empty($wp_query->query_vars['s']) )
                    $pagination['add_args'] = array( 's' => str_replace( ' ' , '+', get_query_var( 's' ) ) );
        }

	echo str_replace('page/1/','', paginate_links( $pagination ) );
    }
}




// Custom Images Size
add_action( 'after_setup_theme', 'mkwvs_images_setup' );
function mkwvs_images_setup() {
    // Format Vidéo 16/9 464 / 261px
    add_image_size( 'header-thumb', 816, 543, true ); // (cropped)
    add_image_size( 'video-thumb', 1000, 540, true ); // (cropped)
    add_image_size( 'news-thumb', 417, 250, true ); // (cropped)
    add_image_size( 'og-image', 1200, 630, true ); // Open Graph / social sharing
}


// Extract Video Type
function mkwvs_get_video_url_id($url){
    if (strpos($url, 'youtu.be') > 0 ||  strpos($url, 'youtube') > 0) {
        preg_match('%(?:youtube(?:-nocookie)?\.com/(?:[^/]+/.+/|(?:v|e(?:mbed)?)/|.*[?&]v=)|youtu\.be/)([^"&?/ ]{11})%i', $url, $match);
        $youtube_id = $match[1];
        return $youtube_id;
    } elseif (strpos($url, 'vimeo') > 0) {
        return (int) substr(parse_url($url, PHP_URL_PATH), 1);
    } else {
        return 'unknown';
    }
}



/*Contact form 7 remove span*/
add_filter('wpcf7_form_elements', function($content) {
    $content = preg_replace('/<(span).*?class="\s*(?:.*\s)?wpcf7-form-control-wrap(?:\s[^"]+)?\s*"[^\>]*>(.*)<\/\1>/i', '\2', $content);

    $content = str_replace('<br />', '', $content);

    return $content;
});


add_filter( 'wp_nav_menu_objects', 'submenu_limit', 10, 2 );

function submenu_limit( $items, $args ) {

    if ( empty( $args->submenu ) ) {
        return $items;
    }

    // $ids       = wp_filter_object_list( $items, array( 'title' => $args->submenu ), 'and', 'ID' );
    $ids       = wp_filter_object_list( $items, array( 'object_id' => $args->submenu ), 'and', 'ID' );

    $parent_id = array_pop( $ids );
    $children  = submenu_get_children_ids( $parent_id, $items );

    foreach ( $items as $key => $item ) {

        if ( ! in_array( $item->ID, $children ) ) {
            unset( $items[$key] );
        }
    }

    return $items;
}

function submenu_get_children_ids( $id, $items ) {

    $ids = wp_filter_object_list( $items, array( 'menu_item_parent' => $id ), 'and', 'ID' );

    foreach ( $ids as $id ) {

        $ids = array_merge( $ids, submenu_get_children_ids( $id, $items ) );
    }

    return $ids;
}


// add_filter('nav_menu_item_args', 'my_nav_menu_item_args', 10, 3);
//
// function my_nav_menu_item_args( $args, $item, $depth ) {
//
// 		// vars
// 		$icon = get_field('status_icon', $item->ID);
//
// 		// append icon
// 		if( $icon != '' ) {
//       $icon_contents = file_get_contents( get_stylesheet_directory().'/img/'.$icon );
// 			$args->after = $icon_contents;
// 		} else {
//       $args->after = '';
//     }
//
//     return $args;
// }
