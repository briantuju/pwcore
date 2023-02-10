<?php

$faqs = get_posts( [
	'post_type'   => 'pw_faq',
	'post_status' => 'publish',
	'numberposts' => - 1
] );

?>

<div class="accordion accordion-flush" id="faq-accordion">
  <?php foreach ( $faqs as $faq ) { ?>
	<?php $id = $faq->ID; ?>

    <div class="accordion-item">
      <h2 class="accordion-header" id="<?php echo "heading" . $id; ?>">
        <button class="accordion-button" type="button" data-bs-toggle="collapse"
                data-bs-target="#<?php echo "collapse" . $id; ?>"
                aria-expanded="true" aria-controls="<?php echo "collapse" . $id; ?>">
		  <?php echo $faq->post_title; ?>
        </button>
      </h2>
      <div id="<?php echo "collapse" . $id; ?>" class="accordion-collapse collapse"
           aria-labelledby="<?php echo "heading" . $id; ?>"
           data-bs-parent="#faq-accordion">
        <div class="accordion-body">
		  <?php echo $faq->post_content; ?>
        </div>
      </div>
    </div>
  <?php } ?>
</div>
