<?php
// exit();
require_once 'autoload.php';

use Transport\{Config, Weather};

$config = Config::get('organization');
if (!$config->weatherLocations) exit();
?>
<style>
  .weather-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
    gap: 1rem;
  }
  .weather-grid-small {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(275px, 1fr));
    gap: 0.5rem;
  }
</style>

<?php foreach ($config->weatherLocations as $item): ?>
  <?php
    $weather = new Weather($item->lat, $item->lon);
    $alerts = $weather->getAlerts();
  ?>
  <?php if (isset($alerts->features) && count($alerts->features) > 0): ?>
    <div class="alert alert-danger mb-3">
      <h5>Weather Alerts</h5>
      <?php foreach ($alerts->features as $alert): ?>
        <div class="alert alert-warning">
          <h6><?= $alert->properties->headline ?></h6>
          <p><?= $alert->properties->description ?></p>
          <div class="font-size:small"><?= $alert->properties->areaDesc ?></div>
        </div>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>
<?php endforeach; ?>

<?php $matrix = []; ?>
<div class="weather-grid mb-3">
  <?php foreach ($config->weatherLocations as $item): ?>
    <?php
    $weather = new Weather($item->lat, $item->lon);
    $data = $weather->data[0];
    $matrix[$item->name] = $weather->data;
    ?>
    <div class="d-flex justify-content-between bg-body-secondary rounded border overflow-hidden">
      <div class="align-self-center">
        <?php if ($data->icon): ?>
          <div class="px-3 text-center">
            <span class="fs-1"><?= $data->icon ?></span>
            <div><?= $weather->getCurrentTemp() ?></div>
          </div>
        <?php endif; ?>
      </div>
      <div class="flex-fill align-self-center bg-light px-3 py-1" style="height:100%">
        <h4 style="font-weight:900;color:chocolate"><?= $item->name ?></h4>
        <div class="d-flex justify-content-between">
          <div>
            <strong><?=$data->name?></strong>
            <?= $data->detailedForecast ?>
          </div>
        </div>
      </div>
    </div>
  <?php endforeach; ?>
</div>

<table class="table table-sm table-striped table-bordered" style="font-size:small">
  <tr>
    <th>FORECAST</th>
    <?php $line = reset($matrix); ?>
    <?php for ($i = 1; $i < 5; $i++): ?>
      <th><?= $line[$i]->name ?></th>
    <?php endfor; ?>
  </tr>
  <?php foreach ($matrix as $name => $data): ?>
    <tr>
      <th><?= $name ?></th>
      <?php for ($i = 1; $i < 5; $i++): ?>
        <td>
        <?= $data[$i]->icon ?>
        <?= $data[$i]->shortForecast ?> - <?= $data[$i]->temperature ?>º<?= $data[$i]->temperatureUnit ?>
        </td>
      <?php endfor; ?>
    </tr>
  <?php endforeach;?>
</table>

<!--
<h5>Tomorrow</h5>
<div class="weather-grid-small mb-3">
  <?php foreach ($config->weatherLocations as $item): ?>
    <?php
    // $weather = new Weather($item->lat, $item->lon);
    // $forecast = $weather->getForecastFor(Date('Y-m-d', strtotime('+1 day')));
    ?>
    <div class="d-flex justify-content-between bg-body-secondary rounded border overflow-hidden">
      <div class="align-self-center">
        <?php if ($forecast->smallIcon): ?>
          <div class="px-3" style="font-size: 1.5em !important;">
            <?= $forecast->smallIcon ?>
          </div>
        <?php endif; ?>
      </div>
      <div class="flex-fill bg-light px-3 py-1 d-flex justify-content-between">
        <div>
          <div class="fs-6 fw-bold" style="color:chocolate"><?= $item->name ?></div>
          <div style="font-weight:300; font-size:small"><?= $forecast->description ?></div>
        </div>
        <div class="align-self-center">
          <div style="font-weight:200; font-size:smaller">Lo: <?= $forecast->min ?></div>
          <div style="font-weight:200; font-size:smaller">Hi: <?= $forecast->max ?></div>
        </div>
      </div>
    </div>
  <?php endforeach; ?>
</div>
-->