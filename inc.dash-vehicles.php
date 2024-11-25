<?php
require_once 'class.data.php';
if (!$db) $db = new data();

$sql = "
  -- This really cool MySQL 8 feature allows us to get the most recent record for each vehicle
  WITH recent_locations AS (
    SELECT vl.*, ROW_NUMBER() OVER (PARTITION BY vehicle_id ORDER BY datetimestamp DESC) AS most_recent
    FROM vehicle_locations AS vl
  )

  SELECT 
    v.id, v.color, v.name, 
    l.driver_id, l.location_id, l.fuel_level, l.mileage, l.clean_exterior, l.clean_interior, l.needs_restocking, l.datetimestamp, l.concerns,
    CASE WHEN lo.short_name IS NULL THEN lo.name ELSE lo.short_name END AS location
  FROM vehicles v
  LEFT OUTER JOIN (
    SELECT * FROM recent_locations r WHERE r.most_recent = 1
  ) l ON l.vehicle_id = v.id
  LEFT OUTER JOIN locations lo ON lo.id = l.location_id
  WHERE
    v.archived IS NULL
";

$list = [];
if ($rs = $db->get_results($sql)) {
  foreach ($rs as $item) {
    // We're only interested in conditions that need our attention
    if ($item->fuel_level && $item->fuel_level <= 20) {
      $list[] = $item;
      continue;
    }
    if ($item->clean_exterior === 0) {
      $list[] = $item;
      continue;
    }
    if ($item->clean_interior === 0) {
      $list[] = $item;
      continue;
    }
    if ($item->needs_restocking === 1) {
      $list[] = $item;
      continue;
    }
  }
}
?>
<?php if (count($list) > 0): ?>
  <div class="card text-bg-warning mb-4">
    <h5 class="card-header overflow-hidden">
      Vehicles needing attention!
    </h5>
    <table class="table table-sm mb-0">
      <thead>
        <tr class="align-middle table-primary">
          <th rowspan="2" class="text-center">As of</th>
          <th rowspan="2" class="text-center">Vehicle</th>
          <th rowspan="2" class="text-center">Location</th>
          <th colspan="4" class="text-center">Needs</th>
          <th rowspan="2" class="text-center">Concerns</th>
        </tr>
        <tr class="table-primary">
          <th class="text-center">Fuel</th>
          <th class="text-center">Clean (ext)</th>
          <th class="text-center">Clean (int)</th>
          <th class="text-center">Restock</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($list as $item): ?>
          <tr>
            <td class="datetime short"><?=$item->datetimestamp?></td>
            <td>
              <button class="btn p-0" onclick="app.openTab('edit-vehicle', 'Vehicle', 'section.view-vehicle.php?id=<?=$item->id?>');"><?=$item->name?></button>
            </td>
            <td><?=$item->location?></td>
            <td class="text-center">
              <?php if ($item->fuel_level == null): ?>
                -
              <?php else: ?>
                <?php if ($item->fuel_level <= 20): ?>
                  <span class="badge bg-danger fs-6"><?=$item->fuel_level?>%</span>
                <?php else:?>
                  <?=$item->fuel_level?>%
                <?php endif; ?>
              <?php endif; ?>
            </td>
            <td class="text-center">
              <?php if ($item->clean_exterior === 0): ?>
                <div class="badge bg-danger fs-6">YES</div>
              <?php elseif ($item->clean_exterior === 1): ?>
                No
              <?php else: ?>
                -
              <?php endif; ?>
            </td>
            <td class="text-center">
              <?php if ($item->clean_interior === 0): ?>
                <div class="badge bg-danger fs-6">YES</div>
              <?php elseif ($item->clean_interior === 1): ?>
                No
              <?php else: ?>
                -
              <?php endif; ?>
            </td>
            <td class="text-center">
              <?php if ($item->needs_restocking === 1): ?>
                <div class="badge bg-danger fs-6">YES</div>
              <?php elseif ($item->needs_restocking === 0): ?>
                No
              <?php else: ?>
                -
              <?php endif; ?>
            </td>
            <td><?=$item->concerns?></td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>

<?php endif;?>