<?php
require_once 'autoload.php';

use Transport\Guest;
?>

<div class="d-flex justify-content-between top-page-buttons">
  <button class="btn btn-sm btn-outline-primary px-2 ms-auto me-2" onclick="$(document).trigger('guests:reloadList');">
    <i class="fa-solid fa-arrow-rotate-right"></i>
    Reload
  </button>
  <button class=" btn btn-outline-primary btn-sm px-2" onclick="$(document).trigger('loadMainSection', { sectionId: 'guests', url: 'section.edit-guest.php', forceReload: true });">
    New Contact / Guest
  </button>
</div>

<div class="d-flex justify-content-between mt-3">
  <h2>Contact List / Guests</h2>
</div>


<table id="table-guests" class="table table-striped">
  <thead>
    <tr class="table-dark">
      <th>Contact Name</th>
      <th>Type</th>
      <th>Phone</th>
      <th>Email</th>
      <th data-bs-toggle="tooltip" data-bs-title="Contact opted in for text notifications">Notifications</th>
      <th class="fit no-sort" data-priority="1">&nbsp;</th>
    </tr>
  </thead>
  <tbody>
    <?php if ($rows = Guest::getAll()): ?>
      <?php foreach ($rows as $row): ?>
        <tr data-id="<?= $row->id ?>">
          <td><?= $row->first_name . ' ' . $row->last_name ?></td>
          <td><?= $row->type ?></td>
          <td><?= $row->phone_number ?></td>
          <td><?= $row->email_address ?></td>
          <td>
            <?php if ($row->opt_in): ?>
              <?php if (!$row->opt_out): ?>
                <span class="badge bg-success fw-light">Opted In</span>
              <?php endif; ?>
            <?php endif; ?>
          </td>
          <td class="text-center align-middle">
            <i class="fa-solid fa-ellipsis fa-xl text-body-tertiary action-icon pointer hidden-content" onclick="$(document).trigger('listActionItem:guest', this)"></i>
          </td>
        </tr>
      <?php endforeach; ?>
    <?php endif; ?>
  </tbody>
</table>


<script>

if (!documentEventExists('guests:reloadList')) {
    $(document).on('guests:reloadList', async (e) => {
      $(document).trigger('loadMainSection', {
        sectionId: 'guests',
        url: 'section.list-guests.php',
        forceReload: true
      });
    });
  }

  if (!documentEventExists('listActionItem:guest')) {
    $(document).on('listActionItem:guest', async (e, el) => {
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
          <button class="dropdown-item" onclick="$(document).trigger('guest:edit', ${id})"><i class="fa-duotone fa-regular fa-pen-to-square"></i> Edit</button>
          ${additionalItems}
          <hr class="dropdown-divider">
          <button class="dropdown-item text-danger" onclick="$(document).trigger('guest:delete', ${id})"><i class="fa-duotone fa-regular fa-trash"></i> Delete</button>
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

  if (!documentEventExists('guest:edit')) {
    $(document).on('guest:edit', async (e, id) => {
      $(document).trigger('loadMainSection', {
        sectionId: 'guests',
        url: `section.edit-guest.php?id=${id}`,
        forceReload: true
      });
    });
  }

  if (!documentEventExists('guest:delete')) {
    $(document).on('guest:delete', async (e, id) => {
      if (await ui.ask('Are you sure you want to delete this contact/guest?')) {
        const resp = await net.get('/api/get.delete-guest.php', {
          id
        });
        if (resp.result) {
          ui.toastr.success('Guest deleted successfully.', 'Success');
          $(document).trigger('guestChange');
          return $(document).trigger('guests:reloadList');
        }
        ui.toastr.error('Failed to delete contact/guest: ' + resp.error, 'Error');
      }
    });
  }

  $(async ƒ => {
    const tableId = 'table-guests';
    const dataTableOptions = {
      responsive: true,
      paging: true,
    };
    const reloadOnEventName = 'guestChange';
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