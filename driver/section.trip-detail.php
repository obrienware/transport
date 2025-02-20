<?php
require_once '../autoload.php';

use Transport\Database;
use Generic\InputHandler;

$id = InputHandler::getInt(INPUT_GET, 'id');
$db = Database::getInstance();
$query = "
SELECT 
  t.*,
  CASE WHEN t.eta IS NOT NULL THEN 'arrival' ELSE 'departure' END AS `type`,
  CASE WHEN t.eta IS NOT NULL THEN pu.iata ELSE do.iata END AS _iata,
  CONCAT(g.first_name,' ',g.last_name) AS guest, g.phone_number,
  CASE WHEN pu.short_name IS NULL THEN pu.name ELSE pu.short_name END AS pickup_from,
  CASE WHEN do.short_name IS NULL THEN do.name ELSE do.short_name END AS dropoff,
  v.name AS vehicle,
  a.flight_number_prefix, a.name as airline, a.image_filename,
  t.vehicle_pu_options, t.vehicle_do_options,
  pu.lat AS pu_lat, pu.lon AS pu_lon,
  do.lat AS do_lat, do.lon AS do_lon
FROM trips t
LEFT OUTER JOIN guests g ON g.id = t.guest_id
LEFT OUTER JOIN locations pu on pu.id = t.pu_location
LEFT OUTER JOIN locations do on do.id = t.do_location
LEFT OUTER JOIN vehicles v ON v.id = t.vehicle_id
LEFT OUTER JOIN airlines a ON a.id = t.airline_id
WHERE 
  t.id = :id
";
$params = ['id' => $id];
$trip = $db->get_row($query, $params);

