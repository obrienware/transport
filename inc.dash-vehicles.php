<?php
require_once 'class.data.php';
if (!$db) $db = new data();
$sql = "
  SELECT v.*, l.name AS location
  FROM vehicles v
  LEFT OUTER JOIN locations l ON l.id = v.location_id
  WHERE 
    v.archived IS NULL 
    AND (
      v.check_engine = 1
      OR (v.default_staging_location_id <> v.location_id AND v.location_id IS NOT NULL)
      OR v.fuel_level <= 25
      OR v.clean_interior = 1
      OR v.clean_exterior = 1
      OR v.restock = 1
    )
  ORDER BY v.name
";
?>
<?php if ($rs = $db->get_results($sql)): ?>
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
        </tr>
        <tr class="table-primary">
          <th class="text-center">Fuel</th>
          <th class="text-center">Clean (ext)</th>
          <th class="text-center">Clean (int)</th>
          <th class="text-center">Restock</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($rs as $item): ?>
          <tr>
            <td class="datetime short"><?=$item->last_update?></td>
            <td>
              <button class="btn p-0" onclick="app.openTab('view-vehicle', 'Vehicle', 'section.view-vehicle.php?id=<?=$item->id?>');">
                <?php if ($item->check_engine): ?>
                  <i class="fa-duotone fa-regular fa-engine-warning text-danger fa-xl"></i>
                <?php endif;?>
                <?=$item->name?>
              </button>
            </td>
            <td><?=$item->location ?: 'unverified'?></td>
            <td class="text-center">
              <?php if ($item->fuel_level == null): ?>
                -
              <?php else: ?>
                <?php if ($item->fuel_level <= 25): ?>
                  <span class="badge bg-danger fs-6"><?=$item->fuel_level?>%</span>
                <?php else:?>
                  <?=$item->fuel_level?>%
                <?php endif; ?>
              <?php endif; ?>
            </td>
            <td class="text-center">
              <?php if ($item->clean_exterior === 1): ?>
                <div class="badge bg-danger fs-6">YES</div>
              <?php else: ?>
                -
              <?php endif; ?>
            </td>
            <td class="text-center">
              <?php if ($item->clean_interior === 1): ?>
                <div class="badge bg-danger fs-6">YES</div>
              <?php else: ?>
                -
              <?php endif; ?>
            </td>
            <td class="text-center">
              <?php if ($item->restock === 1): ?>
                <div class="badge bg-danger fs-6">YES</div>
              <?php else: ?>
                -
              <?php endif; ?>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>

<?php endif;?>