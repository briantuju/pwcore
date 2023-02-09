<?php

// Get ALL meta data
$post_metas = get_post_meta( get_the_ID() );

// Get SINGLE meta data entry
$order_topic = get_post_meta( get_the_ID(), 'topic', true );

unset( $post_metas['_edit_last'] );
unset( $post_metas['_edit_lock'] );

echo "<h1>" . $order_topic . "</h1><br/>";
?>

<div class="d-flex flex-column gap-4 fs-6">
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
