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

<script type="module">
  import { initListPage } from '/js/listpage-helper.js';

  $(async ƒ => {

    const tableId = 'table-departments';
    const loadOnClick = {
      page: 'section.edit-department.php',
      tab: 'edit-department',
      title: 'Department (edit)',
    }
    const dataTableOptions = {
      responsive: true
    };
    const reloadOnEventName = 'departmentChange';
    const parentSectionId = `#<?=$_GET["loadedToId"]?>`;
    const thisURI = `<?=$_SERVER['REQUEST_URI']?>`;

    initListPage({tableId, loadOnClick, dataTableOptions, reloadOnEventName, parentSectionId, thisURI});

    $('#btn-add-department').off('click').on('click', ƒ => {
      app.openTab('edit-department', 'Department (add)', `section.edit-department.php`);
    });

  });

</script>
