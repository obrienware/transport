<!-- Back button -->
<div class="d-flex justify-content-between top-page-buttons mb-2">
  <button class="btn btn-sm btn-outline-primary px-2 me-auto" onclick="$(document).trigger('loadMainSection', { sectionId: 'trips', url: 'section.list-trips.php', forceReload: true });">
    <i class="fa-solid fa-chevron-left"></i>
    List
  </button>
</div>

<?php include 'inc.form-contact.php'; ?>

<h1>New Trip</h1>
<input type="hidden" id="trip-start-date" value="" />
<input type="hidden" id="trip-end-date" value="" />

<div class="grid-container">

  <!-- Trip Summary Card -->
  <card id="trip-head" class="card text-bg-secondary overflow-hidden" style="grid-column: 1 / -1;">
    <div class="card-header">
      Trip Summary
    </div>
    <div class="card-body text-bg-light container-fluid">
      <div class="row">
        <div class="col-12 col-lg-7 col-xxl-5 d-none">
          <label for="trip-summary" class="form-label">Summary</label>
          <input type="text" class="form-control" id="trip-summary" placeholder="Where What - Who e.g. DEN/COS pick up/drop off - Guest/group name" value="<?= $trip->summary ?>">
        </div>
        <div class="col-12 col-sm-6 col-md-8 col-lg-5 col-xl-4 col-xxl-3">
          <label for="trip-pickup-date" class="form-label">When is the pick up?</label>
          <input type="datetime-local" class="form-control" id="trip-pickup-date" value="<?= $_GET['dateHint'] ?? (new DateTime('+1 hour', new DateTimeZone($_SESSION['userTimezone'] ?? 'UTC')))->format('Y-m-d\TH:i') ?>" />
        </div>
        <div class="w-100 d-none d-sm-block d-md-none"></div>
        <div class="col-sm-6 col-lg-4 col-xl-3 col-xxl-2">
          <label class="form-label">Lead Time</label>
          <div class="input-group">
            <input type="text" class="form-control" id="trip-lead-time" value="" placeholder="e.g. 1.5" />
            <span class="input-group-text">hour(s)</span>
          </div>
        </div>
        <div class="col-sm-6 col-lg-4 col-xl-3 col-xxl-2">
          <label class="form-label">Trip Duration?</label>
          <div class="input-group">
            <input type="text" class="form-control" id="trip-duration-hours" value="" placeholder="e.g. 1.5" />
            <span class="input-group-text">hour(s)</span>
          </div>
        </div>
        <div class="col-auto pt-4">
          <button id="btn-trip-next" class="btn btn-secondary">Go</button>
          <button id="btn-trip-change" class="btn btn-secondary d-none">Change</button>
        </div>
      </div>
    </div>
  </card>


  <section id="trip-body" style="display:contents" class="d-none">

    <!-- Pick Up Card -->
    <card class="card bg-dark-subtle">
      <div class="card-header">
        <i class="fa-solid fa-up"></i>
        Pick Up
      </div>
      <div class="card-body bg-body-secondary container-fluid" style="border-radius: 0 0 var(--bs-border-radius) var(--bs-border-radius);">
        <div class="row">
          <div class="col-12">
            <label for="trip-guests" class="form-label">Guest(s) / Group (Name)</label>
            <input type="text" class="form-control" id="trip-guests" placeholder="Group name / Guest name(s)" />
          </div>
          <div class="col-10 col-lg-9">
            <label for="trip-guest" class="form-label">Contact Person</label>
            <input
              type="text"
              class="form-control"
              id="trip-guest"
              placeholder="Contact" />
            <div class="invalid-feedback">Please make a valid selection</div>
          </div>
          <div class="col-auto pt-4">
            <button id="btn-new-contact" class="btn btn-outline-primary"><i class="fa-solid fa-user-plus"></i></button>
          </div>
          <div class="col-12 col-md-6 col-lg-3">
            <label for="trip-passengers" class="form-label">Passengers</label>
            <input type="number" class="form-control" id="trip-passengers" placeholder="# Passengers" value="1">
          </div>
          <div class="col-12 col-lg-9">
            <label for="trip-pu-location" class="form-label">Location</label>
            <input
              type="text"
              class="form-control"
              id="trip-pu-location"
              placeholder="Pick Up Location" />
            <div class="invalid-feedback">Please make a valid selection</div>
          </div>
        </div>
      </div>
    </card>


    <!-- Drop Off Card -->
    <card class="card bg-dark-subtle">
      <div class="card-header">
        <i class="fa-solid fa-down"></i>
        Drop Off
      </div>
      <div class="card-body bg-body-secondary container-fluid" style="border-radius: 0 0 var(--bs-border-radius) var(--bs-border-radius);">
        <div class="row">
          <div class="col-12">
            <label for="trip-do-location" class="form-label">Location</label>
            <input
              type="text"
              class="form-control"
              id="trip-do-location"
              placeholder="Drop Off Location" />
            <div class="invalid-feedback">Please make a valid selection</div>
          </div>
        </div>
      </div>
    </card>


    <!-- Flight Details Card -->
    <card id="flight-info" class="card bg-dark-subtle d-none">
      <div class="card-header">
        <i class="fa-duotone fa-solid fa-plane-tail"></i>
        Flight Details
      </div>
      <div class="card-body bg-body-secondary container-fluid" style="border-radius: 0 0 var(--bs-border-radius) var(--bs-border-radius);">
        <div class="row">
          <div class="col-12">
            <label for="trip-airline-id" class="form-label">Airline</label>
            <div>
              <select id="trip-airline-id" data-live-search="true" show-tick class="form-control" data-size="5"></select>
            </div>
          </div>
          <div id="airline-image" class="col-6 pt-4"></div>
          <div class="col-6">
            <label for="trip-flight-number" class="form-label">Flight Number</label>
            <div class="input-group">
              <span class="input-group-text" id="flight-number-prefix">&nbsp; &nbsp;</span>
              <input type="text" class="form-control" id="trip-flight-number" placeholder="Flight number without the prefix">
            </div>
          </div>
          <div class="offset-1 col-10 d-none" id="eta-section">
            <div class="input-group mt-3">
              <span class="input-group-text">ETA</span>
              <input type="datetime-local" class="form-control" id="trip-eta" value="" min="<?= date('Y-m-d\TH:i') ?>">
            </div>
          </div>

          <div class="offset-1 col-10 d-none" id="etd-section">
            <div class="input-group mt-3">
              <span class="input-group-text">ETD</span>
              <input type="datetime-local" class="form-control" id="trip-etd" value="" min="<?= date('Y-m-d\TH:i') ?>">
            </div>
          </div>
        </div>
      </div>
    </card>


    <!-- Driver / Vehicle Card -->
    <card class="card bg-dark-subtle">
      <div class="card-header">
        Driver / Vehicle
      </div>
      <div class="card-body bg-body-secondary container-fluid" style="border-radius: 0 0 var(--bs-border-radius) var(--bs-border-radius);">
        <div class="row">
          <div class="col-12">
            <label for="trip-driver-id" class="form-label">Driver</label>
            <div><select id="trip-driver-id" class="form-control"></select></div>
          </div>
          <div class="col-12">
            <label for="trip-vehicle-id" class="form-label">Vehicle</label>
            <div><select id="trip-vehicle-id" class="form-control"></select></div>
          </div>
          <div class="col-12">
            <label for="trip-vehicle-pu-options" class="form-label">Where to pick the vehicle up from</label>
            <div>
              <select id="trip-vehicle-pu-options" class="form-control">
                <option></option>
                <option value="pick up from staging" selected>Pick up from staging</option>
                <option value="guest will have vehicle">Guest will have vehicle</option>
                <option value="commence from current location">Commence from current location</option>
              </select>
            </div>
          </div>
          <div class="col-12">
            <label for="trip-vehicle-do-options" class="form-label">Where to leave vehicle</label>
            <div>
              <select id="trip-vehicle-do-options" class="form-control">
                <option></option>
                <option value="return to staging" selected>Return to staging</option>
                <option value="leave vehicle with guest">Leave vehicle with guest(s)</option>
                <option value="remain at destination">Remain at destination</option>
              </select>
            </div>
          </div>
          <div class="col-12">
            <label for="trip-driver-notes" class="form-label">Notes for the driver</label>
            <textarea class="form-control font-handwriting" id="trip-driver-notes" rows="5" style="border: 1px solid khaki;background: #ffffbb;font-size:large"></textarea>
          </div>
        </div>
      </div>
    </card>


    <!-- Requestor Card -->
    <card class="card bg-dark-subtle">
      <div class="card-header">
        Requestor
      </div>
      <div class="card-body bg-body-secondary container-fluid" style="border-radius: 0 0 var(--bs-border-radius) var(--bs-border-radius);">
        <div class="row">
          <div class="col-12">
            <label for="trip-requestor" class="form-label">Requestor</label>
            <input
              type="text"
              class="form-control"
              id="trip-requestor"
              placeholder="Requestor">
            <div class="invalid-feedback">Please make a valid selection</div>
          </div>
          <div class="col-12">
            <label for="trip-guest-notes" class="form-label">Notes for the guest</label>
            <textarea class="form-control font-handwriting" id="trip-guest-notes" rows="5" style="border: 1px solid khaki;background: #ffffbb;font-size:large"></textarea>
          </div>
        </div>
      </div>
    </card>


    <!-- General Card -->
    <card class="card bg-dark-subtle">
      <div class="card-header">
        General
      </div>
      <div class="card-body bg-body-secondary container-fluid" style="border-radius: 0 0 var(--bs-border-radius) var(--bs-border-radius);">
        <div class="row">

          <div class="col-12 col-lg-4 mb-3">
            <div class="pretty p-svg p-curve">
              <input class="~form-check-input" type="checkbox" value="1" id="trip-require-more-info">
              <div class="state p-danger">
                <!-- svg path -->
                <svg class="svg svg-icon" viewBox="0 0 20 20">
                  <path d="M7.629,14.566c0.125,0.125,0.291,0.188,0.456,0.188c0.164,0,0.329-0.062,0.456-0.188l8.219-8.221c0.252-0.252,0.252-0.659,0-0.911c-0.252-0.252-0.659-0.252-0.911,0l-7.764,7.763L4.152,9.267c-0.252-0.251-0.66-0.251-0.911,0c-0.252,0.252-0.252,0.66,0,0.911L7.629,14.566z" style="stroke: white;fill:white;"></path>
                </svg>
                <label>Require More Information</label>
              </div>
            </div>
          </div>

          <div class="col-12">
            <label for="trip-general-notes" class="form-label">General Notes</label>
            <textarea class="form-control font-handwriting" id="trip-general-notes" rows="5" style="border: 1px solid khaki;background: #ffffbb;font-size:large"></textarea>
          </div>
        </div>
      </div>
    </card>

  </section>


