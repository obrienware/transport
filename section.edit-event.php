<?php
require_once 'autoload.php';

use Transport\Event;
use Generic\Utils;

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT, FILTER_NULL_ON_FAILURE);
$id = $id === false ? null : $id;
$event = new Event($id);
$eventId = $event->getId();

if (!is_null($id) && !$eventId)
{
  exit(Utils::showResourceNotFound());
}
?>

<!-- Back button -->
<div class="d-flex justify-content-between top-page-buttons mb-2">
  <button class="btn btn-sm btn-outline-primary px-2 me-auto" onclick="$(document).trigger('loadMainSection', { sectionId: 'events', url: 'section.list-events.php', forceReload: true });">
    <i class="fa-solid fa-chevron-left"></i>
    List
  </button>
</div>

<?php if ($eventId): ?>
  <h2>Edit Event</h2>
<?php else: ?>
  <h2>Add Event</h2>
<?php endif; ?>
<input type="hidden" id="event-id" value="<?= $eventId ?>">


<div class="row">
  <div class="col-12 col-sm-6 col-md-8 col-lg-5 col-xl-4 col-xxl-3">
    <div class="mb-3">
      <label for="event-start-date" class="form-label">Starts</label>
      <input type="datetime-local" class="form-control" id="event-start-date" value="<?= $event->startDate ?>" min="<?= date('Y-m-d\TH:i') ?>">
    </div>
  </div>

  <div class="col-12 col-sm-6 col-md-8 col-lg-5 col-xl-4 col-xxl-3">
    <div class="mb-3">
      <label for="event-end-date" class="form-label">Ends</label>
      <input type="datetime-local" class="form-control" id="event-end-date" value="<?= $event->endDate ?>" min="<?= date('Y-m-d\TH:i') ?>">
    </div>
  </div>

  <div class="col-12 col-xl-8">
    <div class="mb-3">
      <label for="event-name" class="form-label">Description</label>
      <input type="text" class="form-control" id="event-name" placeholder="Event Description" value="<?= $event->name ?>">
    </div>
  </div>

  <div class="col-12 col-sm-6 col-md-12 col-lg-6 col-xl-4">
    <div class="mb-3">
      <label for="event-location" class="form-label">Location</label>
      <input
        type="text"
        class="form-control"
        id="event-location"
        placeholder="Where is this event"
        value="<?= $event->location->name ?>"
        data-value="<?= $event->location->name ?>"
        data-id="<?= $event->locationId ?>">
      <div class="invalid-feedback">Please make a valid selection</div>
    </div>
  </div>

  <div class="col-12 col-sm-6 col-md-12 col-lg-6 col-xl-4 col-xxl-4">
    <div class="mb-3">
      <label for="event-drivers" class="form-label">Drivers</label>
      <div>
        <select id="event-drivers" class="form-control" multiple show-tick>
        </select>
      </div>
    </div>
  </div>

  <div class="col-12 col-sm-6 col-md-12 col-lg-6 col-xl-4 col-xxl-4">
    <div class="mb-3">
      <label for="event-vehicles" class="form-label">Vehicles</label>
      <div>
        <select id="event-vehicles" class="form-control" multiple show-tick>
        </select>
      </div>
    </div>
  </div>

  <div class="col-12 col-sm-6 col-md-12 col-lg-6 col-xl-4 col-xxl-4">
    <div class="mb-3">
      <label for="event-requestor" class="form-label">Requestor</label>
      <input
        type="text"
        class="form-control"
        id="event-requestor"
        placeholder="Requestor"
        value="<?= ($event->requestor) ? $event->requestor->getName() : '' ?>"
        data-value="<?= ($event->requestor) ? $event->requestor->getName() : '' ?>"
        data-id="<?= $event->requestorId ?>">
      <div class="invalid-feedback">Please make a valid selection</div>
    </div>
  </div>

</div>

