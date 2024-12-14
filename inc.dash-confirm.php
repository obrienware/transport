<?php
require_once 'class.config.php';
$config = Config::get('system');
if ($config->alertUnconfirmedTrips === false) die();

require_once 'class.data.php';
if (!$db) $db = new data();

// Check for upcoming trips that need to be confirmed
$sql = "
SELECT * FROM trips 
WHERE 
  confirmed = 0
  AND end_date > NOW()
  AND start_date <= DATE_ADD(CURDATE(), INTERVAL 7 DAY) -- using a 7 day window for upcoming trips
  AND started IS NULL -- no sense in showing this if the trip has already started
  AND completed IS NULL -- or finished.
  AND archived IS NULL -- and not deleted
";
?>
<?php if ($rs = $db->get_results($sql)): ?>
  <div class="row">
    <div class="col-6">
      <div class="card mb-3">
        <h5 class="card-header">Upcoming Trips Not Yet Confirmed</h5>
        <div class="card-body bg-danger-subtle text-center">
          <sup>*</sup>Only once trips are confirmed do all relavent parties start receiving notifications
        </div>
        <ul class="list-group list-group-flush">
          <?php foreach ($rs as $item): ?>
            <li class="list-group-item d-flex justify-content-between">
              <div>
                <button class="btn p-0" onclick="app.openTab('edit-trip', 'Trip (edit)', 'section.edit-trip.php?id=<?=$item->id?>');"><?=$item->summary?></button>
              </div>
              <div class="ms-2 badge bg-primary datetime align-self-center"><?=Date('D', strtotime($item->start_date))?></div>
            </li>
          <?php endforeach; ?>
        </ul>
      </div>
    </div>
  </div>
<?php endif;?>