<?php
require_once 'autoload.php';

use Transport\Config;
use Transport\Database;

$config = Config::get('organization');
if ($config->alertUnconfirmedTrips === false) die();

$db = Database::getInstance();

// Check for upcoming trips that need to be confirmed
$query = "
SELECT * FROM trips 
WHERE 
  confirmed IS NULL
  AND end_date > NOW()
  AND start_date <= DATE_ADD(CURDATE(), INTERVAL 7 DAY) -- using a 7 day window for upcoming trips
  AND started IS NULL -- no sense in showing this if the trip has already started
  AND completed IS NULL -- or finished.
  AND archived IS NULL -- and not deleted
";
?>
<div class="row">
  <?php if ($rows = $db->get_rows($query)): ?>
    <div class="col-6">
      <div class="card mb-3">
        <h5 class="card-header">Upcoming Trips Not Yet Confirmed</h5>
        <div class="card-body bg-danger-subtle text-center">
          <sup>*</sup>Only once trips are confirmed do all relavent parties start receiving notifications
        </div>
        <ul class="list-group list-group-flush">
          <?php foreach ($rows as $row): ?>
            <li class="list-group-item d-flex justify-content-between">
              <div>
                <button class="btn p-0" onclick="app.openTab('edit-trip', 'Trip (edit)', 'section.edit-trip.php?id=<?=$row->id?>');"><?=$row->summary?></button>
              </div>
              <div class="ms-2 badge bg-primary datetime align-self-center"><?=Date('D', strtotime($row->start_date))?></div>
            </li>
          <?php endforeach; ?>
        </ul>
      </div>
    </div>
  <?php endif;?>

  <?php
  $query = "
    SELECT * FROM events 
    WHERE 
      confirmed IS NULL
      AND start_date > NOW()
      AND start_date <= DATE_ADD(CURDATE(), INTERVAL 7 DAY) -- using a 7 day window for upcoming trips
      AND archived IS NULL -- and not deleted
  ";
  ?>
  <?php if ($rows = $db->get_rows($query)): ?>
    <div class="col-6">
      <div class="card mb-3">
        <h5 class="card-header">Upcoming Events Not Yet Confirmed</h5>
        <div class="card-body bg-danger-subtle text-center">
          <sup>*</sup>Only once events are confirmed do all relavent parties start receiving notifications
        </div>
        <ul class="list-group list-group-flush">
          <?php foreach ($rows as $row): ?>
            <li class="list-group-item d-flex justify-content-between">
              <div>
                <button class="btn p-0" onclick="app.openTab('edit-event', 'Event (edit)', 'section.edit-event.php?id=<?=$row->id?>');"><?=$row->name?></button>
              </div>
              <div class="ms-2 badge bg-primary datetime align-self-center"><?=Date('D', strtotime($row->start_date))?></div>
            </li>
          <?php endforeach; ?>
        </ul>
      </div>
    </div>
  <?php endif;?>
</div>
