<?php
require_once 'autoload.php';

use Transport\Event;
use Generic\Utils;

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT, FILTER_NULL_ON_FAILURE);
$id = $id === false ? null : $id;
$event = new Event($id);
$eventId = $event->getId();

if (!is_null($id) && !$eventId) {
  exit(Utils::showResourceNotFound());
}
?>

<div class="container mt-2">
  <div class="d-flex justify-content-between">
    <?php if ($eventId): ?>
      <h2>Edit Event</h2>
    <?php else: ?>
      <h2>New Event</h2>
    <?php endif; ?>
    <button id="btn-duplicate-event" class="btn btn-secondary d-none"><i class="fa-duotone fa-solid fa-copy"></i> Duplicate</button>
  </div>

  <div class="row">
    <div class="col-3">
      <div class="mb-3">
        <label for="event-start-date" class="form-label">Starts</label>
        <input type="datetime-local" class="form-control" id="event-start-date" value="<?=$event->startDate?>" min="<?=date('Y-m-d\TH:i')?>">
      </div>
    </div>

    <div class="col-3">
      <div class="mb-3">
        <label for="event-end-date" class="form-label">Ends</label>
        <input type="datetime-local" class="form-control" id="event-end-date" value="<?=$event->endDate?>" min="<?=date('Y-m-d\TH:i')?>">
      </div>
    </div>

    <div class="col">
      <div class="mb-3">
        <label for="event-name" class="form-label">Description</label>
        <input type="text" class="form-control" id="event-name" placeholder="Event Description" value="<?=$event->name?>">
      </div>
    </div>
  </div>

  <div class="row">
    <div class="col">
      <div class="mb-3">
        <label for="event-location" class="form-label">Location</label>
        <input 
          type="text" 
          class="form-control" 
          id="event-location" 
          placeholder="Where is this event"
          value="<?=$event->location->name?>" 
          data-value="<?=$event->location->name?>" 
          data-id="<?=$event->locationId?>">
        <div class="invalid-feedback">Please make a valid selection</div>
      </div>
    </div>

    <div class="col-4">
      <div class="mb-3">
        <label for="event-requestor" class="form-label">Requestor</label>
        <input 
          type="text" 
          class="form-control" 
          id="event-requestor" 
          placeholder="Requestor" 
          value="<?=($event->requestor) ? $event->requestor->getName() : ''?>" 
          data-value="<?=($event->requestor) ? $event->requestor->getName() : ''?>" 
          data-id="<?=$event->requestorId?>">
        <div class="invalid-feedback">Please make a valid selection</div>
      </div>
    </div>
  </div>


  <div class="row">
    <div class="col">
      <div class="mb-3">
        <label for="event-drivers" class="form-label">Drivers</label>
        <div>
          <select id="event-drivers" class="form-control" multiple show-tick>
          </select>
        </div>
      </div>
    </div>

    <div class="col">
      <div class="mb-3">
        <label for="event-vehicles" class="form-label">Vehicles</label>
        <div>
          <select id="event-vehicles" class="form-control" multiple show-tick>
          </select>
        </div>
      </div>
    </div>
  </div>

  <div class="row">
    <div class="col">
      <div class="mb-3">
        <label for="event-notes" class="form-label">Notes</label>
        <textarea class="form-control" id="event-notes" rows="7"><?=$event->notes?></textarea>
      </div>
    </div>
  </div>

  <div class="row my-4">
    <div class="col d-flex justify-content-between">
      <?php if ($eventId): ?>
        <button class="btn btn-outline-danger px-4" id="btn-delete-event">Delete</button>
      <?php endif; ?>
      <div class="ms-auto">
        <?php if (!$event->confirmed): ?>
          <button class="btn btn-outline-primary px-4 me-2" id="btn-save-confirm-event">Save & Confirm</button>
        <?php endif; ?>
        <button class="btn btn-primary px-4" id="btn-save-event">Save</button>
      </div>
    </div>
  </div>


</div>


