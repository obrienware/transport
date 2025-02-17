<?php
require_once 'autoload.php';

use Transport\User;
?>

<div class="d-flex justify-content-between top-page-buttons">
  <button class="btn btn-sm btn-outline-primary px-2 ms-auto me-2" onclick="$(document).trigger('users:reloadList');">
    <i class="fa-solid fa-arrow-rotate-right"></i>
    Reload
  </button>
  <button class=" btn btn-outline-primary btn-sm px-2" onclick="$(document).trigger('loadMainSection', { sectionId: 'users', url: 'section.edit-user.php', forceReload: true });">
    New User
  </button>
</div>

<div class="d-flex justify-content-between mt-3">
  <h2>Users</h2>
</div>

<table id="table-users" class="table table-striped">
  <thead>
    <tr class="table-dark">
      <th class="fit">ID</th>
      <th>Username</th>
      <th>Real Name</th>
      <th>Position</th>
      <th>Department</th>
      <th data-dt-order="disable">Roles</th>
      <th class="fit no-sort" data-priority="1">&nbsp;</th>
    </tr>
  </thead>
  <tbody>
    <?php if ($rows = User::getUsers()): ?>
      <?php foreach ($rows as $row): ?>
        <tr data-id="<?= $row->id ?>">
          <td class="fit"><?= $row->id ?></td>
          <td><?= $row->username ?></td>
          <td><?= $row->first_name . ' ' . $row->last_name ?></td>
          <td><?= $row->position ?></td>
          <td><?= $row->department ?></td>
          <td>
            <?php if ($row->roles): ?>
              <?php $roles = explode(',', $row->roles); ?>
              <?php foreach ($roles as $role): ?>
                <span class="badge text-bg-primary fw-light"><?= $role ?></span>
              <?php endforeach; ?>
            <?php endif; ?>
          </td>
          <td class="text-center align-middle">
            <i class="fa-solid fa-ellipsis fa-xl text-body-tertiary action-icon pointer hidden-content" onclick="$(document).trigger('listActionItem:user', this)"></i>
          </td>
        </tr>
      <?php endforeach; ?>
    <?php endif; ?>
  </tbody>
</table>


<script>
  
  if (!documentEventExists('users:reloadList')) {
    $(document).on('users:reloadList', async (e) => {
      $(document).trigger('loadMainSection', {
        sectionId: 'users',
        url: 'section.list-users.php',
        forceReload: true
      });
    });
  }

  if (!documentEventExists('listActionItem:user')) {
    $(document).on('listActionItem:user', async (e, el) => {
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
          <button class="dropdown-item" onclick="$(document).trigger('user:edit', ${id})"><i class="fa-duotone fa-regular fa-pen-to-square"></i> Edit</button>
          ${additionalItems}
          <hr class="dropdown-divider">
          <button class="dropdown-item text-danger" onclick="$(document).trigger('user:delete', ${id})"><i class="fa-duotone fa-regular fa-trash"></i> Delete</button>
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

  if (!documentEventExists('user:edit')) {
    $(document).on('user:edit', async (e, id) => {
      $(document).trigger('loadMainSection', {
        sectionId: 'users',
        url: `section.edit-user.php?id=${id}`,
        forceReload: true
      });
    });
  }

  if (!documentEventExists('user:delete')) {
    $(document).on('user:delete', async (e, id) => {
      if (await ui.ask('Are you sure you want to delete this user?')) {
        const resp = await net.get('/api/get.delete-user.php', {
          id
        });
        if (resp.result) {
          ui.toastr.success('User deleted successfully.', 'Success');
          $(document).trigger('userChange');
          return $(document).trigger('users:reloadList');
        }
        ui.toastr.error('Failed to delete user: ' + resp.error, 'Error');
      }
    });
  }


  $(async ƒ => {

    const tableId = 'table-users';
    const dataTableOptions = {
      responsive: true,
      paging: true,
    };
    const reloadOnEventName = 'userChange';
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