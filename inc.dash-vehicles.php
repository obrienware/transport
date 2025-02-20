<?php
require_once 'autoload.php';

use Transport\Database;

$db = Database::getInstance();
$query = "
  SELECT v.*, 
    CASE WHEN v.default_staging_location_id <> v.location_id AND v.location_id IS NOT NULL THEN l.name ELSE NULL END AS location,
    COUNT(s.id) AS snags
  FROM vehicles v
  LEFT OUTER JOIN locations l ON l.id = v.location_id
  LEFT JOIN snags s ON s.vehicle_id = v.id AND s.acknowledged IS NULL AND s.archived IS NULL
  WHERE 
    v.archived IS NULL 
    AND (
      v.check_engine = 1
      OR (v.default_staging_location_id <> v.location_id AND v.location_id IS NOT NULL)
      OR v.fuel_level <= 25
      OR v.clean_interior = 0
      OR v.clean_exterior = 0
      OR v.restock = 1
      OR s.id IS NOT NULL
    )
  GROUP BY v.id
  HAVING COUNT(s.id) > 0 OR (
    v.check_engine = 1
    OR (v.default_staging_location_id <> v.location_id AND v.location_id IS NOT NULL)
    OR v.fuel_level <= 25
    OR v.clean_interior = 0
    OR v.clean_exterior = 0
    OR v.restock = 1
  )
  ORDER BY v.name";
?>

<style>
  .vehicle-alerts-container {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
    gap: 1rem;
    padding: 1rem;
  }
</style>

<?php if ($rows = $db->get_rows($query)): ?>

  <div class="mb-3 bg-warning rounded vehicle-alerts-container">
    <h2 style="grid-column: 1 / -1; font-weight:100" class="mb-0">The following vehicles need your attention:</h2>
    <?php foreach ($rows as $row): ?>

        <div class="card">
          <div class="card-header" style="background-color:<?=$row->color?>">
            <button style="color: #<?=readableColor($row->color)?> !important" class="btn p-0" onclick="$(document).trigger('loadMainSection', {sectionId: 'vehicles', url: 'section.view-vehicle.php?id=<?=$row->id?>', forceReload: true})">
            <?=$row->name?>
            </button>
          </div>
          <ul class="list-group list-group-flush">

            <?php if ($row->check_engine): ?>
              <li class="list-group-item">
                <i class="fa-xl fa-solid fa-engine-warning fa-fw fa-fade me-2" style="color: orangered"></i> Check Engine
              </li>
            <?php endif; ?>

            <?php if (!is_null($row->fuel_level) && $row->fuel_level <= 25): ?>
              <li class="list-group-item">
                <i class="fa-xl fa-duotone fa-solid fa-gas-pump fa-fw" style="color: orangered"></i> Fuel Level: <?=fuelLevelAsFractions($row->fuel_level)?>
              </li>
            <?php endif;?>

            <?php if ($row->clean_interior === 0): ?>
              <li class="list-group-item">
                <i class="fa-xl fa-duotone fa-solid fa-vacuum fa-fw" style="color: cornflowerblue"></i> Needs cleaning
              </li>
            <?php endif; ?>

            <?php if ($row->clean_exterior === 0): ?>
              <li class="list-group-item">
                <i class="fa-xl fa-duotone fa-solid fa-car-wash fa-fw" style="color: cornflowerblue"></i> Needs cleaning
              </li>
            <?php endif; ?>

            <?php if ($row->restock === 1): ?>
              <li class="list-group-item">
                <i class="fa-xl fa-duotone fa-solid fa-bottle-water fa-fw" style="color: cornflowerblue"></i> Needs restocking
              </li>
            <?php endif; ?>

            <?php if ($row->location): ?>
              <li class="list-group-item">
                <i class="fa-xl fa-solid fa-location-xmark fa-fw" style="color: darkorange"></i> <?=$row->location?>
              </li>
            <?php endif; ?>

            <?php if ($row->snags > 0): ?>
              <li class="list-group-item">
                <i class="fa-xl fa-solid fa-exclamation-triangle fa-fw" style="color: darkorange"></i> <?=$row->snags?> Unacknowledged Snag(s)
              </li>
            <?php endif; ?>

            <li class="list-group-item next-trip" data-vehicle-id="<?=$row->id?>"></li>

          </ul>
        </div>

    <?php endforeach; ?>
  </div>


  <script type="module">
    import { get } from '/js/network.js';

    $('.next-trip').each(async function(e, a) {
      const vehicleId = $(a).data('vehicle-id');
      const nextTrip = await get('/api/get.next-trip.php', {id: vehicleId});
      if (nextTrip.starts === null) return $('#nextTripEventDetail').html('Nothing scheduled');
      const starts = moment(nextTrip.starts, 'YYYY-MM-DD H:mm:ss');

      $(this).html(
        `<div style="font-size: small" class="text-black-50">Next trip/event - ` +
        timeago.format(nextTrip.starts).toSentenceCase() + ' (' + starts.format('M/D h:mma') + ') ' 
        + `</div>`
        + `<div style="font-size: small" class="text-black-50"><i class="fa-solid fa-circle-right text-primary"></i> ${nextTrip.name}</div>`
      );
    });

  </script>

<?php endif;?>

<?php
function readableColor($bg)
{
  $bg = str_replace('#', '', $bg);
  $r = hexdec(substr($bg, 0, 2));
  $g = hexdec(substr($bg, 2, 2));
  $b = hexdec(substr($bg, 4, 2));

  $squared_contrast = (
    $r * $r * .299 +
    $g * $g * .587 +
    $b * $b * .114
  );

  if ($squared_contrast > pow(170, 2)) return '000000';
  return 'FFFFFF';
}

function fuelLevelAsFractions($fuel_level)
{
  if ($fuel_level <= 10) return 'Empty';
  if ($fuel_level <= 20) return '⅛';
  if ($fuel_level <= 30) return '¼';
  if ($fuel_level <= 40) return '⅜';
  if ($fuel_level <= 60) return '½';
  if ($fuel_level <= 80) return '¾';
  return 'Full';
}