<div class="row">
  <div class="col-12 col-xxl-8">
    <div class="mb-3">
      <label for="event-notes" class="form-label">Notes</label>
      <textarea class="form-control font-handwriting" id="event-notes" rows="7" style="border: 1px solid khaki;background: #ffffbb;font-size:large"><?= $event->notes ?></textarea>
    </div>
  </div>
</div>

<div class="d-flex justify-content-between mt-3">
  <?php if ($event->getId()): ?>
    <button class="btn btn-outline-danger" onclick="$(document).trigger('buttonDelete:event', <?= $event->getId() ?>)">Delete</button>
    <div class="mx-auto" style="color:lightslategray; font-size:small">
      <div>Created: <?= (new DateTime($event->created))->format('m/d/Y H:ia') ?> (<?=ucwords($event->createdBy)?>)</div>
      <?php if ($event->modified): ?>
        <div>Modified: <?= (new DateTime($event->modified))->format('m/d/Y H:ia') ?> (<?=ucwords($event->modifiedBy)?>)</div>
      <?php endif; ?>
    </div>
  <?php endif; ?>

  <?php if (!$event->isConfirmed()): ?>
    <button class="btn btn-primary ms-auto me-2" onclick="$(document).trigger('buttonSaveAndConfirm:event', '<?= $event->getId() ?>')">Save & Confirm</button>
  <?php endif; ?>
  <button class="btn btn-outline-primary" onclick="$(document).trigger('buttonSave:event', '<?= $event->getId() ?>')">Save</button>
</div>


