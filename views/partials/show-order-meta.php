<?php

// Get ALL meta data
use PWCore\Enums\OrderStatus;

$post_metas = get_post_meta( get_the_ID() );

// Get SINGLE meta data entry
$order        = get_post( get_the_ID() );
$order_topic  = get_post_meta( get_the_ID(), 'topic', true );
$order_status = get_post_meta( get_the_ID(), 'order_status', true );

unset( $post_metas['_edit_last'] );
unset( $post_metas['_edit_lock'] );
?>

<div class="d-flex flex-column gap-4 fs-6">
  <div class="row justify-content-between row-cols-1 row-cols-md-2">
    <div class="col">
      <h1><?php echo $order_topic; ?></h1>
    </div>

    <div class="col">
      <form method="post" id="order-status-form">
        <label for="status" class="form-label fw-bold">Order Status</label>
        <div class="d-flex flex-row gap-4">
          <select name="status" id="status" class="form-control d-inline-block">
			<?php foreach ( OrderStatus::values() as $status ) { ?>
              <option value="<?php echo $status; ?>" <?php if ( $order_status === $status )
				echo 'selected' ?>>
				<?php echo ucfirst( $status ); ?>
              </option>
			<?php } ?>
          </select>

          <button type="submit" class="btn btn-primary mt-2" id="update-status-btn">
            Update
          </button>
        </div>
      </form>
    </div>
  </div>

  <?php foreach ( $post_metas as $key => $value ) {
	switch ( $key ) {
	  case 'description':
		echo "<div><h5 style='margin-bottom: 0.25rem'>Order Description</h5>"
			 . "<div style='padding: 1rem; background-color:#fff; border: 1px solid #cecece'>"
			 . "$value[0]</div></div>";
		break;
	  case 'attachment':
		$url = get_post_meta( get_the_ID(), 'attachment', true )['url'];
		echo "<div><h5 style='margin-bottom: 0.5rem'>Attachment</h5>"
			 . "<a href=\"$url\" rel='noreferrer' target='_blank' style='max-width: max-content'>"
			 . "Download</a></div>";
		break;
	  case 'package_id':
		$package_id   = get_post_meta( get_the_ID(), 'package_id', true );
		$package      = get_post( $package_id );
		$package_meta = get_post_meta( $package_id, 'price', true );
		echo "<div><h5 style='margin-bottom: 0.5rem'>" . "Package" . "</h5>"
			 . "<div><span>$package?->post_title</span>"
			 . "<strong style='margin-left: 1rem'>($ $package_meta)</strong>"
			 . "</div></div>";
		break;
	  case 'order_number':
		echo "<div><h5 style='margin-bottom: 0.5rem'>" . "Order Number" . "</h5>"
			 . "$value[0] <br/></div>";
		break;
	  case 'order_status':
		echo "<div><h5 style='margin-bottom: 0.5rem'>" . "Order Status" . "</h5>"
			 . ucfirst( $value[0] ) . " <br/></div>";
		break;
	  default:
		break;
	}
  } ?>
</div>

<script>
  window.addEventListener("DOMContentLoaded", function() {
    jQuery(document).ready(function($) {
      $("#post").submit(function(e) {
        e.preventDefault();

        const newStatus = $("#status").val();
        const currentStatus = "<?php echo $order_status; ?>";
        if (newStatus === currentStatus) return;

        $("#update-status-btn").html(` <div class="spinner-border spinner-border-sm" role="status">
          <span class="visually-hidden">Loading...</span>
        </div>`);

        $.ajax({
          url: '<?php echo get_rest_url( null, 'pwcore/v1/orders?action=status_update' ) ?>',
          method: "POST",
          beforeSend: function(xhr) {
            xhr.setRequestHeader("X-WP-Nonce", window.wpApiSettings.nonce);
          },
          data: {
            "status": newStatus,
            "order_id": "<?php echo $order->ID; ?>"
          },
          success: function(data) {
            $("#update-status-btn").html("Update");
            alert(data);
          },
          error: function(error) {
            $("#update-status-btn").html("Update");
            alert(error?.responseJSON?.message || "Failed");
          }
        });
      });
    });
  });
</script>
