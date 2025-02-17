<?php
require_once 'autoload.php';

use Transport\Guest;
use Generic\Utils;

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT, FILTER_NULL_ON_FAILURE);
$id = $id === false ? null : $id;
$guest = new Guest($id);

if (!is_null($id) && !$guest->getId())
{
  exit(Utils::showResourceNotFound());
}
$guestId = $guest->getId();
?>

<!-- Back button -->
<div class="d-flex justify-content-between top-page-buttons mb-2">
  <button class="btn btn-sm btn-outline-primary px-2 me-auto" onclick="$(document).trigger('guests:reloadList');">
    <i class="fa-solid fa-chevron-left"></i>
    List
  </button>
</div>

<?php if ($guest->getId()): ?>
  <h2>Edit Contact</h2>
  <input type="hidden" id="guest-id" value="<?= $guest->getId() ?>">
<?php else: ?>
  <h2>Add Contact</h2>
  <input type="hidden" id="guest-id" value="">
<?php endif; ?>


<div class="row">˝
  <div class="col-12 col-lg-4 col-xxl-3">
    <div class="mb-3">
      <label class="form-label" for="guest-first-name">First Name</label>
      <input type="text" class="form-control" id="guest-first-name" placeholder="First Name" value="<?= $guest->firstName ?>">
    </div>
  </div>

  <div class="col-12 col-lg-4 col-xxl-3">
    <div class="mb-3">
      <label class="form-label" for="guest-last-name">Last Name</label>
      <input type="text" class="form-control" id="guest-last-name" placeholder="Last Name" value="<?= $guest->lastName ?>">
    </div>
  </div>

  <div class="col-12 col-lg-4 col-xxl-3">
    <div class="mb-3">
      <label for="guest-type" class="form-label">Type</label>
      <select class="form-select" id="guest-type">
        <option value="">Select Type</option>
        <option value="Guest" <?= $guest->type === 'Guest' ? 'selected' : '' ?>>Guest / VIP</option>
        <option value="Student" <?= $guest->type === 'Student' ? 'selected' : '' ?>>Student / Intern</option>
        <option value="Staff" <?= $guest->type === 'Staff' ? 'selected' : '' ?>>Staff</option>
        <option value="Remote Staff" <?= $guest->type === 'Remote Staff' ? 'selected' : '' ?>>Staff (Remote)</option>
      </select>
    </div>
  </div>

  <div class="col-12 col-lg-4 col-xxl-3">
    <div class="mb-3">
      <label class="form-label" for="guest-phone-number">Phone Number</label>
      <input type="tel" class="form-control" id="guest-phone-number" placeholder="Phone Number" value="<?= $guest->phoneNumber ?>">
    </div>
  </div>

  <div class="col-12 col-lg-4 col-xxl-3">
    <div class="mb-3">
      <label class="form-label" for="guest-email-address">Email Address</label>
      <input type="email" class="form-control" id="guest-email-address" placeholder="Email Address" value="<?= $guest->emailAddress ?>">
    </div>
  </div>
</div>

<div class="d-flex justify-content-between mt-3">
  <?php if ($guestId): ?>
    <button class="btn btn-outline-danger" onclick="$(document).trigger('buttonDelete:guest', <?= $guestId ?>)">Delete</button>
  <?php endif; ?>
  <button class="btn btn-outline-primary" onclick="$(document).trigger('buttonSave:guest', '<?= $guestId ?>')">Save</button>
</div>


<script>
  $(async ƒ => {

    function backToList() {
      $(document).trigger('loadMainSection', {
        sectionId: 'guests',
        url: 'section.list-guests.php',
        forceReload: true
      });
    }

    if (!documentEventExists('buttonSave:guest')) {
      $(document).on('buttonSave:guest', async (e, id) => {
        const guestId = id;
        const resp = await net.post('/api/post.save-guest.php', {
          id: guestId,
          firstName: $('#guest-first-name').cleanProperVal(),
          lastName: $('#guest-last-name').cleanProperVal(),
          emailAddress: $('#guest-email-address').cleanVal(),
          phoneNumber: $('#guest-phone-number').cleanVal(),
          type: $('#guest-type').val()
        });
        if (resp?.result) {
          $(document).trigger('guestChange');
          if (guestId) {
            ui.toastr.success('Contact/Guest saved.', 'Success');
            return backToList();
          }
          ui.toastr.success('Contact/Guest added.', 'Success');
          return backToList();
        }
        ui.toastr.error(resp.result.errors[2], 'Error');
        console.log(resp);
      });
    }

    if (!documentEventExists('buttonDelete:guest')) {
      $(document).on('buttonDelete:guest', async (e, id) => {
        const guestId = id;
        if (await ui.ask('Are you sure you want to delete this contact/guest?')) {
          const resp = await net.get('/api/get.delete-guest.php', {
            id: guestId
          });
          if (resp?.result) {
            $(document).trigger('guestChange');
            ui.toastr.success('Contact/Guest deleted.', 'Success');
            return backToList();
          }
          console.error(resp);
          ui.toastr.error('There seems to be a problem deleting contact/guest.', 'Error');
        }
      });
    }

  });
</script>