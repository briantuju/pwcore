<?php $client_id = $_ENV['PAYPAL_CLIENT_ID']; ?>

<div class="container">
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
                  value: 35
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
          const { payer, status, id, create_time } = details;

          // Ajax call to save to database
          $.ajax({
            type: "POST",
            url: '<?php echo get_rest_url( null, 'pwcore/v1/payments' ) ?>',
            data: {
              invoice_id: "InvoiceId",
              transaction_id: id,
              paid_date: create_time,
              status
            },
            beforeSend: function(xhr) {
              xhr.setRequestHeader("X-WP-Nonce", window.WPNonce?.nonce);
            },
            contentType: false,
            processData: false,
            success: function(data) {
              console.log("Success ✅");
            },
            error: function(error) {
              console.log("Error ❌", error);
            }
          });

          console.log("Transaction completed by " + payer.name.given_name);
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