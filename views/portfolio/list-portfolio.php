<?php

use PWCore\Services\PortfolioService;

$personal  = ( new PortfolioService )->query( 'personal' );
$companies = ( new PortfolioService )->query( 'company' );
?>

<!-- Tab links -->
<div class="tab">
  <button class="tablinks" data-tab="personal" id="defaultOpen">Personal Profile</button>
  <button class="tablinks" data-tab="company">Company Profile</button>
</div>

<!-- Tab content -->
<div id="personal" class="tabcontent">
  <h3>Personal</h3>
  <?php foreach ( $personal as $item ) { ?>
    <p><?php echo $item->post_title; ?></p>
  <?php } ?>
</div>

<div id="company" class="tabcontent">
  <h3>Company</h3>
  <?php foreach ( $companies as $item ) { ?>
    <p><?php echo $item->post_title; ?></p>
  <?php } ?>
</div>

<script>
  window.addEventListener("DOMContentLoaded", function() {
    setTimeout(() => {
      // Get the element with id="defaultOpen" and click on it
      document.getElementById("defaultOpen").click();
    }, 300);

    const tabLinks = document.querySelectorAll(".tablinks");

    tabLinks?.forEach(link => link.addEventListener("click", function(event) {
      console.log(link.dataset.tab);
      event.preventDefault();

      // Declare all variables
      let i, tabcontent, tablinks;

      // Get all elements with class="tabcontent" and hide them
      tabcontent = document.querySelectorAll(".tabcontent");
      for (i = 0; i < tabcontent.length; i++) {
        tabcontent[i].style.display = "none";
      }

      // Get all elements with class="tablinks" and remove the class "active"
      tablinks = document.getElementsByClassName("tablinks");
      for (i = 0; i < tablinks.length; i++) {
        tablinks[i].className = tablinks[i].className.replace(" active", "");
      }

      // Show the current tab, and add an "active" class to the button that opened the tab
      document.getElementById(link.dataset.tab).style.display = "block";
      link.className += " active";
    }));
  });
</script>