<?php
require_once 'autoload.php';

use Transport\{Config, Department, User};
use Generic\Utils;

$config = Config::get('system');
$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT, FILTER_NULL_ON_FAILURE);
$id = $id === false ? null : $id;
$user = new User($id);

if (!is_null($id) && !$user->getId())
{
  exit(Utils::showResourceNotFound());
}
$userId = $user->getId();
?>

<!-- Back button -->
<div class="d-flex justify-content-between top-page-buttons mb-2">
  <button class="btn btn-sm btn-outline-primary px-2 me-auto" onclick="$(document).trigger('users:reloadList');">
    <i class="fa-solid fa-chevron-left"></i>
    List
  </button>
</div>

<?php if ($user->getId()): ?>
  <h2>Edit User</h2>
  <input type="hidden" id="user-id" value="<?= $user->getId() ?>">
<?php else: ?>
  <h2>Add User</h2>
  <input type="hidden" id="user-id" value="">
<?php endif; ?>

<div class="row">

  <div class="col-12 col-lg-4 col-xxl-3">
    <div class="mb-3">
      <label class="form-label" for="user-username">Username</label>
      <input type="text" class="form-control" id="user-username" placeholder="Username" value="<?= $user->username ?>">
    </div>
  </div>

  <div class="col-12 col-lg-4">
    <div class="mb-3 mt-4">
      <div class="pretty p-svg p-curve p-bigger">
        <input class="" type="checkbox" value="reset" id="reset-user">
        <div class="state p-primary">
          <!-- svg path -->
          <svg class="svg svg-icon" viewBox="0 0 20 20">
            <path d="M7.629,14.566c0.125,0.125,0.291,0.188,0.456,0.188c0.164,0,0.329-0.062,0.456-0.188l8.219-8.221c0.252-0.252,0.252-0.659,0-0.911c-0.252-0.252-0.659-0.252-0.911,0l-7.764,7.763L4.152,9.267c-0.252-0.251-0.66-0.251-0.911,0c-0.252,0.252-0.252,0.66,0,0.911L7.629,14.566z" style="stroke: white;fill:white;"></path>
          </svg>
          <label>Reset Password</label>
        </div>
      </div>
    </div>
  </div>

  <div class="col-12"></div>

  <div class="col-12 col-lg-4 col-xxl-3">
    <div class="mb-3">
      <label class="form-label" for="user-first-name">First Name</label>
      <input type="text" class="form-control" id="user-first-name" placeholder="First Name" value="<?= $user->firstName ?>">
    </div>
  </div>

  <div class="col-12 col-lg-4 col-xxl-3">
    <div class="mb-3">
      <label class="form-label" for="user-last-name">Last Name</label>
      <input type="text" class="form-control" id="user-last-name" placeholder="Last Name" value="<?= $user->lastName ?>">
    </div>
  </div>

  <div class="col-12 col-lg-6 col-xxl-4">
    <div class="mb-3">
      <label class="form-label" for="user-email-address">Email Address</label>
      <input type="email" inputmode="email" class="form-control" id="user-email-address" placeholder="Email Address" value="<?= $user->emailAddress ?>">
    </div>
  </div>

  <div class="col-12 col-lg-4 col-xxl-3">
    <div class="mb-3">
      <label class="form-label" for="user-phone-number">Phone Number</label>
      <input type="text" class="form-control" id="user-phone-number" placeholder="Phone Number" value="<?= $user->phoneNumber ?>">
    </div>
  </div>

  <div class="col-12 col-lg-4 col-xxl-3">
    <div class="mb-3">
      <label class="form-label" for="user-position">Position</label>
      <input type="text" class="form-control" id="user-position" placeholder="Position" value="<?= $user->position ?>">
    </div>
  </div>

  <div class="col-12 col-lg-4 col-xxl-3">
    <div class="mb-3">
      <label for="user-department-id" class="form-label">Department</label>
      <div>
        <select id="user-department-id" data-live-search="true" title="Please select a department..." class="show-tick form-select">
          <option value="">Select department</option>
          <?php if ($rows = Department::getAll()): ?>
            <?php foreach ($rows as $row): ?>
              <option value="<?= $row->id ?>" <?= ($row->id == $user->departmentId) ? 'selected' : '' ?>><?= $row->name ?></option>
            <?php endforeach; ?>
          <?php endif; ?>
        </select>
      </div>
    </div>
  </div>

  <div class="col-12 col-lg-3">
    <div class="pretty p-svg p-curve p-bigger mt-4">
      <input class="" type="checkbox" value="cdl" id="user-cdl" <?= $user->CDL ? 'checked' : '' ?>>
      <div class="state p-primary">
        <!-- svg path -->
        <svg class="svg svg-icon" viewBox="0 0 20 20">
          <path d="M7.629,14.566c0.125,0.125,0.291,0.188,0.456,0.188c0.164,0,0.329-0.062,0.456-0.188l8.219-8.221c0.252-0.252,0.252-0.659,0-0.911c-0.252-0.252-0.659-0.252-0.911,0l-7.764,7.763L4.152,9.267c-0.252-0.251-0.66-0.251-0.911,0c-0.252,0.252-0.252,0.66,0,0.911L7.629,14.566z" style="stroke: white;fill:white;"></path>
        </svg>
        <label>Has CDL</label>
      </div>
    </div>
  </div>

  <div class="col-12">
    <label class="form-label mt-3">Roles</label>
  </div>
  <?php if ($config->userRoles): ?>
    <?php foreach ($config->userRoles as $role): ?>
      <?php if ($user->roles) $checked = (array_search($role, $user->roles) !== false) ? 'checked' : ''; ?>

      <div class="col-6 col-lg-3 col-xxl-2">
        <div class="pretty p-svg p-curve p-bigger">
          <input class="user-roles" type="checkbox" value="<?= $role ?>" id="user-role-<?= $role ?>" <?= $checked ?>>
          <div class="state p-primary">
            <!-- svg path -->
            <svg class="svg svg-icon" viewBox="0 0 20 20">
              <path d="M7.629,14.566c0.125,0.125,0.291,0.188,0.456,0.188c0.164,0,0.329-0.062,0.456-0.188l8.219-8.221c0.252-0.252,0.252-0.659,0-0.911c-0.252-0.252-0.659-0.252-0.911,0l-7.764,7.763L4.152,9.267c-0.252-0.251-0.66-0.251-0.911,0c-0.252,0.252-0.252,0.66,0,0.911L7.629,14.566z" style="stroke: white;fill:white;"></path>
            </svg>
            <label style="text-transform:capitalize"><?= $role ?></label>
          </div>
        </div>
      </div>


    <?php endforeach; ?>
  <?php endif; ?>
