<?php

add_shortcode('new_order_form', 'pwcore_create_order_form');

add_action('wp_enqueue_scripts', 'pwcore_enqueue_assets');

add_action('rest_api_init', 'pwcore_create_rest_endpoints');

function pwcore_create_order_form()
{
  include PW_PLUGIN_PATH . './includes/templates/orders/new-order-form.php';
}

function pwcore_enqueue_assets()
{
  wp_enqueue_style(
    'pwcore_style',
    PW_PLUGIN_URL . '/assets/css/style.css',
    false
  );
}


function pwcore_create_rest_endpoints()
{
  register_rest_route('v1/orders', 'store', [
    'methods' => 'POST',
    'callback' => 'pwcore_store_order'
  ]);
}

function pwcore_store_order(WP_REST_Request $data)
{
  $params = $data->get_params();

  // TODO:
  // if (!wp_verify_nonce($params, 'wp_rest')) {
  //   return new WP_REST_Response(null, 422);
  // }

  // Send email to admin
  $admin_mail = get_bloginfo('admin_email');
  $admin_name = get_bloginfo('name');

  $headers = [];
  $headers[] = "From: {$admin_name} <{$admin_mail}>";
  $headers[] = "Reply-to: no-reply@example.com";
  $headers[] = "Content-Type: text/html";
  $message = "<p style='font-size: larger; color: brown'>A new order has been placed</p>";
  $subject = "New Order";

  wp_mail($admin_mail, $subject, $message, $headers);

  return new WP_REST_Response('Order created', 201);
}
