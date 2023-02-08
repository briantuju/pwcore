<?php
$customers = get_users( [
	'role'   => 'subscriber',
	'fields' => [
		'display_name',
		'ID'
	]
] );
?>

<div class="row row-cols-1 gap-4" style="max-width: 420px">
  <p class="text-primary mb-0">
    <strong class="text-uppercase text-danger me-2">NOTE: </strong>
    <span>
        If there is any existing Invoice for the selected order,
        it will be <strong>voided</strong>, hence it cannot be paid
      </span>
  </p>

  <div class="col">
    <div class="mb-3">
      <label class="form-label" for="customer">Customer</label>
      <select name="customer" id="customer" class="form-control" required>
        <option value>Select Customer</option>
		<?php foreach ( $customers as $customer ) { ?>
          <option value="<?php echo $customer->ID; ?>">
			<?php echo $customer->display_name; ?>
          </option>
		<?php } ?>
      </select>
    </div>

    <div class="mb-3 hidden" id="user-loading">
      <div class="spinner-border" role="status">
        <span class="visually-hidden">Loading...</span>
      </div>
    </div>

    <div class="mb-3">
      <label for="orders" class="form-label">Order</label>
      <select name="orders" id="orders" class="form-control" required>
        <option value>Select Order</option>
      </select>
    </div>

    <div class="mb-3">
      <label for="amount" class="form-label">Amount</label>
      <input type="number" name="amount" class="form-control" id="amount" required min="1">
    </div>

    <button type="submit" id="save-invoice" class="btn btn-primary">
      Save
    </button>
  </div>
</div>

<script>
  window.addEventListener("DOMContentLoaded", function() {

    jQuery(document).ready(function($) {

      $("#customer").change(handleSelectChange);

      $("#post").submit(function(e) {
        e.preventDefault();

        const formData = {
          "user_id": $("#customer").val(),
          "order_id": $("#orders").val(),
          "amount": $("#amount").val()
        };

        $.ajax({
          url: '<?php echo get_rest_url( null, 'pwcore/v1/invoices' ) ?>',
          method: "POST",
          beforeSend: function(xhr) {
            xhr.setRequestHeader("X-WP-Nonce", window.wpApiSettings.nonce);
          },
          data: formData,
          success: function(data) {
            alert(data);
            window.location.href = "<?php echo admin_url(); ?>";
          },
          error: function(error) {
            alert(error?.responseJSON?.message || "Failed");
          }
        });
      });

      function handleSelectChange() {
        const userId = $(this).val();

        $("#user-loading").removeClass("hidden");

        $.ajax({
          type: "GET",
          url: '<?php echo get_rest_url( null, 'pwcore/v1/invoices?user_id=' ) ?>'
            + userId,
          success: function(data) {
            const selectData = data?.map(item => ({
              id: item.ID,
              title: item.post_title
            }));

            let output = [];

            $.each(selectData, function(key, value) {
              output.push("<option value=\"" + value.id + "\">" + value.title + "</option>");
            });
            output.unshift("<option value>Select Order</option>");

            $("#orders").html(output.join(""));

            $("#user-loading").addClass("hidden");
          },
          error: function(error) {
            alert(error?.responseJSON?.message || "Failed");
            $("#user-loading").addClass("hidden");
          }
        });
      }
    });
  });
</script>