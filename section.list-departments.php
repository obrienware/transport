<?php
require_once 'autoload.php';

use Transport\Department;
?>

<div class="d-flex justify-content-between top-page-buttons">
  <button class="btn btn-sm btn-outline-primary px-2 ms-auto me-2" onclick="$(document).trigger('departments:reloadList');">
    <i class="fa-solid fa-arrow-rotate-right"></i>
    Reload
  </button>
  <button class=" btn btn-outline-primary btn-sm px-2" onclick="$(document).trigger('loadMainSection', { sectionId: 'departments', url: 'section.edit-department.php', forceReload: true });">
    New Department
  </button>
</div>

<div class="d-flex justify-content-between mt-3">
  <h2>Departments</h2>
</div>

<table id="table-departments" class="table table-striped">
  <thead>
    <tr class="table-dark">
      <th>Departments</th>
      <th class="no-sort no-search text-center fit">May Request</th>
      <th class="fit no-sort no-search" data-priority="1">&nbsp;</th>
    </tr>
  </thead>
  <tbody>
    <?php if ($rows = Department::getAll()): ?>
      <?php foreach ($rows as $row): ?>
        <tr data-id="<?= $row->id ?>">
          <td><?= $row->name ?></td>
          <td class="text-center align-middle fit">
            <?php if ($row->can_submit_requests === 1): ?>
              <i class="fa-solid fa-circle-check fa-xl text-success"></i>
            <?php else: ?>
              <i class="fa-solid fa-circle-xmark fa-xl text-black text-opacity-25"></i>
            <?php endif; ?>
          </td>
          <td class="text-center align-middle">
            <i class="fa-solid fa-ellipsis fa-xl text-body-tertiary action-icon pointer hidden-content" onclick="$(document).trigger('listActionItem:department', this)"></i>
          </td>
        </tr>
      <?php endforeach; ?>
    <?php endif; ?>
  </tbody>
</table>


<script>

if (!documentEventExists('departments:reloadList')) {
    $(document).on('departments:reloadList', async (e) => {
      $(document).trigger('loadMainSection', {
        sectionId: 'departments',
        url: 'section.list-departments.php',
        forceReload: true
      });
    });
  }

  if (!documentEventExists('listActionItem:department')) {
    $(document).on('listActionItem:department', async (e, el) => {
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
          <button class="dropdown-item" onclick="$(document).trigger('department:edit', ${id})"><i class="fa-duotone fa-regular fa-pen-to-square"></i> Edit</button>
          ${additionalItems}
          <hr class="dropdown-divider">
          <button class="dropdown-item text-danger" onclick="$(document).trigger('department:delete', ${id})"><i class="fa-duotone fa-regular fa-trash"></i> Delete</button>
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

  if (!documentEventExists('department:edit')) {
    $(document).on('department:edit', async (e, id) => {
      $(document).trigger('loadMainSection', {
        sectionId: 'departments',
        url: `section.edit-department.php?id=${id}`,
        forceReload: true
      });
    });
  }

  if (!documentEventExists('department:delete')) {
    $(document).on('department:delete', async (e, id) => {
      if (await ui.ask('Are you sure you want to delete this department?')) {
        const resp = await net.get('/api/get.delete-department.php', {
          id
        });
        if (resp.result) {
          ui.toastr.success('Department deleted successfully.', 'Success');
          $(document).trigger('departmentChange');
          return $(document).trigger('departments:reloadList');
        }
        ui.toastr.error('Failed to delete department: ' + resp.error, 'Error');
      }
    });
  }

  
  $(async ƒ => {
    const tableId = 'table-departments';
    const dataTableOptions = {
      responsive: true,
    };
    const reloadOnEventName = 'departmentChange';
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