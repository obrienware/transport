<?php
require_once 'autoload.php';

use Transport\{ Airport, Flight };

$rows = Flight::upcomingFlights();
?>
<h2>Upcoming Flight Statuses</h2>

<style>
  #flight-statuses {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
    gap: 1rem;
  }
</style>

<div id="flight-statuses">
  <?php foreach ($rows as $row): ?>
    <?php $flight = Flight::getFlightStatus($row->flight_number, $row->type, $row->iata, Date('Y-m-d', strtotime($row->target_datetime))); ?>
    <card class="card mb-3 text-bg-primary">
      <div class="card-header d-flex justify-content-between">
        <div>
          <?php if ($row->type === 'arrival'): ?>
            <i class="fa-duotone fa-solid fa-plane-arrival"></i>
          <?php endif;?>
          <?php if ($row->type === 'departure'): ?>
            <i class="fa-duotone fa-solid fa-plane-departure"></i>
          <?php endif;?>
          <span class="mx-3" style="font-weight:900"><?= $row->flight_number; ?></span> <?= $flight->airport_origin_iata ?> <i class="fa-duotone fa-solid fa-circle-arrow-right"></i> <?= $flight->airport_destination_iata ?>
        </div>
        <div class="badge bg-danger align-self-center"><?= Date('D M j', strtotime($row->pickup_date)); ?></div>
      </div>
      <div class="card-body text-bg-light">

        <div class="d-flex justify-content-between border-bottom mb-1">
          <div style="font-size:smaller; font-weight:200"><?=$row->guests;?></div>
          <div style="font-size:smaller; font-weight:200"><?=$row->driver;?></div>
        </div>

        <div class="d-flex justify-content-between border-bottom pb-1">
          <div class="align-self-center">
            <img src="/images/airlines/<?= $row->image_filename; ?>" class="img-fluid" style="max-height:30px">
          </div>
          <?php
          $statusClass = 'text-bg-secondary';
          if ($flight->status_icon === 'green') $statusClass = 'text-bg-success';
          if ($flight->status_icon === 'yellow') $statusClass = 'text-bg-warning';
          if ($flight->status_icon === 'red') $statusClass = 'text-bg-danger';
          ?>
          <div class="align-self-center rounded text-center <?= $statusClass; ?> p-2">
            <?php if ($flight->live): ?>
              <i class="<?=$statusClass?> fa-duotone fa-solid fa-radar fa-lg"></i>
            <?php endif;?>
            <div style="font-size:small"><?=$flight->status_text?></div>
          </div>
        </div>

        <div style="font-size:small" class="mt-2">
          <table class="table table-sm caption-top table-striped-columns">
            <caption class="text-center bg-body-secondary">Departure: <?=$flight->airport_origin?></caption>
            <tr>
              <th class="px-2 py-0">Scheduled</th>
              <td class="px-2 py-0 text-end"><?= $flight->scheduled_departure ? Date('g:ia', strtotime($flight->scheduled_departure)) : ''; ?></td>
            </tr>
            <tr>
              <th class="px-2 py-0">Estimated</th>
              <td class="px-2 py-0 text-end"><?= $flight->estimated_departure ? Date('g:ia', strtotime($flight->estimated_departure)) : ''; ?></td>
            </tr>
            <tr>
              <th class="px-2 py-0">Actual</th>
              <td class="px-2 py-0 text-end"><?= $flight->real_departure ? Date('g:ia', strtotime($flight->real_departure)) : ''; ?></td>
            </tr>
          </table>
        </div>

        <div style="font-size:small" class="mt-2">
          <table class="table table-sm caption-top table-striped-columns mb-0">
            <caption class="text-center bg-body-secondary">Arrival: <?=$flight->airport_destination?></caption>
            <tr>
              <th class="px-2 py-0">Scheduled</th>
              <td class="px-2 py-0 text-end"><?= $flight->scheduled_arrival ? Date('g:ia', strtotime($flight->scheduled_arrival)) : ''; ?></td>
            </tr>
            <tr>
              <th class="px-2 py-0">Estimated</th>
              <td class="px-2 py-0 text-end"><?= $flight->estimated_arrival ? Date('g:ia', strtotime($flight->estimated_arrival)) : ''; ?></td>
            </tr>
            <tr>
              <th class="px-2 py-0">Actual</th>
              <td class="px-2 py-0 text-end"><?= $flight->real_arrival ? Date('g:ia', strtotime($flight->real_arrival)) : ''; ?></td>
            </tr>
          </table>
        </div>

      </div>
      <div class="card-footer d-flex justify-content-between">
        <div>
          <div class="" style="font-size:small">
            Updated: <?=$flight->updated?>
          </div>
        </div>
      </div>
    </card>
  <?php endforeach; ?>
</div>