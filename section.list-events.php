<?php
date_default_timezone_set($_ENV['TZ'] ?: 'America/Denver');
require_once 'class.data.php';
$db = data::getInstance();
$query = "
  SELECT 
    e.*, 
    (
      SELECT GROUP_CONCAT(CONCAT(first_name,' ',last_name) SEPARATOR ', ') FROM users d WHERE FIND_IN_SET(d.id, e.driver_ids)
    ) AS drivers,
    (
      SELECT GROUP_CONCAT(v.name SEPARATOR ', ') FROM vehicles v WHERE FIND_IN_SET(v.id, e.vehicle_ids)
    ) AS vehicles,
    CASE WHEN (l.short_name IS NOT NULL) THEN l.short_name ELSE l.name END AS location
  FROM events e
  LEFT OUTER JOIN locations l ON l.id = e.location_id
  WHERE
    start_date > DATE_SUB(CURDATE(), INTERVAL 7 DAY)
    AND e.archived IS NULL
  ORDER BY start_date ASC
";
?>
<div class="container-fluid">
  <div class="d-flex justify-content-between mt-2">
    <h2>Events</h2>
    <button id="btn-add-event" class="btn btn-outline-primary btn-sm my-auto px-3">
      New Event
    </button>
  </div>

  <?php if ($rows = $db->get_rows($query)): ?>

    <table id="table-events" class="table align-middle table-hover row-select">
      <thead>
        <tr>
          <th class="fit">Confirmed</th>
          <th class="fit">From</th>
          <th class="fit">To</th>
          <th>Description</th>
          <th>Where</th>
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
            <td class="text-center fit" data-order="<?=$row->confirmed?>">
              <?php if ($row->confirmed): ?>
                <i class="fa-regular fa-square-check fa-xl text-success"></i>
              <?php else: ?>
                <i class="fa-solid fa-ellipsis fa-xl text-black-50"></i>
              <?php endif; ?>
            </td>
            <td class="fit datetime short" data-order="<?=$row->start_date?>"><?=$row->start_date?></td>
            <td class="fit datetime short" data-order="<?=$row->end_date?>"><?=$row->end_date?></td>
            <td>
              <?php if ($row->cancellation_requested): ?>
                <i class="badge bg-danger">Cancelled</i>
              <?php endif;?>
              <?=$row->name?>
            </td>
            <td><?=$row->location?></td>
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

        if ($.fn.dataTable.isDataTable('#table-events') ) {
          dataTable = $('#table-events').DataTable();
        } else {
          dataTable = $('#table-events').DataTable({
            responsive: true,
            paging: true
          });
        }

        function bindRowClick () {
          $('#table-events tbody tr').off('click').on('click', ƒ => {
            ƒ.preventDefault(); // in the case of an anchor tag. (we don't want to navigating anywhere)
            const self = ƒ.currentTarget;
            const id = $(self).data('id');
            targetId = id;
            app.openTab('view-event', 'Event (view)', `section.view-event.php?id=${id}`);
          });
        }
        bindRowClick()

        dataTable.on('draw.dt', bindRowClick);

        $(document).off('eventChange.ns').on('eventChange.ns', reloadSection);
      });

    </script>

  <?php else: ?>

    <div class="container-fluid text-center">
      <div class="alert alert-info mt-5 w-50 mx-auto">
        <h1 class="fw-bold">All clear!</h1>
        <p class="lead">There are no upcoming events at this time.</p>
      </div>
    </div>

  <?php endif; ?>

</div>
<script type="text/javascript">

  $(async ƒ => {
    $('#btn-add-event').off('click').on('click', ƒ => {
      app.openTab('new-event', 'New Event', `section.edit-event.php`);
    });
  });

</script>