<script type="module">
  import * as input from '/js/formatters.js';
  import * as ui from '/js/notifications.js';
  import * as net from '/js/network.js';

  $(async ƒ => {

    let drivers;
    let vehicles;
    let startDate;
    let endDate

    const eventId = <?=$eventId ?? 'null'?>;
    $('select').selectpicker();

    $('#event-requestor').off('blur').on('blur', function () {
      if (cleanVal('#event-requestor') == '') $('#event-requestor').removeData('id');
    });

    $('#event-location').off('blur').on('blur', function () {
      if (cleanVal('#event-location') == '') $('#event-location').removeData('id');
    });

    if (eventId) {
      startDate = moment('<?=$event->startDate?>', 'YYYY-MM-DD H:mm:ss');
      endDate = moment('<?=$event->endDate?>', 'YYYY-MM-DD H:mm:ss');
      await loadResources();
      $('#event-drivers').selectpicker('val', ['<?=implode("','", $event->drivers)?>']);
      $('#event-vehicles').selectpicker('val', ['<?=implode("','", $event->vehicles)?>']);
    }

    async function loadResources ()
    {
      if (startDate && endDate) {
        // Load the resources!
        drivers = await net.get('/api/get.available-drivers.php', {
          startDate: startDate.format('YYYY-MM-DD HH:mm:00'),
          endDate: endDate.format('YYYY-MM-DD HH:mm:59'),
          eventId
        });
        vehicles = await net.get('/api/get.available-vehicles.php', {
          startDate: startDate.format('YYYY-MM-DD HH:mm:00'),
          endDate: endDate.format('YYYY-MM-DD HH:mm:59'),
          eventId
        });
      }

      $('#event-drivers').selectpicker('destroy');
      $('#event-drivers option').remove();
      $.each(drivers, function (i, item) {
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
      $('#event-drivers').selectpicker()

      $('#event-vehicles').selectpicker('destroy');
      $('#event-vehicles option').remove();
      $.each(vehicles, function (i, item) {
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

    $('#event-start-date').on('change', function () {
      startDate = moment($('#event-start-date').val());
      loadResources();
    });
    $('#event-end-date').on('change', function () {
      endDate = moment($('#event-end-date').val());
      loadResources();
    });

    new Autocomplete(document.getElementById('event-location'), {
      fullWidth: true,
      highlightTyped: true,
      liveServer: true,
      server: '/api/get.autocomplete-locations.php',
      searchFields: ['label', 'short_name'],
      onSelectItem: (data) => {
        $('#event-location')
          .data('id', data.value)
          .data('type', data.type)
          .data('value', data.label)
          .removeClass('is-invalid');
      },
      fixed: true,
    });

    new Autocomplete(document.getElementById('event-requestor'), {
      fullWidth: true,
      highlightTyped: true,
      liveServer: true,
      server: '/api/get.autocomplete-requestors.php',
      onSelectItem: (data) => {
        $('#event-requestor')
          .data('id', data.value)
          .data('value', data.label)
          .removeClass('is-invalid');
      },
      fixed: true,
    });


    function getData () {
      const data = {
        eventId
      }
      data.startDate = startDate.format('YYYY-MM-DD HH:mm:ss');
      data.endDate = endDate.format('YYYY-MM-DD HH:mm:ss');
      data.name = input.cleanVal('#event-name');
      data.drivers = input.val('#event-drivers');
      data.vehicles = input.val('#event-vehicles');
      data.notes = input.cleanVal('#event-notes');
      if ($('#event-location').val()) data.locationId = $('#event-location').data('id');
      if ($('#event-requestor').val()) data.requestorId = $('#event-requestor').data('id');
      return data;
    }

    $('#btn-save-event').off('click').on('click', async function () {
      if (!startDate || !endDate) return ui.toastr.error('Please select a start and end date.', 'Error');
      if (startDate.isAfter(endDate)) return ui.toastr.error('Start date cannot be after end date.', 'Error');

      const buttonSavedText = $('#btn-save-event').text();
      $('#btn-save-event').prop('disabled', true).text('Saving...');

      const data = getData();
      const resp = await net.post('/api/post.save-event.php', data);
      if (resp?.result) {
        $(document).trigger('eventChange', {eventId});
        app.closeOpenTab();
        if (eventId) return ui.toastr.success('Event saved.', 'Success');
        $('#btn-save-event').prop('disabled', false).text(buttonSavedText);
        return ui.toastr.success('Event added.', 'Success')
      }
      ui.toastr.error(resp . result . errors[2], 'Error');
      console.log(resp);
      $('#btn-save-event').prop('disabled', false).text(buttonSavedText);
    });

    $('#btn-save-confirm-event').off('click').on('click', async function () {
      const buttonSavedText = $('#btn-save-confirm-event').text();
      $('#btn-save-confirm-event').prop('disabled', true).text('Saving...');

      const data = await getData();
      if (data) {
        const resp = await net.post('/api/post.save-event.php', data);
        if (resp?.result) {
          const id = eventId || resp?.result;
          const newResp = await net.post('/api/post.confirm-event.php', {id});
          if (newResp?.result) {
            $(document).trigger('eventChange');
            app.closeOpenTab();
            $('#btn-save-confirm-event').prop('disabled', false).text(buttonSavedText);
            return ui.toastr.success('Event added.', 'Success');
          }
          $('#btn-save-confirm-event').prop('disabled', false).text(buttonSavedText);
          return ui.toastr.error('Seems to be a problem confirming this event!', 'Error');
        }
        ui.toastr.error(resp.result.errors[2], 'Error');
        console.error(resp);
        $('#btn-save-confirm-event').prop('disabled', false).text(buttonSavedText);
      }
    });

    $('#btn-delete-event').on('click', async ƒ => {
      if (await ui.ask('Are you sure you want to delete this event?')) {
        const buttonSavedText = $('#btn-delete-event').text();
        $('#btn-delete-event').prop('disabled', true).text('Deleting...');

        const resp = await net.get('/api/get.delete-event.php', {
          id: '<?=$eventId?>'
        });
        if (resp?.result) {
          $(document).trigger('eventChange', {eventId});
          $('#btn-delete-event').prop('disabled', false).text(buttonSavedText);
          app.closeOpenTab();
          return ui.toastr.success('Event deleted.', 'Success')
        }
        console.log(resp);
        ui.toastr.error('There seems to be a problem deleting event.', 'Error');
        $('#btn-delete-event').prop('disabled', false).text(buttonSavedText);
      }
    });
  });

</script>