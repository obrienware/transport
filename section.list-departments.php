<?php 
require_once 'autoload.php';

use Transport\Department;
?>
<div class="container">
  <div class="d-flex justify-content-between mt-3">
    <h2>Departments</h2>
    <button id="btn-add-department" class="btn btn-outline-primary btn-sm my-auto px-3">
      Add Department
    </button>
  </div>
  <table id="table-departments" class="table table-striped table-hover row-select">
    <thead>
      <tr class="table-dark">
        <th>Departments</th>
        <th class="no-sort no-search text-center fit">May Request</th>
      </tr>
    </thead>
    <tbody>
      <?php if ($rows = Department::getAll()): ?>
        <?php foreach ($rows as $row): ?>
          <tr data-id="<?=$row->id?>">
            <td><?=$row->name?></td>
            <td class="text-center align-middle fit">
              <?php if ($row->can_submit_requests === 1): ?>
                <i class="fa-solid fa-circle-check fa-xl text-success"></i>
              <?php else: ?>
                <i class="fa-solid fa-circle-xmark fa-xl text-black text-opacity-25"></i>
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
      $('#<?=$_GET["loadedToId"]?>').load(`<?=$_SERVER['REQUEST_URI']?>`); // Refresh this page
    }

    if ($.fn.dataTable.isDataTable('#table-departments')) {
      dataTable = $('#table-departments').DataTable();
    } else {
      dataTable = $('#table-departments').DataTable({
        responsive: true
      });
    }

    $('#table-departments tbody tr').on('click', ƒ => {
      ƒ.preventDefault(); // in the case of an anchor tag. (we don't want to navigating anywhere)
      const self = ƒ.currentTarget;
      const id = $(self).data('id');
      targetId = id;
      app.openTab('edit-department', 'Department (edit)', `section.edit-department.php?id=${id}`);
    });

    $('#btn-add-department').off('click').on('click', ƒ => {
      app.openTab('edit-department', 'Department (add)', `section.edit-department.php`);
    });

    $(document).off('departmentChange.ns').on('departmentChange.ns', reloadSection);

  });

</script>
