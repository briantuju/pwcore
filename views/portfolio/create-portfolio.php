<?php

pwcore_needs_login();

$image = get_post_meta( $post->ID, 'image', true );

?>

<form enctype="multipart/form-data" method="post">
  <div class="row row-cols-1 gap-4" style="max-width: 420px">
    <div class="col">
      <div class="mb-3">
        <label for="name" class="form-label">Name</label>
        <input type="text" name="name" class="form-control" id="name" required value="<?php echo $post->post_title; ?>">
      </div>
      <div class="mb-3">
        <label for="url" class="form-label">Wikipedia Url</label>
        <input type="text" name="url" class="form-control" id="url" required value="<?php echo $post->url; ?>">
      </div>
      <div class="mb-3">
        <label for="type" class="form-label">Portfolio Type</label>
        <select name="type" id="type" required class="form-control">
          <option value="personal">Personal</option>
          <option value="company">Company</option>
        </select>
      </div>
      <div class="mb-3">
        <label for="image" class="form-label">Image</label>
        <input type="file" name="image" class="form-control" id="image"
			<?php if ( ! $post->post_title ) {
			  echo "required";
			} ?>
        >
      </div>
	  <?php if ( $post->image ) { ?>
        <div class="mb-3">
          <img src="<?php echo $image['url'] ?>" alt="<?php echo $post->post_title ?>">
        </div>
	  <?php } ?>
      <button type="submit" class="btn btn-primary" id="submit-portfolio-btn">
        Save
      </button>
    </div>
  </div>
</form>

<script>
  window.addEventListener("DOMContentLoaded", function() {
    const post = <?= json_encode( $post ) ?>;

    jQuery(document).ready(function($) {
      $("#post").submit(function(e) {
        e.preventDefault();

        $("#submit-portfolio-btn").html(` <div class="spinner-border" role="status">
          <span class="visually-hidden">Loading...</span>
        </div>`);

        let fd = new FormData();
        fd.append("name", $("#name").val());
        fd.append("url", $("#url").val());
        fd.append("type", $("#type").val());
        fd.append("image", $("#image")[0].files[0]);
        if (post?.post_title) {
          fd.append("ID", post.ID);
        }

        $.ajax({
          url: '<?php echo get_rest_url( null, 'pwcore/v1/portfolio' ) ?>',
          method: "POST",
          beforeSend: function(xhr) {
            xhr.setRequestHeader("X-WP-Nonce", window.wpApiSettings.nonce);
          },
          data: fd,
          processData: false,
          contentType: false,
          success: function() {
            window.location.reload();
          },
          error: function(error) {
            alert(error?.responseJSON?.message || "Failed");
          }
        });
      });
    });
  });
</script>