<?php
require_once 'autoload.php';

use Generic\Utils;
use Transport\VehicleReservation;

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT, FILTER_NULL_ON_FAILURE);
$id = $id === false ? null : $id;
$reservation = new VehicleReservation($id);
$reservationId = $reservation->getId();

if (!is_null($id) && !$reservation->getId())
{
  exit(Utils::showResourceNotFound());
}
?>

<!-- Back button -->
<div class="d-flex justify-content-between top-page-buttons mb-2">
  <button class="btn btn-sm btn-outline-primary px-2 me-auto" onclick="$(document).trigger('loadMainSection', { sectionId: 'reservations', url: 'section.list-reservations.php', forceReload: true });">
    <i class="fa-solid fa-chevron-left"></i>
    List
  </button>
</div>

<div id="reservation-form" style="display: contents;">
  <?php if ($reservationId): ?>
    <h2>Edit Reservation</h2>
  <?php else: ?>
    <h2>Add Reservation</h2>
  <?php endif; ?>
  <input type="hidden" id="reservation-id" value="<?= $reservationId ?>">

  <div class="row">
    <div class="col-12 col-sm-6 col-md-8 col-lg-5 col-xl-4 col-xxl-3">
      <div class="mb-3">
        <label for="reservation-start-date" class="form-label">Starts</label>
        <input type="datetime-local" class="form-control" id="reservation-start-date" value="<?= $reservation->startDateTime ?>">
      </div>
    </div>

    <div class="col-12 col-sm-6 col-md-8 col-lg-5 col-xl-4 col-xxl-3">
      <div class="mb-3">
        <label for="reservation-end-date" class="form-label">Ends</label>
        <input type="datetime-local" class="form-control" id="reservation-end-date" value="<?= $reservation->endDateTime ?>">
      </div>
    </div>

    <div class="col-12 col-sm-6 col-md-12 col-lg-6 col-xl-4 col-xxl-3">
      <div class="mb-3">
        <label for="reservation-guest" class="form-label">Guest / Driver</label>
        <input
          type="text"
          class="form-control"
          id="reservation-guest"
          placeholder="Contact"
          value="<?= $reservation->guestId ? $reservation->guest->getName() : '' ?>"
          data-value="<?= $reservation->guestId ? $reservation->guest->getName() : '' ?>"
          data-id="<?= $reservation->guestId ?>">
        <div class="invalid-feedback">Please make a valid selection</div>
      </div>
    </div>

    <div class="col-12 col-sm-6 col-md-12 col-lg-6 col-xl-4 col-xxl-3">
      <div class="mb-3">
        <label for="reservation-vehicle-id" class="form-label">Vehicle</label>
        <div><select id="reservation-vehicle-id" class="form-control" data-container="#vehicle-edit-container"></select></div>
      </div>
    </div>

    <div class="col-12 col-xl-8 col-xxl-6">
      <div class="mb-3">
        <label for="reservation-reason" class="form-label">Reason / Purpose</label>
        <input type="text" class="form-control" id="reservation-reason" value="<?= $reservation->reason ?>">
      </div>
    </div>

    <div class="col-12 col-sm-6 col-md-12 col-lg-6 col-xl-4 col-xxl-3">
      <label for="reservation-requestor" class="form-label">Requestor</label>
      <input
        type="text"
        class="form-control"
        id="reservation-requestor"
        placeholder="Requestor"
        value="<?= ($reservation->requestor) ? $reservation->requestor->getName() : '' ?>"
        data-value="<?= ($reservation->requestor) ? $reservation->requestor->getName() : '' ?>"
        data-id="<?= $reservation->requestorId ?>">
      <div class="invalid-feedback">Please make a valid selection</div>
    </div>
  </div>

  <div class="d-flex justify-content-between mt-3">
    <?php if ($reservation->getId()): ?>
      <button class="btn btn-outline-danger" onclick="$(document).trigger('buttonDelete:reservation', <?= $reservation->getId() ?>)">Delete</button>
      <div class="mx-auto" style="color:lightslategray; font-size:small">
        <div>Created: <?= (new DateTime($reservation->created))->format('m/d/Y H:ia') ?> (<?= ucwords($reservation->createdBy) ?>)</div>
        <?php if ($reservation->modified): ?>
          <div>Modified: <?= (new DateTime($reservation->modified))->format('m/d/Y H:ia') ?> (<?= ucwords($reservation->modifiedBy) ?>)</div>
        <?php endif; ?>
      </div>
    <?php endif; ?>

    <?php if (!$reservation->isConfirmed()): ?>
      <button class="btn btn-primary ms-auto me-2" onclick="$(document).trigger('buttonSaveAndConfirm:reservation', '<?= $reservation->getId() ?>')">Save & Confirm</button>
    <?php endif; ?>
    <button class="btn btn-outline-primary" onclick="$(document).trigger('buttonSave:reservation', '<?= $reservation->getId() ?>')">Save</button>
  </div>

</div>



