<?php 
require_once 'autoload.php';

use Transport\Blockout;
?>

<div class="d-flex justify-content-between top-page-buttons">
  <button class="btn btn-sm btn-outline-primary px-2 ms-auto me-2" onclick="$(document).trigger('blockout:reloadList');">
    <i class="fa-solid fa-arrow-rotate-right"></i>
    Reload
  </button>
  <button class=" btn btn-outline-primary btn-sm px-2" onclick="$(document).trigger('loadMainSection', { sectionId: 'blockouts', url: 'section.edit-blockout.php', forceReload: true });">
    New Block Out
  </button>
</div>

<div class="d-flex justify-content-between mt-2">
  <h2>Driver Block Out Dates</h2>
</div>

<table id="table-blockouts" class="table table-striped">
  <thead>
    <tr class="table-dark">
      <th>Name</th>
      <th class="fit">From</th>
      <th class="fit">To</th>
      <th data-dt-order="disable">Note</th>
      <th class="fit no-sort" data-dt-order="disable" data-priority="1">&nbsp;</th>
    </tr>
  </thead>
  <tbody>
    <?php if ($rows = Blockout::getAll()): ?>
      <?php foreach ($rows as $row): ?>
        <tr data-id="<?=$row->id?>">
          <td><?=$row->user?></td>
          <td class="datetime fit" data-order="<?=$row->from_datetime?>"><?=$row->from_datetime?></td>
          <td class="datetime fit" data-order="<?=$row->to_datetime?>"><?=$row->to_datetime?></td>
          <td><?=$row->note?></td>
          <td class="text-center align-middle">
            <i class="fa-solid fa-ellipsis fa-xl text-body-tertiary action-icon pointer hidden-content" onclick="$(document).trigger('listActionItem:blockout', this)"></i>
          </td>
        </tr>
      <?php endforeach; ?>
    <?php endif; ?>
  </tbody>
</table>

<script>

  if (!documentEventExists('blockout:reloadList')) {
    $(document).on('blockout:reloadList', async (e) => {
      $(document).trigger('loadMainSection', {
        sectionId: 'blockouts',
        url: 'section.list-blockout-dates.php',
        forceReload: true
      });
    });
  }

  if (!documentEventExists('listActionItem:blockout')) {
    $(document).on('listActionItem:blockout', async (e, el) => {
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
          <button class="dropdown-item" onclick="$(document).trigger('blockout:edit', ${id})"><i class="fa-duotone fa-regular fa-pen-to-square"></i> Edit</button>
          <hr class="dropdown-divider">
          <button class="dropdown-item text-danger" onclick="$(document).trigger('blockout:delete', ${id})"><i class="fa-duotone fa-regular fa-trash"></i> Delete</button>
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

  if (!documentEventExists('blockout:edit')) {
    $(document).on('blockout:edit', async (e, id) => {
      $(document).trigger('loadMainSection', {
        sectionId: 'blockouts',
        url: `section.edit-blockout.php?id=${id}`,
        forceReload: true
      });
    });
  }

  if (!documentEventExists('blockout:delete')) {
    $(document).on('blockout:delete', async (e, id) => {
      if (await ui.ask('Are you sure you want to delete this blockout date?')) {
        const resp = await net.get('/api/get.delete-blockout.php', { id });
        if (resp.result) {
          ui.toastr.success('Blockout date deleted successfully.', 'Success');
          $(document).trigger('blockoutChange');
          return $(document).trigger('blockout:reloadList');
        }
        ui.toastr.error('Failed to delete blockout date: ' + resp.error, 'Error');
      }
    });
  }

  $(async ƒ => {

    const tableId = 'table-blockouts';
    const dataTableOptions = {
      responsive: true
    };
    const reloadOnEventName = 'blockoutChange';
    const parentSectionId = `#<?=$_GET["loadedToId"]?>`;
    const thisURI = `<?=$_SERVER['REQUEST_URI']?>`;

    initListPage({tableId, dataTableOptions, reloadOnEventName, parentSectionId, thisURI});
  });

</script>
