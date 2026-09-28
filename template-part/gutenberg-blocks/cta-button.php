<?php

/**
 * CTA Button Block Template.
 *
 * @param   array $block The block settings and attributes.
 * @param   string $content The block inner HTML (empty).
 * @param   bool $is_preview True during AJAX preview.
 * @param   (int|string) $post_id The post ID this block is saved to.
 */

// Create id attribute allowing for custom "anchor" value.
// $id = 'testimonial-' . $block['id'];
// if( !empty($block['anchor']) ) {
//     $id = $block['anchor'];
// }

// Create class attribute allowing for custom "className" and "align" values.
$className = 'cta';
if( !empty($block['className']) ) {
    $className .= ' ' . $block['className'];
}
if( !empty($block['align']) ) {
    $className .= ' align' . $block['align'];
}

// Load values and assign defaults.
// $type = get_field('cta_type')['value'] ?: 'main';
$label = get_field('cta_label') ?: 'Libellé';
// $icon = get_field('cta_icon') ?: '';
$link = get_field('cta_link');
$link_url = is_array($link) ? ($link['url'] ?? '') : $link;
// $background_color = get_field('background_color');
// $text_color = get_field('text_color');
?>
  <a class="<?php echo $className; ?>" href="<?php echo $link_url; ?>">
    <span class="a-button__label"><?php echo $label; ?></span>
  </a>
