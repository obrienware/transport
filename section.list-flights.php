<?php
require_once 'class.data.php';
if (!isset($db)) $db = new data();
$sql = "
SELECT 
  t.id AS trip_id,
  t.guests,
  t.end_date,
  a.iata AS arrival,
  t.eta,
  b.iata AS departure,
  t.etd,
  CONCAT(l.flight_number_prefix,' ',t.flight_number) AS flight_number,
  t.flight_status,
  t.flight_status_as_at AS as_at,
  t.flight_info
FROM trips t
LEFT OUTER JOIN locations a ON a.id = t.pu_location
LEFT OUTER JOIN locations b ON b.id = t.do_location
LEFT OUTER JOIN airlines l ON l.id = airline_id
WHERE
 	(t.eta IS NOT NULL OR t.etd IS NOT NULL)
	AND
	(t.eta IS NULL OR DATE(eta) >= CURDATE())
	AND
	(t.etd IS NULL OR DATE(etd) >= CURDATE())	
	AND t.archived IS NULL
ORDER BY COALESCE(t.eta, t.etd) -- This is brilliant! Orders by either ETA OR ETD where the other is NULL!
";
?>
<div class="container-fluid">
  <h1>Flight Statuses</h1>
  <?php if ($rs = $db->get_results($sql)): ?>

    <table class="table table-sm table-bordered table-striped table-striped-columns">
      <thead>
        <tr>
          <th>Guests/Group</th>
          <th>Airport</th>
          <th>Flight</th>
          <th>Scheduled</th>
          <th>Estimated</th>
          <th>Actual</th>
          <th>Status</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($rs as $item): ?>
          <?php
            $flight = null;
            $rowClass = '';
            if (Date('Y-m-d') == Date('Y-m-d', strtotime($item->end_date)) AND $item->flight_info) {
              $rowClass = 'table-info';
              $data = json_decode($item->flight_info);
              $type = ($item->arrival) ? 'arrival' : 'departure';
              foreach ($data as $value) {
                if ($type === 'arrival') {
                  if ($value->arrival->iataCode == $item->arrival) $flight = $value;
                } else {
                  if ($value->departure->iataCode == $item->departure)  $flight = $value;
                }
              }
            }
          ?>
          <tr class="<?=$rowClass?>">
            <td><?=$item->guests?></td>
            <td><?=($item->arrival) ?: $item->departure?></td>
            <td><?=$item->flight_number?></td>
            <td class="fit">
              <?php if ($item->etd): ?>
                <div class="d-flex justify-content-between">
                  <div><?=Date('m/d g:ia', strtotime($item->etd))?></div>
                  <div class="badge bg-primary align-self-center ms-2">departure</div>
                </div>
              <?php else: ?>
                <div class="d-flex justify-content-between">
                  <div><?=Date('m/d g:ia', strtotime($item->eta))?></div>
                  <div class="badge bg-primary align-self-center ms-2">arrival</div>
                </div>
              <?php endif; ?>
            </td>
            <td>
              <?php if ($flight): ?>
                <?php if ($item->eta AND $flight->arrival->estimatedTime) :?>
                  <?=Date('g:ia', strtotime($flight->arrival->estimatedTime))?>
                <?php elseif ($flight->departure->estimatedTime): ?>
                  <?=Date('g:ia', strtotime($flight->departure->estimatedTime))?>
                <?php endif; ?>
              <?php endif; ?>
            </td>
            <td>
              <?php if ($flight): ?>
                <?php if ($item->eta AND $flight->arrival->actualTime) :?>
                  <?=Date('g:ia', strtotime($flight->arrival->actualTime))?>
                <?php elseif ($flight->departure->actualTime): ?>
                  <?=Date('g:ia', strtotime($flight->departure->actualTime))?>
                <?php endif; ?>
              <?php endif; ?>
            </td>
            <td><?=$item->flight_status?></td>
          </tr>
        <?php endforeach;?>
      </tbody>
    </table>


  <?php else: ?>

    <div class="alert alert-info my-5">
      <p class="lead">There are no flights currently being tracked.</p>
    </div>

  <?php endif; ?>
</div>