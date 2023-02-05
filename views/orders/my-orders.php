<?php
$user_id = wp_get_current_user()?->ID;

$orders = get_posts( [
	'post_type'   => 'pw_orders',
	'numberposts' => - 1,
	'meta_query'  => [
		[
			'key'   => 'user_id',
			'value' => $user_id,
		]
	],
] );
?>

<div class="">
  <table class="table">
    <thead>
    <tr>
      <th scope="col">Order Number</th>
      <th scope="col">Topic</th>
      <th scope="col">Status</th>
      <th scope="col">Package</th>
    </tr>
    </thead>

    <tbody>
	<?php foreach ( $orders as $order ) { ?>
      <tr>
        <th scope="row"><?php echo $order->order_number; ?></th>
        <td><?php echo $order->post_title; ?></td>
        <td><?php echo ucfirst( $order->order_status ); ?></td>
        <td><?php echo get_post( $order->package_id )?->post_title; ?></td>
      </tr>
	<?php } ?>
    </tbody>
  </table>
</div>
