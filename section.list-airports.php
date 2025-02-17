<?php
require_once 'autoload.php';

use Transport\Airport;
?>

<div class="d-flex justify-content-between top-page-buttons">
  <button class="btn btn-sm btn-outline-primary px-2 ms-auto me-2" onclick="$(document).trigger('airports:reloadList');">
    <i class="fa-solid fa-arrow-rotate-right"></i>
    Reload
  </button>
  <button class=" btn btn-outline-primary btn-sm px-2" onclick="$(document).trigger('loadMainSection', { sectionId: 'airports', url: 'section.edit-airport.php', forceReload: true });">
    New Airport
  </button>
</div>

<div class="d-flex justify-content-between mt-3">
  <h2>Airports</h2>
</div>

<table id="table-airports" class="table table-striped table-hover row-select">
  <thead>
    <tr class="table-dark">
      <th>IATA</th>
      <th>Airport</th>
      <th data-bs-toggle="tooltip" data-bs-title="Time to arrive at airport before scheduled flight.">Lead Time</th>
      <th data-bs-toggle="tooltip" data-bs-title="Time to travel from airport to staging location.">Travel Time</th>
      <th class="fit no-sort" data-priority="1">&nbsp;</th>
    </tr>
  </thead>
  <tbody>
    <?php if ($rows = Airport::getAll()): ?>
      <?php foreach ($rows as $row): ?>
        <tr data-id="<?= $row->id ?>">
          <td><?= $row->iata ?></td>
          <td><?= $row->name ?></td>
          <td data-order="<?= $row->lead_time ?>"><?= intdiv($row->lead_time, 60) . ':' . sprintf('%02s', ($row->lead_time % 60)) ?></td>
          <td data-order="<?= $row->travel_time ?>"><?= intdiv($row->travel_time, 60) . ':' . sprintf('%02s', ($row->travel_time % 60)) ?></td>
          <td class="text-center align-middle">
            <i class="fa-solid fa-ellipsis fa-xl text-body-tertiary action-icon pointer hidden-content" onclick="$(document).trigger('listActionItem:airport', this)"></i>
          </td>
        </tr>
      <?php endforeach; ?>
    <?php endif; ?>
  </tbody>
</table>


<script>

  if (!documentEventExists('airports:reloadList')) {
    $(document).on('airports:reloadList', async (e) => {
      $(document).trigger('loadMainSection', {
        sectionId: 'airports',
        url: 'section.list-airports.php',
        forceReload: true
      });
    });
  }

  if (!documentEventExists('listActionItem:airport')) {
    $(document).on('listActionItem:airport', async (e, el) => {
      e.stopPropagation();
      e.stopImmediatePropagation();

      const id = $(el).closest('tr').data('id');
      const offset = $(el).offset();
      const myRandomId = Math.random().toString(36).substring(7);

      // Remove any existing dropdown menus
      $(document).trigger('click');

      let additionalItems = '';

      // Create the dropdown menu
      const dropdownMenu = `
        <div id="${myRandomId}" data-id="${id}" class="dropdown-menu show shadow" style="position: absolute; left: ${offset.top}px; top: ${offset.left}px; z-index: 1000;">
          <button class="dropdown-item" onclick="$(document).trigger('airport:edit', ${id})"><i class="fa-duotone fa-regular fa-pen-to-square"></i> Edit</button>
          ${additionalItems}
          <hr class="dropdown-divider">
          <button class="dropdown-item text-danger" onclick="$(document).trigger('airport:delete', ${id})"><i class="fa-duotone fa-regular fa-trash"></i> Delete</button>
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

  if (!documentEventExists('airport:edit')) {
    $(document).on('airport:edit', async (e, id) => {
      $(document).trigger('loadMainSection', {
        sectionId: 'airports',
        url: `section.edit-airport.php?id=${id}`,
        forceReload: true
      });
    });
  }

  if (!documentEventExists('airport:delete')) {
    $(document).on('airport:delete', async (e, id) => {
      if (await ui.ask('Are you sure you want to delete this airport?')) {
        const resp = await net.get('/api/get.delete-airport.php', {
          id
        });
        if (resp.result) {
          ui.toastr.success('Airport deleted successfully.', 'Success');
          $(document).trigger('airportChange');
          return $(document).trigger('airports:reloadList');
        }
        ui.toastr.error('Failed to delete airport: ' + resp.error, 'Error');
      }
    });
  }

  $(async ƒ => {
    const tableId = 'table-airports';
    const dataTableOptions = {
      responsive: true
    };
    const reloadOnEventName = 'airportChange';
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