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

</div>


<script type="module">
  import { initListPage } from '/js/listpage-helper.js';

  $(async ƒ => {

    const tableId = 'table-reservations';
    const loadOnClick = {
      page: 'section.edit-reservation.php',
      tab: 'edit-reservation',
      title: 'Reservation (edit)',
    }
    const dataTableOptions = {
      responsive: true
    };
    const reloadOnEventName = 'reservationChange';
    const parentSectionId = `#<?=$_GET["loadedToId"]?>`;
    const thisURI = `<?=$_SERVER['REQUEST_URI']?>`;

    initListPage({tableId, loadOnClick, dataTableOptions, reloadOnEventName, parentSectionId, thisURI});

    $('#btn-add-reservation').off('click').on('click', ƒ => {
      app.openTab('edit-reservation', 'Reservation (new)', `section.edit-reservation.php`);
    });
  });

</script>
