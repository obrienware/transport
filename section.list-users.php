<?php 
require_once 'autoload.php';

use Transport\User;
?>
<div class="container">
  <div class="d-flex justify-content-between mt-3">
    <h2>Users</h2>
    <button id="btn-add-user" class="btn btn-outline-primary btn-sm my-auto px-3">
      Add User
    </button>
  </div>
  <table id="table-users" class="table table-striped table-hover row-select">
    <thead>
      <tr class="table-dark">
        <th class="fit">ID</th>
        <th>Username</th>
        <th>Real Name</th>
        <th>Position</th>
        <th>Department</th>
        <th data-dt-order="disable">Roles</th>
      </tr>
    </thead>
    <tbody>
      <?php if ($rows = User::getUsers()): ?>
        <?php foreach ($rows as $row): ?>
          <tr data-id="<?=$row->id?>">
            <td class="fit"><?=$row->id?></td>
            <td><?=$row->username?></td>
            <td><?=$row->first_name.' '.$row->last_name?></td>
            <td><?=$row->position?></td>
            <td><?=$row->department?></td>
            <td>
              <?php if ($row->roles): ?>
                <?php $roles = explode(',', $row->roles); ?>
                <?php foreach ($roles as $role): ?>
                  <span class="badge text-bg-primary fw-light"><?=$role?></span>
                <?php endforeach; ?>
              <?php endif; ?>
            </td>
          </tr>
        <?php endforeach; ?>
      <?php endif; ?>
    </tbody>
  </table>
</div>

<script type="module">
  import { initListPage } from '/js/listpage-helper.js';

  $(async ƒ => {

    const tableId = 'table-users';
    const loadOnClick = {
      page: 'section.edit-user.php',
      tab: 'edit-user',
      title: 'User (edit)',
    }
    const dataTableOptions = {
      responsive: true
    };
    const reloadOnEventName = 'userChange';
    const parentSectionId = `#<?=$_GET["loadedToId"]?>`;
    const thisURI = `<?=$_SERVER['REQUEST_URI']?>`;

    initListPage({tableId, loadOnClick, dataTableOptions, reloadOnEventName, parentSectionId, thisURI});

    $('#btn-add-user').on('click', ƒ => {
      app.openTab('edit-user', 'User (edit)', `section.edit-user.php`);
    });

  });

</script>