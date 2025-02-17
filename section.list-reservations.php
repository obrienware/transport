<?php
require_once 'autoload.php';

use Transport\VehicleReservation;

$rows = VehicleReservation::getAll();
?>

<div class="d-flex justify-content-between top-page-buttons">
  <button class="btn btn-sm btn-outline-primary px-2 ms-auto me-2" onclick="$(document).trigger('reservation:reloadList');">
    <i class="fa-solid fa-arrow-rotate-right"></i>
    Reload
  </button>
  <button class=" btn btn-outline-primary btn-sm px-2" onclick="$(document).trigger('loadMainSection', { sectionId: 'reservations', url: 'section.edit-reservation.php', forceReload: true });">
    New Reservation
  </button>
</div>


<div class="d-flex justify-content-between mt-2">
  <h2>Reservations</h2>
</div>

<table id="table-reservations" class="table align-middle table-striped">
  <thead>
    <tr class="table-dark">
      <th class="fit no-sort" data-priority="4">Confirmed</th>
      <th data-priority="1">Vehicle</th>
      <th data-priority="2">Guest</th>
      <th class="fit">From</th>
      <th class="fit">To</th>
      <th>Reason</th>
      <th class="fit no-sort" data-priority="3">&nbsp</th>
    </tr>
  </thead>
  <tbody>
    <?php foreach ($rows as $row): ?>
      <tr data-id="<?= $row->id ?>" data-confirmed="<?= $row->confirmed ?>">
        <td class="text-center fit" data-order="<?= $row->confirmed ?>">
          <?php if ($row->confirmed): ?>
            <i class="fa-solid fa-circle-check fa-xl text-success"></i>
          <?php else: ?>
            <i class="fa-solid fa-circle-xmark fa-xl text-black text-opacity-25"></i>
          <?php endif; ?>
        </td>
        <td><?= $row->vehicle ?></td>
        <td><?= $row->guest ?></td>
        <td class="fit datetime text-center align-middle" data-order="<?= $row->start_datetime ?>"><?= $row->start_datetime ?></td>
        <td class="fit datetime text-center align-middle" data-order="<?= $row->end_datetime ?>"><?= $row->end_datetime ?></td>
        <td><?= $row->reason ?></td>
        <td class="text-center align-middle">
          <i class="fa-solid fa-ellipsis fa-xl text-body-tertiary action-icon pointer hidden-content" onclick="$(document).trigger('listActionItem:reservation', this)"></i>
        </td>
      </tr>
    <?php endforeach; ?>
  </tbody>
</table>


<script>

  if (!documentEventExists('reservation:reloadList')) {
    $(document).on('reservation:reloadList', async (e) => {
      $(document).trigger('loadMainSection', {
        sectionId: 'reservations',
        url: 'section.list-reservations.php',
        forceReload: true
      });
    });
  }

  if (!documentEventExists('listActionItem:reservation')) {
    $(document).on('listActionItem:reservation', async (e, el) => {
      e.stopPropagation();
      e.stopImmediatePropagation();

      const id = $(el).closest('tr').data('id');
      const confirmed = $(el).closest('tr').data('confirmed');
      const offset = $(el).offset();
      const myRandomId = Math.random().toString(36).substring(7);

      // Remove any existing dropdown menus
      $(document).trigger('click');

      let additionalItems = '';
      if (!confirmed) {
        additionalItems += `
          <button class="dropdown-item" onclick="$(document).trigger('reservation:confirm', ${id})"><i class="fa-duotone fa-regular fa-file-check"></i> Confirm</button>
        `;
      }

      // Create the dropdown menu
      const dropdownMenu = `
        <div id="${myRandomId}" data-id="${id}" class="dropdown-menu show shadow" style="position: absolute; left: ${offset.top}px; top: ${offset.left}px; z-index: 1000;">
          <button class="dropdown-item" onclick="$(document).trigger('reservation:edit', ${id})"><i class="fa-duotone fa-regular fa-pen-to-square"></i> Edit</button>
          ${additionalItems}
          <hr class="dropdown-divider">
          <button class="dropdown-item text-danger" onclick="$(document).trigger('reservation:delete', ${id})"><i class="fa-duotone fa-regular fa-trash"></i> Delete</button>
        </div>
      `;

      // Append the dropdown menu to the body
      $('body').append(dropdownMenu);

      // Calculate the position of the dropdown menu
      const dropdownElement = $('#' + myRandomId);
      const dropdownWidth = dropdownElement.outerWidth();
      const leftPosition = event.pageX - dropdownWidth;

      // Set the position of the dropdown menu
      dropdownElement.css({
        left: `${leftPosition}px`,
        top: `${event.pageY}px`
      });

      console.log('dropdownElement:', dropdownElement);

      // Remove the dropdown menu when clicking outside
      setTimeout(() => {
        $(document).on('click', function() {
          $('#' + myRandomId).remove();
        });
      }, 100);
    });
  }

  if (!documentEventExists('reservation:edit')) {
    $(document).on('reservation:edit', async (e, id) => {
      $(document).trigger('loadMainSection', {
        sectionId: 'reservations',
        url: `section.edit-reservation.php?id=${id}`,
        forceReload: true
      });
    });
  }

  if (!documentEventExists('reservation:delete')) {
    $(document).on('reservation:delete', async (e, id) => {
      if (await ui.ask('Are you sure you want to delete this reservation?')) {
        const resp = await net.get('/api/get.delete-reservation.php', { id });
        if (resp.result) {
          ui.toastr.success('Reservation deleted successfully.', 'Success');
          $(document).trigger('reservationChange');
          return $(document).trigger('reservation:reloadList');
        }
        ui.toastr.error('Failed to delete reservation: ' + resp.error, 'Error');
      }
    });
  }

  if (!documentEventExists('reservation:confirm')) {
    $(document).on('reservation:confirm', async (e, id) => {
      const resp = await net.post('/api/post.confirm-reservation.php', { id });
      if (resp.result) {
        ui.toastr.success('Reservation confirmed successfully.', 'Success');
        $(document).trigger('reservationChange');
        return $(document).trigger('reservation:reloadList');
      }
      ui.toastr.error('Failed to confirm reservation: ' + resp.error, 'Error');
    });
  }


  $(async ƒ => {
    const tableId = 'table-reservations';
    const dataTableOptions = {
      responsive: true,
      paging: true,
      order: [[3, 'asc']],
    };
    const reloadOnEventName = 'reservationChange';
    const parentSectionId = `#<?= $_GET["loadedToId"] ?>`;
    const thisURI = `<?= $_SERVER['REQUEST_URI'] ?>`;

    initListPage({
      tableId,
      dataTableOptions,
      reloadOnEventName,
      parentSectionId,
      thisURI
    });
  });
</script>