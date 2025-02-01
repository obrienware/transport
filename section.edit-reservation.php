<?php
require_once 'autoload.php';

use Transport\VehicleReservation;

$id = !empty($_GET['id']) ? (int)$_GET['id'] : null;
$reservation = new VehicleReservation($id);
$reservationId = $reservation->getId();
?>
<?php if (isset($_GET['id']) && !$reservation->getId()): ?>

  <div class="container-fluid text-center">
    <div class="alert alert-danger mt-5 w-50 mx-auto">
      <h1 class="fw-bold">Oh dear!</h1>
      <p class="lead">I can't seem to find that reservation! <i class="fa-duotone fa-solid fa-face-thinking"></i></p>
    </div>
  </div>

<?php else: ?>

  <div id="reservation-form" class="container mt-2">
    <?php if ($reservationId): ?>
      <h2>Edit Reservation</h2>
    <?php else: ?>
      <h2>Add Reservation</h2>
    <?php endif; ?>

    <div class="row">
      <div class="col-3">
        <div class="mb-3">
          <label for="reservation-start-date" class="form-label">Starts</label>
          <input type="datetime-local" class="form-control" id="reservation-start-date" value="<?=$reservation->startDateTime?>">
        </div>
      </div>

      <div class="col-3">
        <div class="mb-3">
          <label for="reservation-end-date" class="form-label">Ends</label>
          <input type="datetime-local" class="form-control" id="reservation-end-date" value="<?=$reservation->endDateTime?>">
        </div>
      </div>
    </div>

    <div class="row">
      <div class="col-3">
        <div class="mb-3">
          <label for="reservation-guest" class="form-label">Guest</label>
          <input 
            type="text" 
            class="form-control" 
            id="reservation-guest" 
            placeholder="Contact" 
            value="<?=$reservation->guestId ? $reservation->guest->getName() : ''?>" 
            data-value="<?=$reservation->guestId ? $reservation->guest->getName() : ''?>" 
            data-id="<?=$reservation->guestId?>">
          <div class="invalid-feedback">Please make a valid selection</div>
        </div>
      </div>

      <div class="col-3">
        <div class="mb-3">
          <label for="reservation-vehicle-id" class="form-label">Vehicle</label>
          <div><select id="reservation-vehicle-id" class="form-control" data-container="#vehicle-edit-container"></select></div>
        </div>
      </div>

      <div class="col">
        <div class="mb-3">
          <label for="reservation-reason" class="form-label">Reason / Purpose</label>
          <input type="text" class="form-control" id="reservation-reason" value="<?=$reservation->reason?>">
        </div>
      </div>
    </div>

    <div class="row">
      <div class="col-3">
        <label for="reservation-requestor" class="form-label">Requestor</label>
        <input 
          type="text" 
          class="form-control" 
          id="reservation-requestor" 
          placeholder="Requestor" 
          value="<?=($reservation->requestor) ? $reservation->requestor : ''?>" 
          data-value="<?=($reservation->requestor) ? $reservation->requestor : ''?>" 
          data-id="<?=$reservation->requestorId?>">
        <div class="invalid-feedback">Please make a valid selection</div>
      </div>
    </div>

    <div class="row my-4">
      <div class="col d-flex justify-content-between">
        <?php if ($reservationId): ?>
          <button class="btn btn-outline-danger px-4" id="btn-delete-reservation">Delete</button>
        <?php endif; ?>
        <div class="ms-auto">
          <?php if (!$reservation->isConfirmed()): ?>
            <button class="btn btn-outline-primary px-4 me-2" id="btn-save-confirm-reservation">Save & Confirm</button>
          <?php endif; ?>
          <button class="btn btn-primary px-4" id="btn-save-reservation">Save</button>
        </div>
      </div>
    </div>

  </div>

  <script type="text/javascript">

    $(async ƒ => {

      // --- DECLARATIONS ---

      const reservationId = <?=$reservationId ?: 'null'?>;
      let vehicles;
      let startDateTime;
      let endDateTime;

      function getData () {
        const data = { reservationId };
        let control;

        data.startDateTime = startDateTime.format('YYYY-MM-DD HH:mm:ss');
        data.endDateTime = endDateTime.format('YYYY-MM-DD HH:mm:ss');
        if ($('#reservation-guest').val()) data.guestId = $('#reservation-guest').data('id');
        data.vehicleId = val('#reservation-vehicle-id'); data.vehicleId = (data.vehicleId == '') ? null : parseInt(data.vehicleId);
        data.reason = cleanVal('#reservation-reason');
        if ($('#event-requestor').val()) data.requestorId = $('#event-requestor').data('id');
        return data;
      }

      async function loadResources () {
        vehicles = await get('/api/get.available-vehicles.php', {
          startDate: startDateTime.format('YYYY-MM-DD HH:mm:00'),
          endDate: endDateTime.format('YYYY-MM-DD HH:mm:59'),
          reservationId
        });
        $('#reservation-vehicle-id').selectpicker('destroy');
        $('#reservation-vehicle-id option').remove();
        $('#reservation-vehicle-id').append($('<option>'));
        $.each(vehicles, function (i, item) {
          const optionProps = {
            value: item.id,
            text: item.name,
            // 'data-content': `<i class="bi bi-square-fill" style="color:${item.color}"></i> ${item.name}`,
          }
          if (!item.available) {
            optionProps.style = `background-color:crimson; color: white`;
            optionProps['data-icon'] = 'fa-solid fa-triangle-exclamation';
          }
          $('#reservation-vehicle-id').append($('<option>', optionProps));
        });
        $('#reservation-vehicle-id').selectpicker();
      }


      // --- INITIALIZATIONS ---

      new Autocomplete(document.getElementById('reservation-guest'), {
        fullWidth: true,
        // highlightTyped: true,
        liveServer: true,
        server: '/api/get.autocomplete-guests.php',
        onSelectItem: (data) => {
          $('#reservation-guest')
            .data('id', data.value)
            .data('value', data.label)
            .removeClass('is-invalid');
        },
        fixed: true,
      });

      new Autocomplete(document.getElementById('reservation-requestor'), {
        fullWidth: true,
        highlightTyped: true,
        liveServer: true,
        server: '/api/get.autocomplete-requestors.php',
        onSelectItem: (data) => {
          $('#reservation-requestor')
            .data('id', data.value)
            .data('value', data.label)
            .removeClass('is-invalid');
        },
        fixed: true,
      });

      $('#reservation-form select').selectpicker();


      // --- EVENTS ---

      // Update the vehicle list when either of the dates change
      $('#reservation-start-date').on('change', function () {
        startDateTime = moment($('#reservation-start-date').val());
        loadResources();
      });
      $('#reservation-end-date').on('change', function () {
        endDateTime = moment($('#reservation-end-date').val());
        loadResources();
      });

      
      $('#btn-save-reservation').off('click').on('click', async function () {
        const data = getData();

        // Validation
        if (!startDateTime || !endDateTime) return toastr.error('Please select a start and end date.', 'Error');
        if (startDateTime.isAfter(endDateTime)) return toastr.error('Start date cannot be after end date.', 'Error');
        if (startDateTime.isBefore(moment())) return toastr.error('Start date cannot be in the past.', 'Error');
        if (!data.guestId) return toastr.error('Please select a guest.', 'Error');
        if (!data.vehicleId) return toastr.error('Please select a vehicle.', 'Error');
        if (data?.reason?.length <= 0) return toastr.error('Please enter a reason/purpose.', 'Error');

        const buttonSavedText = $('#btn-save-reservation').text();
        $('#btn-save-reservation').prop('disabled', true).text('Saving...');

        const resp = await post('/api/post.save-reservation.php', data);
        if (resp?.result) {
          app.closeOpenTab();
          $(document).trigger('reservationChange', {reservationId});
          if (reservationId) return toastr.success('Vehicle reservation saved.', 'Success');
          $('#btn-save-reservation').prop('disabled', false).text(buttonSavedText);
          return toastr.success('Reservation added.', 'Success')
        }
        toastr.error(resp.error, 'Error');
        console.log(resp);
        $('#btn-save-reservation').prop('disabled', false).text(buttonSavedText);
      });


      $('#btn-save-confirm-reservation').off('click').on('click', async function () {
        const data = getData();

        // Validation
        if (!startDateTime || !endDateTime) return toastr.error('Please select a start and end date.', 'Error');
        if (startDateTime.isAfter(endDateTime)) return toastr.error('Start date cannot be after end date.', 'Error');
        if (startDateTime.isBefore(moment())) return toastr.error('Start date cannot be in the past.', 'Error');
        if (!data.guestId) return toastr.error('Please select a guest.', 'Error');
        if (!data.vehicleId) return toastr.error('Please select a vehicle.', 'Error');
        if (data?.reason?.length <= 0) return toastr.error('Please enter a reason/purpose.', 'Error');

        const buttonSavedText = $('#btn-save-confirm-reservation').text();
        $('#btn-save-confirm-reservation').prop('disabled', true).text('Saving...');

        const resp = await post('/api/post.save-reservation.php', data);
        if (resp?.result) {
          // If we have successfully saved the reservation, we can now go ahead and confirm it
          const id = reservationId || resp?.result;
          const newResp = await post('/api/post.confirm-reservation.php', {id});
          if (newResp?.result) {
            $(document).trigger('reservationChange');
            app.closeOpenTab();
            return toastr.success('Reservation updated and confirmed.', 'Success');
          }
          $('#btn-save-confirm-reservation').prop('disabled', false).text(buttonSavedText);
          return toastr.error('Seems to be a problem confirming this reservation!', 'Error');
        }
        toastr.error(resp.error, 'Error');
        console.error(resp);
        $('#btn-save-confirm-reservation').prop('disabled', false).text(buttonSavedText);
      });


      $('#btn-delete-reservation').on('click', async ƒ => {
        if (await ask('Are you sure you want to delete this reservation?')) {
          const buttonSavedText = $('#btn-delete-reservation').text();
          $('#btn-delete-reservation').prop('disabled', true).text('Deleting...');

          const resp = await get('/api/get.delete-reservation.php', { id: reservationId });
          if (resp?.result) {
            $(document).trigger('reservationChange', {reservationId});
            $('#btn-delete-reservation').prop('disabled', false).text(buttonSavedText);
            app.closeOpenTab();
            return toastr.success('Reservation deleted.', 'Success')
          }
          console.log(resp);
          toastr.error('There seems to be a problem deleting this reservation.', 'Error');
          $('#btn-delete-reservation').prop('disabled', false).text(buttonSavedText);
        }
      });


      // --- ACTIONS ---

      if (reservationId) {
        startDateTime = moment('<?=$reservation->startDateTime?>', 'YYYY-MM-DD H:mm:ss');
        endDateTime = moment('<?=$reservation->endDateTime?>', 'YYYY-MM-DD H:mm:ss');
        await loadResources();
        $('#reservation-vehicle-id').selectpicker('val', '<?=$reservation->vehicleId?>');
      }


    });



<?php endif; ?>