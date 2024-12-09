<?php
date_default_timezone_set($_ENV['TZ'] ?: 'America/Denver');
require_once 'class.flight.php';
require_once 'class.data.php';
if (!isset($db)) $db = new data();

$sql = "
SELECT 
  t.summary,
  t.pickup_date,
  CASE WHEN t.ETA IS NOT NULL THEN t.ETA ELSE t.ETD END AS target_datetime,
  CASE WHEN t.ETA IS NOT NULL THEN 'arrival' ELSE 'departure' END AS `type`,
  CASE WHEN t.ETA IS NOT NULL THEN a.iata ELSE b.iata END AS iata,
  CONCAT(l.flight_number_prefix, t.flight_number) AS flight_number,
  l.name AS airline
FROM trips t
LEFT OUTER JOIN airlines l ON l.id = t.airline_id
LEFT OUTER JOIN locations a ON a.id = t.pu_location
LEFT OUTER JOIN locations b ON b.id = t.do_location
WHERE
 	(t.eta IS NOT NULL OR t.etd IS NOT NULL)
	AND
	(t.eta IS NULL OR DATE(eta) >= DATE_SUB(CURDATE(), INTERVAL 3 DAY))
	AND
	(t.etd IS NULL OR DATE(etd) >= DATE_SUB(CURDATE(), INTERVAL 3 DAY))	
	AND t.archived IS NULL
  AND DATE(t.pickup_date) < DATE_ADD(CURDATE(), INTERVAL 7 DAY)
ORDER BY COALESCE(t.eta, t.etd) -- This is brilliant! Orders by either ETA OR ETD where the other is NULL!
";


?>
<div class="container-fluid">
  <h1>Flight Statuses</h1>
  <?php if ($rs = $db->get_results($sql)): ?>

    <?php foreach ($rs as $item): ?>
      <?php 
        $flight = Flight::getFlightStatus($item->flight_number, $item->type, $item->iata, Date('Y-m-d', strtotime($item->target_datetime)));
        switch ($flight->status_icon) {
          case 'green':
            $tableClass = "table-success";
            break;
          case 'yellow':
            $tableClass = "table-warning";
            break;
          case 'red':
            $tableClass = "table-danger";
            break;
          default:
            $tableClass = "table-secondary";
            break;
        }
        $classArrival = '';
        $classDeparture = '';
        if ($item->type == 'arrival') $classArrival = 'bg-info-subtle';
        if ($item->type == 'departure') $classDeparture = 'bg-info-subtle';
      ?>
      <div class="mb-3">
        <table class="table table-sm table-bordered <?=$tableClass?>">
          <caption class="caption-top"><?=Date('m/d/Y', strtotime($item->pickup_date)).': '.$item->summary?></caption>
          <thead>
            <tr>
              <th data-bs-toggle="tooltip" data-bs-title="Flight Number">Flight</th>
              <th data-bs-toggle="tooltip" data-bs-title="Active">Act</th>
              <th data-bs-toggle="tooltip" data-bs-title="Status">Status</th>
              <th class="<?=$classDeparture?>" data-bs-toggle="tooltip" data-bs-title="Origin"><i class="fa-duotone fa-solid fa-plane-departure"></i> DEP</th>
              <th class="<?=$classDeparture?>" data-bs-toggle="tooltip" data-bs-title="Scheduled Departure">Sch</th>
              <th class="<?=$classDeparture?>" data-bs-toggle="tooltip" data-bs-title="Estimated Departure">Est</th>
              <th class="<?=$classDeparture?>" data-bs-toggle="tooltip" data-bs-title="Actual Departure">Act</th>
              <th class="fit">&nbsp;</th>
              <th class="<?=$classArrival?>" data-bs-toggle="tooltip" data-bs-title="Destination"><i class="fa-duotone fa-solid fa-plane-arrival"></i> ARR</th>
              <th class="<?=$classArrival?>" data-bs-toggle="tooltip" data-bs-title="Scheduled Arrival">Sch</th>
              <th class="<?=$classArrival?>" data-bs-toggle="tooltip" data-bs-title="Estimated Arrival">Est</th>
              <th class="<?=$classArrival?>" data-bs-toggle="tooltip" data-bs-title="Actual Arrival">Act</th>
            </tr>
          </thead>
          <tbody>
            <tr class="<?=$tableClass?>">
              <td data-bs-toggle="tooltip" data-bs-title="<?=$item->airline?>"><?=$flight->flight_number?></td>
              <td><?=$flight->status_live ? '<i class="fa-solid fa-check"></i>' : ''?></td>
              <td><?=$flight->status_text?></td>
              <td data-bs-toggle="tooltip" data-bs-title="<?=$flight->airport_origin?>"><?=$flight->airport_origin_iata?></td>
              <td><?=$flight->scheduled_departure ? Date('g:ia', strtotime($flight->scheduled_departure)) : '-'?></td>
              <td><?=$flight->estimated_departure ? Date('g:ia', strtotime($flight->estimated_departure)) : '-'?></td>
              <td><?=$flight->real_departure ? Date('g:ia', strtotime($flight->real_departure)) : '-'?></td>
              <td class="fit">&nbsp;</td>
              <td data-bs-toggle="tooltip" data-bs-title="<?=$flight->airport_destination?>"><?=$flight->airport_destination_iata?></td>
              <td><?=$flight->scheduled_arrival ? Date('g:ia', strtotime($flight->scheduled_arrival)) : '-'?></td>
              <td><?=$flight->estimated_arrival ? Date('g:ia', strtotime($flight->estimated_arrival)) : '-'?></td>
              <td><?=$flight->real_arrival ? Date('g:ia', strtotime($flight->real_arrival)) : '-'?></td>
            </tr>
          </tbody>
        </table>
      </div>
    <?php endforeach; ?>

  <?php else: ?>

    <div class="alert alert-info my-5">
      <p class="lead">There are no flights currently being tracked.</p>
    </div>

  <?php endif; ?>
</div>