<?php
require_once 'autoload.php';

use Transport\DriverNote;
?>
<div class="d-flex justify-content-between top-page-buttons">
  <button class="btn btn-sm btn-outline-primary px-2 ms-auto me-2" onclick="$(document).trigger('driverNotes:reloadList');">
    <i class="fa-solid fa-arrow-rotate-right"></i>
    Reload
  </button>
  <button class=" btn btn-outline-primary btn-sm px-2" onclick="$(document).trigger('loadMainSection', { sectionId: 'driverNotes', url: 'section.edit-driver-note.php', forceReload: true });">
    New Note
  </button>
</div>

<div class="d-flex justify-content-between mt-2">
  <h2>Driver Notes</h2>
</div>

<table id="tbl-driver-notes" class="table table-striped">
  <thead>
    <tr class="table-dark">
      <th>Title</th>
      <th class="fit no-sort" data-priority="1">&nbsp</th>
    </tr>
  </thead>
  <tbody>
    <?php if ($rows = DriverNote::getAll()): ?>
      <?php foreach ($rows as $row): ?>
        <tr data-id="<?=$row->id?>">
          <td><?=$row->title?></td>
          <td class="text-center align-middle">
            <i class="fa-solid fa-ellipsis fa-xl text-body-tertiary action-icon pointer hidden-content" onclick="$(document).trigger('listActionItem:driverNotes', this)"></i>
          </td>
        </tr>
      <?php endforeach; ?>
    <?php endif; ?>
  </tbody>
</table>

  
<script>

if (!documentEventExists('driverNotes:reloadList')) {
    $(document).on('driverNotes:reloadList', async (e) => {
      $(document).trigger('loadMainSection', {
        sectionId: 'driverNotes',
        url: 'section.list-driver-notes.php',
        forceReload: true
      });
    });
  }

  if (!documentEventExists('listActionItem:driverNotes')) {
    $(document).on('listActionItem:driverNotes', async (e, el) => {
      e.stopPropagation();
      e.stopImmediatePropagation();

      const id = $(el).closest('tr').data('id');
      const offset = $(el).offset();
      const myRandomId = Math.random().toString(36).substring(7);

      // Remove any existing dropdown menus
      $(document).trigger('click');

      // Create the dropdown menu
      const dropdownMenu = `
        <div id="${myRandomId}" data-id="${id}" class="dropdown-menu show shadow" style="position: absolute; left: ${offset.top}px; top: ${offset.left}px; z-index: 1000;">
          <button class="dropdown-item" onclick="$(document).trigger('driverNote:edit', ${id})"><i class="fa-duotone fa-regular fa-pen-to-square"></i> Edit</button>
          <hr class="dropdown-divider">
          <button class="dropdown-item text-danger" onclick="$(document).trigger('driverNote:delete', ${id})"><i class="fa-duotone fa-regular fa-trash"></i> Delete</button>
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

  if (!documentEventExists('driverNote:edit')) {
    $(document).on('driverNote:edit', async (e, id) => {
      $(document).trigger('loadMainSection', {
        sectionId: 'driverNotes',
        url: `section.edit-driver-note.php?id=${id}`,
        forceReload: true
      });
    });
  }

  if (!documentEventExists('driverNote:delete')) {
    $(document).on('driverNote:delete', async (e, id) => {
      if (await ui.ask('Are you sure you want to delete this note?')) {
        const resp = await net.get('/api/get.delete-driver-note.php', { id });
        if (resp.result) {
          ui.toastr.success('Note deleted successfully.', 'Success');
          $(document).trigger('driverNoteChange');
          return $(document).trigger('driverNotes:reloadList');
        }
        ui.toastr.error('Failed to delete note: ' + resp.error, 'Error');
      }
    });
  }



  $(async ƒ => {

    const tableId = 'tbl-driver-notes';
    const dataTableOptions = {
      responsive: true
    };
    const reloadOnEventName = 'driverNoteChange';
    const parentSectionId = `#<?=$_GET["loadedToId"]?>`;
    const thisURI = `<?=$_SERVER['REQUEST_URI']?>`;

    initListPage({tableId, dataTableOptions, reloadOnEventName, parentSectionId, thisURI});
  });

</script>

