<?php
require_once '../autoload.php';
date_default_timezone_set($_SESSION['userTimezone'] ?? $_ENV['TZ']);

use Transport\{ Location, Weather };

$id = (int) $_GET['location_id'];
$location = new Location($id);
$latitude = (string)$location->lat;
$longitude = (string)$location->lon;

$weather = new Weather($latitude, $longitude);
$date = $_GET['date'] ?? Date('Y-m-d H:i:s');
$data = $weather->getForecastFor($date);
?>
<div class="d-flex justify-content-between bg-body-secondary mt-1 px-2 rounded">
  <?php if ($data->icon): ?>
    <div style="width:40px; min-height:40px" class="pt-2">
      <?=$data->icon?>
    </div>
  <?php endif; ?>
  <div class="flex-fill align-self-center"><?=$data->shortForecast?></div>
  <div class="align-self-center" style="font-size: small;">
      <div><?= $data->temperature ?>º<?= $data->temperatureUnit ?></div>
  </div>
</div>