</div>


<div class="d-flex justify-content-between mt-3">
  <?php if ($userId): ?>
    <button class="btn btn-outline-danger" onclick="$(document).trigger('buttonDelete:user', <?= $userId ?>)">Delete</button>
  <?php endif; ?>
  <button class="btn btn-outline-primary" onclick="$(document).trigger('buttonSave:user', '<?= $userId ?>')">Save</button>
</div>

<script>
  $(async ƒ => {

    function backToList() {
      $(document).trigger('loadMainSection', {
        sectionId: 'users',
        url: 'section.list-users.php',
        forceReload: true
      });
    }

    if (!documentEventExists('buttonSave:user')) {
      $(document).on('buttonSave:user', async (e, id) => {
        const userId = id;
        const resp = await net.post('/api/post.save-user.php', {
          id: userId,
          username: $('#user-username').cleanLowerVal(),
          firstName: $('#user-first-name').cleanProperVal(),
          lastName: $('#user-last-name').cleanProperVal(),
          emailAddress: $('#user-email-address').cleanVal(),
          phoneNumber: $('#user-phone-number').cleanVal(),
          position: $('#user-position').cleanVal(),
          departmentId: $('#user-department-id').val(),
          cdl: $('#user-cdl').isChecked(),
          roles: $('.user-roles:checked').map((idx, el) => $(el).val()).get().join(','),
          resetPassword: $('#reset-user').isChecked()
        });
        if (resp?.result) {
          $(document).trigger('userChange');
          if (userId) {
            ui.toastr.success('User saved.', 'Success');
            return backToList();
          }
          ui.toastr.success('User added.', 'Success');
          return backToList();
        }
        ui.toastr.error(resp.result.errors[2], 'Error');
        console.error(resp);
      });
    }

    if (!documentEventExists('buttonDelete:user')) {
      $(document).on('buttonDelete:user', async (e, id) => {
        const userId = id;
        if (await ui.ask('Are you sure you want to delete this user?')) {
          const resp = await net.get('/api/get.delete-user.php', {
            id: userId
          });
          if (resp?.result) {
            $(document).trigger('userChange');
            ui.toastr.success('User deleted.', 'Success');
            return backToList();
          }
          console.error(resp);
          ui.toastr.error('There seems to be a problem deleting user.', 'Error');
        }
      });
    }

  });
</script>