<?php
date_default_timezone_set($_ENV['TZ'] ?: 'America/Denver');
require_once 'class.data.php';
$db = new data();
$query = "
  SELECT 
  	t.id, t.driver_id, t. vehicle_id, t.airline_id,
  	t.summary, t.start_date, t.end_date, t.eta, t.etd, t.iata, t.flight_number, t.confirmed,
    t.started, t.completed, t.cancellation_requested,
  	a.name AS airline, a.flight_number_prefix,
  	CONCAT(d.first_name, ' ', SUBSTRING(d.last_name,1,1), '.') AS driver, d.phone_number,
  	v.name AS vehicle, v.description,
  	CONCAT(g.first_name, ' ', g.last_name) AS guest,
  	g.phone_number AS guest_contact_number,
    CASE WHEN (pu.short_name IS NOT NULL) THEN pu.short_name ELSE pu.name END AS pickup_location,
    CASE WHEN (do.short_name IS NOT NULL) THEN do.short_name ELSE do.name END AS dropoff_location
  FROM trips t
  LEFT OUTER JOIN guests g ON t.guest_id = g.id
  LEFT OUTER JOIN users d ON t.driver_id = d.id
  LEFT OUTER JOIN vehicles v ON t.vehicle_id = v.id
  LEFT OUTER JOIN airlines a ON t.airline_id = a.id
  LEFT OUTER JOIN locations pu ON t.pu_location = pu.id
  LEFT OUTER JOIN locations do ON t.do_location = do.id
  WHERE
    start_date > DATE_SUB(CURDATE(), INTERVAL 7 DAY)
    AND t.archived IS NULL
  ORDER BY start_date ASC
";
?>
<div class="container-fluid">
  <div class="d-flex justify-content-between mt-2">
    <h2>Trips</h2>
    <button id="btn-add-trip"" class="btn btn-outline-primary btn-sm my-auto px-3" onclick="app.openTab('new-trip', 'New Trip', 'section.new-trip.php')">
      New Trip
    </button>
  </div>
  <?php if ($rows = $db->get_rows($query)): ?>

    <table id="table-trips" class="table align-middle table-hover row-select table-bordered">
      <thead>
        <tr>
          <th class="fit">Confirmed</th>
          <th class="fit">When</th>
          <th data-dt-order="disable">Trip Summary</th>
          <th class="text-start">Pick Up</th>
          <th data-dt-order="disable">Drop Off</th>
          <th>Driver</th>
          <th>Vehicle</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($rows as $row): ?>
          <?php 
            $tdClass = '';
            if ($row->cancellation_requested) {
              $tdClass = 'table-secondary';
            } elseif (!$row->end_date OR strtotime($row->end_date) <= strtotime('now') OR $row->completed) {
              $tdClass = 'table-secondary';
            } elseif (Date('Y-m-d') <= Date('Y-m-d', strtotime($row->end_date)) && Date('Y-m-d') >= Date('Y-m-d', strtotime($row->start_date))) {
              $tdClass = 'table-success';
            }
          ?>
          <tr data-id="<?=$row->id?>" class="<?=$tdClass?>">
            <!-- Confirmed -->
            <td class="text-center fit" data-order="<?=$row->confirmed?>">
              <?php if ($row->confirmed): ?>
                <i class="fa-regular fa-square-check fa-xl text-success"></i>
              <?php else: ?>
                <i class="fa-solid fa-ellipsis fa-xl text-black-50"></i>
              <?php endif; ?>
            </td>
            <!-- When -->
            <td class="fit datetime short" data-order="<?=$row->start_date?>"><?=$row->start_date?></td>
            <!-- Trip Summary -->
            <td>
              <div class="d-flex justify-content-between">
                <?php if ($row->completed) :?>
                  <i class="fa-duotone fa-regular fa-circle-check text-success align-self-center me-2" data-bs-toggle="tooltip" data-bs-title="Trip complete."></i>
                <?php elseif ($row->started): ?>
                  <i class="fa-duotone fa-solid fa-spinner-third fa-spin text-success align-self-center me-2" data-bs-toggle="tooltip" data-bs-title="Currently in progress..."></i>
                <?php endif;?>
                <?php if ($row->cancellation_requested): ?>
                  <i class="badge bg-danger align-self-center me-2">Cancelled</i>
                <?php endif;?>
                <div><?=$row->summary?></div>
              </div>
            </td>
            <!-- Pick Up -->
            <td class="text-nowrap text-start" data-order="<?=$row->start_date?>">
              <div>
                <span class="time"><?=($row->start_date) ? Date('g:ia', strtotime($row->start_date)) : ''?></span>: 
                <?=$row->pickup_location?>
              </div>
              <?php if ($row->eta): ?>
                <span class="badge text-bg-secondary">
                  <i class="fa-duotone fa-solid fa-plane-arrival"></i>
                  <?=$row->flight_number_prefix.' '.$row->flight_number?>
                </span>
                <small><?=Date('g:ia', strtotime($row->eta))?></small>
              <?php endif;?>
            </td>
            <!-- Drop Off -->
            <td class="text-nowrap">
              <div><?=$row->dropoff_location?></div>
              <?php if ($row->etd): ?>
                <span class="badge text-bg-secondary">
                  <i class="fa-duotone fa-solid fa-plane-departure"></i>
                  <?=$row->flight_number_prefix.' '.$row->flight_number?>
                </span>
                <small><?=Date('g:ia', strtotime($row->etd))?></small>
              <?php endif;?>
            </td>
            <!-- Driver -->
            <td><?=$row->driver ?: '<i class="badge bg-danger">Unassinged</i>'?></td>
            <!-- Vehicle -->
            <td><?=$row->vehicle ?: '<i class="badge bg-danger">Unassinged</i>'?></td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>


    <script type="text/javascript">

      $(async ƒ => {

        let dataTable;
        let targetId;

        function reloadSection () {
          $('#<?=$_REQUEST["loadedToId"]?>').load(`<?=$_SERVER['REQUEST_URI']?>`); // Refresh this page
        }

        if ($.fn.dataTable.isDataTable('#table-trips') ) {
          dataTable = $('#table-trips').DataTable();
        } else {
          dataTable = $('#table-trips').DataTable({
            responsive: true,
            paging: true,
            order: [[1, 'asc']],
          });
        }

        function bindRowClick () {
          $('#table-trips tbody tr').off('click').on('click', ƒ => {
            ƒ.preventDefault(); // in the case of an anchor tag. (we don't want to navigating anywhere)
            const self = ƒ.currentTarget;
            const id = $(self).data('id');
            targetId = id;
            app.openTab('view-trip', 'Trip (view)', `section.view-trip.php?id=${id}`);
          });
        }
        bindRowClick();
        dataTable.on('draw.dt', bindRowClick);

        $(document).off('tripChange.ns').on('tripChange.ns', reloadSection);

      });

    </script>


  <?php else: ?>

    <div class="container-fluid text-center">
      <div class="alert alert-info mt-5 w-50 mx-auto">
        <h1 class="fw-bold">All clear!</h1>
        <p class="lead">There are no upcoming trips at this time.</p>
      </div>
    </div>

  <?php endif; ?>
</div>
