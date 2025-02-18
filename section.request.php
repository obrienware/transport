<?php
require_once 'autoload.php';

use Transport\{ Airline, Airport, User };

$user = new User($_SESSION['user']->id);
?>

<style>

  #request strong {
    color: var(--bs-primary);
    font-weight: 800;
  }

</style>

<input type="hidden" id="user-requestor-id" value="<?=$user->getId()?>">
<div class="container">

  <section id="request-type-section" class="section d-none">
    <p class="lead mb-3">
      Great! Let's get started with your request. Please select the request below that most closely matches your needs.
    </p>

    <input type="radio" class="btn-check" name="options-base" id="option1" autocomplete="off" value="airport-dropoff">
    <label class="btn text-start w-100 mb-3" for="option1">
      <div class="fs-5" style="font-weight:900">Airport Drop Off</div>
      <div class="text-black-50" style="font-weight:200">Pick up a person, persons or group and take them to the airport.</div>
    </label>

    <input type="radio" class="btn-check" name="options-base" id="option2" autocomplete="off" value="airport-pickup">
    <label class="btn text-start w-100 mb-3" for="option2">
      <div class="fs-5" style="font-weight:900">Airport Pick Up</div>
      <div class="text-black-50" style="font-weight:200">Pick up a person, persons or group from the airport.</div>
    </label>

    <input type="radio" class="btn-check" name="options-base" id="option3" autocomplete="off" value="point-to-point">
    <label class="btn text-start w-100 mb-3" for="option3">
      <div class="fs-5" style="font-weight:900">Transport Point to Point</div>
      <div class="text-black-50" style="font-weight:200">Transport a person, persons or group from one location to another.</div>
    </label>

    <input type="radio" class="btn-check" name="options-base" id="option4" autocomplete="off" value="vehicle">
    <label class="btn text-start w-100 mb-3" for="option4">
      <div class="fs-5" style="font-weight:900">Vehicle Request</div>
      <div class="text-black-50" style="font-weight:200">Request the use of a ministry vehicle (without a driver).</div>
    </label>

    <input type="radio" class="btn-check" name="options-base" id="option5" autocomplete="off" value="event">
    <label class="btn text-start w-100 mb-3" for="option5">
      <div class="fs-5" style="font-weight:900">Event Request</div>
      <div class="text-black-50" style="font-weight:200">Request the use of ministry vehicle(s) and driver(s) for a period of time.</div>
    </label>

    <div class="text-end">
      <button class="btn btn-outline-secondary btn-lg px-4 me-4" onclick="cancelWizard()">Cancel</button>
      <button class="btn btn-primary btn-lg px-4 btn-next" onclick="processNext('type')" disabled>Next</button>
    </div>

  </section>

  <section id="request-whom-section" class="section d-none">
    <p class="lead mb-3">
      Who are you requesting this service for?
    </p>
    <div class="mb-3">
      <label for="whom-name" class="form-label">Name of person, persons or group</label>
      <input type="text" class="form-control" id="whom-name" placeholder="e.g. Hawaii Missions Group">
    </div>
    <div class="mb-3">
      <label for="whom-pax" class="form-label">Number of people</label>
      <input type="number" class="form-control" id="whom-pax" placeholder="10" value="1">
    </div>
    <div class="mb-3">
      <label for="whom-contact-person" class="form-label" id="whom-contact-person-label">Contact Person</label>
      <input type="text" class="form-control" id="whom-contact-person" placeholder="e.g. Joe Smith">
    </div>
    <div class="mb-3">
      <label for="whom-contact-phone" class="form-label" id="whom-contact-phone-label">Contact Phone Number</label>
      <input type="text" class="form-control" id="whom-contact-phone" placeholder="(719) 123-4567">
    </div>
    <div class="text-end">
      <button class="btn btn-outline-secondary btn-lg px-4 me-4" onclick="cancelWizard()">Cancel</button>
      <button class="btn btn-primary btn-lg px-4 btn-next" onclick="processNext('whom')" disabled>Next</button>
    </div>
  </section>

  <section id="request-airport-section" class="section d-none">
    <p class="lead mb-3">
      Please select the airport and flight details.
    </p>
    <div class="row">
      <div class="col">
        <div class="mb-3">
          <label for="airport" class="form-label">Airport</label>
          <select class="form-select" id="airport">
            <option value="">Please Select</option>
            <?php foreach (Airport::getAll() as $airport): ?>
              <option value="<?=$airport->iata?>" data-lead-time="<?=$airport->lead_time?>" data-travel-time="<?=$airport->travel_time?>"><?=$airport->name?> (<?=$airport->iata?>)</option>
            <?php endforeach; ?>
          </select>
        </div>
      </div>
    </div>

    <div class="row">
      <div class="col">
        <div class="mb-3">
          <label for="flight-airline" class="form-label">Airline</label>
          <select class="form-select" id="flight-airline">
            <option value="">Please Select</option>
            <?php foreach (Airline::getAll() as $airline): ?>
              <option value="<?=$airline->id?>" data-prefix="<?=$airline->flight_number_prefix?>"><?=$airline->name?></option>
            <?php endforeach; ?>
          </select>
        </div>
      </div>
      <div class="col-3">
        <div class="mb-3">
          <label for="flight-number" class="form-label">Flight Number</label>
          <div class="input-group">
            <span class="input-group-text" id="flight-number-prefix"></span>
            <input type="text" class="form-control" id="flight-number" placeholder="1234">
          </div>
        </div>
      </div>
    </div>

    <div class="row">
      <div class="col">
        <div class="mb-3">
          <label for="flight-time" class="form-label" id="flight-time-label">Estimated Time of Departure</label>
          <input type="datetime-local" class="form-control" id="flight-time">
        </div>
      </div>
    </div>

    <div class="text-end">
      <button class="btn btn-outline-secondary btn-lg px-4 me-4" onclick="cancelWizard()">Cancel</button>
      <button class="btn btn-primary btn-lg px-4 btn-next" onclick="processNext('flight')" disabled>Next</button>
    </div>
  </section>

  <section id="request-location-section" class="section d-none">
    <p class="lead mb-3" id="location-description">
      
    </p>
    <div class="mb-3">
      <label for="location" class="form-label">Location (name and address)</label>
      <textarea class="form-control" id="location" rows="4"></textarea>
    </div>

    <div class="text-end">
      <button class="btn btn-outline-secondary btn-lg px-4 me-4" onclick="cancelWizard()">Cancel</button>
      <button class="btn btn-primary btn-lg px-4 btn-next" onclick="processNext('location')" disabled>Next</button>
    </div>
  </section>

  <section id="request-pickup-time-section" class="section d-none">
    <p class="lead mb-3">
      Please confirm the date and time to be picked up
    </p>
    <div class="row">
      <div class="col">
        <div class="mb-3">
          <label for="pickup-date" class="form-label">Pick Up Date</label>
          <input type="date" class="form-control" id="pickup-date">
        </div>
      </div>
      <div class="col">
        <div class="mb-3">
          <label for="pickup-time" class="form-label">Pick Up Time</label>
          <input type="time" class="form-control" id="pickup-time">
        </div>
      </div>
    </div>
    <div class="text-end">
      <button class="btn btn-outline-secondary btn-lg px-4 me-4" onclick="cancelWizard()">Cancel</button>
      <button class="btn btn-primary btn-lg px-4 btn-next" onclick="processNext('pickup')" disabled>Next</button>
    </div>
  </section>

  <section id="request-destination-section" class="section d-none">
    <p class="lead mb-3">
      Please provide the destination location.
    </p>
    <div class="mb-3">
      <label for="destination" class="form-label">Destination (name and address)</label>
      <textarea class="form-control" id="destination" rows="4"></textarea>
    </div>
    <div class="text-end">
      <button class="btn btn-outline-secondary btn-lg px-4 me-4" onclick="cancelWizard()">Cancel</button>
      <button class="btn btn-primary btn-lg px-4 btn-next" onclick="processNext('destination')" disabled>Next</button>
    </div>
  </section>

  <section id="request-from-till-section" class="section d-none">
    <p class="lead mb-3">
      When will you need the vehicle?
    </p>
    <div class="row">
      <div class="col">
        <div class="mb-3">
          <label for="from-datetime" class="form-label">From (when)</label>
          <input type="datetime-local" class="form-control" id="from-datetime">
        </div>
      </div>
      <div class="col">
        <div class="mb-3">
          <label for="till-datetime" class="form-label">Till (when)</label>
          <input type="datetime-local" class="form-control" id="till-datetime">
        </div>
      </div>
    </div>
    <div class="text-end">
      <button class="btn btn-outline-secondary btn-lg px-4 me-4" onclick="cancelWizard()">Cancel</button>
      <button class="btn btn-primary btn-lg px-4 btn-next" onclick="processNext('from-till')" disabled>Next</button>
    </div>
  </section>

  <section id="request-notes-section" class="section d-none">
    <p class="lead mb-3">
      Please provide any additional notes or instructions.
    </p>
    <div class="mb-3">
      <label for="notes" class="form-label">Notes</label>
      <textarea class="form-control" id="notes" rows="4"></textarea>
    </div>
    <div class="text-end">
      <button class="btn btn-outline-secondary btn-lg px-4 me-4" onclick="cancelWizard()">Cancel</button>
      <button class="btn btn-primary btn-lg px-4 btn-next" onclick="processNext('notes')" disabled>Next</button>
    </div>
  </section>

  <section id="request-event-detail-section" class="section d-none">
    <p class="lead mb-3">
      Please provide the details of the event.
    </p>
    <div class="mb-3">
      <label for="event-detail" class="form-label">Event/Activity Detail</label>
      <textarea class="form-control" id="event-detail" rows="4"></textarea>
    </div>
    <div class="text-end">
      <button class="btn btn-outline-secondary btn-lg px-4 me-4" onclick="cancelWizard()">Cancel</button>
      <button class="btn btn-primary btn-lg px-4 btn-next" onclick="processNext('event-detail')" disabled>Next</button>
    </div>
  </section>

