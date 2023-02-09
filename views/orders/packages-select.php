<?php

$packages = get_posts( [
	'post_type'   => 'pw_packages',
	'post_status' => 'publish',
	'numberposts' => - 1
] );
?>

<div class="wider container my-4">
  <div
    class="p-4 shadow-lg rounded-3 d-flex flex-column flex-md-row justify-content-between align-items-center gap-4 mb-5">
    <span>
      To create your order, select a package below, then click the continue button
    </span>

    <button type="button" class="btn btn-success max-w-xs" disabled id="continue-btn">
      Continue
    </button>
  </div>

  <div class="package_grid">
	<?php foreach ( $packages as $package ) { ?>
      <div
        class="package_container <?php if ( $package->featured ) {
		  echo "package_container_featured";
		} ?>"
        id="<?php echo $package->post_title; ?>"
        data-package_id="<?php echo $package->ID; ?>"
      >
        <div class="package_header">
          <h3 class="package_title"><?php echo $package->post_title; ?></h3>

          <div>
            <span>$ <?php echo $package->price; ?> </span>
          </div>
        </div>

        <div class="package_content">
          <div><?php echo $package->post_content; ?></div>
        </div>

        <div>
          <button class="btn btn-primary package-submit" data-title="<?php echo $package->post_title; ?>">
            Get Started
          </button>
        </div>

		<?php if ( $package->featured ) { ?>
          <div class="package_container_extra">
            Most Popular
          </div>
		<?php } ?>
      </div>
	<?php } ?>
  </div>
</div>

<script>
  window.addEventListener("DOMContentLoaded", function() {
    // Get DOM elements
    const submitBtns = document.querySelectorAll(".package-submit");

    // Handle click events
    submitBtns?.forEach(btn => {
      btn.addEventListener("click", function(event) {
        const id = event.currentTarget.dataset?.title;
        const pkg = document.getElementById(id);

        // Reset other instance before applying styles
        resetStyles(id);
        pkg?.classList.toggle("package_container_active");

        // Enable the continue button
        updateContinueButton();

        // Scroll to top
        window.scrollTo({ top: 0, behavior: "smooth" });
      });
    });

    const updateContinueButton = () => {
      const continueBtn = document.querySelector("#continue-btn");
      const pkg = document.querySelector(".package_container_active");

      continueBtn.disabled = !pkg;
    };

    const resetStyles = (id) => {
      const pkgs = document.querySelectorAll(".package_container");
      pkgs.forEach(pkg => {
        if (pkg.id !== id) pkg.classList.remove("package_container_active");
      });
    };
  });

</script>