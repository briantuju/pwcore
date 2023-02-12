<?php

require_once( PW_PLUGIN_PATH . '/vendor/autoload.php' );

use PWCore\Enums\InvoiceStatus;
use PWCore\Services\Orders\OrderService;

pwcore_needs_login();

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

$order   = get_post( $order_id );
$invoice = ( new OrderService )->get_pending_payment( $order_id );

if ( $invoice ) {
  $order_payment_url = $site_url . "/order-payment?invoice_id=$invoice->ID"
					   . "&order_id=" . $order_id;
}

?>

<div class="container bg-light">
  <div class="row gap-4">
    <div class="col-12">
	  <?php if ( isset( $order_payment_url ) && $invoice?->invoice_status === InvoiceStatus::PENDING->value ) { ?>
        <div class="d-flex gap-2 text-center flex-column p-4 shadow bg-danger-subtle rounded-3">
          <h3 class="fs-5">You have a pending payment for this order</h3>
          <span>Use the link below to make the payment</span>
          <a href="<?php echo $order_payment_url; ?>">
            Pay Now
          </a>
        </div>
	  <?php } ?>
    </div>

    <div class="col-12">
      <h1 class="fs-3">
        <span><?php echo $order->order_number; ?></span>
        <span class="mx-1">|</span>
        <span><?php echo $order->post_title; ?></span>
      </h1>
      <hr>
    </div>

    <div class="col-12 d-flex flex-row gap-4">
      <strong>Order Status</strong>
      <span><?php echo ucfirst( $order->order_status ); ?></span>
    </div>

    <div class="col-12 d-flex flex-row gap-4">
      <strong>Package</strong>
      <span><?php echo get_post( $order->package_id )?->post_title; ?></span>
      <strong>($ <?php echo $invoice?->amount ?? get_post( $order->package_id )?->price; ?>)</strong>
    </div>

    <div class="col-12 d-flex flex-row gap-4">
      <strong>Deadline</strong>
      <span><?php echo $order->deadline ?></span>
    </div>

    <div class="col-12 d-flex flex-row gap-4">
      <strong>Attachments</strong>
      <span>
        <?php
		$attachment = get_post_meta( $order_id, 'attachment', true );
		$url        = $attachment['url'];
		if ( count( $attachment ) ) {
		  echo "<a href=\"$url\">Download</a>";
		} else {
	  echo "No attachment";
    }
		?>
      </span>
    </div>

    <div class="col-12 d-flex flex-column gap-2">
      <strong>Your instructions</strong>
      <textarea readonly class="form-control">
        <?php echo $order->description ?>
      </textarea>
    </div>
  </div>
</div>