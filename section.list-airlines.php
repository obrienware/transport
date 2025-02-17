<?php
require_once 'autoload.php';

use Transport\Airline;
?>

<div class="d-flex justify-content-between top-page-buttons">
  <button class="btn btn-sm btn-outline-primary px-2 ms-auto me-2" onclick="$(document).trigger('airlines:reloadList');">
    <i class="fa-solid fa-arrow-rotate-right"></i>
    Reload
  </button>
  <button class=" btn btn-outline-primary btn-sm px-2" onclick="$(document).trigger('loadMainSection', { sectionId: 'airlines', url: 'section.edit-airline.php', forceReload: true });">
    New Airline
  </button>
</div>

<div class="d-flex justify-content-between mt-3">
  <h2>Airlines</h2>
</div>

<table id="table-airlines" class="table table-striped table-hover row-select">
  <thead>
    <tr class="table-dark">
      <th data-dt-order="disable">&nbsp;</th>
      <th>AirLine</th>
      <th data-bs-toggle="tooltip" data-bs-title="Flight Number Prefix.">Prefix</th>
      <th class="fit no-sort" data-priority="1">&nbsp;</th>
    </tr>
  </thead>
  <tbody>
    <?php if ($rows = Airline::getAll()): ?>
      <?php foreach ($rows as $row): ?>
        <tr data-id="<?= $row->id ?>">
          <td>
            <?php if ($row->image_filename): ?>
              <img src="/images/airlines/<?= $row->image_filename ?>" style="max-height:35px">
            <?php endif; ?>
          </td>
          <td class="align-middle"><?= $row->name ?></td>
          <td class="fw-bold fs-4 align-middle"><?= $row->flight_number_prefix ?></td>
          <td class="text-center align-middle">
            <i class="fa-solid fa-ellipsis fa-xl text-body-tertiary action-icon pointer hidden-content" onclick="$(document).trigger('listActionItem:airline', this)"></i>
          </td>

        </tr>
      <?php endforeach; ?>
    <?php endif; ?>
  </tbody>
</table>


<script>

  if (!documentEventExists('airlines:reloadList')) {
    $(document).on('airlines:reloadList', async (e) => {
      $(document).trigger('loadMainSection', {
        sectionId: 'airlines',
        url: 'section.list-airlines.php',
        forceReload: true
      });
    });
  }

  if (!documentEventExists('listActionItem:airline')) {
    $(document).on('listActionItem:airline', async (e, el) => {
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
          <button class="dropdown-item" onclick="$(document).trigger('airline:edit', ${id})"><i class="fa-duotone fa-regular fa-pen-to-square"></i> Edit</button>
          ${additionalItems}
          <hr class="dropdown-divider">
          <button class="dropdown-item text-danger" onclick="$(document).trigger('airline:delete', ${id})"><i class="fa-duotone fa-regular fa-trash"></i> Delete</button>
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

  if (!documentEventExists('airline:edit')) {
    $(document).on('airline:edit', async (e, id) => {
      $(document).trigger('loadMainSection', {
        sectionId: 'airlines',
        url: `section.edit-airline.php?id=${id}`,
        forceReload: true
      });
    });
  }

  if (!documentEventExists('airline:delete')) {
    $(document).on('airline:delete', async (e, id) => {
      if (await ui.ask('Are you sure you want to delete this airline?')) {
        const resp = await net.get('/api/get.delete-airline.php', {
          id
        });
        if (resp.result) {
          ui.toastr.success('Airline deleted successfully.', 'Success');
          $(document).trigger('airlineChange');
          return $(document).trigger('airlines:reloadList');
        }
        ui.toastr.error('Failed to delete airline: ' + resp.error, 'Error');
      }
    });
  }

  $(async ƒ => {
    const tableId = 'table-airlines';
    const dataTableOptions = {
      responsive: true
    };
    const reloadOnEventName = 'airlineChange';
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