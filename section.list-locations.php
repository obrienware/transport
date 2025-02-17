<?php
require_once 'autoload.php';

use Transport\Location;
?>

<div class="d-flex justify-content-between top-page-buttons">
  <button class="btn btn-sm btn-outline-primary px-2 ms-auto me-2" onclick="$(document).trigger('locations:reloadList');">
    <i class="fa-solid fa-arrow-rotate-right"></i>
    Reload
  </button>
  <button class=" btn btn-outline-primary btn-sm px-2" onclick="$(document).trigger('loadMainSection', { sectionId: 'locations', url: 'section.edit-location.php', forceReload: true });">
    New Location
  </button>
</div>

<div class="d-flex justify-content-between mt-3">
  <h2>Locations</h2>
</div>

<table id="table-locations" class="table table-striped">
  <thead>
    <tr class="table-dark">
      <th>Name</th>
      <th data-priority="2">Short Name</th>
      <th>Type</th>
      <th>Map Address</th>
      <th class="fit no-sort" data-priority="1">&nbsp;</th>
    </tr>
  </thead>
  <tbody>
    <?php if ($rows = Location::getAll()): ?>
      <?php foreach ($rows as $row): ?>
        <tr data-id="<?= $row->id ?>">
          <td><?= $row->name ?></td>
          <td><?= $row->short_name ?></td>
          <td><?= $row->type ?></td>
          <td data-order="<?= $row->map_address ?>">
            <?php if ($row->place_id): ?>
              <i class="fa-solid fa-location-check fa-xl text-primary me-2"></i>
            <?php else: ?>
              <i class="fa-solid fa-location-exclamation fa-xl text-warning me-2"></i>
            <?php endif; ?>
            <?= $row->map_address ?>
          </td>
          <td class="text-center align-middle">
            <i class="fa-solid fa-ellipsis fa-xl text-body-tertiary action-icon pointer hidden-content" onclick="$(document).trigger('listActionItem:location', this)"></i>
          </td>
        </tr>
      <?php endforeach; ?>
    <?php endif; ?>
  </tbody>
</table>


<script>
  if (!documentEventExists('locations:reloadList')) {
    $(document).on('locations:reloadList', async (e) => {
      $(document).trigger('loadMainSection', {
        sectionId: 'locations',
        url: 'section.list-locations.php',
        forceReload: true
      });
    });
  }

  if (!documentEventExists('listActionItem:location')) {
    $(document).on('listActionItem:location', async (e, el) => {
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
          <button class="dropdown-item" onclick="$(document).trigger('location:edit', ${id})"><i class="fa-duotone fa-regular fa-pen-to-square"></i> Edit</button>
          ${additionalItems}
          <hr class="dropdown-divider">
          <button class="dropdown-item text-danger" onclick="$(document).trigger('location:delete', ${id})"><i class="fa-duotone fa-regular fa-trash"></i> Delete</button>
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

  if (!documentEventExists('location:edit')) {
    $(document).on('location:edit', async (e, id) => {
      $(document).trigger('loadMainSection', {
        sectionId: 'locations',
        url: `section.edit-location.php?id=${id}`,
        forceReload: true
      });
    });
  }

  if (!documentEventExists('location:delete')) {
    $(document).on('location:delete', async (e, id) => {
      if (await ui.ask('Are you sure you want to delete this location?')) {
        const resp = await net.get('/api/get.delete-location.php', {
          id
        });
        if (resp.result) {
          ui.toastr.success('Location deleted successfully.', 'Success');
          $(document).trigger('locationChange');
          return $(document).trigger('locations:reloadList');
        }
        ui.toastr.error('Failed to delete location: ' + resp.error, 'Error');
      }
    });
  }

  $(async ƒ => {

    const tableId = 'table-locations';
    const dataTableOptions = {
      responsive: true,
      paging: true,
    };
    const reloadOnEventName = 'locationChange';
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