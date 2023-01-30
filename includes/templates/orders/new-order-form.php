<div id="form-success" style="padding: 1rem; background-color: green; color: white; display: none;"></div>

<div id="form-errors" style="padding: 1rem; background-color: red; color: wheat; display: none;"></div>

<form method="post" id="new-order-form" class="pw_new_order_form">
  <?php wp_nonce_field('wp_rest') ?>

  <div class="pw_form_item">
    <label class="pw_form_label" for="topic">Topic</label>
    <input type="text" id="topic" name="topic" placeholder="Order Topic" />
  </div>

  <div class="pw_form_item">
    <label class="pw_form_label" for="description">Description</label>
    <textarea name="description" id="description" cols="30" rows="10" placeholder="Tell us about your order"></textarea>
  </div>

  <div class="pw_form_item">
    <label class="pw_form_label" for="deadline">Deadline</label>
    <input type="date" id="deadline" name="deadline" />
  </div>

  <div class="pw_form_item">
    <label class="pw_form_label" for="service_id">Service</label>
    <input type="text" id="service_id" name="service_id" />
  </div>

  <button type="submit">
    Continue
  </button>
</form>

<script>
  window.addEventListener("DOMContentLoaded", function() {
    jQuery(document).ready(function($) {

      $('#new-order-form').submit(function(event) {
        event.preventDefault();

        let form = $(this);

        $.ajax({
          type: "POST",
          url: '<?php echo get_rest_url(null, 'v1/orders/store') ?>',
          data: form.serialize(),
          success: function(data) {
            console.log(data);
            $("#form-success").html(data || "Success").fadeIn()
          },
          error: function(error) {
            $("#form-errors").html(error.responseJSON.message || "Failed").fadeIn()
          },
        })
      })

    })
  })
</script>