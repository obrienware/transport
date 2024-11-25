<?php
require_once 'inc.functions.php';
require_once 'inc.components.php';
$waypointCount = countWaypoints($tripId);
?>
<?=getTripHeader($tripId);?>
<?php if ($waypointCount > 0): ?>

  <?php $seq = getCurrentWaypointSequence($tripId); ?>
  <div class="mt-4 mb-2"><small class="text-muted">Trip Progress</small></div>
  <?=stepIndicator($seq, $waypointCount)?>
  <?php $wp = getWaypoint($tripId, $seq); ?>

  <div class="card border-primary mt-3">
    <div class="card-header bg-primary text-light">WAYPOINT <?=$seq +1?>: <?=$wp->description?></div>
    <div class="card-body">
      <h5 class="card-title"><?=$wp->location?></h5>
      <div class="mt-3 d-flex justify-content-around">
        <button id="btn-cancel-trip" class="btn btn-outline-danger">Cancel</button>
        <a href="https://maps.apple.com/?q=<?=$wp->lat?>,<?=$wp->lon?>" target="map" class="btn btn-outline-primary"><i class="fa fa-map-location-dot"></i></a>
        <button id="btn-complete-waypoint" class="btn btn-primary">Destination Reached</button>
      </div>
    </div>
  </div>

  <script>

    $(async ƒ => {

      const tripId = <?=$tripId ?: 'null'?>;
      const seq = '<?=$seq?>';
      $('#btn-complete-waypoint').off('click').on('click', async ƒ => {
        const resp = await get('api/get.complete-waypoint.php', {tripId, seq});
        if (app.debug) console.log('resp:', resp);
        console.log('resp.complete:', resp.complete);
        if (resp.complete) return app.postTripSurvey(tripId);
        app.loadInitialPage();
      });

    });

  </script>  

<?php else: ?>

  <div class="row mt-5">
    <div class="col d-flex justify-content-between  align-items-end">
      <div>
        <button class="btn btn-outline-danger btn-lg">Cancel</button>
      </div>
      <div>
        <button class="btn btn-primary btn-lg">Complete Now</button>
      </div>
    </div>
  </div>

<?php endif;?>
