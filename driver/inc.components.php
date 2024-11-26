<?php
require_once 'class.data.php';
if (!$db) $db = new data();


function getTripHeader($tripId) {
 ob_start();
 global $db;
 $sql = "
  SELECT 
    t.*,
    CONCAT(g.first_name,' ',g.last_name) AS guest, g.phone_number,
    CASE WHEN pu.short_name IS NULL THEN pu.name ELSE pu.short_name END AS pickup_from,
    CASE WHEN do.short_name IS NULL THEN do.name ELSE do.short_name END AS dropoff,
    v.name AS vehicle,
    a.flight_number_prefix, a.name as airline, a.image_filename
  FROM trips t
  LEFT OUTER JOIN guests g ON g.id = t.guest_id
  LEFT OUTER JOIN locations pu on pu.id = t.pu_location
  LEFT OUTER JOIN locations do on do.id = t.do_location
  LEFT OUTER JOIN vehicles v ON v.id = t.vehicle_id
  LEFT OUTER JOIN airlines a ON a.id = t.airline_id
  WHERE 
    t.id = :id
  ";
  $data = ['id' => $tripId];
  $trip = $db->get_row($sql, $data);
  ?>
  
<div class="row mb-2">
  <div class="col">
    <div class="card shadow">
      <div class="card-header  d-flex justify-content-between align-items-start">
        <div>Trip Info</div>
        <span class="badge bg-dark"><?=$trip->vehicle?></span>
      </div>
      <ul class="list-group list-group-flush">

        <li class="list-group-item d-flex justify-content-between align-items-start">
          <div class="me-auto">
            <div class="fw-bold"><?=$trip->summary?></div>
          </div>
        </li>

        <li class="list-group-item d-flex justify-content-between align-items-center ps-2">
          <i class="fa-solid fa-arrow-up me-2"></i>
          <div class="flex-fill">
            <div class="d-flex justify-content-between align-items-center">
              <div class="fw-bold"><?=$trip->guest?></div>
              <small><a class="btn btn-sm btn-primary py-0" href="tel:<?=$trip->phone_number?>"><?=$trip->phone_number?></a></small>
            </div>
            <div class="d-flex justify-content-between align-items-center">
              <div><?=$trip->pickup_from?></div>
              <small><?=Date('g:ia', strtotime($trip->pickup_date))?></small>
            </div>            
          </div>
        </li>

        <li class="list-group-item ps-2">
          <div class="d-flex justify-content-between align-items-center">
            <i class="fa-solid fa-arrow-down me-2"></i>
            <div class="flex-fill">
              <div class="d-flex justify-content-between align-items-center">
                <div><?=$trip->dropoff?></div>
              </div>
            </div>
          </div>
        </li>

        <?php if ($trip->flight_number): ?>
          <li class="list-group-item d-flex justify-content-between align-items-center">
            <div class="me-2">
              <img src="/images/airlines/<?=$trip->image_filename?>" alt="<?=$trip->airline?>" class="img-fluid" />
            </div>
            <span style="font-size: large" class="badge bg-info"><?=$trip->flight_number_prefix.' '.$trip->flight_number?></span>
          </li>
        <?php endif; ?>

      </ul>
    </div>
  </div>
</div>

 <?php
 return ob_get_clean();
}


function stepIndicator ($stepsCompleted, $numberOfSteps) {
  $output = '<div class="d-flex justify-content-center align-items-center">';
  $output .= '<div class="progresses">';
  for ($i = 1; $i <= $numberOfSteps; $i++) {
    if ($i <= $stepsCompleted) {
      $output .= '<div class="steps complete">';
      $output .= '<span><i class="fa fa-check"></i></span>';
      $output .= '</div>';
    } else {
      $output .= '<div class="steps">';
      $output .= '<span class="font-weight-bold">'.$i.'</span>';
      $output .= '</div>';

    }
    if ($i < $numberOfSteps) {
      if ($i < $stepsCompleted) {
        $output .= '<span class="line complete"></span>';
      } else {
        $output .= '<span class="line"></span>';
      }
    }
  }
  $output .= '</div>';
  $output .= '</div>';
  return $output;
}