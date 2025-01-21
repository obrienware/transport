<?php
require_once 'autoload.php';

use Transport\Config;
use Transport\Department;
use Transport\User;

$config = Config::get('system');
$id = !empty($_GET['id']) ? (int)$_GET['id'] : null;
$user = new User($id);
?>
<?php if (isset($_GET['id']) && !$user->getId()): ?>

  <div class="container-fluid text-center">
    <div class="alert alert-danger mt-5 w-50 mx-auto">
      <h1 class="fw-bold">Oh dear!</h1>
      <p class="lead">I can't seem to find that user! <i class="fa-duotone fa-solid fa-face-thinking"></i></p>
    </div>
  </div>

<?php else: ?>

  <div class="container mt-2">
    <?php if ($user->getId()): ?>
      <h2>Edit User</h2>
    <?php else: ?>
      <h2>Add User</h2>
    <?php endif; ?>
    <div>
      <div class="row">
        <div class="col-md-6">

          <div class="form-floating mb-3">
            <input type="text" class="form-control" id="user-username" placeholder="Username" value="<?=$user->username?>">
            <label for="user-username">Username</label>
          </div>
          <div class="form-check mb-3">
            <input class="form-check-input" type="checkbox" value="reset" id="reset-user">
            <label class="form-check-label" for="reset-user">Reset Password</label>
          </div>
          <div class="form-floating mb-3">
            <input type="text" class="form-control" id="user-first-name" placeholder="First Name" value="<?=$user->firstName?>">
            <label for="user-first-name">First Name</label>
          </div>
          <div class="form-floating mb-3">
            <input type="text" class="form-control" id="user-last-name" placeholder="Last Name" value="<?=$user->lastName?>">
            <label for="user-last-name">Last Name</label>
          </div>
          <div class="form-floating mb-3">
            <input type="email" class="form-control" id="user-email-address" placeholder="Email Address" value="<?=$user->emailAddress?>">
            <label for="user-email-address">Email Address</label>
          </div>
          <div class="form-floating mb-3">
            <input type="text" class="form-control" id="user-phone-number" placeholder="Phone Number" value="<?=$user->phoneNumber?>">
            <label for="user-phone-number">Phone Number</label>
          </div>
          <div class="form-floating mb-3">
            <input type="text" class="form-control" id="user-position" placeholder="Position" value="<?=$user->position?>">
            <label for="user-position">Position</label>
          </div>

          <div class="mb-3">
            <label for="user-department-id" class="form-label">Department</label>
            <div>
              <select id="user-department-id" data-live-search="true" title="Please select a department..." class="show-tick">
                <?php if ($rows = Department::getAll()): ?>
                  <?php foreach ($rows as $row): ?>
                    <option value="<?=$row->id?>" <?=($row->id == $user->departmentId) ? 'selected' : ''?>><?=$row->name?></option>
                  <?php endforeach; ?>
                <?php endif; ?>
              </select>
            </div>
          </div>


          <div class="form-check">
            <input class="form-check-input" type="checkbox" value="cdl" id="user-cdl" <?=$user->CDL ? 'checked' : ''?>>
            <label class="form-check-label" for="user-cdl">Has CDL</label>
          </div>


        </div>
        <div class="col-md-6">
          <label class="form-label">Roles</label>
          <?php if ($config->userRoles): ?>
            <?php foreach ($config->userRoles as $role): ?>
              <?php if ($user->roles) $checked = (array_search($role, $user->roles) !== false) ? 'checked' : ''; ?>
              <div class="form-check form-check-lg">
                <input class="form-check-input user-roles" type="checkbox" value="<?=$role?>" id="user-role-<?=$role?>" <?=$checked?>>
                <label class="form-check-label" for="user-role-<?=$role?>">
                  <?=$role?>
                </label>
              </div>
            <?php endforeach; ?>
          <?php endif;?>
        </div>
      </div>

      <div class="row my-4">
        <div class="col d-flex justify-content-between">
          <?php if ($user->getId()): ?>
            <button class="btn btn-outline-danger px-4" id="btn-delete-user">Delete</button>
          <?php endif; ?>
          <button class="btn btn-primary px-4" id="btn-save-user">Save</button>
        </div>
      </div>

    </div>
  </div>

  <script type="text/javascript">

    $(async ƒ => {

      const userId = <?=$user->getId() ?: 'null'?>;
      $('#btn-save-user').off('click').on('click', async ƒ => {
        const resp = await post('/api/post.save-user.php', {
          id: userId,
          username: cleanVal('#user-username'),
          firstName: cleanVal('#user-first-name'),
          lastName:cleanVal('#user-last-name'),
          emailAddress: cleanVal('#user-email-address'),
          phoneNumber: cleanVal('#user-phone-number'),
          position: cleanVal('#user-position'),
          departmentId: cleanVal('#user-department-id'),
          cdl: checked('#user-cdl'),
          roles: $('.user-roles:checked').map((idx, el) => $(el).val()).get().join(','),
          resetPassword: checked('#reset-user')
        });
        if (resp?.result) {
          $(document).trigger('userChange', {userId});
          app.closeOpenTab();
          if (userId) return toastr.success('User saved.', 'Success');
          return toastr.success('User added.', 'Success')
        }
        toastr.error(resp . result . errors[2], 'Error');
        console.log(resp);
      });

      $('#btn-delete-user').off('click').on('click', async ƒ => {
        if (await ask('Are you sure you want to delete this user?')) {
          const resp = await get('/api/get.delete-user.php', {
            id: '<?=$user->getId()?>'
          });
          if (resp?.result) {
            $(document).trigger('userChange', {userId});
            app.closeOpenTab();
            return toastr.success('User deleted.', 'Success')
          }
          console.log(resp);
          toastr.error('There seems to be a problem deleting user.', 'Error');
        }
      });

      $('select').selectpicker();

    });

  </script>

<?php endif; ?>
