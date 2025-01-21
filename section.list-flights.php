<?php
require_once 'autoload.php';

use Transport\Database;
use Transport\Flight;

$query = "
SELECT 
  t.summary, t.guests,
  t.pickup_date,
  CASE WHEN t.ETA IS NOT NULL THEN t.ETA ELSE t.ETD END AS target_datetime,
  CASE WHEN t.ETA IS NOT NULL THEN 'arrival' ELSE 'departure' END AS `type`,
  CASE WHEN t.ETA IS NOT NULL THEN a.iata ELSE b.iata END AS iata,
  CONCAT(l.flight_number_prefix, t.flight_number) AS flight_number,
  l.name AS airline,
  l.image_filename,
  d.first_name AS driver
FROM trips t
LEFT OUTER JOIN airlines l ON l.id = t.airline_id
LEFT OUTER JOIN locations a ON a.id = t.pu_location
LEFT OUTER JOIN locations b ON b.id = t.do_location
LEFT OUTER JOIN users d ON d.id = t.driver_id
WHERE
 	(t.eta IS NOT NULL OR t.etd IS NOT NULL)
	AND
	(t.eta IS NULL OR DATE(eta) >= CURDATE())
	AND
	(t.etd IS NULL OR DATE(etd) >= CURDATE())	
	AND t.archived IS NULL
  AND DATE(t.pickup_date) < DATE_ADD(CURDATE(), INTERVAL 7 DAY) -- Looking 7 days ahead
ORDER BY COALESCE(t.eta, t.etd) -- This is brilliant! Orders by either ETA OR ETD where the other is NULL!
";

function showFlightsFor($iata, $type)
{
  $db = Database::getInstance();
  global $query;
  $count = 0;
  if ($rows = $db->get_rows($query)) {
    foreach ($rows as $row) {
      if ($row->type !== $type) continue;
      if ($row->iata !== $iata) continue;
      if (!$row->flight_number) continue;
      $flight = Flight::getFlightStatus($row->flight_number, $row->type, $row->iata, Date('Y-m-d', strtotime($row->target_datetime)));
      if (!$flight->flight_number) continue; // exclude flights we didn't track.
      $count++;
      echo '<tr class="border-0 border-5 border-top">';

      echo '<td class="fit text-center align-middle border-bottom" rowspan="2">';
      echo '<div class="badge bg-danger">'.Date('M', strtotime($row->pickup_date)).'</div>';
      echo '<div>'.Date('D', strtotime($row->pickup_date)).'</div>';
      echo '<div class="fs-4 fw-bold font-monospace">'.Date('d', strtotime($row->pickup_date)).'</div>';
      echo '</td>';

      echo '<td class="border-bottom">';
      echo '<img src="/images/airlines/'.$row->image_filename.'" class="img-fluid" style="max-height:30px">';
      echo '</td>';

      echo '<td class="border-bottom align-middle">';
      echo $flight->flight_number;
      echo '</td>';

      echo '<td class="border-bottom align-middle">';
      echo $flight->airport_origin_iata.' <i class="fa-solid fa-circle-arrow-right"></i> '.$flight->airport_destination_iata;
      echo '</td>';

      $colorClass = '';
      if ($flight->status_icon === 'green') $colorClass = 'border-success';
      if ($flight->status_icon === 'yellow') $colorClass = 'border-warning';
      if ($flight->status_icon === 'red') $colorClass = 'border-danger';
      echo '<td rowspan="2" class="text-center align-middle border-0 border-5 border-start '.$colorClass.'">';
      if ($type === 'arrival') {
        if ($flight->real_arrival) echo '<div class="fw-bold">'.Date('g:ia', strtotime($flight->real_arrival)).'</div><div>Actual</div>';
        elseif ($flight->estimated_arrival) echo '<div class="fw-bold">'.Date('g:ia', strtotime($flight->estimated_arrival)).'</div><div>Estimated</div>';
        elseif ($flight->scheduled_arrival) echo '<div class="fw-bold">'.Date('g:ia', strtotime($flight->scheduled_arrival)).'</div><div>Scheduled</div>';
      }
      if ($type === 'departure') {
        if ($flight->real_departure) echo '<div class="fw-bold">'.Date('g:ia', strtotime($flight->real_departure)).'</div><div>Actual</div>';
        elseif ($flight->estimated_departure) echo '<div class="fw-bold">'.Date('g:ia', strtotime($flight->estimated_departure)).'</div><div>Estimated</div>';
        elseif ($flight->scheduled_departure) echo '<div class="fw-bold">'.Date('g:ia', strtotime($flight->scheduled_departure)).'</div><div>Scheduled</div>';        
      }
      echo '</td>';

      echo '</tr>';

      echo '<tr>';
      echo '<td colspan="3">';
      echo '<div class="d-flex justify-content-between">';
      echo '<div>'.$row->guests.'</div>';
      echo '<div class="text-muted">| '.$row->driver.'</div>';
      echo '</div>';
      echo '</td>';
      echo '</tr>';
    }
  }
  if ($count === 0) {
    echo '<tr>';
    echo '<td class="text-center">';
    echo 'No up-coming flights being tracked at this time';
    echo '</td>';
    echo '</tr>';
  }
}

?>
<div class="container-fluid mt-2">
  <h1>Flight Statuses</h1>

  <?php if ($airports = $db->get_rows("SELECT * FROM airports")): ?>
    <?php foreach ($airports as $airport): ?>
      <div class="row mb-4">
        <div class="col">
          <div class="card text-bg-primary">
            <div class="card-header d-flex justify-content-between">
              <div><?=$airport->iata?>: <?=$airport->name?></div>
              <div><i class="fa-duotone fa-solid fa-plane-arrival"></i> ARRIVALS</div>
            </div>
            <table class="table mb-0" id="<?=$airport->iata?>-arrival">
              <?=showFlightsFor($airport->iata, 'arrival'); ?>
            </table>
          </div>
        </div>

        <div class="col">
          <div class="card text-bg-primary">
            <div class="card-header d-flex justify-content-between">
              <div><?=$airport->iata?>: <?=$airport->name?></div>
              <div><i class="fa-duotone fa-solid fa-plane-departure"></i> DEPARTURES</div>
            </div>
            <table class="table mb-0" id="<?=$airport->iata?>-departure">
              <?=showFlightsFor($airport->iata, 'departure'); ?>
            </table>
          </div>
        </div>
      </div>
    <?php endforeach; ?>
  <?php endif;?>

</div>