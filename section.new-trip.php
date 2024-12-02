<div class="container-fluid">
  
  <?php include 'inc.form-contact.php'; ?>

  <h1>New Trip</h1>
  <input type="hidden" id="trip-start-date" value="" />
  <input type="hidden" id="trip-end-date" value="" />

  <section id="trip-head">
    <div class="row">
      <div class="col-4">
        <div class="mb-3">
          <label for="trip-pickup-date" class="form-label">When is the pick up?</label>
          <div
            class="input-group log-event"
            id="datetimepicker-trip-pickup-date"
            data-td-target-input="nearest"
            data-td-target-toggle="nearest">
            <input
              id="trip-pickup-date"
              type="text"
              class="form-control"
              data-td-target="#datetimepicker-trip-pickup-date"
              value="<?=$_REQUEST['dateHint']?>"/>
            <span
              class="input-group-text"
              data-td-target="#datetimepicker-trip-pickup-date"
              data-td-toggle="datetimepicker">
                <i class="fa-duotone fa-calendar"></i>
            </span>
          </div>
        </div>
      </div>

      <div class="col-6">
        <div class="row">
          <div class="col-4">
            <label class="form-label">Lead Time</label>
            <div class="input-group mb-3">
              <input type="text" class="form-control" id="trip-lead-time" value="" placeholder="e.g. 1.5"/>
              <span class="input-group-text">hour(s)</span>
            </div>
          </div>
          <div class="col-4">
            <label class="form-label">Trip Duration?</label>
            <div class="input-group mb-3">
              <input type="text" class="form-control" id="trip-duration-hours" value="" placeholder="e.g. 1.5"/>
              <span class="input-group-text">hour(s)</span>
            </div>
          </div>
          <div class="col-auto pt-4">
            <button id="btn-trip-next" class="btn btn-secondary">Go</button>
            <button id="btn-trip-change" class="btn btn-secondary d-none">Change</button>
          </div>
        </div>
      </div>
    </div>
  </section>

  <section id="trip-body" class="d-none">

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
      <div class="col">
        <div class="mb-3">
          <label for="trip-pu-location" class="form-label">Location</label>
          <input 
            type="text" 
            class="form-control" 
            id="trip-pu-location" 
            placeholder="Pick Up Location"/>
          <div class="invalid-feedback">Please make a valid selection</div>
        </div>
      </div>
    </div>

    <div class="row">
      <div class="col">
        <div class="mb-3">
          <label for="trip-guests" class="form-label">Guest(s) / Group</label>
          <input type="text" class="form-control" id="trip-guests">
        </div>
      </div>
      
      <div class="col-3">
        <div class="mb-3">
          <label for="trip-guest" class="form-label">Contact Person</label>
          <input 
            type="text" 
            class="form-control" 
            id="trip-guest" 
            placeholder="Contact"/>
          <div class="invalid-feedback">Please make a valid selection</div>
        </div>
      </div>

      <div class="col-auto pt-4">
        <button id="btn-new-contact" class="btn btn-outline-primary"><i class="fa-solid fa-user-plus"></i></button>
      </div>

      <div class="col-2">
        <div class="mb-3">
          <label for="trip-summary" class="form-label">Total Passengers</label>
          <input type="number" class="form-control" id="trip-passengers" placeholder="# Passengers" value="1">
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
            placeholder="Drop Off Location" />
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
              <div><select id="trip-vehicle-id" class="form-control"></select></div>
            </div>
          </div>

          <div class="col">
            <div class="mb-3">
              <label for="trip-driver-id" class="form-label">Driver</label>
              <div><select id="trip-driver-id" class="form-control"></select></div>
            </div>
          </div>
        </div>

        <div class="row">
          <div class="col">
            <div class="mb-3">
              <label for="trip-vehicle-pu-options" class="form-label">Pick up options</label>
              <div>
                <select id="trip-vehicle-pu-options" class="form-control">
                  <option></option>
                  <option value="pick up from staging" selected>Pick up from staging</option>
                  <option value="guest will have vehicle">Guest will have vehicle</option>
                  <option value="commence from current location">Commence from current location</option>
                </select>
              </div>
            </div>
          </div>
          <div class="col">
            <div class="mb-3">
              <label for="trip-vehicle-do-options" class="form-label">Drop off options</label>
              <div>
                <select id="trip-vehicle-do-options" class="form-control">
                  <option></option>
                  <option value="return to staging" selected>Return to staging</option>
                  <option value="leave vehicle with guest">Leave vehicle with guest(s)</option>
                  <option value="remain at destination">Remain at destination</option>
                </select>
              </div>
            </div>
          </div>
        </div>


      </div>

      <div class="col">
        <section id="flight-info" class="d-none">
          <div class="row">
            <h4><i class="fa-duotone fa-solid fa-plane-tail"></i> Flight</h4>
          </div>
          <div class="row">
            <div class="col">
              <div class="mb-3">
                <label for="trip-airline-id" class="form-label">Airline</label>
                <div>
                  <select id="trip-airline-id" data-live-search="true" show-tick class="form-control" data-size="5"></select>
                </div>
              </div>
            </div>
            <div class="col">
              <div class="mb-3">
                <label for="trip-flight-number" class="form-label">Flight Number</label>
                <div class="input-group">
                  <span class="input-group-text" id="flight-number-prefix">&nbsp; &nbsp;</span>
                  <input type="text" class="form-control" id="trip-flight-number" placeholder="Flight number without the prefix">
                </div>
              </div>
            </div>
          </div>

          <div class="row">
            <div id="airline-image" class="col-6"></div>
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
                    data-td-target="#datetimepicker2"/>
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
                    data-td-target="#datetimepicker3"/>
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
            placeholder="Requestor">
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
      <div class="col text-end">
        <button id="btn-save-finalize-trip" class="btn btn-primary">Save & Finalize</button>
        <!-- <button id="btn-save-link-trip" class="btn btn-outline-primary">Save & Link to second trip</button> -->
        <button id="btn-save-trip" class="btn btn-outline-primary">Save</button>
      </div>
    </div>

  </section>
