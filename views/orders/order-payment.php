<?php

pwcore_needs_login();

$site_url   = get_site_url();
$order_id   = $_GET['order_id'];
$invoice_id = $_GET['invoice_id'];
$client_id  = $_ENV['PAYPAL_CLIENT_ID'];

if ( ! $order_id || ! $invoice_id ) {
  echo "<div>";
  echo "<h1>You have accessed this page illegally</h1>";
  echo "<p>Go back " . "<a href=\"$site_url\">home</a>";
  echo "</p>";
  echo "</div>";

  return;
}

$order   = get_post( $order_id );
$invoice = get_post( $invoice_id );
$amount  = get_post_meta( $invoice->ID, 'amount', true );
?>

<div class="container bg-light p-4 rounded">
  <div class="row gap-4 mb-4">
    <div class="col-12">
      <h1 class="fs-2">
        <span class="fw-normal">Order</span> <?php echo $order->order_number; ?>
      </h1>
    </div>

    <div class="col-12">
      <strong class="fs-1">$ <?php echo $amount; ?></strong>
    </div>

    <div class="col-12 d-flex flex-row gap-4">
      <strong>Status</strong>
      <span><?php echo ucfirst( $order->order_status ); ?></span>
    </div>

    <div class="col-12 d-flex flex-row gap-4">
      <strong>Package</strong>
      <span><?php echo get_post( $order->package_id )?->post_title; ?></span>
    </div>
  </div>

  <!-- Set up a container element for the button -->
  <div id="paypal-button-container"></div>
</div>

<script
  defer
  src="https://www.paypal.com/sdk/js?client-id=<?php echo $client_id; ?>&components=buttons&intent=capture"
></script>

<script>
  window.addEventListener("DOMContentLoaded", function() {
    let paypal = window.paypal;

    jQuery(document).ready(function($) {

      paypal.Buttons({
        style: {
          layout: "horizontal",
          height: 40,
          color: "blue",
          label: "pay"
        },
        createOrder: async function(data, actions) {
          // Set up the transaction
          return await actions.order.create({
            purchase_units: [
              {
                amount: {
                  currency_code: "USD",
                  value: '<?php echo $amount; ?>'
                }
              }
            ],
            application_context: {
              shipping_preference: "NO_SHIPPING",
              landing_page: "NO_PREFERENCE"
            }
          });
        },
        onApprove: async function(data, actions) {
          // This function captures the funds from the transaction.
          const details = await actions.order.capture();
          const { payer, id } = details;

          const apiData = {
            ref_number: id,
            invoice_id: "<?php echo $invoice_id;?>",
            order_id: "<?php echo $order_id;?>"
          };

          // Ajax call to save to database
          $.ajax({
            type: "POST",
            url: '<?php echo get_rest_url( null, 'pwcore/v1/payments' ) ?>',
            data: apiData,
            beforeSend: function(xhr) {
              xhr.setRequestHeader("X-WP-Nonce", window.WPNonce?.nonce);
            },
            success: function(data) {
              console.log("Success ✅");
            },
            error: function(error) {
              console.log("Error ❌", error);
            }
          });

          window.location.href = "<?php echo $site_url . '/my-orders';?>";
        },
        onError: function(err) {
          // For example, redirect to a specific error page
          console.log("PayPal error:\n", err);
        },
        onCancel: function(data) {
          alert("You have cancelled this transaction");
        }
      }).render("#paypal-button-container");

    });
  });
</script>