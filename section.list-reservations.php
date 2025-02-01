<?php
require_once 'autoload.php';

use Transport\VehicleReservation;
$rows = VehicleReservation::getAll();
?>
<div class="container-fluid">
  <div class="d-flex justify-content-between mt-2">
    <h2>Vehicle Reservations</h2>
    <button id="btn-add-reservation" class="btn btn-outline-primary btn-sm my-auto px-3">
      New Reservation
    </button>
  </div>

  <?php if ($rows): ?>

    <table id="table-reservations" class="table align-middle table-hover row-select">
      <thead>
        <tr class="table-dark">
          <th class="fit">Confirmed</th>
          <th>Vehicle</th>
          <th>Guest</th>
          <th class="fit">From</th>
          <th class="fit">To</th>
          <th>Reason</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($rows as $row): ?>
          <tr data-id="<?=$row->id?>">
            <td class="text-center fit" data-order="<?=$row->confirmed?>">
              <?php if ($row->confirmed): ?>
                <i class="fa-solid fa-circle-check fa-xl text-success"></i>
              <?php else: ?>
                <i class="fa-solid fa-circle-xmark fa-xl text-black text-opacity-25"></i>
              <?php endif; ?>
            </td>
            <td><?=$row->vehicle?></td>
            <td><?=$row->guest?></td>
            <td class="fit datetime short" data-order="<?=$row->start_datetime?>"><?=$row->start_datetime?></td>
            <td class="fit datetime short" data-order="<?=$row->end_datetime?>"><?=$row->end_datetime?></td>
            <td><?=$row->reason?></td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>

    <script type="text/javascript">

      $(async ƒ => {

        let dataTable;
        let targetId;

        function reloadSection () {
          $('#<?=$_GET["loadedToId"]?>').load(`<?=$_SERVER['REQUEST_URI']?>`); // Refresh this page
        }

        if ($.fn.dataTable.isDataTable('#table-reservations') ) {
          dataTable = $('#table-reservations').DataTable();
        } else {
          dataTable = $('#table-reservations').DataTable({
            responsive: true,
            paging: true
          });
        }

        function bindRowClick () {
          $('#table-reservations tbody tr').off('click').on('click', ƒ => {
            ƒ.preventDefault(); // in the case of an anchor tag. (we don't want to navigating anywhere)
            const self = ƒ.currentTarget;
            const id = $(self).data('id');
            targetId = id;
            app.openTab('edit-reservation', 'Reservation (edit)', `section.edit-reservation.php?id=${id}`);
          });
        }
        bindRowClick()

        dataTable.on('draw.dt', bindRowClick);

        $(document).off('reservationChange.ns').on('reservationChange.ns', reloadSection);
      });

    </script>

  <?php else: ?>

    <div class="container-fluid text-center">
      <div class="alert alert-info mt-5 w-50 mx-auto">
        <h1 class="fw-bold">All clear!</h1>
        <p class="lead">There are no upcoming vehicle reservations at this time.</p>
      </div>
    </div>

  <?php endif; ?>

</div>
<script type="text/javascript">

  $(async ƒ => {
    $('#btn-add-reservation').off('click').on('click', ƒ => {
      app.openTab('edit-reservation', 'Reservation (new)', `section.edit-reservation.php`);
    });
  });

</script>