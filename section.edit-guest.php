<?php
require_once 'class.guest.php';
$item = new Guest($_REQUEST['id']);
?>
<?php if (isset($_REQUEST['id']) && !$item->guestId): ?>

  <div class="container-fluid text-center">
    <div class="alert alert-danger mt-5 w-50 mx-auto">
      <h1 class="fw-bold">Oh dear!</h1>
      <p class="lead">I can't seem to find that guest! <i class="fa-duotone fa-solid fa-face-thinking"></i></p>
    </div>
  </div>

<?php else: ?>

  <div class="container mt-2">
    <?php if ($item->guestId): ?>
      <h2>Edit Guest</h2>
    <?php else: ?>
      <h2>Add Guest</h2>
    <?php endif; ?>
    <div>
      <div class="row">
        <div class="col-md-6">

          <div class="form-floating mb-3">
            <input type="text" class="form-control" id="guest-group-name" placeholder="Group Name" value="<?=$item->groupName?>">
            <label for="guest-group-name">Group Name</label>
          </div>
          <div class="form-floating mb-3">
            <input type="text" class="form-control" id="guest-group-size" placeholder="Group Size" value="<?=$item->groupSize?>">
            <label for="guest-group-size">Group Size</label>
          </div>
          <div class="form-floating mb-3">
            <input type="text" class="form-control" id="guest-first-name" placeholder="First Name" value="<?=$item->firstName?>">
            <label for="guest-first-name">First Name</label>
          </div>
          <div class="form-floating mb-3">
            <input type="text" class="form-control" id="guest-last-name" placeholder="Last Name" value="<?=$item->lastName?>">
            <label for="guest-last-name">Last Name</label>
          </div>
          <div class="form-floating mb-3">
            <input type="text" class="form-control" id="guest-phone-number" placeholder="Phone Number" value="<?=$item->phoneNumber?>">
            <label for="guest-phone-number">Phone Number</label>
          </div>
          <div class="form-floating mb-3">
            <input type="email" class="form-control" id="guest-email-address" placeholder="Email Address" value="<?=$item->emailAddress?>">
            <label for="guest-email-address">Email Address</label>
          </div>

        </div>
      </div>

      <div class="row my-4">
        <div class="col d-flex justify-content-between">
          <?php if ($item->guestId): ?>
            <button class="btn btn-outline-danger px-4" id="btn-delete-guest">Delete</button>
          <?php endif; ?>
          <button class="btn btn-primary px-4" id="btn-save-guest">Save</button>
        </div>
      </div>

    </div>
  </div>

  <script type="text/javascript">

    $(async ƒ => {

      const guestId = <?=$item->guestId ?: 'null'?>;
      $('#btn-save-guest').off('click').on('click', async ƒ => {
        const resp = await post('/api/post.save-guest.php', {
          id: guestId,
          groupName: cleanVal('#guest-group-name'),
          groupSize: cleanVal('#guest-group-size'),
          firstName: cleanVal('#guest-first-name'),
          lastName:cleanVal('#guest-last-name'),
          emailAddress: cleanVal('#guest-email-address'),
          phoneNumber: cleanVal('#guest-phone-number'),
        });
        if (resp?.result?.result) {
          // setTimeout(ƒ => {location.href = 'page.guests.list.php'}, 3000);
          $(document).trigger('guestChange', {guestId});
          app.closeOpenTab();
          if (guestId) return toastr.success('Guest saved.', 'Success');
          return toastr.success('Guest added.', 'Success')
        }
        toastr.error(resp . result . errors[2], 'Error');
        console.log(resp);
      });

      $('#btn-delete-guest').on('click', async ƒ => {
        if (await ask('Are you sure you want to delete this guest?')) {
          const resp = await get('/api/get.delete-guest.php', {
            id: '<?=$item->guestId?>'
          });
          if (resp?.result) {
            $(document).trigger('guestChange', {guestId});
            app.closeOpenTab();
            return toastr.success('Guest deleted.', 'Success')
          }
          console.log(resp);
          toastr.error('There seems to be a problem deleting guest.', 'Error');
        }
      });

    });

  </script>

<?php endif; ?>