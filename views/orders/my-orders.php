<?php

use PWCore\Services\Orders\OrderService;

pwcore_needs_login();

$order_details_url = get_site_url() . '/order-details?order_id=';
$user_id           = wp_get_current_user()?->ID;

$orders = ( new OrderService )->get_orders_by_user_id( $user_id );
?>

<div class="">
  <table class="table table-bordered table-striped table-hover table-responsive">
    <caption>My orders</caption>
    <thead class="table-dark">
    <tr>
      <th scope="col">Order Number</th>
      <th scope="col">Topic</th>
      <th scope="col">Status</th>
      <th scope="col">Package</th>
    </tr>
    </thead>

    <tbody class="table-group-divider">
	<?php foreach ( $orders as $order ) { ?>
      <tr>
        <th scope="row">
          <a href="<?php echo $order_details_url . $order->ID; ?>">
			<?php echo $order->order_number; ?>
          </a>
        </th>
        <td><?php echo $order->post_title; ?></td>
        <td><?php echo ucfirst( $order->order_status ); ?></td>
        <td><?php echo get_post( $order->package_id )?->post_title; ?></td>
      </tr>
	<?php } ?>
    </tbody>
  </table>
</div>
