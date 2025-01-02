<?php
require_once 'class.event.php';
$event = new Event($_REQUEST['id']);
$eventId = $event->getId();
?>
<?php if (isset($_REQUEST['id']) && !$eventId): ?>

  <div class="container-fluid text-center">
    <div class="alert alert-danger mt-5 w-50 mx-auto">
      <h1 class="fw-bold">Oh dear!</h1>
      <p class="lead">I can't seem to find that event! <i class="fa-duotone fa-solid fa-face-thinking"></i></p>
    </div>
  </div>

<?php else: ?>

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
          <div
            class="input-group log-event"
            id="datetimepicker1"
            data-td-target-input="nearest"
            data-td-target-toggle="nearest">
            <input
              id="event-start-date"
              type="text"
              class="form-control"
              data-td-target="#datetimepicker1"
              value="<?=($event->startDate) ? Date('m/d/Y h:i A', strtotime($event->startDate)) : '' ?>"/>
            <span
              class="input-group-text"
              data-td-target="#datetimepicker1"
              data-td-toggle="datetimepicker">
              <i class="fa-duotone fa-calendar"></i>
            </span>
          </div>
        </div>
      </div>

      <div class="col-3">
        <div class="mb-3">
          <label for="event-end-date" class="form-label">Ends</label>
          <div
            class="input-group log-event"
            id="datetimepicker2"
            data-td-target-input="nearest"
            data-td-target-toggle="nearest">
            <input
              id="event-end-date"
              type="text"
              class="form-control"
              data-td-target="#datetimepicker2"
              value="<?=($event->endDate) ? Date('m/d/Y h:i A', strtotime($event->endDate)) : '' ?>"/>
            <span
              class="input-group-text"
              data-td-target="#datetimepicker2"
              data-td-toggle="datetimepicker">
              <i class="fa-duotone fa-calendar"></i>
            </span>
          </div>
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


  <script type="text/javascript">

    $(async ƒ => {

      let drivers;
      let vehicles;
      let startDate;
      let endDate

      const eventId = <?=$eventId ?: 'null'?>;
      $('select').selectpicker();

      new tempusDominus.TempusDominus(document.getElementById('datetimepicker1'), tempusConfigDefaults);
      new tempusDominus.TempusDominus(document.getElementById('datetimepicker2'), tempusConfigDefaults);

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
          drivers = await get('/api/get.available-drivers.php', {
            startDate: startDate.format('YYYY-MM-DD HH:mm:00'),
            endDate: endDate.format('YYYY-MM-DD HH:mm:59'),
            eventId
          });
          vehicles = await get('/api/get.available-vehicles.php', {
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
        startDate = moment($('#event-start-date').val(), 'MM/DD/YYYY h:mm A');
        loadResources();
      });
      $('#event-end-date').on('change', function () {
        endDate = moment($('#event-end-date').val(), 'MM/DD/YYYY h:mm A');
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
        data.name = cleanVal('#event-name');
        data.drivers = val('#event-drivers');
        data.vehicles = val('#event-vehicles');
        data.notes = cleanVal('#event-notes');
        if ($('#event-location').val()) data.locationId = $('#event-location').data('id');
        if ($('#event-requestor').val()) data.requestorId = $('#event-requestor').data('id');
        return data;
      }

      $('#btn-save-event').off('click').on('click', async function () {
        const data = getData();
        const resp = await post('/api/post.save-event.php', data);
        if (resp?.result) {
          $(document).trigger('eventChange', {eventId});
          app.closeOpenTab();
          if (eventId) return toastr.success('Event saved.', 'Success');
          return toastr.success('Event added.', 'Success')
        }
        toastr.error(resp . result . errors[2], 'Error');
        console.log(resp);
      });

      $('#btn-save-confirm-event').off('click').on('click', async function () {
        const data = await getData();
        if (data) {
          const resp = await post('/api/post.save-event.php', data);
          if (resp?.result) {
            const id = eventId || resp?.result;
            const newResp = await post('/api/post.confirm-event.php', {id});
            if (newResp?.result) {
              $(document).trigger('eventChange');
              app.closeOpenTab();
              return toastr.success('Event added.', 'Success');
            }
            return toastr.error('Seems to be a problem confirming this event!', 'Error');
          }
          toastr.error(resp.result.errors[2], 'Error');
          console.error(resp);
        }
      });

      $('#btn-delete-event').on('click', async ƒ => {
        if (await ask('Are you sure you want to delete this event?')) {
          const resp = await get('/api/get.delete-event.php', {
            id: '<?=$eventId?>'
          });
          if (resp?.result) {
            $(document).trigger('eventChange', {eventId});
            app.closeOpenTab();
            return toastr.success('Event deleted.', 'Success')
          }
          console.log(resp);
          toastr.error('There seems to be a problem deleting event.', 'Error');
        }
      });
    });

<?php endif;?>