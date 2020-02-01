<?php

/**
 * Plugin Name: My Cool Border Box
 * Author: Brad
 * Version: 1.0.0
 */

function loadMyBlockFiles() {
  wp_enqueue_script(
    'my-super-unique-handle',
    plugin_dir_url(__FILE__) . 'my-block.js',
    array('wp-blocks', 'wp-i18n', 'wp-editor'),
    true
  );
}
 
add_action('enqueue_block_editor_assets', 'loadMyBlockFiles');

/* To make your block "dynamic" uncomment
  the code below and in your JS have your "save"
  method return null
*/


function borderBoxOutput($props) {
  // return value should correspond to return value from edit function in ./my-block.js so edit screen and front end page match
  $color = get_field( 'color' ); // field set by register_meta below
  return '<h3 style="border: 5px solid ' . $color . '">' . $props['content'] . '</h3>';
}

register_block_type( 'brad/border-box', array(
  'render_callback' => 'borderBoxOutput',
) );

/* To Save Post Meta from your block uncomment
  the code below and adjust the post type and
  meta name values accordingly. If you want to
  allow multiple values (array) per meta remove
  the 'single' property.
*/


function myBlockMeta() {
  // meta data comes from the 'attributes' property of the 'registerBlockType' function in ./my-block.js
  register_meta('post', 'color', array('show_in_rest' => true, 'type' => 'string', 'single' => true));
}

add_action('init', 'myBlockMeta');
