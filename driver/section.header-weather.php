<?php
require_once 'class.weather.php';
require_once 'class.location.php';

$location = new Location($_REQUEST['location_id']);
$latitude = $location->lat;
$longitude = $location->lon;

$weather = new Weather($latitude, $longitude);
$date = $_REQUEST['date'] ?: NULL;
if ($date) {
  $data = $weather->getForecastFor($date);
} else {
  $data = $weather->getWeather();
}
?>
<div class="d-flex justify-content-between bg-body-secondary mt-1 px-2 rounded">
  <?php if ($data->icon): ?>
    <div style="width:40px; min-height:40px">
      <img src="<?=$data->icon?>" class="img-fluid">
    </div>
  <?php endif; ?>
  <div class="flex-fill align-self-center"><?=$data->description?></div>
  <div class="align-self-center" style="font-size: small;">
    <?php if ($date): ?>
      <div>Lo: <?=$data->min?></div>
      <div>Hi: <?=$data->max?></div>
    <?php else: ?>
      <div>Currently</div>
      <div><?=$data->temp?></div>
    <?php endif; ?>
  </div>
</div>