$isIOS = strpos($_SERVER['HTTP_USER_AGENT'], 'iPhone') !== false || strpos($_SERVER['HTTP_USER_AGENT'], 'iPad') !== false;
if ($trip->pu_lat && $trip->pu_lon) {
  $mapUrlPickup = $isIOS ? "https://maps.apple.com/?daddr={$trip->pu_lat},{$trip->pu_lon}" : "https://www.google.com/maps/dir/?api=1&destination={$trip->pu_lat},{$trip->pu_lon}";
}
if ($trip->do_lat && $trip->do_lon) {
  $mapUrlDropoff = $isIOS ? "https://maps.apple.com/?daddr={$trip->do_lat},{$trip->do_lon}" : "https://www.google.com/maps/dir/?api=1&destination={$trip->do_lat},{$trip->do_lon}";
}
?>
<div class="row mb-2">
  <div class="col">
    <div class="card shadow">
      <div class="card-header  d-flex justify-content-between align-items-start">
        <div>Trip Info</div>
        <span class="badge bg-dark"><?=$trip->vehicle?></span>
      </div>
      <ul class="list-group list-group-flush">

        <!-- Start -->
        <li class="list-group-item d-flex justify-content-between align-items-center ps-2 bg-success-subtle">
          <i class="fa-solid fa-circle-arrow-right me-2"></i>
          <div class="flex-fill">
            <div class="d-flex justify-content-between align-items-center">
              <div class="fw-bolder">Start</div>
            </div>
            <div class="d-flex justify-content-between align-items-center">
              <div><?=$trip->vehicle_pu_options?></div>
              <small><?=Date('g:ia', strtotime($trip->start_date))?></small>
            </div>
          </div>
        </li>

        <!-- Pick up -->
        <li class="list-group-item d-flex justify-content-between align-items-center ps-2">
          <i class="fa-solid fa-arrow-up me-2"></i>
          <div class="flex-fill">
            <div class="d-flex justify-content-between align-items-center">
              <div class="fw-bolder"><?=$trip->guests?></div>
            </div>            
            <div class="d-flex justify-content-between align-items-center">
              <div>
                <?php if ($mapUrlPickup): ?>
                  <a href="<?=$mapUrlPickup?>" target="_blank" class="text-decoration-none">
                    <i class="fa-solid fa-location-arrow"></i>
                  </a>
                <?php endif; ?>
                <?=$trip->pickup_from?>
              </div>
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
                <div>
                  <?php if ($mapUrlDropoff): ?>
                    <a href="<?=$mapUrlDropoff?>" target="_blank" class="text-decoration-none">
                      <i class="fa-solid fa-location-arrow"></i>
                    </a>
                  <?php endif; ?>
                  <?=$trip->dropoff?>
                </div>
              </div>
              <section id="weather-at-dropoff-location"></section>
            </div>
          </div>
        </li>

        <!-- End -->
        <li class="list-group-item d-flex justify-content-between align-items-center ps-2 bg-primary-subtle">
          <i class="fa-solid fa-circle-arrow-left me-2"></i>
          <div class="flex-fill">
            <div class="d-flex justify-content-between align-items-center">
              <div class="fw-bolder">End</div>
            </div>
            <div class="d-flex justify-content-between align-items-center">
              <div><?=$trip->vehicle_do_options?></div>
              <!-- <small><?=Date('g:ia', strtotime($trip->start_date))?></small> -->
            </div>
          </div>
        </li>
      </ul>
    </div>


    <card class="card shadow mt-4">
      <div class="card-header">
        <?=$trip->type == 'arrival' ? 'Arrival' : 'Departure'?> Information
      </div>
      <ul class="list-group list-group-flush">

        <!-- Flight Information -->
        <?php if ($trip->flight_number): ?>
          <li class="list-group-item">
            <div class="d-flex justify-content-between align-items-center">
              <div class="me-4">
                <img src="/images/airlines/<?=$trip->image_filename?>" alt="<?=$trip->airline?>" class="img-fluid" />
              </div>
              <div class="text-end">
                <span style="font-size: large;color: gold;" class="badge bg-black"><?=$trip->flight_number_prefix.' '.$trip->flight_number?></span>
                <div id="status_text" class="badge w-100 nowrap"></div>
              </div>
            </div>

            <div class="d-flex justify-content-between align-items-center">
              <?php if ($trip->eta): ?>
                <div style="font-size: small" class="nowrap">Original ETA: <?=Date('g:ia', strtotime($trip->eta))?></div>
              <?php elseif ($trip->etd): ?>
                <div style="font-size: small" class="nowrap">Original ETD: <?=Date('g:ia', strtotime($trip->etd))?></div>
              <?php endif; ?>
              <div id="status_time" style="font-size: small" class="ms-auto nowrap"></div>
            </div>
          </li>
          <li class="list-group-item bg-body-secondary d-flex justify-content-between align-items-center py-1">
            <small id="flight-status-last-checked">?</small>
            <small id="flight-status-last-updated">?</small>
          </li>
        <?php endif; ?>
      </ul>
    </card>

    <!-- Driver Notes -->
    <?php if ($trip->driver_notes): ?>
      <card class="card shadow mt-4 text-bg-warning">
        <div class="card-header">
          Driver Notes
        </div>
        <div class="card-body">
          <?=nl2br($trip->driver_notes)?>
        </div>
      </card>
    <?php endif; ?>


  </div>
</div>
<div class="row mt-4">
  <div class="col text-end">
    <button class="btn btn-outline-primary px-4" onclick="showTripList()">
      <i class="fa-solid fa-chevron-left me-2"></i>
      Back
    </button>
  </div>
</div>