</div>


<script>

  let request = {
    requestorId: <?=$user->getId()?>,
  }; // This will hold all the content needed to create the request

  function cancelWizard() 
  {
    $('.section').addClass('d-none');
    $('#calendar-section').removeClass('d-none');
    $('.btn-check').prop('checked', false);
    $('.btn-next').prop('disabled', true);
    $('#whom-name').val('');
    $('#whom-pax').val(1);
    $('#whom-contact-person').val('');
    $('#whom-contact-phone').val('');
    $('#whom-contact-person-label').html('Contact Person');
    $('#whom-contact-phone-label').html('Contact Phone Number');
    $('#airport').val('');
    $('#flight-airline').val('');
    $('#flight-number').val('');
    $('#flight-etd').val('');
    $('#flight-number-prefix').html('');
    $('#flight-time').val('');
    $('#location').val('');
    $('#pickup-date').val('');
    $('#pickup-time').val('');
    $('#destination').val('');
    $('#from-datetime').val('');
    $('#till-datetime').val('');
    $('#notes').val('');
    $('#event-detail').val('');

    request = {
      requestorId: <?=$user->getId()?>,
    }; // Reset the request object
    showNext();
  }

  function showNext()
  {
    $('.section').addClass('d-none');
    if (!request.type) {
      $('#calendar-section').addClass('d-none');
      $('#request-type-section').removeClass('d-none');
    }

    switch (request.type) {
      case 'airport-dropoff':
        if (!request.whom) {
          $('#request-whom-section').removeClass('d-none');
          return;
        }
        if (!request.airport || !request.flight) {
          $('#flight-time-label').html('Estimated Time of <strong>Departure</strong>');
          $('#request-airport-section').removeClass('d-none');
          return;
        }
        if (!request.location) {
          $('#location-description').html('Please provide the location where the person, persons or group will be <strong>picked up</strong> from.');
          $('#request-location-section').removeClass('d-none');
          return;
        }
        if (!request.datetime) {
          // We should calculate the pickup time based on the flight departure time and the lead time
          const datetime = moment(request.flight.flightTime)
            .subtract(request.flight.leadTime, 'minutes')
            .subtract(request.flight.travelTime, 'minutes');
          $('#pickup-date').val(datetime.format('YYYY-MM-DD'));
          $('#pickup-time').val(datetime.format('HH:mm'));
          $('#request-pickup-time-section').removeClass('d-none');
          return;
        }
        if (request.notes == undefined) {
          $('#request-notes-section').removeClass('d-none');
          return;
        }
        // Show a summary of the request and ask for confirmation
        // Process the request
        return submitRequest();
        break;

      case 'airport-pickup':
        if (!request.whom) {
          $('#request-whom-section').removeClass('d-none');
          return;
        }
        if (!request.airport || !request.flight) {
          $('#flight-time-label').html('Estimated Time of <strong>Arrival</strong>');
          $('#request-airport-section').removeClass('d-none');
          return;
        }
        if (!request.location) {
          $('#location-description').html('Please provide the location where the person, persons or group will be <strong>dropped off</strong>.');
          $('#request-location-section').removeClass('d-none');
          return;
        }
        if (request.notes == undefined) {
          $('#request-notes-section').removeClass('d-none');
          return;
        }
        // Show a summary of the request and ask for confirmation
        // Process the request
        return submitRequest();
        break;
      
      case 'point-to-point':
        if (!request.whom) {
          $('#request-whom-section').removeClass('d-none');
          return;
        }
        if (!request.location) {
          $('#location-description').html('Please provide the location where the person, persons or group will be <strong>picked up</strong>.');
          $('#request-location-section').removeClass('d-none');
          return;
        }
        if (!request.datetime) {
          $('#request-pickup-time-section').removeClass('d-none');
          return;
        }
        if (!request.destination) {
          $('#request-destination-section').removeClass('d-none');
          return;
        }
        if (request.notes == undefined) {
          $('#request-notes-section').removeClass('d-none');
          return;
        }
        // Show a summary of the request and ask for confirmation
        // Process the request
        return submitRequest();
        break;

      case 'vehicle':
        if (!request.whom) {
          $('#whom-contact-person-label').html('Driver Name');
          $('#whom-contact-phone-label').html('Driver Phone Number');
          $('#request-whom-section').removeClass('d-none');
          return;
        }
        if (!request.startDate || !request.endDate) {
          $('#request-from-till-section').removeClass('d-none');
          return;
        }
        if (request.notes == undefined) {
          $('#request-notes-section').removeClass('d-none');
          return;
        }
        // Show a summary of the request and ask for confirmation
        // Process the request
        return submitRequest();
        break;

      case 'event':
        if (!request.detail) {
          $('#request-event-detail-section').removeClass('d-none');
          return;
        }
        if (!request.startDate || !request.endDate) {
          $('#request-from-till-section').removeClass('d-none');
          return;
        }
        if (request.notes == undefined) {
          $('#request-notes-section').removeClass('d-none');
          return;
        }
        // Show a summary of the request and ask for confirmation
        // Process the request
        return submitRequest();
        break;
    }
  }

  function processNext(stage)
  {
    if (stage == 'type') {
      const type = $('input[name="options-base"]:checked').val();
      request.type = type;
      console.log('request:', request);
      return showNext();
    }

    if (stage == 'whom') {
      const whom = {
        name: input.cleanProperVal('#whom-name'),
        pax: $('#whom-pax').intVal(),
        contactPerson: input.cleanProperVal('#whom-contact-person'),
        contactPhoneNumber: $('#whom-contact-phone').val(),
      };
      if (!whom.name || !whom.contactPerson || !whom.contactPhoneNumber) {
        return alert('Please fill in all the fields');
      }
      request.whom = whom;
      console.log('request:', request);
      return showNext();
    }

    if (stage == 'flight') {
      const airport = $('#airport').val();
      const airlineId = $('#flight-airline').intVal();
      const airline = $('#flight-airline option:selected').text();
      const flightNumber = $('#flight-number').intVal().toString();
      const flightNumberPrefix = $('#flight-number-prefix').text();
      const flightTime = $('#flight-time').val();
      const leadTime = $('#airport option:selected').data('lead-time');
      const travelTime = $('#airport option:selected').data('travel-time');
      const flight = {
        airlineId,
        airline,
        flightNumber,
        flightNumberPrefix,
        flightTime,
        leadTime,
        travelTime,
      };
      if (!airport || !airlineId || !flightNumber || !flightTime) {
        return alert('Please fill in all the fields');
      }
      request.airport = airport;
      request.flight = flight;
      console.log('request:', request);
      return showNext();
    }

    if (stage == 'location') {
      const location = $('#location').val();
      if (!location) {
        return alert('Please fill in all the fields');
      }
      request.location = location;
      console.log('request:', request);
      return showNext();
    }

    if (stage == 'pickup') {
      const pickupDate = $('#pickup-date').val();
      const pickupTime = $('#pickup-time').val();
      const datetime = `${pickupDate}T${pickupTime}`;
      if (!pickupDate || !pickupTime) {
        return alert('Please fill in all the fields');
      }
      request.datetime = datetime;
      console.log('request:', request);
      return showNext();
    }

    if (stage == 'notes') {
      const notes = $('#notes').val();
      request.notes = notes;
      console.log('request:', request);
      return showNext();
    }

    if (stage == 'destination') {
      const destination = $('#destination').val();
      if (!destination) {
        return alert('Please fill in all the fields');
      }
      request.destination = destination;
      console.log('request:', request);
      return showNext();
    }

    if (stage == 'from-till') {
      const fromDatetime = $('#from-datetime').val();
      const tillDatetime = $('#till-datetime').val();
      if (!fromDatetime || !tillDatetime) {
        return alert('Please fill in all the fields');
      }
      request.startDate = fromDatetime;
      request.endDate = tillDatetime;
      console.log('request:', request);
      return showNext();
    }

    if (stage == 'event-detail') {
      const eventDetail = $('#event-detail').val();
      if (!eventDetail) {
        return alert('Please fill in all the fields');
      }
      request.detail = eventDetail;
      console.log('request:', request);
      return showNext();
    }
  }

  async function submitRequest()
  {
    $('#request').html('<h2>Submitting request <img src="/images/ellipsis.svg" height="40" alt="..."></h2>');
    console.log('submitting request:', request);
    const result = await net.post('/api/post.save-request.php', request);
    if (result?.result) {
      ui.toastr.success('Request submitted successfully', 'Success');
      cancelWizard();
      return $(document).trigger('menuSelect', 'calendar');
    }
    $('#request').html('<h2>Request failed.</h2>');
    ui.toastr.error('There was an error submitting the request', 'Error');
  }

  $(async ƒ => {
    let requestorId = <?=$user->getId()?>;

    $('.btn-check').on('change', e => {
      $('.btn-next').prop('disabled', false);
    });

    $('#flight-airline').on('change', e => {
      const prefix = $('#flight-airline option:selected').data('prefix');
      $('#flight-number-prefix').html(prefix);
    });

    const airport_dropoff_request = {
      type: 'airport-dropoff',
      whom: {
        name: 'Hawaii Missions Group', //this can be the name of a group or people
        pax: 10, // The number of people
        contactPerson: 'Richard', // The name of the contact person
        contactPhoneNumber: '(719) 425-5764', // The phone number of the contact person
      },
      airport: 'DEN', // This can also be a value selected from the database
      flight: {
        airlineId: 0, // The id of the airline in our database
        airline: 'United Airlines', // The name of our airline
        etd: '2024-12-25 05:00:00', // Formatted date/time of flight arrival (or if etd, the flight departure)
        flightNumber: '1234',
        flightNumberPrefix: 'UA'
      },
      location: 'Charis',  // Pick up or drop off location depending on the type?
      notes: 'Additional notes here', // Additional notes
      // Pick up date/time will be calculated based on the airport, flight departure and lead time and recommended to the requestor
      datetime: '2024-12-25 05:00:00', // Pickup date and time
    }

    const airport_pickup_request = {
      type: 'airport-pickup',
      whom: {
        name: 'Hawaii Missions Group', //this can be the name of a group or people
        pax: 10, // The number of people
        contactPerson: 'Richard', // The name of the contact person
        contactPhoneNumber: '(719) 425-5764', // The phone number of the contact person
      },
      airport: 'DEN', // This can also be a value selected from the database
      flight: {
        airlineId: 0, // The id of the airline in our database
        airline: 'United Airlines', // The name of our airline
        eta: '2024-12-25 05:00:00', // Formatted date/time of flight arrival (or if etd, the flight departure)
        flightNumber: '1234',
        flightNumberPrefix: 'UA'
      },
      location: 'Charis',  // Pick up or drop off location depending on the type?
      notes: 'Additional notes here', // Additional notes
      // Trip start date/time will be calculated based on the airport and flight arrival time
    }

    const point_to_point_request = {
      type: 'point-to-point',
      whom: {
        name: 'Hawaii Missions Group', //this can be the name of a group or people
        pax: 10, // The number of people
        contactPerson: 'Richard', // The name of the contact person
        contactPhoneNumber: '(719) 425-5764', // The phone number of the contact person
      },
      datetime: '2024-12-25 05:00:00', // Pickup date and time
      location: 'Charis',  // Pick up location
      destination: 'City Hall',  // Drop off location
      notes: 'Additional notes here', // Additional notes
    }

    const vehicle_request = {
      type: 'vehicle',
      whom: {
        name: 'Hawaii Missions Group', //this can be the name of a group or people
        pax: 10, // The number of people
        contactPerson: 'Richard', // The name of the driver!
        contactPhoneNumber: '(719) 425-5764', // The phone number of the driver!
      },
      startDate: '2024-12-25 05:00:00', // When the vehicle is needed (from)
      endDate: '2024-12-25 07:00:00', // When the vehicle is needed (till)
      notes: 'Additional notes here', // Additional notes
    }

    const event_request = {
      type: 'event',
      detail: 'Details of the event', // The details of the event
      startDate: '2024-12-25 05:00:00', // When the event is starting
      endDate: '2024-12-25 07:00:00', // When the event is ending
      notes: 'Additional notes here', // Additional notes
    }








    // To kick us off:
    showNext();
  });

</script>
