<?php
require_once 'autoload.php';

use Transport\Department;
use Transport\Utils;

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT, FILTER_NULL_ON_FAILURE);
$id = $id === false ? null : $id;
$department = new Department($id);

if (!is_null($id) && !$department->getId()) {
  exit(Utils::showResourceNotFound());
}
?>

<div class="container mt-2">
  <?php if ($department->getId()): ?>
    <h2>Edit Department</h2>
  <?php else: ?>
    <h2>Add Department</h2>
  <?php endif; ?>
  <div>
    <div class="row">
      <div class="col-md-6">

        <div class="form-floating mb-3">
          <input type="text" class="form-control" id="department-name" placeholder="Department Name" value="<?=$department->name?>">
          <label for="department-name">Department Name</label>
        </div>

        <div class="form-check">
          <input class="form-check-input" type="checkbox" value="cdl" id="department-may-request" <?=$department->mayRequest ? 'checked' : ''?>>
          <label class="form-check-label" for="department-may-request">Can Submit Requests</label>
        </div>

      </div>
    </div>

    <div class="row my-4">
      <div class="col d-flex justify-content-between">
        <?php if ($department->getId()): ?>
          <button class="btn btn-outline-danger px-4" id="btn-delete-department">Delete</button>
        <?php endif; ?>
        <button class="btn btn-primary px-4" id="btn-save-department">Save</button>
      </div>
    </div>

  </div>
</div>

<script type="module">
  import * as input from '/js/formatters.js';
  import * as ui from '/js/notifications.js';
  import * as net from '/js/network.js';

  $(async ƒ => {

    const departmentId = <?=$department->getId() ?: 'null'?>;
    $('#btn-save-department').off('click').on('click', async ƒ => {
      const resp = await net.post('/api/post.save-department.php', {
        id: departmentId,
        name: input.cleanVal('#department-name'),
        mayRequest: input.checked('#department-may-request'),
      });
      if (resp?.result) {
        $(document).trigger('departmentChange', {departmentId});
        app.closeOpenTab();
        if (departmentId) return ui.toastr.success('Department saved.', 'Success');
        return ui.toastr.success('Department added.', 'Success')
      }
      ui.toastr.error(resp .result.errors[2], 'Error');
      console.log(resp);
    });

    $('#btn-delete-department').off('click').on('click', async ƒ => {
      if (await ui.ask('Are you sure you want to delete this department?')) {
        const resp = await net.get('/api/get.delete-department.php', {
          id: departmentId
        });
        if (resp?.result) {
          $(document).trigger('departmentChange', {departmentId});
          app.closeOpenTab();
          return ui.toastr.success('Department deleted.', 'Success')
        }
        console.log(resp);
        ui.toastr.error('There seems to be a problem deleting department.', 'Error');
      }
    });

  });

</script>
