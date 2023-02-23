<?php

add_shortcode( 'pw_get_started_modal', 'pwcore_show_get_started_modal' );

function pwcore_show_get_started_modal( array|string $attr, $content ) {
  $args = shortcode_atts( [
	  'text' => 'Get started',
	  'id'   => 'blink'
  ], $attr );

  return '
<!-- Button trigger modal -->
<button id=' . $args["id"] . ' type="button" class="modal-btn" data-bs-toggle="modal" data-bs-target="#getStartedModal">
' . $args["text"] . '
</button>
<!-- Modal -->
<div class="modal fade" id="getStartedModal" tabindex="-1" aria-labelledby="getStartedModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h3 class="modal-title fs-5" id="getStartedModalLabel">Fill the form to get started</h3>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        ' . $content . '
      </div>
    </div>
  </div>
</div>';
}