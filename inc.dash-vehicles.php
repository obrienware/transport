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
      OR v.clean_interior = 0
      OR v.clean_exterior = 0
      OR v.restock = 1
    )
  ORDER BY v.name
";
?>
<?php if ($rs = $db->get_results($sql)): ?>

  <div class="row row-cols-2 rows-cols-md-3 row-cols-xl-4 row-cols-xxl-5 g-4 mb-4">
    <?php foreach ($rs as $item): ?>

      <div class="col">
        <div class="card">
          <div class="card-header" style="background-color:<?=$item->color?>">
            <button style="color: #<?=readableColor($item->color)?> !important" class="btn p-0" onclick="app.openTab('view-vehicle', 'Vehicle', 'section.view-vehicle.php?id=<?=$item->id?>');">
            <?=$item->name?>
            </button>
          </div>
          <ul class="list-group list-group-flush">

            <?php if ($item->check_engine): ?>
              <li class="list-group-item">
                <i class="fa-xl fa-solid fa-engine-warning fa-fw" style="color: orangered"></i> Check Engine
              </li>
            <?php endif; ?>

            <?php if (!is_null($item->fuel_level) && $item->fuel_level <= 25): ?>
              <li class="list-group-item">
                <i class="fa-xl fa-solid fa-triangle-exclamation fa-fw" style="color: orangered"></i> Fuel Level: <?=$item->fuel_level?>%
              </li>
            <?php endif;?>

            <?php if ($item->clean_interior === 0): ?>
              <li class="list-group-item">
                <i class="fa-xl fa-solid fa-triangle-exclamation fa-fw" style="color: orangered"></i> Needs interior cleaning
              </li>
            <?php endif; ?>

            <?php if ($item->clean_exterior === 0): ?>
              <li class="list-group-item">
                <i class="fa-xl fa-solid fa-triangle-exclamation fa-fw" style="color: orangered"></i> Needs exterior cleaning
              </li>
            <?php endif; ?>

            <?php if ($item->restock === 1): ?>
              <li class="list-group-item">
                <i class="fa-xl fa-solid fa-triangle-exclamation fa-fw" style="color: orangered"></i> Needs restocking
              </li>
            <?php endif; ?>

          </ul>
        </div>
      </div>

    <?php endforeach; ?>
  </div>

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

  if ($squared_contrast > pow(170, 2)) {
    return '000000';
  } else {
    return 'FFFFFF';
  }
}