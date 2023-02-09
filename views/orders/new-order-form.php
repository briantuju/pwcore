<?php include_once PW_PLUGIN_PATH . '/views/orders/packages-select.php'; ?>

<style>
    .offcanvas-end {
        width: 100%;
    }

    @media (min-width: 1280px) {
        .wider {
            min-width: 1024px;
        }

        .offcanvas {
            min-width: 900px;
        }
    }
</style>

<div class="offcanvas offcanvas-end" data-bs-backdrop="static" tabindex="-1" id="offcanvas"
     aria-labelledby="offcanvasLabel">
  <div class="offcanvas-header">
    <h5 class="offcanvas-title" id="offcanvasLabel">Create Order</h5>
    <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
  </div>

  <div class="offcanvas-body">
    <form method="post" id="new-order-form" class="p-4 bg-white rounded-4 border mx-auto"
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

      <button type="submit" class="btn btn-primary" id="submit-order-btn">
        Continue
      </button>
    </form>
  </div>
</div>

<script>
  window.addEventListener("DOMContentLoaded", function() {
    const continueBtn = document.querySelector("#continue-btn");
    const offcanvasEl = document.getElementById("offcanvas");

    // Show package offcanvas on click
    continueBtn?.addEventListener("click", function(e) {
      e.stopPropagation();
      const bsOffcanvas = new window.bootstrap.Offcanvas(offcanvasEl);
      bsOffcanvas?.show();
    });

    jQuery(document).ready(function($) {

      $("#new-order-form").submit(function(event) {
        event.preventDefault();

        // Check to confirm a package was selected
        const pkg = document.querySelector(".package_container_active");
        if (!pkg) {
          return alert("Please select a package");
        }

        $("#submit-order-btn").html(` <div class="spinner-border" role="status">
          <span class="visually-hidden">Loading...</span>
        </div>`);

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
            $("#submit-order-btn").html(`Continue`);
            alert("Order Created");
            window.location.reload();
            // $("#form-success").html(data || "Success").show().fadeIn();
          },
          error: function(error) {
            $("#submit-order-btn").html(`Continue`);
            alert(error?.responseJSON || "Failed");
            // $("#form-errors").html(error?.responseJSON?.message || "Failed").fadeIn();
          }
        });
      });

    });
  });
</script>