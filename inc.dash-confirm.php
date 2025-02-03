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
$tripRows = $db->get_rows($query);

$query = "
SELECT * FROM events 
WHERE 
  confirmed IS NULL
  AND start_date > NOW()
  AND start_date <= DATE_ADD(CURDATE(), INTERVAL 7 DAY) -- using a 7 day window for upcoming trips
  AND archived IS NULL -- and not deleted
";
$eventRows = $db->get_rows($query);

$query = "
SELECT 
  r.*,
  CONCAT(g.first_name, ' ', g.last_name) AS guest
FROM vehicle_reservations r
LEFT OUTER JOIN guests g ON r.guest_id = g.id
WHERE 
  r.confirmed IS NULL
  AND r.end_datetime > NOW()
  AND r.start_datetime <= DATE_ADD(CURDATE(), INTERVAL 7 DAY) -- using a 7 day window for upcoming trips
  AND r.archived IS NULL -- and not deleted
";
$reservationRows = $db->get_rows($query);


?>

<style>
  #unconfirmed-items-container {
    display: grid;
    /* grid-template-columns: repeat(3, 1fr); */
    grid-template-columns: repeat(auto-fill, minmax(400px, 1fr));
    gap: 1rem;
  }
</style>

<?php if ($tripRows || $eventRows || $reservationRows): ?>

  <div id="unconfirmed-items-container" class="mb-3">

    <div class="card text-bg-danger">
      <h5 class="card-header"><i class="fa-solid fa-circle-exclamation"></i> Unconfirmed Items</h5>
      <div class="card-body text-bg-warning">
        <p>There are upcoming items that have not yet been confirmed. Please review and confirm these items as soon as possible.</p>
        <p class="mb-0">Only once trips, events, or vehicle reservations are confirmed do all relavent parties start receiving notifications.</p>
      </div>
    </div>

    <?php if ($tripRows): ?>
      <div class="card">
          <h5 class="card-header">Trips</h5>
          <ul class="list-group list-group-flush">
            <?php foreach ($tripRows as $row): ?>
              <li class="list-group-item d-flex justify-content-between">
                <div>
                  <button class="btn p-0" onclick="app.openTab('edit-trip', 'Trip (edit)', 'section.edit-trip.php?id=<?=$row->id?>');"><?=$row->summary?></button>
                </div>
                <div class="ms-2 badge bg-primary datetime align-self-center"><?=Date('D', strtotime($row->start_date))?></div>
              </li>
            <?php endforeach; ?>
          </ul>
        </div>
    <?php endif;?>

    <?php if ($eventRows): ?>
      <div class="card">
          <h5 class="card-header">Events</h5>
          <ul class="list-group list-group-flush">
            <?php foreach ($eventRows as $row): ?>
              <li class="list-group-item d-flex justify-content-between">
                <div>
                  <button class="btn p-0" onclick="app.openTab('edit-event', 'Event (edit)', 'section.edit-event.php?id=<?=$row->id?>');"><?=$row->name?></button>
                </div>
                <div class="ms-2 badge bg-primary datetime align-self-center"><?=Date('D', strtotime($row->start_date))?></div>
              </li>
            <?php endforeach; ?>
          </ul>
        </div>
    <?php endif;?>

    <?php if ($reservationRows): ?>
      <div class="card">
          <h5 class="card-header">Vehicle Reservations</h5>
          <ul class="list-group list-group-flush">
            <?php foreach ($reservationRows as $row): ?>
              <li class="list-group-item d-flex justify-content-between">
                <div>
                  <button class="btn p-0" onclick="app.openTab('edit-reservation', 'Reservation (edit)', 'section.edit-reservation.php?id=<?=$row->id?>');"><?=$row->guest?></button>
                </div>
                <div class="ms-2 badge bg-primary datetime align-self-center"><?=Date('D', strtotime($row->start_datetime))?></div>
              </li>
            <?php endforeach; ?>
          </ul>
        </div>      
    <?php endif; ?>

  </div>
<?php endif; ?>
