<?php
require_once '../autoload.php';

use Transport\Trip;
use Generic\Utils;

$trips = Trip::upcomingTrips();
?>
<?php if ($trips): ?>

  <h3>Your Upcoming Trips</h3>
  <?php foreach ($trips as $trip): ?>
    <?php
    $accentClass = 'bg-light';
    $showDate = Utils::showDate($trip->start_date);
    if ($showDate == 'today') $accentClass = 'bg-danger';
    if ($showDate == 'tomorrow') $accentClass = 'bg-warning';
    ?>
    <div class="card mb-4 shadow">
      <div class="card-header text-<?=$accentClass?> text-center"><?=$showDate?></div>
      <div class="card-body pt-2">
        <h5 class="card-title"><?=$trip->summary?></h5>
        
        <small>
          <div class="d-flex">
            <span class="bg-success-subtle badge align-self-baseline me-2">Start</span>
            <?=Date('g:ia', strtotime($trip->start_date))?>
          </div>
          <div class="d-flex">
            <span class="bg-primary badge align-self-baseline me-2"><i class="fa fa-arrow-up"></i> PU</span>
            <div>
              <div><?=$trip->guests?></div>
              <div><?=$trip->pickup_from?> @<?=Date('g:ia', strtotime($trip->pickup_date))?></div>
            </div>
          </div>
          <div class="d-flex">
            <span class="bg-primary badge align-self-baseline me-2"><i class="fa fa-arrow-down"></i> DO</span>
            <?=$trip->dropoff?>
          </div>
        </small>

        <a href="#" onclick="showTripDetail(<?=$trip->id?>)" class="stretched-link"></a>
      </div>
    </div>
  <?php endforeach; ?>

<?php else: ?>

<div class="alert alert-info">
  <h5 class="fw-bold"><i class="fa-solid fa-check-double"></i> All Clear!</h5>
  You have no trips coming up!
</div>

<?php endif;?>