<script>
  $(async ƒ => {

    function backToList() {
      $(document).trigger('loadMainSection', {
        sectionId: 'events',
        url: 'section.list-events.php',
        forceReload: true
      });
    }

    $('#events select').selectpicker({ container: false });

    $('#event-requestor').off('blur').on('blur', function() {
      if ($('#event-requestor').cleanVal() == '') $('#event-requestor').removeData('id');
    });

    $('#event-location').off('blur').on('blur', function() {
      if ($('#event-location').cleanVal() == '') $('#event-location').removeData('id');
    });

    if ($('#event-id').val()) {
      await loadResources();
      $('#event-drivers').selectpicker('val', ['<?= implode("','", $event->drivers) ?>']);
      $('#event-vehicles').selectpicker('val', ['<?= implode("','", $event->vehicles) ?>']);
    }

    async function loadResources() {
      const eventId = $('#event-id').val();
      const startDate = moment($('#event-start-date').val());
      const endDate = moment($('#event-end-date').val());
      
      if (!startDate.isValid() || !endDate.isValid()) return;

      // Load the resources!
      const drivers = await net.get('/api/get.available-drivers.php', {
        startDate: startDate.format('YYYY-MM-DD HH:mm:00'),
        endDate: endDate.format('YYYY-MM-DD HH:mm:59'),
        eventId
      });
      const vehicles = await net.get('/api/get.available-vehicles.php', {
        startDate: startDate.format('YYYY-MM-DD HH:mm:00'),
        endDate: endDate.format('YYYY-MM-DD HH:mm:59'),
        eventId
      });

      $('#event-drivers').selectpicker('destroy');
      $('#event-drivers option').remove();
      $.each(drivers, function(i, item) {
        const optionProps = {
          value: item.id,
          text: item.driver,
          // disabled: !item.available,
        }
        if (!item.available) {
          optionProps.style = `background-color:crimson; color: white`;
          optionProps['data-icon'] = 'fa-solid fa-triangle-exclamation';
        }
        $('#event-drivers').append($('<option>', optionProps));
      });
      $('#event-drivers').selectpicker({ container: false });

      $('#event-vehicles').selectpicker('destroy');
      $('#event-vehicles option').remove();
      $.each(vehicles, function(i, item) {
        const optionProps = {
          value: item.id,
          text: item.name,
        }
        if (!item.available) {
          optionProps.style = `background-color:crimson; color: white`;
          optionProps['data-icon'] = 'fa-solid fa-triangle-exclamation';
        }
        $('#event-vehicles').append($('<option>', optionProps));
      });
      $('#event-vehicles').selectpicker()
    }

    $('#event-start-date').on('change', loadResources);
    $('#event-end-date').on('change', loadResources);

    buildAutoComplete({
      selector: 'event-location',
      apiUrl: '/api/get.autocomplete-locations.php',
      searchFields: ['label', 'short_name'],
    });
    buildAutoComplete({
      selector: 'event-requestor',
      apiUrl: '/api/get.autocomplete-requestors.php'
    });


    function getData() {
      const eventId = $('#event-id').val();
      const data = { eventId, id: eventId };
      const startDate = moment($('#event-start-date').val());
      const endDate = moment($('#event-end-date').val());

      data.startDate = startDate.format('YYYY-MM-DD HH:mm:ss');
      data.endDate = endDate.format('YYYY-MM-DD HH:mm:ss');
      data.name = $('#event-name').cleanVal();
      data.drivers = $('#event-drivers').val();
      data.vehicles = $('#event-vehicles').val();
      data.notes = $('#event-notes').cleanVal();
      if ($('#event-location').val()) data.locationId = $('#event-location').data('id');
      if ($('#event-requestor').val()) data.requestorId = $('#event-requestor').data('id');
      return data;
    }

    if (!documentEventExists('buttonSave:event')) {
      $(document).on('buttonSave:event', async (e, id) => {
        const startDate = moment($('#event-start-date').val());
        const endDate = moment($('#event-end-date').val());

        if (!startDate.isValid() || !endDate.isValid()) return ui.toastr.error('Please select a valid start and end date.', 'Error');
        if (startDate.isAfter(endDate)) return ui.toastr.error('Start date cannot be after end date.', 'Error');

        const data = getData();

        if (data.eventId != id) {
          console.trace('Event ID mismatch.', data.eventId, id);
          return ui.toastr.error('Event ID mismatch.', 'Error');
        }

        const resp = await net.post('/api/post.save-event.php', data);
        if (resp?.result) {
          $(document).trigger('eventChange', { id });
          if (data.eventId) {
            ui.toastr.success('Event saved.', 'Success');
            return backToList();
          }
        }
        ui.toastr.error(resp.result.errors[2], 'Error');
        console.log(resp);
      });
    }

    if (!documentEventExists('buttonSaveAndConfirm:event')) {
      $(document).on('buttonSaveAndConfirm:event', async (e, id) => {
        const startDate = moment($('#event-start-date').val());
        const endDate = moment($('#event-end-date').val());

        if (!startDate.isValid() || !endDate.isValid()) return ui.toastr.error('Please select a valid start and end date.', 'Error');
        if (startDate.isAfter(endDate)) return ui.toastr.error('Start date cannot be after end date.', 'Error');

        const data = getData();

        if (data.eventId != id) {
          console.trace('Event ID mismatch.', data.eventId, id);
          return ui.toastr.error('Event ID mismatch.', 'Error');
        }

        const resp = await net.post('/api/post.save-event.php', data);
        if (resp?.result) {
          // Confirm the event
          const newResp = await net.post('/api/post.confirm-event.php', { id });
          if (newResp?.result) {
            ui.toastr.success('Event Saved and Confirmed.', 'Success');
            $(document).trigger('eventChange');
            return backToList();
          }
        }
        ui.toastr.error(resp.result.errors[2], 'Error');
        console.log(resp);
      });
    }

    if (!documentEventExists('buttonDelete:event')) {
      $(document).on('buttonDelete:event', async (e, id) => {
        if (await ui.ask('Are you sure you want to delete this event?')) {
          const resp = await net.get('/api/get.delete-event.php', {
            id
          });
          if (resp?.result) {
            $(document).trigger('eventChange', { id });
            ui.toastr.success('Event deleted.', 'Success');
            return backToList();
          }
          console.log(resp);
          ui.toastr.error('There seems to be a problem deleting this event.', 'Error');
        }
      });
    }

  });
</script>