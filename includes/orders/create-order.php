<?php

add_shortcode('new_order_form', 'pwcore_create_order_form');

add_action('wp_enqueue_scripts', 'pwcore_enqueue_assets');

add_action('rest_api_init', 'pwcore_create_rest_endpoints');

add_action('init', 'pwcore_create_orders_page');

add_action('add_meta_boxes', 'pwcore_create_meta_box');

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

function pwcore_create_meta_box()
{
  add_meta_box(
    'pw_orders_meta_box',
    'Order Details',
    'pwcore_show_order',
    'pw_orders'
  );
}

function pwcore_show_order()
{
  // Get ALL meta data
  $post_metas = get_post_meta(get_the_ID());

  // Get SINGLE meta data entry
  $order_topic = get_post_meta(get_the_ID(), 'topic', true);

  echo "<h1>" . $order_topic . "</h1><br/>";

  unset($post_metas['_edit_last']);
  unset($post_metas['_edit_lock']);

  foreach ($post_metas as $key => $value) {
    echo "<strong>" . ucfirst($key) . "</strong>" . " => {$value[0]} <br/> <br/>";
  }
}

function pwcore_create_orders_page()
{
  $args = [
    'public' => true,
    'has_archive' => true,
    'labels' => [
      'name' => 'Orders',
      'singular_name' => 'Order',
      'edit_item' => ''
    ],
    'supports' => false,
    'capability_type' => 'post',
    'capabilities' => [
      'create_posts' => false
    ],
    'map_meta_cap' => true
  ];

  register_post_type('pw_orders', $args);
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

  // Clean unwanted data before saving
  unset($params['_wpnonce']);
  unset($params['_wp_http_referer']);
  pwcore_save_order($params);

  wp_mail($admin_mail, $subject, $message, $headers);

  return new WP_REST_Response('Order created', 201);
}

function pwcore_save_order(array $data)
{
  $order_data = [
    'post_title' => $data['topic'],
    'post_type' => 'pw_orders',
    'post_status' => 'publish'
  ];

  $post_id = wp_insert_post($order_data);

  foreach ($data as $key => $value) {
    add_post_meta($post_id, $key, $value);
  }
}
