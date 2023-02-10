<?php

add_shortcode( 'pw_get_started_modal', 'pwcore_show_get_started_modal' );

function pwcore_show_get_started_modal( array|string $attr, $content ) {
  $args = shortcode_atts( [
	  'form_short_code' => ''
  ], $attr );

  return '
<!-- Button trigger modal -->
<button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#getStartedModal">
Get started
</button>
<!-- Modal -->
<div class="modal fade" id="getStartedModal" tabindex="-1" aria-labelledby="getStartedModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h1 class="modal-title fs-5" id="getStartedModalLabel">Fill the form to get started</h1>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        ' . $content . '
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>';
}