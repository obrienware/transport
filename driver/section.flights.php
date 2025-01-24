<?php
require_once '../autoload.php';

use Transport\Airport;
use Transport\Flight;

function showFlightsFor($iata, $type)
{
  $count = 0;
  if ($rows = Flight::upcomingFlights()) {
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
      echo '<div>';
      echo '<img src="/images/airlines/'.$row->image_filename.'" class="img-fluid" style="max-height:30px">';
      echo '</div>';

      echo '<div>';
      echo $flight->flight_number.' ';

      echo $flight->airport_origin_iata.' <i class="fa-solid fa-circle-arrow-right"></i> '.$flight->airport_destination_iata;
      echo '</div>';
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
      echo '<td>';
      // echo '<div class="d-flex justify-content-between">';
      echo '<div class="d-flex justify-content-between">';
      echo '<div>'.$row->guests.'</div>';
      echo '<div class="text-end text-muted align-self-center" style="white-space:nowrap">| '.$row->driver.'</div>';
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

  <?php if ($airports = Airport::getAll()): ?>
    <?php foreach ($airports as $airport): ?>
      <div class="row mb-4">
        <div class="col">
          <div class="card text-bg-primary overflow-hidden">
            <div class="card-header d-flex justify-content-between">
              <div><?=$airport->iata?>: <?=$airport->name?></div>
              <div><i class="fa-duotone fa-solid fa-plane-arrival"></i> ARRIVALS</div>
            </div>
            <table class="table mb-0" id="<?=$airport->iata?>-arrival">
              <?=showFlightsFor($airport->iata, 'arrival'); ?>
            </table>
          </div>
        </div>
      </div>
      <div class="row mb-4">
        <div class="col">
          <div class="card text-bg-primary overflow-hidden">
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