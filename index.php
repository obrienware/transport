<?php
require 'inc.user.php';
include 'inc.header.php';
include 'inc.menu.php';
?>
<div class="container-fluid mt-3">

<!-- <span class="loader"><span class="loader-inner"></span></span> -->
<!-- <a id="username" data-type="text" data-pk="1" data-url="/post" data-title="Enter username" data-mode="inline">superuser</a> -->

  <ul class="nav nav-tabs" id="myTab" role="tablist">
    <li class="nav-item" role="presentation">
      <button class="nav-link active" id="home-tab" data-bs-toggle="tab" data-bs-target="#home-tab-pane" type="button" role="tab" aria-controls="home-tab-pane" aria-selected="true">Home</button>
    </li>
  </ul>
  <div class="tab-content" id="myTabContent">
    <div class="tab-pane fade show active px-4 py-2" id="home-tab-pane" role="tabpanel" aria-labelledby="home-tab" tabindex="0">Home contents</div>
  </div>

</div><!-- .container-fluid -->

<script type="module">
  import { get } from '/js/network.js';
  // const usernameEl = document.getElementById('username');
  // const popover = new DarkEditable(usernameEl);

  $(async ƒ => {

    <?php if (array_search($_SESSION['view'], ['developer','manager']) !== false):?>
      $('#home-tab-pane').load('section.home-manager.php');
    <?php elseif (array_search($_SESSION['view'], ['driver']) !== false):?>
      $('#home-tab-pane').load('section.home-driver.php');
    <?php elseif (array_search($_SESSION['view'], ['requestor']) !== false):?>
      $('#home-tab-pane').load('section.home-requestor.php');
    <?php endif; ?>

    // ping every minute
    setInterval(async () => {
      const resp = await get('/api/ping.php');
      if (!resp.result) location.reload();
    }, 60 * 1000);

  });

</script>
<?php include 'inc.footer.php';