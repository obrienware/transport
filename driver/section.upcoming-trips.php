<?php
date_default_timezone_set($_ENV['TZ'] ?: 'America/Denver');

function showDate($date) {
  $baseline = Date('Y-m-d', strtotime($date));
  if (Date('Y-m-d') == Date('Y-m-d', strtotime($baseline))) return 'TODAY';
  if (Date('Y-m-d') == Date('Y-m-d', strtotime($baseline.' -1 day'))) return 'TOMORROW';
  return 'In '.ago('now', $date).' ('.Date('l m/d @ g:ia', strtotime($date)).')';
}

function ago($time1, $time2 = 'now', $short = false) {
	if ($short) {
		$periods = array("sec", "min", "hr", "day", "wk", "mth", "yr", "dec");
	} else {
		$periods = array("second", "minute", "hour", "day", "week", "month", "year", "decade");
	}
	$lengths = array("60","60","24","7","4.35","12","10");
	$time1 = strtotime($time1);
	$time2 = strtotime($time2);

	$difference = $time2 - $time1;

	for($j = 0; $difference >= $lengths[$j] && $j < count($lengths)-1; $j++) {
		$difference /= $lengths[$j];
	}
	$difference = round($difference);
	if($difference != 1) $periods[$j].= "s";
	return "$difference $periods[$j]";
}


require_once 'class.data.php';
if (!$db) $db = new data();

$sql = "
  SELECT 
    t.*,
    CONCAT(g.first_name,' ',g.last_name) AS guest,
    CASE WHEN pu.short_name IS NULL THEN pu.name ELSE pu.short_name END AS pickup_from,
    CASE WHEN do.short_name IS NULL THEN do.name ELSE do.short_name END AS dropoff
    -- v.name AS vehicle,
    -- a.flight_number_prefix
  FROM trips t
  LEFT OUTER JOIN guests g ON g.id = t.guest_id
  LEFT OUTER JOIN locations pu on pu.id = t.pu_location
  LEFT OUTER JOIN locations do on do.id = t.do_location
  -- LEFT OUTER JOIN vehicles v ON v.id = t.vehicle_id
  -- LEFT OUTER JOIN airlines a ON a.id = t.airline_id
  WHERE 
    t.driver_id = :id
    AND t.end_date >= CURDATE()
    AND t.archived IS NULL
    -- AND t.start_date < DATE_ADD(CURDATE(), INTERVAL 7 DAY)
    AND completed IS NULL
  ORDER BY t.start_date
  LIMIT 5
";
$data = ['id' => $_SESSION['user']->id];
$trips = $db->get_results($sql, $data);
?>
<?php if ($trips): ?>

  <h3>Your Upcoming Trips</h3>
  <?php foreach ($trips as $trip): ?>
    <?php
    $badgeClass = 'bg-dark-subtle';
    $showDate = showDate($trip->start_date);
    if ($showDate == 'TODAY') $badgeClass = 'bg-success';
    if ($showDate == 'TOMORROW') $badgeClass = 'bg-danger';
    ?>
    <div class="card mb-2 shadow">
      <div class="card-body">
        <div class="badge <?=$badgeClass?>"><?=$showDate?></div>
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