</div><!-- .grid-container -->

<div id="trip-buttons" class="d-flex justify-content-between mt-3 d-none">
  <button id="btn-save-confirm-trip" class="btn btn-primary ms-auto me-2">Save & Confirm</button>
  <button id="btn-save-trip" class="btn btn-outline-primary">Save</button>
</div>




<script>
  $(async ƒ => {

    function backToList() {
      $(document).trigger('loadMainSection', {
        sectionId: 'trips',
        url: 'section.list-trips.php',
        forceReload: true
      });
    }

    const wait = ms => new Promise(resolve => setTimeout(resolve, ms));

    const airlines = await net.get('/api/get.resource-airlines.php');

    function checkForFlight() {
      if ($('#trip-pu-location').data('type') === 'airport' || $('#trip-do-location').data('type') === 'airport') {
        $('#flight-info').removeClass('d-none');
        if ($('#trip-pu-location').data('type') === 'airport') {
          $('#eta-section').removeClass('d-none');
          if ($('#trip-eta').val() == '') {
            $('#trip-eta').val($('#trip-pickup-date').val());
          }
        } else {
          $('#eta-section').addClass('d-none');
        }
        if ($('#trip-do-location').data('type') === 'airport') {
          $('#etd-section').removeClass('d-none');
          if ($('#trip-etd').val() == '') {
            $('#trip-etd').val($('#trip-pickup-date').val());
          }
        } else {
          $('#etd-section').addClass('d-none');
        }
      } else {
        $('#flight-info').addClass('d-none');
      }
    }
    checkForFlight();

    const contactForm = new ContactClass('#contactModal');

    contactForm.onUpdate = async function(e, formData) {
      const resp = await net.post('/api/post.save-guest.php', formData);
      if (resp?.result) {
        $(document).trigger('guestChange');
        if (resp?.result) {
          $('#trip-guest')
            .val(formData.firstName + ' ' + formData.lastName)
            .data('id', resp?.result)
            .data('value', formData.firstName + ' ' + formData.lastName)
        }
        console.log(resp);
      }
    }

    $('#btn-new-contact').off('click').on('click', e => {
      contactForm.show();
    });

    $('#trips input').off('keyup').on('keyup', function(e) {
      $(this).removeClass('is-invalid');
    });


    $('#trip-airline-id').append($('<option>'));
    $.each(airlines, function(i, item) {
      $('#trip-airline-id').append($('<option>', {
        value: item.id,
        text: item.name
      }));
    });
    $('select').selectpicker({
      container: false
    });

    $('#btn-trip-next').off('click').on('click', async () => {
      const leadTime = isNaN($('#trip-lead-time').floatVal()) ? 0 : parseInt($('#trip-lead-time').floatVal() * 60);
      const pickupDate = moment($('#trip-pickup-date').val());

      // if ($('#trip-summary').val() == '') {
      //   await ui.alertError('You need a summary before proceeding.', 'Oops!');
      //   $('#trip-summary').addClass('is-invalid');
      //   return false;
      // }

      if (!pickupDate.isValid()) {
        await ui.alertError('You need a specify a valid date before proceeding.', 'Oops!');
        $('#trip-pickup-date').addClass('is-invalid');
        return false;
      }

      const startDate = moment(pickupDate).subtract(leadTime, 'm');

      const duration = Math.abs($('#trip-duration-hours').floatVal());
      console.log('duration:', duration);
      if (duration <= 0) {
        await ui.alertError('You need a specify a valid trip duration before proceeding.', 'Oops!');
        await wait(300);
        $('#trip-duration-hours').select().focus();
        return false;
      }

      const endDate = moment(startDate).add(duration, 'h');
      console.log('endDate:', endDate.format());
      if (moment().isAfter(endDate)) {
        await ui.alertError('This trip has already passed.', 'Oops!');
        return false;
      }

      if (moment().isAfter(pickupDate)) {
        const answer = await ui.ask('This trip is already in progress. Do you wish to continue?');
        if (!answer) return false;
      }

      $('#trip-start-date').val(startDate.format('YYYY-MM-DD HH:mm:ss')); // We'll just keep the format the same as the start-date for simplicity
      $('#trip-end-date').val(endDate.format('YYYY-MM-DD HH:mm:ss')); // We'll just keep the format the same as the start-date for simplicity
      $('#trip-head').find('input').attr('disabled', true).attr('readonly', true);
      $('#btn-trip-next').addClass('d-none');
      $('#btn-trip-change').removeClass('d-none');
      $('#trip-body,#trip-buttons').removeClass('d-none');

      const saveDriverId = $('#trip-driver-id').val();
      const saveVehicleId = $('#trip-vehicle-id').val();

      // Load the resources!
      const drivers = await net.get('/api/get.available-drivers.php', {
        startDate: startDate.format('YYYY-MM-DD HH:mm:00'),
        endDate: endDate.format('YYYY-MM-DD HH:mm:59')
      });
      const vehicles = await net.get('/api/get.available-vehicles.php', {
        startDate: startDate.format('YYYY-MM-DD HH:mm:00'),
        endDate: endDate.format('YYYY-MM-DD HH:mm:59')
      });

      $('#trip-vehicle-id').selectpicker('destroy');
      $('#trip-vehicle-id option').remove();
      $('#trip-vehicle-id').append($('<option>'));
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
        $('#trip-vehicle-id').append($('<option>', optionProps));
      });
      $('#trip-vehicle-id').selectpicker();

      $('#trip-driver-id').selectpicker('destroy');
      $('#trip-driver-id option').remove();
      $('#trip-driver-id').append($('<option>'));
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
        $('#trip-driver-id').append($('<option>', optionProps));
      });
      $('#trip-driver-id').selectpicker();

      // If these values were previously set AND if the resource is still available, it will be re-set.
      $('#trip-driver-id').selectpicker('val', saveDriverId);
      $('#trip-vehicle-id').selectpicker('val', saveVehicleId);

      $('#trip-start-date').val(startDate.format('YYYY-MM-DD HH:mm:ss'));
      $('#trip-end-date').val(endDate.format('YYYY-MM-DD HH:mm:ss'));

    });


    $('#btn-trip-change').off('click').on('click', async () => {
      if (await ui.ask('Changing the date and/or duration could affect the availability of your resources (vehicles and drivers) and will therefore need to be reset. Are you sure you want to do this?')) {
        $('#trip-head').find('input').attr('disabled', false).attr('readonly', false);
        $('#btn-trip-next').removeClass('d-none');
        $('#btn-trip-change').addClass('d-none');
        $('#trip-body,#trip-buttons').addClass('d-none');
      }
    });

    $('#trip-pickup-date').on('change', function(e) {
      $(this).removeClass('is-invalid');
    });

    buildAutoComplete({
      selector: 'trip-pu-location',
      apiUrl: '/api/get.autocomplete-locations.php',
      searchFields: ['label', 'short_name'],
    });

    buildAutoComplete({
      selector: 'trip-guest',
      apiUrl: '/api/get.autocomplete-guests.php',
    });

    buildAutoComplete({
      selector: 'trip-do-location',
      apiUrl: '/api/get.autocomplete-locations.php',
      searchFields: ['label', 'short_name'],
    });

    $('#trip-airline-id').on('changed.bs.select', function(e, clickedIndex, isSelected, previousValue) {
      const airlineId = $('#trip-airline-id').val();
      const item = airlines.filter(airline => airline.id == airlineId);
      const airline = item[0];
      if (airline) {
        $('#flight-number-prefix').html(airline.flight_number_prefix);
        $('#airline-image').html(airline.image_filename ? `<img src="/images/airlines/${airline.image_filename}" class="img-fluid"/>` : '');
      }
    });

    buildAutoComplete({
      selector: 'trip-requestor',
      apiUrl: '/api/get.autocomplete-requestors.php',
    });

    $('#btn-save-trip').off('click').on('click', async ƒ => {
      const data = await getData();
      if (data.vehicleDOOptions === 'leave vehicle with guest' && data.guestId) {
        if (await ui.ask('Do you want to create a linked vehicle reservation for this guest?')) {
          data.createVehicleReservation = true;
        }
      }
      if (data) {
        const resp = await net.post('/api/post.save-trip.php', data);
        if (resp?.result) {
          $(document).trigger('tripChange');
          ui.toastr.success('Trip added.', 'Success');
          return backToList();
        }
        ui.toastr.error(resp.result.errors[2], 'Error');
        console.log(resp);
      }
    });

    $('#btn-save-confirm-trip').off('click').on('click', async e => {
      const saveButtonText = $('#btn-save-confirm-trip').text();
      $('#btn-save-confirm-trip').prop('disabled', true).text('Saving...');
      const data = await getData();
      if (data.vehicleDOOptions === 'leave vehicle with guest' && data.guestId) {
        if (await ui.ask('Do you want to create a linked vehicle reservation for this guest?')) {
          data.createVehicleReservation = true;
        }
      }
      const resp = await net.post('/api/post.save-trip.php', data);
      if (resp?.result) {
        const id = resp?.result;
        const newResp = await net.post('/api/post.confirm-trip.php', {
          id
        });
        if (newResp?.result) {
          $(document).trigger('tripChange');
          ui.toastr.success('Trip added.', 'Success');
          return backToList();
        }
        $('#btn-save-confirm-trip').prop('disabled', false).text(saveButtonText);
        return ui.toastr.error('Seems to be a problem finalizing this trip!', 'Error');
      }
      ui.toastr.error(resp.result.errors[2], 'Error');
      console.error(resp);
      $('#btn-save-confirm-trip').prop('disabled', false).text(saveButtonText);
    });

    async function getData() {
      const data = {};
      let control;
      let controlDataValue;

      data.summary = input.cleanVal('#trip-summary');
      data.startDate = $('#trip-start-date').val() || null;
      data.pickupDate = $('#trip-pickup-date').val() || null;
      data.endDate = $('#trip-end-date').val() || null;

      control = $('#trip-pu-location');
      controlDataValue = $(`<tag>${control.data('value')}</tag>`).text(); // We need this so that values like "Richard O&#039;Brien" can be seen as "Richard O'Brien"
      if (controlDataValue != control.val() && control.val() != '') {
        control.addClass('is-invalid');
        if (await ui.ask(`"${control.val()}" is not a recognized location. Would you like to add a new location?`)) {
          $(document).trigger('loadMainSection', {
            sectionId: 'locations',
            url: 'section.edit-locations.php',
            forceReload: true
          });
        }
        return false;
      }
      data.puLocationId = control.data('id');

      control = $('#trip-guest');
      controlDataValue = $(`<tag>${control.data('value')}</tag>`).text(); // We need this so that values like "Richard O&#039;Brien" can be seen as "Richard O'Brien"
      if (controlDataValue != control.val() && control.val() != '') {
        if (debug) {
          console.log('control:', control);
          console.log('control.data(\'value\'):', control.data('value'));
          console.log('controlDataValue:', controlDataValue);
          console.log('control.val():', control.val());
        }
        control.addClass('is-invalid');
        if (await ui.ask(`"${control.val()}" is not a recognized contact. Would you like to add a new one?`)) {
          $(document).trigger('loadMainSection', {
            sectionId: 'guests',
            url: 'section.edit-guests.php',
            forceReload: true
          });
        }
        return false
      }
      data.guestId = control.data('id');
      data.guests = input.cleanVal('#trip-guests');
      data.passengers = input.cleanDigitsVal('#trip-passengers');

      control = $('#trip-do-location');
      if (control.data('value') != control.val() && control.val() != '') {
        control.addClass('is-invalid');
        if (await ui.ask(`"${control.val()}" is not a recognized location. Would you like to add a new location?`)) {
          $(document).trigger('loadMainSection', {
            sectionId: 'locations',
            url: 'section.edit-locations.php',
            forceReload: true
          });
        }
        return false;
      }
      data.doLocationId = control.data('id');

      data.vehicleId = $('#trip-vehicle-id').val();
      data.driverId = $('#trip-driver-id').val();
      data.airlineId = $('#trip-airline-id').val();
      data.flightNumber = input.cleanUpperVal('#trip-flight-number');

      // We cannot have an ETA AND an ETD. This has previously precipitated errors
      if ($('#trip-pu-location').data('type') === 'airport') {
        data.ETA = $('#trip-eta').val() ? moment($('#trip-eta').val()).format('YYYY-MM-DD HH:mm:ss') : null;
        data.ETD = null;
      } else {
        data.ETD = $('#trip-etd').val() ? moment($('#trip-etd').val()).format('YYYY-MM-DD HH:mm:ss') : null;
        data.ETA = null;
      }

      data.vehiclePUOptions = $('#trip-vehicle-pu-options').val();
      data.vehicleDOOptions = $('#trip-vehicle-do-options').val();

      control = $('#trip-requestor');
      if (control.data('value') != control.val() && control.val() != '') {
        control.addClass('is-invalid');
        if (await ui.ask(`"${control.val()}" is not a recognized user. Would you like to add a new user?`)) {
          $(document).trigger('loadMainSection', {
            sectionId: 'users',
            url: 'section.edit-users.php',
            forceReload: true
          });
        }
        return false;
      }
      data.requestorId = control.data('id');

      data.guestNotes = $('#trip-guest-notes').cleanVal();
      data.driverNotes = $('#trip-driver-notes').cleanVal();
      data.generalNotes = $('#trip-general-notes').cleanVal();

      data.requireMoreInfo = $('#trip-require-more-info').isChecked();

      return data;
    }

    $('#trip-pu-location, #trip-do-location').on('change', function(e) {
      checkForFlight();
    });

  });
</script>