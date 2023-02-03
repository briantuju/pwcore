<?php include_once PW_PLUGIN_PATH . '/views/orders/packages-select.php'; ?>

<style>
    @media (min-width: 1280px) {
        .wider {
            min-width: 1024px;
        }
    }
</style>

<div class="wider container">
  <div class="row row-cols-1 row-cols-md-2 gap-4">
    <div class="col">
      <form method="post" id="new-order-form" class="p-4 bg-white rounded-4 border max-w-lg mx-auto"
            enctype="multipart/form-data">
        <div class="mb-3">
          <label class="form-label" for="topic">Topic</label>
          <input type="text" id="topic" class="form-control" name="topic" placeholder="Order Topic" required />
        </div>

        <div class="mb-3">
          <label class="form-label" for="description">Description</label>
          <textarea name="description" class="form-control" id="description" cols="30" rows="10"
                    placeholder="Tell us about your order"
                    required></textarea>
        </div>

        <div class="mb-3">
          <label class="form-label" for="deadline">Deadline</label>
          <input type="date" id="deadline" class="form-control" name="deadline" required />
        </div>

        <div class="mb-3">
          <label class="form-label" for="attachment">Attachment</label>
          <input type="file" id="attachment" class="form-control" name="attachment" required />
        </div>

        <button type="submit" class="btn btn-primary">
          Continue
        </button>
      </form>
    </div>
  </div>
</div>

<script>
  window.addEventListener("DOMContentLoaded", function() {
    jQuery(document).ready(function($) {

      $("#new-order-form").submit(function(event) {
        event.preventDefault();

        // Check to confirm a package was selected
        const pkg = document.querySelector(".package_container_active");
        if (!pkg) {
          return alert("Please select a package");
        }

        let fd = new FormData(this);
        fd.append("package_id", pkg.dataset["package_id"]);

        $.ajax({
          type: "POST",
          url: '<?php echo get_rest_url( null, 'pwcore/v1/orders' ) ?>',
          data: fd,
          beforeSend: function(xhr) {
            xhr.setRequestHeader("X-WP-Nonce", window.WPNonce?.nonce);
          },
          contentType: false,
          processData: false,
          success: function(data) {
            alert("Order Created");
            window.location.reload();
            // $("#form-success").html(data || "Success").show().fadeIn();
          },
          error: function(error) {
            alert(error?.responseJSON?.message || "Failed");
            // $("#form-errors").html(error?.responseJSON?.message || "Failed").fadeIn();
          }
        });
      });

    });
  });
</script>