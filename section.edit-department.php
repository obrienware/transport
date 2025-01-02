<?php
require_once 'class.department.php';
$department = new Department($_REQUEST['id']);
?>
<?php if (isset($_REQUEST['id']) && !$department->getId()): ?>

  <div class="container-fluid text-center">
    <div class="alert alert-danger mt-5 w-50 mx-auto">
      <h1 class="fw-bold">Oh dear!</h1>
      <p class="lead">I can't seem to find that department! <i class="fa-duotone fa-solid fa-face-thinking"></i></p>
    </div>
  </div>

<?php else: ?>

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

  <script type="text/javascript">

    $(async ƒ => {

      const departmentId = <?=$department->getId() ?: 'null'?>;
      $('#btn-save-department').off('click').on('click', async ƒ => {
        const resp = await post('/api/post.save-department.php', {
          id: departmentId,
          name: cleanVal('#department-name'),
          mayRequest: checked('#department-may-request'),
        });
        if (resp?.result) {
          $(document).trigger('departmentChange', {departmentId});
          app.closeOpenTab();
          if (departmentId) return toastr.success('Department saved.', 'Success');
          return toastr.success('Department added.', 'Success')
        }
        toastr.error(resp .result.errors[2], 'Error');
        console.log(resp);
      });

      $('#btn-delete-department').off('click').on('click', async ƒ => {
        if (await ask('Are you sure you want to delete this department?')) {
          const resp = await get('/api/get.delete-department.php', {
            id: departmentId
          });
          if (resp?.result) {
            $(document).trigger('departmentChange', {departmentId});
            app.closeOpenTab();
            return toastr.success('Department deleted.', 'Success')
          }
          console.log(resp);
          toastr.error('There seems to be a problem deleting department.', 'Error');
        }
      });

    });

  </script>

<?php endif; ?>