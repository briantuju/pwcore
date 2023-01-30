<form method="post" id="new-order-form" class="pw_new_order_form">
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