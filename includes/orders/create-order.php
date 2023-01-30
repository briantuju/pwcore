<?php

add_shortcode('new_order_form', 'show_create_order_form');

add_action('wp_enqueue_scripts', 'pwcore_enqueue_style');

function show_create_order_form()
{
  include PW_PLUGIN_PATH . './includes/templates/orders/new-order-form.php';
}

function pwcore_enqueue_style()
{
  wp_enqueue_style('pwcore_style', PW_PLUGIN_URL . '/assets/css/style.css', false);
}