</div>

<script type="text/javascript">

  $(async ƒ => {

    let drivers;
    let vehicles;
    const airlines = await get('/api/get.resource-airlines.php');
    const pickupDateControl = new tempusDominus.TempusDominus(document.getElementById('datetimepicker-trip-pickup-date'), tempusConfigDefaults);
    const eta = new tempusDominus.TempusDominus(document.getElementById('datetimepicker2'), tempusConfigDefaults);
    const etd = new tempusDominus.TempusDominus(document.getElementById('datetimepicker3'), tempusConfigDefaults);

    function checkForFlight() {
      if ($('#trip-pu-location').data('type') === 'airport' || $('#trip-do-location').data('type') === 'airport') {
        $('#flight-info').removeClass('d-none');
        if ($('#trip-pu-location').data('type') === 'airport') {
          $('#eta-section').removeClass('d-none');
          if ($('#trip-eta').val() == '') $('#trip-eta').val($('#trip-pickup-date').val());
        } else {
          $('#eta-section').addClass('d-none');
        }
        if ($('#trip-do-location').data('type') === 'airport') {
          $('#etd-section').removeClass('d-none');
          if ($('#trip-etd').val() == '') $('#trip-etd').val($('#trip-pickup-date').val());
        } else {
          $('#etd-section').addClass('d-none');
        }
      } else {
        $('#flight-info').addClass('d-none');
      }
    }
    checkForFlight();

    const contactForm = new ContactClass('#contactModal');

    contactForm.onUpdate = async function (e, formData) {
      const resp = await post('/api/post.save-guest.php', formData);
      if (resp?.result) {
        $(document).trigger('guestChange');
        if (resp?.result?.result) {
          $('#trip-guest')
            .val(formData.firstName + ' ' + formData.lastName)
            .data('id', resp?.result?.result)
            .data('value', formData.firstName + ' ' + formData.lastName)
        }
        console.log(resp);
      }
    }    

    $('#btn-new-contact').off('click').on('click', e => {
      contactForm.show();
    });


    $('#trip-airline-id').append($('<option>'));
    $.each(airlines, function (i, item) {
      $('#trip-airline-id').append($('<option>', {
        value: item.id,
        text: item.name
      }));
    });
    $('select').selectpicker();

    $('#btn-trip-next').off('click').on('click', async () => {
      const pickupDate = moment($('#trip-pickup-date').val(), 'MM/DD/YYYY h:mm A');
      let endDate;
      let startDate;

      if (!pickupDate.isValid()) {
        await alertError('You need a specify a valid date before proceeding.', 'Oops!');
        $('#trip-pickup-date').addClass('is-invalid');
        return false;
      }

      const leadTime = isNaN(parseFloat(cleanNumberVal('#trip-lead-time'))) ? 0 : parseInt(cleanNumberVal('#trip-lead-time') * 60);
      console.log('leadTime:', leadTime);
      startDate = moment(pickupDate).subtract(leadTime, 'm');
      console.log('startDate:', startDate.format());
      
      const duration = Math.abs(cleanNumberVal('#trip-duration-hours'));
      console.log('duration:', duration);
      if (duration <= 0) {
        await alertError('You need a specify a valid trip duration before proceeding.', 'Oops!');
        $('#trip-duration-hours').select().focus(); // FIXME: There seems to be an issue regaring aria-hidden when I do this?
        return false;
      }

      endDate = moment(startDate).add(duration, 'h');
      console.log('endDate:', endDate.format());
      if (moment().isAfter(endDate)) {
        await alertError('This trip has already passed.', 'Oops!');
        return false;
      }

      if (moment().isAfter(pickupDate)) {
        const answer = await ask('This trip is already in progress. Do you wish to continue?');
        if (!answer) return false;
      }

      $('#trip-start-date').val(startDate.format('MM/DD/YYYY h:mm A')); // We'll just keep the format the same as the start-date for simplicity
      $('#trip-end-date').val(endDate.format('MM/DD/YYYY h:mm A')); // We'll just keep the format the same as the start-date for simplicity
      $('#trip-head').find('input').attr('disabled', true).attr('readonly', true);
      $('#btn-trip-next').addClass('d-none');
      $('#btn-trip-change').removeClass('d-none');
      $('#trip-body').removeClass('d-none');

      const saveDriverId = val('#trip-driver-id');
      const saveVehicleId = val('#trip-vehicle-id');

      // Load the resources!
      drivers = await get('/api/get.available-drivers.php', {
        startDate: startDate.format('YYYY-MM-DD HH:mm:00'),
        endDate: endDate.format('YYYY-MM-DD HH:mm:59')
      });
      vehicles = await get('/api/get.available-vehicles.php', {
        startDate: startDate.format('YYYY-MM-DD HH:mm:00'),
        endDate: endDate.format('YYYY-MM-DD HH:mm:59')
      });

      $('#trip-vehicle-id').selectpicker('destroy');
      $('#trip-vehicle-id option').remove();
      $('#trip-vehicle-id').append($('<option>'));
      $.each(vehicles, function (i, item) {
        $('#trip-vehicle-id').append($('<option>', {
          value: item.id,
          text: item.name,
          'data-content': `<i class="bi bi-square-fill" style="color:${item.color}"></i> ${item.name}`,
          disabled: !item.available,
        }));
      });
      $('#trip-vehicle-id').selectpicker();

      $('#trip-driver-id').selectpicker('destroy');
      $('#trip-driver-id option').remove();
      $('#trip-driver-id').append($('<option>'));
      $.each(drivers, function (i, item) {
        $('#trip-driver-id').append($('<option>', {
          value: item.id,
          text: item.driver,
          disabled: !item.available,
        }));
      });
      $('#trip-driver-id').selectpicker();

      // If these values were previously set AND if the resource is still available, it will be re-set.
      $('#trip-driver-id').selectpicker('val', saveDriverId);
      $('#trip-vehicle-id').selectpicker('val', saveVehicleId);

    });

    $('#btn-trip-change').off('click').on('click', async () => {
      if (await ask('Changing the date and/or duration could affect the availability of your resources (vehicles and drivers) and will therefore need to be reset. Are you sure you want to do this?')) {
        $('#trip-head').find('input').attr('disabled', false).attr('readonly', false);
        $('#btn-trip-next').removeClass('d-none');
        $('#btn-trip-change').addClass('d-none');
        $('#trip-body').addClass('d-none');
      }
    });

    $('#trip-pickup-date').on('change', function (e) {
      $(this).removeClass('is-invalid');
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
      }
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
      }
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
      }
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
      }
    });

    $('#btn-save-trip').off('click').on('click', async ƒ => {
      const data = await getData();
      if (data) {
        const resp = await post('/api/post.save-trip.php', data);
        if (resp?.result?.result) {
          $(document).trigger('tripChange');
          toastr.success('Trip added.', 'Success');
          app.closeOpenTab();
          return;
        }
        toastr.error(resp.result.errors[2], 'Error');
        console.log(resp);
      }
    });

    $('#btn-save-finalize-trip').off('click').on('click', async e => {
      const data = await getData();
      if (data) {
        const resp = await post('/api/post.save-trip.php', data);
        if (resp?.result?.result) {
          const id = resp?.result?.result;
          const newResp = await post('/api/post.finalize-trip.php', {id});
          if (newResp?.result) {
            $(document).trigger('tripChange');
            app.closeOpenTab();
            return toastr.success('Trip added.', 'Success');
          }
          return toastr.error('Seems to be a problem finalizing this trip!', 'Error');
        }
        toastr.error(resp.result.errors[2], 'Error');
        console.error(resp);
      }
    });

    async function getData() {
      const data = {};
      let control;

      data.summary = cleanVal('#trip-summary');
      data.startDate = val('#trip-start-date') ? moment(val('#trip-start-date'), 'MM/DD/YYYY h:mm A').format('YYYY-MM-DD HH:mm:ss') : null;
      data.pickupDate = val('#trip-pickup-date') ? moment(val('#trip-pickup-date'), 'MM/DD/YYYY h:mm A').format('YYYY-MM-DD HH:mm:ss') : null;
      data.endDate = val('#trip-end-date') ? moment(val('#trip-end-date'), 'MM/DD/YYYY h:mm A').format('YYYY-MM-DD HH:mm:ss') : null;

      control = $('#trip-pu-location');
      if (control.data('value') != control.val() && control.val() != '') {
        control.addClass('is-invalid');
        if (await ask(`"${control.val()}" is not a recognized location. Would you like to add a new location?`)) {
          app.openTab('edit-location', 'Location (add)', `section.edit-location.php`);
        }
        return false;
      }
      data.puLocationId = control.data('id');

      control = $('#trip-guest');
      if (control.data('value') != control.val() && control.val() != '') {
        control.addClass('is-invalid');
        if (await ask(`"${control.val()}" is not a recognized guest or group. Would you like to add a new one?`)) {
          app.openTab('edit-guest', 'Guests/Groups (add)', `section.edit-guest.php`);
        }
        return false
      }
      data.guestId = control.data('id');
      data.guests = cleanVal('#trip-guests');
      data.passengers = cleanDigitsVal('#trip-passengers');

      control = $('#trip-do-location');
      if (control.data('value') != control.val() && control.val() != '') {
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
      if (control.data('value') != control.val() && control.val() != '') {
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

</script>