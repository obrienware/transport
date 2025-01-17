<?php
date_default_timezone_set($_ENV['TZ'] ?: 'America/Denver');
require_once 'class.flight.php';
require_once 'class.data.php';
$db = data::getInstance();
$query = "
SELECT 
  t.*,
  CASE WHEN t.eta IS NOT NULL THEN 'arrival' ELSE 'departure' END AS `type`,
  CASE WHEN t.eta IS NOT NULL THEN pu.iata ELSE do.iata END AS _iata,
  CONCAT(g.first_name,' ',g.last_name) AS guest, g.phone_number,
  CASE WHEN pu.short_name IS NULL THEN pu.name ELSE pu.short_name END AS pickup_from,
  CASE WHEN do.short_name IS NULL THEN do.name ELSE do.short_name END AS dropoff,
  v.name AS vehicle,
  a.flight_number_prefix, a.name as airline, a.image_filename
FROM trips t
LEFT OUTER JOIN guests g ON g.id = t.guest_id
LEFT OUTER JOIN locations pu on pu.id = t.pu_location
LEFT OUTER JOIN locations do on do.id = t.do_location
LEFT OUTER JOIN vehicles v ON v.id = t.vehicle_id
LEFT OUTER JOIN airlines a ON a.id = t.airline_id
WHERE 
  t.id = :id
";
$params = ['id' => $_REQUEST['id']];
$trip = $db->get_row($query, $params);
?>
<div class="row mb-2">
  <div class="col">
    <div class="card shadow">
      <div class="card-header  d-flex justify-content-between align-items-start">
        <div>Trip Info</div>
        <span class="badge bg-dark"><?=$trip->vehicle?></span>
      </div>
      <ul class="list-group list-group-flush">

        <!-- Pick up -->
        <li class="list-group-item d-flex justify-content-between align-items-center ps-2">
          <i class="fa-solid fa-arrow-up me-2"></i>
          <div class="flex-fill">
            <div class="d-flex justify-content-between align-items-center">
              <div class="fw-bolder"><?=$trip->guests?></div>
            </div>            
            <div class="d-flex justify-content-between align-items-center">
              <div><?=$trip->pickup_from?></div>
              <small><?=Date('g:ia', strtotime($trip->pickup_date))?></small>
            </div>            
            <div class="d-flex justify-content-between align-items-center">
              <div class="fw-lighter"><?=$trip->guest?></div>
              <small><a class="btn btn-sm btn-primary py-0" href="tel:<?=$trip->phone_number?>"><?=$trip->phone_number?></a></small>
            </div>
            <section id="weather-at-pickup-location"></section>
          </div>
        </li>

        <!-- Drop off -->
        <li class="list-group-item ps-2">
          <div class="d-flex justify-content-between align-items-center">
            <i class="fa-solid fa-arrow-down me-2"></i>
            <div class="flex-fill">
              <div class="d-flex justify-content-between align-items-center">
                <div><?=$trip->dropoff?></div>
              </div>
              <section id="weather-at-dropoff-location"></section>
            </div>
          </div>
        </li>

        <?php if ($trip->flight_number): ?>
          <li class="list-group-item d-flex justify-content-between align-items-center">
            <div class="me-4">
              <img src="/images/airlines/<?=$trip->image_filename?>" alt="<?=$trip->airline?>" class="img-fluid" />
            </div>
            <div>
              <span style="font-size: large" class="badge bg-info"><?=$trip->flight_number_prefix.' '.$trip->flight_number?></span>
              <?php $flight = Flight::getFlightStatus($trip->flight_number_prefix.$trip->flight_number, $trip->type, $trip->_iata, Date('Y-m-d', strtotime($trip->pickup_date))); ?>
              <?php
              $backgroundColor = 'gray';
              switch ($flight->icon) {
                case 'green':
                  $backgroundColor = 'green';
                  break;
                case 'yellow':
                  $backgroundColor = 'orange';
                  break;
                case 'red':
                  $backgroundColor = 'red';
                  break;
              }
              ?>
              <div class="badge w-100" style="background-color: <?=$backgroundColor?>">
                <?=$flight->status_text ?: 'scheduled'?>
              </div>
              <div style="font-size: small">
                <?php if ($trip->type === 'arrival'): ?>
                  <?php if ($flight->real_arrival): ?>
                    ATA: <?=Date('g:ia', strtotime($flight->real_arrival))?> 
                  <?php elseif ($flight->estimated_arrival): ?>
                    ETA: <?=Date('g:ia', strtotime($flight->estimated_arrival))?>
                  <?php else: ?>
                    STA: <?=Date('g:ia', strtotime($flight->scheduled_arrival ?: $trip->eta))?>
                  <?php endif; ?>
                <?php elseif ($trip->type === 'departure'): ?>
                  <?php if ($flight->real_departure): ?>
                    ATD: <?=Date('g:ia', strtotime($flight->real_departure))?> 
                  <?php elseif ($flight->estimated_departure): ?>
                    ETD: <?=Date('g:ia', strtotime($flight->estimated_departure))?>
                  <?php else: ?>
                    STD: <?=Date('g:ia', strtotime($flight->scheduled_departure ?: $trip->etd))?>
                  <?php endif; ?>
                <?php endif;?>
              </div>
            </div>
          </li>
        <?php endif; ?>

        <?php if ($trip->driver_notes): ?>
          <li class="list-group-item bg-warning">
            <?=nl2br($trip->driver_notes)?>
          </li>
        <?php endif; ?>

      </ul>
    </div>
  </div>
</div>
<div class="row">
  <div class="col text-end">
    <button class="btn btn-primary" onclick="showTripList()">Back</button>
  </div>
</div>

<script>
  $(async ƒ => {
    const pickupDateTime = moment('<?=$trip->pickup_date?>', 'YYYY-MM-DD HH:mm:ss');
    if (pickupDateTime.isSame(moment(), 'day')) {
      // Trip is today
      $('#weather-at-pickup-location').load('section.header-weather.php?location_id=<?=$trip->pu_location?>');
      $('#weather-at-dropoff-location').load('section.header-weather.php?location_id=<?=$trip->do_location?>');
    }
    if (pickupDateTime.isBetween(moment(), moment().add(7, 'day'), 'day')) {
      // Within the next 7 days (forecast period)
      const date = pickupDateTime.format('YYYY-MM-DD');
      $('#weather-at-pickup-location').load('section.header-weather.php?location_id=<?=$trip->pu_location?>&date=' + date);
      $('#weather-at-dropoff-location').load('section.header-weather.php?location_id=<?=$trip->do_location?>&date=' + date);
    }
  });
</script>
