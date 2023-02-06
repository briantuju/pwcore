<?php

require_once( PW_PLUGIN_PATH . '/vendor/autoload.php' );

use PWCore\Enums\InvoiceStatus;

$site_url = get_site_url();
$order_id = $_GET['order_id'];

if ( ! $order_id ) {
  echo "<div>";
  echo "<h1>You have accessed this page illegally</h1>";
  echo "<p>Go back " . "<a href=\"$site_url\">home</a>";
  echo "</p>";
  echo "</div>";

  return;
}

$order    = get_post( $order_id );
$invoices = get_posts( [
	'post_type'   => 'pw_invoices',
	'numberposts' => 1,
	'meta_query'  => [
		[
			'key'   => 'order_id',
			'value' => $order_id,
		]
	],
] );

$invoice = $invoices[0];

$order_payment_url = $site_url . "/order-payment?invoice_id=$invoice?->ID"
					 . "&order_id=";
?>

<div class="container">
  <div class="row gap-4">
    <div class="col-12">
      <h1><?php echo $order->post_title; ?></h1>
    </div>

    <div class="col-12 d-flex flex-row gap-4">
      <strong>Status</strong>
      <span><?php echo ucfirst( $order->order_status ); ?></span>
      <span><?php if ( $invoice?->invoice_status === InvoiceStatus::PENDING->value ) { ?>
          <a href="<?php echo $order_payment_url . $order->ID; ?>">Pay now</a>
		<?php } ?>
      </span>
    </div>

    <div class="col-12 d-flex flex-row gap-4">
      <strong>Package</strong>
      <span><?php echo get_post( $order->package_id )?->post_title; ?></span>
      <strong>($ <?php echo get_post( $order->package_id )?->price; ?>)</strong>
    </div>

    <div class="col-12 d-flex flex-row gap-4">
      <strong>Attachments</strong>
      <span>
        <?php
		$attachment = get_post_meta( $order_id, 'attachment', true );
		$url        = $attachment['url'];
		echo "<a href=\"$url\">Download</a>";
		?>

      </span>
    </div>
  </div>
</div>