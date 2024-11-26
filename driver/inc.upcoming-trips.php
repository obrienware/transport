<?php
include 'inc.util-functions.php';
require_once 'class.data.php';
if (!$db) $db = new data();

// Is there a trip in progress?
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
    AND t.start_date < DATE_ADD(CURDATE(), INTERVAL 7 DAY)
    AND completed IS NULL
  ORDER BY t.start_date
  LIMIT 3
";
$data = ['id' => $_SESSION['user']->id];
$trips = $db->get_results($sql, $data);
?>
<?php if ($trips): ?>

  <h3>Your Upcoming Trips</h3>

  <?php foreach ($trips as $trip): ?>
    <div class="card mb-2 shadow">
      <div class="card-body">
        <div class="badge bg-dark-subtle"><?=showDate($trip->start_date)?></div>
        <h5 class="card-title"><?=$trip->summary?></h5>
        
        <small>
          <div class="d-flex">
            <span class="bg-primary badge align-self-baseline me-2"><i class="fa fa-arrow-up"></i> PU</span>
            <?=Date('g:ia', strtotime($trip->start_date))?>, <?=$trip->guest?> @ <?=$trip->pickup_from?>
          </div>
          <div class="d-flex">
            <span class="bg-primary badge align-self-baseline me-2"><i class="fa fa-arrow-down"></i> DO</span>
            <?=$trip->dropoff?>
          </div>
        </small>

        <a href="#" onclick="app.load('section.trip.php?id=<?=$trip->id?>')" class="stretched-link"></a>
      </div>
    </div>
  <?php endforeach; ?>

<?php else: ?>

    <div class="alert alert-info">
      <h5 class="fw-bold"><i class="fa-solid fa-check-double"></i> All Clear!</h5>
      You have no trips coming up in the next week!
    </div>

    <div class="d-flex justify-content-around">
      <button class="btn btn-primary px-5" onclick="location.reload()">Refresh</button>
    </div>

<?php endif;?>