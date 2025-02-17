<?php
require_once 'autoload.php';

use Transport\Department;
use Generic\Utils;

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT, FILTER_NULL_ON_FAILURE);
$id = $id === false ? null : $id;
$department = new Department($id);

if (!is_null($id) && !$department->getId())
{
  exit(Utils::showResourceNotFound());
}
$departmentId = $department->getId();
?>

<!-- Back button -->
<div class="d-flex justify-content-between top-page-buttons mb-2">
  <button class="btn btn-sm btn-outline-primary px-2 me-auto" onclick="$(document).trigger('departments:reloadList');">
    <i class="fa-solid fa-chevron-left"></i>
    List
  </button>
</div>

<?php if ($department->getId()): ?>
  <h2>Edit Department</h2>
  <input type="hidden" id="department-id" value="<?= $departmentId ?>">
<?php else: ?>
  <h2>Add Department</h2>
  <input type="hidden" id="department-id" value="">
<?php endif; ?>


<div class="row">

  <div class="col-12 col-lg-4 col-xxl-3">
    <div class="mb-3">
      <label class="form-label" for="department-name">Department Name</label>
      <input type="text" class="form-control" id="department-name" placeholder="Department Name" value="<?= $department->name ?>">
    </div>
  </div>

  <div class="col-12 col-lg-4">
    <div class="mb-3 mt-4">
      <div class="pretty p-svg p-curve p-bigger">
        <input class="" type="checkbox" value="yes" id="department-may-request" <?= $department->mayRequest ? 'checked' : '' ?>>
        <div class="state p-primary">
          <!-- svg path -->
          <svg class="svg svg-icon" viewBox="0 0 20 20">
            <path d="M7.629,14.566c0.125,0.125,0.291,0.188,0.456,0.188c0.164,0,0.329-0.062,0.456-0.188l8.219-8.221c0.252-0.252,0.252-0.659,0-0.911c-0.252-0.252-0.659-0.252-0.911,0l-7.764,7.763L4.152,9.267c-0.252-0.251-0.66-0.251-0.911,0c-0.252,0.252-0.252,0.66,0,0.911L7.629,14.566z" style="stroke: white;fill:white;"></path>
          </svg>
          <label>May Submit Requests</label>
        </div>
      </div>
    </div>
  </div>

</div>

<div class="d-flex justify-content-between mt-3">
  <?php if ($departmentId): ?>
    <button class="btn btn-outline-danger" onclick="$(document).trigger('buttonDelete:department', <?= $departmentId ?>)">Delete</button>
  <?php endif; ?>
  <button class="btn btn-outline-primary" onclick="$(document).trigger('buttonSave:department', '<?= $departmentId ?>')">Save</button>
</div>


<script>
  $(async ƒ => {

    function backToList() {
      $(document).trigger('loadMainSection', {
        sectionId: 'departments',
        url: 'section.list-departments.php',
        forceReload: true
      });
    }

    if (!documentEventExists('buttonSave:department')) {
      $(document).on('buttonSave:department', async (e, id) => {
        const departmentId = id;
        const resp = await net.post('/api/post.save-department.php', {
          id: departmentId,
          name: $('#department-name').cleanProperVal(),
          mayRequest: $('#department-may-request').isChecked(),
        });
        if (resp?.result) {
          $(document).trigger('departmentChange');
          if (departmentId) {
            ui.toastr.success('Department saved.', 'Success');
            return backToList();
          }
          ui.toastr.success('Department added.', 'Success');
          return backToList();
        }
        ui.toastr.error(resp.result.errors[2], 'Error');
        console.log(resp);
      });
    }

    if (!documentEventExists('buttonDelete:department')) {
      $(document).on('buttonDelete:department', async (e, id) => {
        const departmentId = id;
        if (await ui.ask('Are you sure you want to delete this department?')) {
          const resp = await net.get('/api/get.delete-department.php', {
            id: departmentId
          });
          if (resp?.result) {
            $(document).trigger('departmentChange');
            ui.toastr.success('Department deleted.', 'Success');
            return backToList();
          }
          console.error(resp);
          ui.toastr.error('There seems to be a problem deleting department.', 'Error');
        }
      });
    }
  });
</script>