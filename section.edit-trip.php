<?php
require_once 'class.trip.php';
$trip = new Trip(tripId: $_REQUEST['id']);
?>
<?php if (isset($_REQUEST['id']) && !$trip->tripId): ?>

  <div class="container-fluid text-center">
    <div class="alert alert-danger mt-5 w-50 mx-auto">
      <h1 class="fw-bold">Oh dear!</h1>
      <p class="lead">I can't seem to find that trip! <i class="fa-duotone fa-solid fa-face-thinking"></i></p>
    </div>
  </div>

<?php else: ?>

  <div id="vehicle-edit-container" class="container mt-2">
    <input type="hidden" id="trip-end-date" value="<?=$trip->endDate?>" />

    <div class="d-flex justify-content-between">
      <?php if ($trip->isEditable()): ?>
        <?php if ($trip->tripId): ?>

          <h2>Edit Trip</h2>

          <?php else: ?>
          <h2>Add Trip</h2>
        <?php endif; ?>
      <?php endif;?>
      
      
    </div>

    <div class="mb-5">
      <input type="hidden" id="tripId" name="tripId" value="<?=$trip->tripId?>" />

      <div class="row">
        <div class="col">
          <div class="mb-3">
            <label for="trip-summary" class="form-label">Summary</label>
            <input type="text" class="form-control" id="trip-summary" placeholder="Summary" value="<?=$trip->summary?>">
          </div>
        </div>
      </div>

      <div class="row mt-4">
        <h4>Pick up</h4>
      </div>

      <div class="row">
        <div class="col-2">
          <div class="mb-3">
            <label for="trip-lead-time" class="form-label" data-bs-toggle="tooltip" data-bs-title="When the actual trip starts">Lead Time</label>
            <div class="input-group mb-3">
              <input type="text" class="form-control" id="trip-lead-time" value="<?=round(abs((strtotime($trip->startDate) - strtotime($trip->pickupDate))/60/60))?>" placeholder="e.g. 1.5"/>
              <span class="input-group-text">hour(s)</span>
            </div>
          </div>
        </div>

        <div class="col-3">
          <div class="mb-3">
            <label for="trip-pickup-date" class="form-label" data-bs-toggle="tooltip" data-bs-title="The point at which you'd meet the guest/group">Date / Time</label>
            <div
              class="input-group log-event"
              id="datetimepicker1"
              data-td-target-input="nearest"
              data-td-target-toggle="nearest">
              <input
                id="trip-pickup-date"
                type="text"
                class="form-control"
                data-td-target="#datetimepicker1"
                value="<?=($trip->pickupDate) ? Date('m/d/Y h:i A', strtotime($trip->pickupDate)) : '' ?>"/>
              <span
                class="input-group-text"
                data-td-target="#datetimepicker1"
                data-td-toggle="datetimepicker">
                <i class="fa-duotone fa-calendar"></i>
              </span>
            </div>
          </div>
        </div>
        <div class="col-2">
          <div class="mb-3">
            <label for="trip-duration" class="form-label" data-bs-toggle="tooltip" data-bs-title="From start to end">Total Trip Duration</label>
            <div class="input-group mb-3">
              <input type="text" class="form-control" id="trip-duration" value="<?=round(abs((strtotime($trip->endDate) - strtotime($trip->startDate))/60/60),2)?>" placeholder="e.g. 1.5"/>
              <span class="input-group-text">hour(s)</span>
            </div>

            <input type="hidden" id="trip-end-date" value="<?=$trip->endDate?>" />
          </div>
        </div>

      </div>

      <div class="row">
        <div class="col">
          <div class="mb-3">
            <label for="trip-pu-location" class="form-label">Location</label>
            <input 
              type="text" 
              class="form-control" 
              id="trip-pu-location" 
              placeholder="Pick Up Location" 
              value="<?=$trip->puLocation->name?>" 
              data-id="<?=$trip->puLocationId?>"
              data-value="<?=$trip->puLocation->name?>"
              data-type="<?=$trip->puLocation? $trip->puLocation->type : ''?>">
              <div class="invalid-feedback">Please make a valid selection</div>
          </div>
        </div>
      </div>

      <div class="row">
        <div class="col">
          <div class="mb-3">
            <label for="trip-guests" class="form-label">Guest(s) / Group</label>
            <input type="text" class="form-control" id="trip-guests" placeholder="" value="<?=$trip->guests?>">
          </div>
        </div>
        
        <div class="col-3">
          <div class="mb-3">
            <label for="trip-guest" class="form-label">Contact Person</label>
            <input 
              type="text" 
              class="form-control" 
              id="trip-guest" 
              placeholder="Contact" 
              value="<?=$trip->guestId ? $trip->guest->getName() : ''?>" 
              data-value="<?=$trip->guestId ? $trip->guest->getName() : ''?>" 
              data-id="<?=$trip->guestId?>">
            <div class="invalid-feedback">Please make a valid selection</div>
          </div>
        </div>

        <div class="col-2">
          <div class="mb-3">
            <label for="trip-passengers" class="form-label">Total Passengers</label>
            <input type="number" class="form-control" id="trip-passengers" placeholder="# Passengers" value="<?=$trip->passengers?>">
          </div>
        </div>

        
      </div>

      <div class="row mt-4">
        <h4>Drop off</h4>
      </div>
      <div class="row">
        <div class="col">
          <div class="mb-3">
            <label for="trip-do-location" class="form-label">Location</label>
            <input 
              type="text" 
              class="form-control" 
              id="trip-do-location" 
              placeholder="Drop Off Location" 
              value="<?=$trip->doLocation->name?>" 
              data-value="<?=$trip->doLocation->name?>" 
              data-id="<?=$trip->doLocationId?>" 
              data-type="<?=$trip->doLocation? $trip->doLocation->type : ''?>">
              <div class="invalid-feedback">Please make a valid selection</div>
          </div>
        </div>
      </div>

      <div class="row mt-4">
        <div class="col-6">
          <div class="row">
            <h4><i class="fa-duotone fa-solid fa-steering-wheel"></i> Driver</h4>
          </div>
          <div class="row">
            <div class="col">
              <div class="mb-3">
                <label for="trip-vehicle-id" class="form-label">Vehicle</label>
                <div><select id="trip-vehicle-id" class="form-control" data-container="#vehicle-edit-container"></select></div>
              </div>
            </div>

            <div class="col">
              <div class="mb-3">
                <label for="trip-driver-id" class="form-label">Driver</label>
                <div><select id="trip-driver-id" class="form-control" data-container="#vehicle-edit-container"></select></div>
              </div>
            </div>
          </div>

          <div class="row">
            <div class="col">
              <div class="mb-3">
                <label for="trip-vehicle-pu-options" class="form-label">Pick up options</label>
                <div>
                  <select id="trip-vehicle-pu-options" class="form-control" data-container="#vehicle-edit-container">
                    <option></option>
                    <option value="pick up from staging" <?=$trip->vehiclePUOptions == 'pick up from staging' ? 'selected' : ''?> >Pick up from staging</option>
                    <option value="guest will have vehicle" <?=$trip->vehiclePUOptions == 'guest will have vehicle' ? 'selected' : ''?> >Guest will have vehicle</option>
                    <option value="commence from current location" <?=$trip->vehiclePUOptions == 'commence from current location' ? 'selected' : ''?> >Commence from current location</option>
                  </select>
                </div>
              </div>
            </div>
            <div class="col">
              <div class="mb-3">
                <label for="trip-vehicle-do-options" class="form-label">Drop off options</label>
                <div>
                  <select id="trip-vehicle-do-options" class="form-control" data-container="#vehicle-edit-container">
                    <option></option>
                    <option value="return to staging" <?=$trip->vehicleDOOptions == 'return to staging' ? 'selected' : ''?> >Return to staging</option>
                    <option value="leave vehicle with guest" <?=$trip->vehicleDOOptions == 'leave vehicle with guest' ? 'selected' : ''?> >Leave vehicle with guest(s)</option>
                    <option value="remain at destination" <?=$trip->vehicleDOOptions == 'remain at destination' ? 'selected' : ''?> >Remain at destination</option>
                  </select>
                </div>
              </div>
            </div>
          </div>

        </div>

        <div class="col-6">
          <section id="flight-info" class="d-none">
            <div class="row">
              <h4><i class="fa-duotone fa-solid fa-plane-tail"></i> Flight</h4>
            </div>
            <div class="row">
            <div class="col">
              <div class="mb-3">
                <label for="trip-airline-id" class="form-label">Airline</label>
                <div>
                  <select id="trip-airline-id" data-live-search="true" show-tick class="form-control" data-size="5" data-container="#vehicle-edit-container"></select>
                </div>
              </div>
              </div>
              <div class="col">
                <div class="mb-3">
                  <label for="trip-flight-number" class="form-label">Flight Number</label>
                  <div class="input-group">
                    <span class="input-group-text" id="flight-number-prefix"><?=$trip->airline ? $trip->airline->flightNumberPrefix : '&nbsp;&nbsp;'?></span>
                    <input type="text" class="form-control" id="trip-flight-number" placeholder="Flight number without the prefix" value="<?=$trip->flightNumber?>">
                  </div>
                </div>
              </div>
            </div>

            <div class="row">
              <div id="airline-image" class="col-6">
                <?php if ($trip->airline->imageFilename): ?>
                  <img src="/images/airlines/<?=$trip->airline->imageFilename?>" class="img-fluid">
                <?php endif; ?>
              </div>
              <div class="col-6 d-none" id="eta-section">
                <div class="mb-3">
                  <label for="trip-eta" class="form-label">ETA</label>
                  <div
                    class="input-group log-event"
                    id="datetimepicker2"
                    data-td-target-input="nearest"
                    data-td-target-toggle="nearest">
                    <input
                      id="trip-eta"
                      type="text"
                      class="form-control"
                      data-td-target="#datetimepicker2"
                      value="<?=($trip->ETA) ? Date('m/d/Y h:i A', strtotime($trip->ETA)) : '' ?>"/>
                    <span
                      class="input-group-text"
                      data-td-target="#datetimepicker2"
                      data-td-toggle="datetimepicker">
                      <i class="fa-duotone fa-calendar"></i>
                    </span>
                  </div>
                </div>
              </div>

              <div class="col-6 d-none" id="etd-section">
                <div class="mb-3">
                  <label for="trip-etd" class="form-label">ETD</label>
                  <div
                    class="input-group log-event"
                    id="datetimepicker3"
                    data-td-target-input="nearest"
                    data-td-target-toggle="nearest">
                    <input
                      id="trip-etd"
                      type="text"
                      class="form-control"
                      data-td-target="#datetimepicker3"
                      value="<?=($trip->ETD) ? Date('m/d/Y h:i A', strtotime($trip->ETD)) : '' ?>"/>
                    <span
                      class="input-group-text"
                      data-td-target="#datetimepicker3"
                      data-td-toggle="datetimepicker">
                      <i class="fa-duotone fa-calendar"></i>
                    </span>
                  </div>
                </div>
              </div>
            </div>
          </section>
        </div>
      </div>

      <div class="row mt-4">
        <h4>General</h4>
      </div>
      <div class="row">
        <div class="col-4">
          <div class="mb-3">
            <label for="trip-requestor" class="form-label">Requestor</label>
            <input 
              type="text" 
              class="form-control" 
              id="trip-requestor" 
              placeholder="Requestor" 
              value="<?=$trip->requestorId ? $trip->requestor->getName() : ''?>" 
              data-value="<?=$trip->requestorId ? $trip->requestor->getName() : ''?>" 
              data-id="<?=$trip->requestorId?>"/>
            <div class="invalid-feedback">Please make a valid selection</div>
          </div>
        </div>
      </div>

      <div class="row">
        <div class="col-4">
          <div class="mb-3">
            <label for="trip-guest-notes" class="form-label">Guest Notes</label>
            <textarea class="form-control" id="trip-guest-notes" rows="5"><?=$trip->guestNotes?></textarea>
          </div>
        </div>
        <div class="col-4">
          <div class="mb-3">
            <label for="trip-driver-notes" class="form-label">Driver Notes</label>
            <textarea class="form-control" id="trip-driver-notes" rows="5"><?=$trip->driverNotes?></textarea>
          </div>
        </div>
        <div class="col-4">
          <div class="mb-3">
            <label for="trip-general-notes" class="form-label">General Notes</label>
            <textarea class="form-control" id="trip-general-notes" rows="5"><?=$trip->generalNotes?></textarea>
          </div>
        </div>
      </div>

      
      <div class="row">
        <div class="col d-flex justify-content-between">
          <?php if ($trip->tripId): ?>
            <button id="btn-delete-trip" class="btn btn-outline-danger">Delete</button>
          <?php endif; ?>

          <?php if ($trip->isEditable()): ?>
            <button id="btn-save-trip" class="btn btn-outline-primary">Save</button>
          <?php endif;?>
        </div>
      </div>

    </div>
  </div>

  <script type="text/javascript">

    $(async ƒ => {

      let formDirty = false;
      const tripId = <?=$trip->tripId ?: 'null'?>;
      let drivers;
      let vehicles;
      let startDate;
      let pickupDate;
      let endDate;

      const pickupDateControl = new tempusDominus.TempusDominus(document.getElementById('datetimepicker1'), tempusConfigDefaults);
      const eta = new tempusDominus.TempusDominus(document.getElementById('datetimepicker2'), tempusConfigDefaults);
      const etd = new tempusDominus.TempusDominus(document.getElementById('datetimepicker3'), tempusConfigDefaults);
      const airlines = await get('/api/get.resource-airlines.php');

      $('#trip-airline-id').append($('<option>'));
      $.each(airlines, function (i, item) {
        $('#trip-airline-id').append($('<option>', {
          value: item.id,
          text: item.name
        }));
      });

      $('select').selectpicker();

      async function loadResources () {
        drivers = await get('/api/get.available-drivers.php', {
          startDate: startDate.format('YYYY-MM-DD HH:mm:00'),
          endDate: endDate.format('YYYY-MM-DD HH:mm:59'),
          tripId
        });
        $('#trip-driver-id').selectpicker('destroy');
        $('#trip-driver-id option').remove();
        $('#trip-driver-id').append($('<option>'));
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
          $('#trip-driver-id').append($('<option>', optionProps));
        });
        $('#trip-driver-id').selectpicker()

        vehicles = await get('/api/get.available-vehicles.php', {
          startDate: startDate.format('YYYY-MM-DD HH:mm:00'),
          endDate: endDate.format('YYYY-MM-DD HH:mm:59'),
          tripId
        });
        $('#trip-vehicle-id').selectpicker('destroy');
        $('#trip-vehicle-id option').remove();
        $('#trip-vehicle-id').append($('<option>'));
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
          $('#trip-vehicle-id').append($('<option>', optionProps));
        });
        $('#trip-vehicle-id').selectpicker();

      }

      $('input').off('change').on('change', e => {
        formDirty = true;
        $('#trip-action-buttons button').addClass('disabled');
      })

      if (tripId) {
        startDate = moment('<?=$trip->startDate?>', 'YYYY-MM-DD H:mm:ss');
        pickupDate = moment('<?=$trip->pickupDate?>', 'YYYY-MM-DD H:mm:ss');
        endDate = moment('<?=$trip->endDate?>', 'YYYY-MM-DD H:mm:ss');
        await loadResources();
        $('#trip-airline-id').selectpicker('val', '<?=$trip->airlineId?>');
        $('#trip-driver-id').selectpicker('val', '<?=$trip->driverId?>');
        $('#trip-vehicle-id').selectpicker('val', '<?=$trip->vehicleId?>');
      }

      $('#trip-airline-id').append($('<option>'));
      $.each(airlines, function (i, item) {
        $('#trip-airline-id').append($('<option>', {
          value: item.id,
          text: item.name
        }));
      });



      







      // $('#btn-duplicate-trip').off('click').on('click', async function () {
      //   const resp = await get('/api/get.duplicate-trip.php', {id: tripId});
      //   const newId = resp.result;
      //   app.closeOpenTab()
      //   app.openTab('edit-trip', 'Trip (edit)', `section.edit-trip.php?id=${newId}`);
      // });

      $('#trip-pickup-date, #trip-duration, #trip-lead-time').on('change', async ƒ => {
        const leadTime = isNaN(parseFloat(cleanNumberVal('#trip-lead-time'))) ? 0 : parseInt(cleanNumberVal('#trip-lead-time') * 60);
        pickupDate = moment($('#trip-pickup-date').val(), 'MM/DD/YYYY h:mm A');
        startDate = moment(pickupDate).subtract(leadTime, 'm');
        endDate = moment(startDate).add(cleanNumberVal('#trip-duration'), 'h');

        // The vehicle and/or driver may not be available in the new period, but if they are they will remain "selected".
        const saveDriverId = val('#trip-driver-id');
        const saveVehicleId = val('#trip-vehicle-id');
        await loadResources();
        $('#trip-driver-id').selectpicker('val', saveDriverId);
        $('#trip-vehicle-id').selectpicker('val', saveVehicleId);
      });





      new Autocomplete(document.getElementById('trip-pu-location'), {
        fullWidth: true,
        // highlightTyped: true,
        liveServer: true,
        server: '/api/get.autocomplete-locations.php',
        searchFields: ['label', 'short_name'],
        onSelectItem: (data) => {
          $('#trip-pu-location')
            .data('id', data.value)
            .data('type', data.type)
            .data('value', data.label)
            .removeClass('is-invalid');
          checkForFlight();
        },
        fixed: true,
      });

      new Autocomplete(document.getElementById('trip-guest'), {
        fullWidth: true,
        // highlightTyped: true,
        liveServer: true,
        server: '/api/get.autocomplete-guests.php',
        onSelectItem: (data) => {
          $('#trip-guest')
            .data('id', data.value)
            .data('value', data.label)
            .removeClass('is-invalid');
        },
        fixed: true,
      });

      new Autocomplete(document.getElementById('trip-do-location'), {
        fullWidth: true,
        // highlightTyped: true,
        liveServer: true,
        server: '/api/get.autocomplete-locations.php',
        searchFields: ['label', 'short_name'],
        onSelectItem: (data) => {
          $('#trip-do-location')
            .data('id', data.value)
            .data('type', data.type)
            .data('value', data.label)
            .removeClass('is-invalid');
          checkForFlight();
        },
        fixed: true,
      });


      $('#trip-airline-id').on('changed.bs.select', function (e, clickedIndex, isSelected, previousValue) {
        const airlineId = $('#trip-airline-id').val();
        const item = airlines.filter(airline => airline.id == airlineId);
        const airline = item[0];
        if (airline) {
          $('#flight-number-prefix').html(airline.flight_number_prefix);
          $('#airline-image').html(airline.image_filename ? `<img src="/images/airlines/${airline.image_filename}" class="img-fluid"/>` : '');
        }
      });

      new Autocomplete(document.getElementById('trip-requestor'), {
        fullWidth: true,
        // highlightTyped: true,
        liveServer: true,
        server: '/api/get.autocomplete-requestors.php',
        onSelectItem: (data) => {
          $('#trip-requestor')
            .data('id', data.value)
            .data('value', data.label)
            .removeClass('is-invalid');
        },
        fixed: true,
      });


      function checkForFlight() {
        if ($('#trip-pu-location').data('type') === 'airport' || $('#trip-do-location').data('type') === 'airport') {
          $('#flight-info').removeClass('d-none');
          if ($('#trip-pu-location').data('type') === 'airport') {
            $('#eta-section').removeClass('d-none');
          } else {
            $('#eta-section').addClass('d-none');
          }
          if ($('#trip-do-location').data('type') === 'airport') {
            $('#etd-section').removeClass('d-none');
          } else {
            $('#etd-section').addClass('d-none');
          }
        } else {
          $('#flight-info').addClass('d-none');
        }
      }
      checkForFlight();

      $('#btn-save-trip').off('click').on('click', async ƒ => {
        const data = await getData();
        if (data) {
          const resp = await post('/api/post.save-trip.php', data);
          if (resp?.result?.result) {
            $(document).trigger('tripChange', {tripId});
            app.closeOpenTab();
            if (tripId) {
              app.openTab('view-trip', 'Trip (view)', `section.view-trip.php?id=${tripId}`);
              return toastr.success('Trip saved.', 'Success');
            }
            app.openTab('view-trip', 'Trip (view)', `section.view-trip.php?id=${resp?.result?.result}`);
            return toastr.success('Trip added.', 'Success');
          }
          toastr.error(resp.result.errors[2], 'Error');
          console.error(resp);
        }
      });

      $('#btn-delete-trip').off('click').on('click', async ƒ => {
        if (await ask('Are you sure you want to delete this trip?')) {
          const resp = await get('/api/get.delete-trip.php', {
            id: tripId
          });
          if (resp?.result) {
            $(document).trigger('tripChange', {tripId});
            app.closeOpenTab();
            return toastr.success('Trip deleted.', 'Success')
          }
          console.error(resp);
          toastr.error('There seems to be a problem deleting this trip.', 'Error');
        }
      });

      $('input, textarea').on('change', getData);




      async function getData() {
        const data = {};
        let control;

        data.id = tripId;
        data.summary = cleanVal('#trip-summary');
        data.startDate = startDate.format('YYYY-MM-DD HH:mm:ss');
        data.pickupDate = pickupDate.format('YYYY-MM-DD HH:mm:ss');
        data.endDate = endDate.format('YYYY-MM-DD HH:mm:ss');

        control = $('#trip-pu-location');
        if (control.data('value') != control.val()) {
          control.addClass('is-invalid');
          if (await ask(`"${control.val()}" is not a recognized location. Would you like to add a new location?`)) {
            app.openTab('edit-location', 'Location (add)', `section.edit-location.php`);
          }
          return false;
        }
        data.puLocationId = control.data('id');

        control = $('#trip-guest');
        if ($('#trip-guest').data('value') != $('#trip-guest').val()) {
          $('#trip-guest').addClass('is-invalid');
          if (await ask(`"${$('#trip-guest').val()}" is not a recognized guest or group. Would you like to add a new one?`)) {
            app.openTab('edit-guest', 'Guests/Groups (add)', `section.edit-guest.php`);
          }
          return false
        }
        data.guestId = control.data('id');
        data.guests = cleanVal('#trip-guests');
        data.passengers = cleanNumberVal('#trip-passengers');

        control = $('#trip-do-location');
        if (control.data('value') != control.val()) {
          control.addClass('is-invalid');
          if (await ask(`"${control.val()}" is not a recognized location. Would you like to add a new location?`)) {
            app.openTab('edit-location', 'Location (add)', `section.edit-location.php`);
          }
          return false;
        }
        data.doLocationId = control.data('id');

        data.vehicleId = val('#trip-vehicle-id');
        data.driverId = val('#trip-driver-id');
        data.airlineId = val('#trip-airline-id');

        data.flightNumber = cleanUpperVal('#trip-flight-number');
        data.ETA = val('#trip-eta') ? moment(val('#trip-eta'), 'MM/DD/YYYY h:mm A').format('YYYY-MM-DD HH:mm:ss') : null;
        data.ETD = val('#trip-etd') ? moment(val('#trip-etd'), 'MM/DD/YYYY h:mm A').format('YYYY-MM-DD HH:mm:ss') : null;

        data.vehiclePUOptions = $('#trip-vehicle-pu-options').val();
        data.vehicleDOOptions = $('#trip-vehicle-do-options').val();

        control = $('#trip-requestor');
        if (control.data('value') != control.val()) {
          control.addClass('is-invalid');
          if (await ask(`"${control.val()}" is not a recognized user. Would you like to add a new user?`)) {
            app.openTab('edit-user', 'User (add)', `section.edit-user.php`);
          }
          return false;
        }
        data.requestorId = control.data('id');

        data.guestNotes = cleanVal('#trip-guest-notes');
        data.driverNotes = cleanVal('#trip-driver-notes');
        data.generalNotes = cleanVal('#trip-general-notes');
        return data;
      }

    });

    <?php if (!$trip->isEditable()): ?>
      $('.tab-pane.active input').prop('disabled', true);
      $('.tab-pane.active textarea').prop('disabled', true);
      $('.tab-pane.active select').prop('disabled', true);
      $('.tab-pane.active select').selectpicker('destroy')
    <?php endif;?>

  </script>

<?php endif;?>