<script type="module">
  import * as input from '/js/formatters.js';
  import * as ui from '/js/notifications.js';
  import * as net from '/js/network.js';

  $(async ƒ => {
    const loadingWeatherTemplate = `
      <div class="bg-body-secondary mt-1 px-2 rounded">
        <img src="/images/ellipsis.svg" style="width:40px; min-height:40px" class="me-2" />
        Checking the weather...
      </div>
    `;
    const pickupDateTime = moment('<?=$trip->pickup_date?>', 'YYYY-MM-DD HH:mm:ss');
    if (pickupDateTime.isSame(moment(), 'day')) {
      // Trip is today
      $('#weather-at-pickup-location').html(loadingWeatherTemplate).load('section.header-weather.php?location_id=<?=$trip->pu_location?>');
      $('#weather-at-dropoff-location').html(loadingWeatherTemplate).load('section.header-weather.php?location_id=<?=$trip->do_location?>');
    }
    if (pickupDateTime.isBetween(moment(), moment().add(7, 'day'), 'day')) {
      // Within the next 7 days (forecast period)
      console.log('Forecast requested');
      const date = pickupDateTime.format('YYYY-MM-DD');
      $('#weather-at-pickup-location').html(loadingWeatherTemplate).load('section.header-weather.php?location_id=<?=$trip->pu_location?>&date=' + date);
      $('#weather-at-dropoff-location').html(loadingWeatherTemplate).load('section.header-weather.php?location_id=<?=$trip->do_location?>&date=' + date);
    }

    async function checkFlightStatus() {
      const flight = '<?=$trip->flight_number_prefix.$trip->flight_number?>';
      const type = '<?=$trip->type?>';
      const iata = '<?=$trip->_iata?>';
      const date = '<?=Date('Y-m-d', strtotime($trip->pickup_date))?>';
      const eta = '<?=Date('g:ia', strtotime($trip->pickup_date))?>';
      const etd = '<?=Date('g:ia', strtotime($trip->pickup_date))?>';
      const res = await net.get(`api/get.flight-status.php?flight=${flight}&type=${type}&iata=${iata}&date=${date}`);
      console.log(res);
      $('#flight-status-last-checked').html(`Checked: ${moment().format('h:mma')}`);
      $('#flight-status-last-updated').html(`Updated: ${moment(res.updated).format('h:mma')} (${moment(res.updated).fromNow()})`);


      $('#status_text').html(res.status_text);
      switch (res.status_icon) {
        case 'green':
          $('#status_text').css('background-color', 'green');
          break;
        case 'yellow':
          $('#status_text').css('background-color', 'orange');
          break;
        case 'red':
          $('#status_text').css('background-color', 'red');
          break;
        default:
          $('#status_text').css('background-color', 'gray');
      }

      const real_arrival = res.real_arrival ? moment(res.real_arrival, 'YYYY-MM-DD HH:mm:ss').format('h:mma') : 'unknown';
      const estimated_arrival = res.estimated_arrival ? moment(res.estimated_arrival, 'YYYY-MM-DD HH:mm:ss').format('h:mma') : 'unknown';
      const scheduled_arrival = res.scheduled_arrival ? moment(res.scheduled_arrival, 'YYYY-MM-DD HH:mm:ss').format('h:mma*') : eta;

      const real_departure = res.real_departure ? moment(res.real_departure, 'YYYY-MM-DD HH:mm:ss').format('h:mma') : 'unknown';
      const estimated_departure = res.estimated_departure ? moment(res.estimated_departure, 'YYYY-MM-DD HH:mm:ss').format('h:mma') : 'unknown';
      const scheduled_departure = res.scheduled_departure ? moment(res.scheduled_departure, 'YYYY-MM-DD HH:mm:ss').format('h:mma*') : etd;

      if (type === 'arrival') {
        if (res.real_arrival) return $('#status_time').html(`Actual: ${real_arrival}`);
        if (res.estimated_arrival) return $('#status_time').html(`Current ETA: ${estimated_arrival}`);
        return $('#status_time').html(`Now scheduled: ${scheduled_arrival}`);
      }
      if (res.real_departure) return $('#status_time').html(`Actual: ${real_departure}`);
      if (res.estimated_departure) return $('#status_time').html(`Current ETD: ${estimated_departure}`);
      return $('#status_time').html(`Now scheduled: ${scheduled_departure}`);
    }

    clearTimeout(window.flightCheckTimer);
    window.flightCheckTimer = setInterval(checkFlightStatus, 60000);
    checkFlightStatus();
  });
</script>
