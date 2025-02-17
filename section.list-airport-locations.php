<?php
require_once 'autoload.php';

use Transport\AirportLocation;
?>

<div class="d-flex justify-content-between top-page-buttons">
  <button class="btn btn-sm btn-outline-primary px-2 ms-auto me-2" onclick="$(document).trigger('airportLocations:reloadList');">
    <i class="fa-solid fa-arrow-rotate-right"></i>
    Reload
  </button>
  <button class=" btn btn-outline-primary btn-sm px-2" onclick="$(document).trigger('loadMainSection', { sectionId: 'airportLocations', url: 'section.edit-airport-location.php', forceReload: true });">
    New Airport Location
  </button>
</div>

<div class="d-flex justify-content-between mt-3">
  <h2>Airport Locations</h2>
</div>

<table id="table-airport-locations" class="table table-striped">
  <thead>
    <tr class="table-dark">
      <th>Airport</th>
      <th>Airline</th>
      <th>&nbsp;</th>
      <th>Location</th>
      <th class="fit no-sort" data-priority="1">&nbsp;</th>
    </tr>
  </thead>
  <tbody>
    <?php if ($rows = AirportLocation::getAll()): ?>
      <?php foreach ($rows as $row): ?>
        <tr data-id="<?= $row->id ?>">
          <td><?= $row->airport ?></td>
          <td><?= $row->airline ?></td>
          <td><?= $row->type ?></td>
          <td><?= $row->location ?></td>
          <td class="text-center align-middle">
            <i class="fa-solid fa-ellipsis fa-xl text-body-tertiary action-icon pointer hidden-content" onclick="$(document).trigger('listActionItem:airportLocation', this)"></i>
          </td>
        </tr>
      <?php endforeach; ?>
    <?php endif; ?>
  </tbody>
</table>

<script>

  if (!documentEventExists('airportLocations:reloadList')) {
    $(document).on('airportLocations:reloadList', async (e) => {
      $(document).trigger('loadMainSection', {
        sectionId: 'airportLocations',
        url: 'section.list-airport-locations.php',
        forceReload: true
      });
    });
  }

  if (!documentEventExists('listActionItem:airportLocation')) {
    $(document).on('listActionItem:airportLocation', async (e, el) => {
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
          <button class="dropdown-item" onclick="$(document).trigger('airportLocation:edit', ${id})"><i class="fa-duotone fa-regular fa-pen-to-square"></i> Edit</button>
          ${additionalItems}
          <hr class="dropdown-divider">
          <button class="dropdown-item text-danger" onclick="$(document).trigger('airportLocation:delete', ${id})"><i class="fa-duotone fa-regular fa-trash"></i> Delete</button>
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

  if (!documentEventExists('airportLocation:edit')) {
    $(document).on('airportLocation:edit', async (e, id) => {
      $(document).trigger('loadMainSection', {
        sectionId: 'airportLocations',
        url: `section.edit-airport-location.php?id=${id}`,
        forceReload: true
      });
    });
  }

  if (!documentEventExists('airportLocation:delete')) {
    $(document).on('airportLocation:delete', async (e, id) => {
      if (await ui.ask('Are you sure you want to delete this airport location?')) {
        const resp = await net.get('/api/get.delete-airport-location.php', {
          id
        });
        if (resp.result) {
          ui.toastr.success('Airport location deleted successfully.', 'Success');
          $(document).trigger('airportLocationChange');
          return $(document).trigger('airportLocations:reloadList');
        }
        ui.toastr.error('Failed to delete airport location: ' + resp.error, 'Error');
      }
    });
  }


  $(async ƒ => {
    const tableId = 'table-airport-locations';
    const dataTableOptions = {
      responsive: true,
      paging: true,
    };
    const reloadOnEventName = 'airportLocationChange';
    const parentSectionId = `#airportLocations`;
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