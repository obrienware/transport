<?php
require_once 'autoload.php';

use Transport\{ Config, Database };

$config = Config::get('organization');
// if ($config->alertUnconfirmedTrips === false) exit();

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
  .unconfirmed-items-container {
    display: grid;
    /* grid-template-columns: repeat(3, 1fr); */
    grid-template-columns: repeat(auto-fill, minmax(400px, 1fr));
    gap: 1rem;
    padding: 1rem;
  }
</style>

<?php if ($tripRows || $eventRows || $reservationRows): ?>

  <div class="mb-3 bg-warning rounded unconfirmed-items-container">

    <?php if ($tripRows): ?>
      <div class="card">
          <h5 class="card-header">Unconfirmed Trips</h5>
          <ul class="list-group list-group-flush">
            <?php foreach ($tripRows as $row): ?>
              <li class="list-group-item d-flex justify-content-between">
                <div>
                  <button class="btn p-0 text-start" onclick="$(document).trigger('loadMainSection', {sectionId: 'trips', url: 'section.edit-trip.php?id=<?=$row->id?>'})"><?=$row->summary?></button>
                </div>
                <div class="ms-2 badge bg-primary datetime align-self-center"><?=Date('D', strtotime($row->start_date))?></div>
              </li>
            <?php endforeach; ?>
          </ul>
        </div>
    <?php endif;?>

    <?php if ($eventRows): ?>
      <div class="card">
          <h5 class="card-header">Unconfirmed Events</h5>
          <ul class="list-group list-group-flush">
            <?php foreach ($eventRows as $row): ?>
              <li class="list-group-item d-flex justify-content-between">
                <div>
                  <button class="btn p-0 text-start" onclick="$(document).trigger('loadMainSection', {sectionId: 'events', url: 'section.edit-event.php?id=<?=$row->id?>'})"><?=$row->name?></button>
                </div>
                <div class="ms-2 badge bg-primary datetime align-self-center"><?=Date('D', strtotime($row->start_date))?></div>
              </li>
            <?php endforeach; ?>
          </ul>
        </div>
    <?php endif;?>

    <?php if ($reservationRows): ?>
      <div class="card">
          <h5 class="card-header">Unconfirmed Vehicle Reservations</h5>
          <ul class="list-group list-group-flush">
            <?php foreach ($reservationRows as $row): ?>
              <li class="list-group-item d-flex justify-content-between">
                <div>
                  <button class="btn p-0 text-start" onclick="$(document).trigger('loadMainSection', {sectionId: 'reservations', url: 'section.edit-reservation.php?id=<?=$row->id?>'})"><?=$row->guest?></button>
                </div>
                <div class="ms-2 badge bg-primary datetime align-self-center"><?=Date('D', strtotime($row->start_datetime))?></div>
              </li>
            <?php endforeach; ?>
          </ul>
        </div>      
    <?php endif; ?>

  </div>
<?php endif; ?>
