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
          <input type="datetime-local" class="form-control" id="trip-pickup-date" value="<?=$_GET['dateHint']?>" min="<?=date('Y-m-d\TH:i')?>" />
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
                <input type="datetime-local" class="form-control" id="trip-eta" value="" min="<?=date('Y-m-d\TH:i')?>">
              </div>
            </div>

            <div class="col-6 d-none" id="etd-section">
              <div class="mb-3">
                <label for="trip-etd" class="form-label">ETD</label>
                <input type="datetime-local" class="form-control" id="trip-etd" value="" min="<?=date('Y-m-d\TH:i')?>">
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
        <button id="btn-save-confirm-trip" class="btn btn-primary">Save & Confirm</button>
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

    function checkForFlight() {
      if ($('#trip-pu-location').data('type') === 'airport' || $('#trip-do-location').data('type') === 'airport') {
        $('#flight-info').removeClass('d-none');
        if ($('#trip-pu-location').data('type') === 'airport') {
          $('#eta-section').removeClass('d-none');
          if ($('#trip-eta').val() == '') {
            $('#trip-eta').val($('#trip-pickup-date').val());
            // const jsDate = moment($('#trip-pickup-date').val()).toDate();
            // const parsedDate = eta.dates.parseInput(jsDate);
            // eta.dates.setValue(parsedDate, eta.dates.lastPickedIndex);
          }
        } else {
          $('#eta-section').addClass('d-none');
        }
        if ($('#trip-do-location').data('type') === 'airport') {
          $('#etd-section').removeClass('d-none');
          if ($('#trip-etd').val() == '') {
            $('#trip-etd').val($('#trip-pickup-date').val());
            // const jsDate = moment($('#trip-pickup-date').val()).toDate();
            // const parsedDate = etd.dates.parseInput(jsDate);
            // etd.dates.setValue(parsedDate, etd.dates.lastPickedIndex);
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

    contactForm.onUpdate = async function (e, formData) {
      const resp = await post('/api/post.save-guest.php', formData);
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


    $('#trip-airline-id').append($('<option>'));
    $.each(airlines, function (i, item) {
      $('#trip-airline-id').append($('<option>', {
        value: item.id,
        text: item.name
      }));
    });
    $('select').selectpicker();

    $('#btn-trip-next').off('click').on('click', async () => {
      const pickupDate = moment($('#trip-pickup-date').val());
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
        await wait(300);
        $('#trip-duration-hours').select().focus();
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

      $('#trip-start-date').val(startDate.format('YYYY-MM-DD HH:mm:ss')); // We'll just keep the format the same as the start-date for simplicity
      $('#trip-end-date').val(endDate.format('YYYY-MM-DD HH:mm:ss')); // We'll just keep the format the same as the start-date for simplicity
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

    $('#btn-save-trip').off('click').on('click', async ƒ => {
      const data = await getData();
      if (data) {
        const resp = await post('/api/post.save-trip.php', data);
        if (resp?.result) {
          $(document).trigger('tripChange');
          toastr.success('Trip added.', 'Success');
          app.closeOpenTab();
          app.openTab('view-trip', 'Trip (view)', `section.view-trip.php?id=${resp?.result}`);
          return;
        }
        toastr.error(resp.result.errors[2], 'Error');
        console.log(resp);
      }
    });

    $('#btn-save-confirm-trip').off('click').on('click', async e => {
      const saveButtonText = $('#btn-save-confirm-trip').text();
      $('#btn-save-confirm-trip').prop('disabled', true).text('Saving...');
      const data = await getData();
      if (data) {
        const resp = await post('/api/post.save-trip.php', data);
        if (resp?.result) {
          const id = resp?.result;
          const newResp = await post('/api/post.confirm-trip.php', {id});
          if (newResp?.result) {
            $(document).trigger('tripChange');
            app.closeOpenTab();
            app.openTab('view-trip', 'Trip (view)', `section.view-trip.php?id=${id}`);
            return toastr.success('Trip added.', 'Success');
          }
          $('#btn-save-confirm-trip').prop('disabled', false).text(saveButtonText);
          return toastr.error('Seems to be a problem finalizing this trip!', 'Error');
        }
        toastr.error(resp.result.errors[2], 'Error');
        console.error(resp);
        $('#btn-save-confirm-trip').prop('disabled', false).text(saveButtonText);
      }
    });

    async function getData() {
      const data = {};
      let control;

      data.summary = cleanVal('#trip-summary');
      data.startDate = val('#trip-start-date') || null;
      data.pickupDate = val('#trip-pickup-date') || null;
      data.endDate = val('#trip-end-date') || null;

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

      // We cannot have an ETA AND an ETD. This has previously precipitated errors
      if ($('#trip-pu-location').data('type') === 'airport') {
        data.ETA = val('#trip-eta') ? moment(val('#trip-eta')).format('YYYY-MM-DD HH:mm:ss') : null;
        data.ETD = null;
      } else {
        data.ETD = val('#trip-etd') ? moment(val('#trip-etd')).format('YYYY-MM-DD HH:mm:ss') : null;
        data.ETA = null;
      }

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