<?php
include 'inc.stdout-log.php';
require 'inc.user.php';
include 'inc.header.php';
include 'inc.menu.php';
?>
<div class="container-fluid mt-3">


<ul class="nav nav-tabs" id="myTab" role="tablist">
  <li class="nav-item" role="presentation">
    <button class="nav-link active" id="home-tab" data-bs-toggle="tab" data-bs-target="#home-tab-pane" type="button" role="tab" aria-controls="home-tab-pane" aria-selected="true">Home</button>
  </li>
</ul>
<div class="tab-content" id="myTabContent">
  <div class="tab-pane fade show active px-4 py-2" id="home-tab-pane" role="tabpanel" aria-labelledby="home-tab" tabindex="0">Home contents</div>
</div>

</div>

<script type="text/javascript">

  $(async ƒ => {
    $('#home-tab-pane').load('section.home.php');

    // ping every minute
    setInterval(async () => {
      const resp = await get('/api/ping.php');
      if (!resp.result) {
        location.reload();
      }
    }, 60 * 1000);
  });

</script>

<?php include 'inc.footer.php';