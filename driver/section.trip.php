<?php
require_once 'inc.components.php';
require_once 'inc.functions.php';
$tripId = $_REQUEST['id'];
include 'inc.trip-header.php';
$waypointCount = countWaypoints($tripId);
?>

<!-- start step indicators -->
<?php if ($waypointCount > 0): ?>
  <div class="mt-4 mb-2"><small class="text-muted">Trip Progress</small></div>
  <?=stepIndicator(0, $waypointCount);?>
<?php endif; ?>
<!-- end step indicators -->

<div class="row mt-5">
  <div class="col d-flex justify-content-between  align-items-end">
    <div>
      <button onclick="app.goHome()" class="btn btn-outline-primary btn-lg"><i class="fa-solid fa-house"></i></button>
    </div>
    <div>
      <?php if ($waypointCount > 0): ?>
        <div><small>Ready to drive?</small></div>
        <button id="btn-start" class="btn btn-primary btn-lg">Start Now</button>
      <?php endif; ?>
    </div>
  </div>
</div>

<script>

    $(async ƒ => {

      const tripId = <?=$tripId ?: 'null'?>;
      $('#btn-start').off('click').on('click', async ƒ => {
        const resp = await get('api/get.start-trip.php', {tripId});
        app.loadInitialPage();        
      });

    });

</script>