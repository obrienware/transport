<?php
date_default_timezone_set($_ENV['TZ'] ?: 'America/Denver');
require_once 'class.data.php';
$db = new data();
$sql = "
  SELECT 
  	t.id, t.driver_id, t. vehicle_id, t.airline_id,
  	t.summary, t.start_date, t.end_date, t.eta, t.etd, t.iata, t.flight_number, t.finalized,
    t.started, t.completed,
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
<div class="d-flex justify-content-between mt-3">
  <h2>Trips</h2>
  <button id="btn-add-trip"" class="btn btn-outline-primary btn-sm my-auto px-3">
    New Trip
  </button>
</div>
<?php if ($rs = $db->get_results($sql)): ?>

  <table id="table-trips" class="table align-middle table-hover row-select">
    <thead>
      <tr>
        <th class="fit">Finalized</th>
        <th class="fit">When</th>
        <th data-dt-order="disable">Trip Summary</th>
        <th data-dt-order="disable">Contact(s)</th>
        <th>Pick Up</th>
        <th data-dt-order="disable">Drop Off</th>
        <th>Driver</th>
        <th>Vehicle</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($rs as $item): ?>
        <?php 
          $tdClass = '';
          if (strtotime($item->end_date) <= strtotime('now') OR $item->completed) {
            $tdClass = 'table-secondary';
          } elseif (Date('Y-m-d') <= Date('Y-m-d', strtotime($item->end_date)) && Date('Y-m-d') >= Date('Y-m-d', strtotime($item->start_date))) {
            $tdClass = 'table-success';
          }
        ?>
        <tr data-id="<?=$item->id?>" class="<?=$tdClass?>">
          <td class="text-center fit" data-order="<?=$item->finalized?>">
            <?php if ($item->finalized): ?>
              <i class="fa-regular fa-square-check fa-lg text-success"></i>
            <?php else: ?>
              <i class="fa-regular fa-square fa-lg"></i>
            <?php endif; ?>
          </td>
          <td class="fit datetime short" data-order="<?=$item->start_date?>"><?=$item->start_date?></td>
          <td>
            <?php if ($item->completed) :?>
              <i class="fa-duotone fa-regular fa-circle-check text-success" data-bs-toggle="tooltip" data-bs-title="Trip complete."></i>
            <?php elseif ($item->started): ?>
              <i class="fa-duotone fa-solid fa-spinner-third fa-spin text-success" data-bs-toggle="tooltip" data-bs-title="Currently in progress..."></i>
            <?php endif;?>
            <?=$item->summary?>
          </td>
          <td><?=$item->guest?></td>
          <td class="text-nowrap" data-order="<?=$item->start_date?>">
            <div>
              <span class="time"><?=($item->start_date) ? Date('g:ia', strtotime($item->start_date)) : ''?></span>: 
              <?=$item->pickup_location?>
            </div>
            <?php if ($item->eta): ?>
              <span class="badge text-bg-secondary">
                <i class="fa-duotone fa-solid fa-plane-arrival"></i>
                <?=$item->flight_number_prefix.' '.$item->flight_number?>
              </span>
              <small><?=Date('g:ia', strtotime($item->eta))?></small>
            <?php endif;?>
          </td>
          <td class="text-nowrap">
            <div><?=$item->dropoff_location?></div>
            <?php if ($item->etd): ?>
              <span class="badge text-bg-secondary">
                <i class="fa-duotone fa-solid fa-plane-departure"></i>
                <?=$item->flight_number_prefix.' '.$item->flight_number?>
              </span>
              <small><?=Date('g:ia', strtotime($item->etd))?></small>
            <?php endif;?>
          </td>
          <td><?=$item->driver ?: '<i class="badge bg-danger">Unassinged</i>'?></td>
          <td><?=$item->vehicle ?: '<i class="badge bg-danger">Unassinged</i>'?></td>
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
          order: [[1, 'asc']]
        });
      }

      function bindRowClick () {
        $('#table-trips tbody tr').off('click').on('click', ƒ => {
          ƒ.preventDefault(); // in the case of an anchor tag. (we don't want to navigating anywhere)
          const self = ƒ.currentTarget;
          const id = $(self).data('id');
          targetId = id;
          app.openTab('edit-trip', 'Trip (edit)', `section.edit-trip.php?id=${id}`);
        });
      }
      bindRowClick()

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

<script type="text/javascript">

  $(async ƒ => {
    $('#btn-add-trip').off('click').on('click', ƒ => {
        app.openTab('new-trip', 'New Trip', `section.new-trip.php`);
      });
  });

</script>