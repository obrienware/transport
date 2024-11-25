<?php require_once 'class.user.php';?>
<div class="container">
  <div class="d-flex justify-content-between mt-3">
    <h2>Users</h2>
    <button id="btn-add-user" class="btn btn-outline-primary btn-sm my-auto px-3">
      <i class="fa-duotone fa-solid fa-user-plus"></i>
      Add User
    </button>
  </div>
  <table id="table-users" class="table table-striped table-hover row-select">
    <thead>
      <tr>
        <th>Username</th>
        <th>Real Name</th>
        <th>Position</th>
        <th>Department</th>
        <th data-dt-order="disable">Roles</th>
      </tr>
    </thead>
    <tbody>
      <?php if ($rs = User::getUsers()): ?>
        <?php foreach ($rs as $item): ?>
          <tr data-id="<?=$item->id?>">
            <td><?=$item->username?></td>
            <td><?=$item->first_name.' '.$item->last_name?></td>
            <td><?=$item->position?></td>
            <td><?=$item->department?></td>
            <td>
              <?php if ($item->roles): ?>
                <?php $roles = explode(',', $item->roles); ?>
                <?php foreach ($roles as $role): ?>
                  <span class="badge text-bg-primary"><?=$role?></span>
                <?php endforeach; ?>
              <?php endif; ?>
            </td>
          </tr>
        <?php endforeach; ?>
      <?php endif; ?>
    </tbody>
  </table>
</div>

<script type="text/javascript">

  $(async ƒ => {

    let dataTable;
    let targetId;

    function reloadSection () {
      $('#<?=$_REQUEST["loadedToId"]?>').load(`<?=$_SERVER['REQUEST_URI']?>`); // Refresh this page
    }

    if ($.fn.dataTable.isDataTable('#table-users')) {
      dataTable = $('#table-users').DataTable();
    } else {
      dataTable = $('#table-users').DataTable({
        responsive: true
      });
    }

    $('#table-users tbody tr').off('click').on('click', ƒ => {
      ƒ.preventDefault(); // in the case of an anchor tag. (we don't want to navigating anywhere)
      const self = ƒ.currentTarget;
      const id = $(self).data('id');
      targetId = id;
      app.openTab('edit-user', 'User (edit)', `section.edit-user.php?id=${id}`);
    });
    $('#btn-add-user').on('click', ƒ => {
      app.openTab('edit-user', 'User (edit)', `section.edit-user.php`);
    });

    $(document).off('userChange.ns').on('userChange.ns', reloadSection);

  });

</script>