<script>
  $(async ƒ => {

    // --- FUNCTIONS ---

    function backToList() {
      $(document).trigger('loadMainSection', {
        sectionId: 'reservations',
        url: 'section.list-reservations.php',
        forceReload: true
      });
    }

    function getData() {
      const reservationId = $('#reservation-id').val() || null;
      const startDateTime = moment($('#reservation-start-date').val());
      const endDateTime = moment($('#reservation-end-date').val());
      const data = {
        reservationId,
        id: reservationId
      };

      let control;

      data.startDateTime = startDateTime.format('YYYY-MM-DD HH:mm:ss');
      data.endDateTime = endDateTime.format('YYYY-MM-DD HH:mm:ss');
      if ($('#reservation-guest').val()) data.guestId = $('#reservation-guest').data('id');
      data.vehicleId = input.val('#reservation-vehicle-id');
      data.vehicleId = (data.vehicleId == '') ? null : parseInt(data.vehicleId);
      data.reason = input.cleanVal('#reservation-reason');
      if ($('#reservation-requestor').val()) data.requestorId = $('#reservation-requestor').data('id');
      return data;
    }

    async function loadResources() {
      const reservationId = $('#reservation-id').val();
      const startDateTime = moment($('#reservation-start-date').val());
      const endDateTime = moment($('#reservation-end-date').val());

      if (!startDateTime.isValid() || !endDateTime.isValid()) return;

      const vehicles = await net.get('/api/get.available-vehicles.php', {
        startDate: startDateTime.format('YYYY-MM-DD HH:mm:00'),
        endDate: endDateTime.format('YYYY-MM-DD HH:mm:59'),
        reservationId
      });
      $('#reservation-vehicle-id').selectpicker('destroy');
      $('#reservation-vehicle-id option').remove();
      $('#reservation-vehicle-id').append($('<option>'));
      $.each(vehicles, function(i, item) {
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
      $('#reservation-vehicle-id').selectpicker({
        container: false
      });
    }


    // --- INITIALIZATIONS ---
    buildAutoComplete({
      selector: 'reservation-guest',
      apiUrl: '/api/get.autocomplete-guests.php'
    });

    buildAutoComplete({
      selector: 'reservation-requestor',
      apiUrl: '/api/get.autocomplete-requestors.php'
    });

    $('#reservation-form select').selectpicker({
      container: false
    });


    // --- EVENTS ---

    // Update the vehicle list when either of the dates change
    $('#reservation-start-date, #reservation-end-date').on('change', loadResources);

    if (!documentEventExists('buttonSave:reservation')) {
      $(document).on('buttonSave:reservation', async (e, id) => {
        const startDateTime = moment($('#reservation-start-date').val());
        const endDateTime = moment($('#reservation-end-date').val());

        const data = getData();

        if (data.reservationId && data.reservationId != id) {
          console.trace('ID mismatch', data.reservationId, id);
          return ui.toastr.error('Reservation ID mismatch.', 'Error');
        }

        // Validation
        if (!startDateTime.isValid() || !endDateTime.isValid()) return ui.toastr.error('Please select a start and end date.', 'Error');
        if (startDateTime.isAfter(endDateTime)) return ui.toastr.error('Start date cannot be after end date.', 'Error');
        if (endDateTime.isBefore(moment())) return ui.toastr.error('End date cannot be in the past.', 'Error');
        if (!data.guestId) return ui.toastr.error('Please select a guest.', 'Error');
        if (!data.vehicleId) return ui.toastr.error('Please select a vehicle.', 'Error');

        const resp = await net.post('/api/post.save-reservation.php', data);
        if (resp?.result) {
          $(document).trigger('reservationChange', {
            id
          });
          if (data.reservationId) return ui.toastr.success('Vehicle reservation saved.', 'Success');
          ui.toastr.success('Reservation added.', 'Success');
          return backToList();
        }
        ui.toastr.error(resp.error, 'Error');
        console.log(resp);
      });
    }

    if (!documentEventExists('buttonSaveAndConfirm:reservation')) {
      $(document).on('buttonSaveAndConfirm:reservation', async (e, id) => {
        const startDateTime = moment($('#reservation-start-date').val());
        const endDateTime = moment($('#reservation-end-date').val());
        const data = getData();

        if (data.reservationId != id) {
          console.trace('ID mismatch', data.reservationId, id);
          return ui.toastr.error('Reservation ID mismatch.', 'Error');
        }

        // Validation
        if (!startDateTime.isValid() || !endDateTime.isValid()) return ui.toastr.error('Please select a start and end date.', 'Error');
        if (startDateTime.isAfter(endDateTime)) return ui.toastr.error('Start date cannot be after end date.', 'Error');
        if (endDateTime.isBefore(moment())) return ui.toastr.error('End date cannot be in the past.', 'Error');
        if (!data.guestId) return ui.toastr.error('Please select a guest.', 'Error');
        if (!data.vehicleId) return ui.toastr.error('Please select a vehicle.', 'Error');

        const resp = await net.post('/api/post.save-reservation.php', data);
        if (resp?.result) {
          // Confirm the reservation
          const newResp = await net.post('/api/post.confirm-reservation.php', {
            id
          });
          if (newResp?.result) {
            ui.toastr.success('Reservation Saved and Confirmed.', 'Success');
            $(document).trigger('reservationChange');
            return backToList();
          }
        }
        ui.toastr.error(resp.error, 'Error');
        console.log(resp);
      });
    }

    if (!documentEventExists('buttonDelete:reservation')) {
      $(document).on('buttonDelete:reservation', async (e, id) => {
        if (await ui.ask('Are you sure you want to delete this reservation?')) {
          const resp = await net.get('/api/get.delete-reservation.php', {
            id
          });
          if (resp?.result) {
            $(document).trigger('reservationChange', {
              id
            });
            ui.toastr.success('Reservation deleted.', 'Success');
            return backToList();
          }
          console.log(resp);
          ui.toastr.error('There seems to be a problem deleting this reservation.', 'Error');
        }
      });
    }


    // --- ACTIONS ---

    if ($('#reservation-id').val()) {
      await loadResources();
      $('#reservation-vehicle-id').selectpicker('val', '<?= $reservation->vehicleId ?>');
    }

  });
</script>