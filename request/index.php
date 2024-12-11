<?php
require_once 'class.airport.php';
$airports = Airport::getAirports();
$airportOptions = '<option>Make your selection</option>';
foreach ($airports as $airport) {
  $airportOptions .= '<option value="'.$airport->iata.'">'.$airport->name.'</option>';
}

require_once 'class.airline.php';
$airlines = Airline::getAirlines();
$airlineOptions = '<option>Make your selection</option>';
foreach ($airlines as $airline) {
  $airlineOptions .= '<option value="'.$airline->id.'" data-prefix="'.$airline->flight_number_prefix.'">'.$airline->name.'</option>';
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="utf-8">

	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	<meta name="description" content="">
	<meta name="author" content="Richard O'Brien">
	<link rel="icon" type="image/png" href="/Icon.png" />

	<title>Transportation Request</title>

	<!-- Fontawesome - necessary for icons -->
	<script src="https://kit.fontawesome.com/cc9f38bd60.js" crossorigin="anonymous"></script>

	<!-- Necessary Javascript that should come before anything else -->
	<!-- Still rely heavily on jQuery. It serves our purpose quite well -->
	<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js" integrity="sha512-v2CJ7UaYy4JwqLDIrZUI/4hqeoQieOmAZNXBeQyjo21dadnwR+8ZaIJVT8EE2iyI61OV8e6M8PP2/4hpQINQ/g==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
	<!-- And of course our Bootstrap javascript -->
	<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.3/js/bootstrap.bundle.min.js" integrity="sha512-7Pi/otdlbbCR+LnW+F7PwFcSDJOuUJB3OxtEHbg4vSMvzvJjde4Po1v4BR9Gdc9aXNUNFVUY+SK51wWT8WF0Gg==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
	<!-- Popperjs -->
	<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js" integrity="sha256-BRqBN7dYgABqtY9Hd4ynE+1slnEw+roEPFzQ7TRRfcg=" crossorigin="anonymous"></script>

	<!-- Date / Time picker -->
	<!-- Tempus Dominus JavaScript -->
	<script src="https://cdn.jsdelivr.net/npm/@eonasdan/tempus-dominus@6.9.4/dist/js/tempus-dominus.min.js" crossorigin="anonymous"></script>
	<script src="/js/tempus-dominus-defaults.js"></script>
	<!-- Tempus Dominus Styles -->
	<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@eonasdan/tempus-dominus@6.9.4/dist/css/tempus-dominus.min.css" crossorigin="anonymous">

	<!-- Moment -->
	<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.30.1/moment-with-locales.min.js" integrity="sha512-4F1cxYdMiAW98oomSLaygEwmCnIP38pb4Kx70yQYqRwLVCs3DbRumfBq82T08g/4LJ/smbFGFpmeFlQgoDccgg==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/moment-timezone/0.5.46/moment-timezone-with-data.min.js" integrity="sha512-4MAP/CJtK3ASCmbYjYxWAbHWASAx1UYMc1i83cBdQZXegqFfqSZ9WqpmkRGfvzeAI18yvKiDTlgX/TLNMpxkSQ==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
	<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/timeago.js/4.0.2/timeago.full.min.js"></script>

	<script src="https://cdnjs.cloudflare.com/ajax/libs/lodash.js/4.17.21/lodash.min.js" integrity="sha512-WFN04846sdKMIP5LKNphMaWzU7YpMyCU245etK3g/2ARYbPK9Ub18eG+ljU96qKRCWh+quCY7yefSmlkQw1ANQ==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>

	<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

	<!-- All our customized javascript -->
	<script type="text/javascript" src="/js/common.js?<?=filemtime('/js/common.js')?>"></script>

	<!-- Stylesheets -->
	<!-- Our main (custom) Bootstrap theme -->
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.3/css/bootstrap.min.css" integrity="sha512-jnSuA4Ss2PkkikSOLtYs8BlYIeeIK1h99ty4YfvRPAlzr377vr3CXDb7sb7eEEBYjDtcYj+AjBH3FLv5uSJuXg==" crossorigin="anonymous" referrerpolicy="no-referrer" />

	<!-- And of course our own styling -->
	<link rel="stylesheet" type="text/css" href="/css/style.css?<?=filemtime('/css/style.css')?>">

</head>

<body>
  <div class="container-login100">
    <div class="row w-100">
      <div class="bg-white rounded d-flex flex-column shadow-lg p-4 mx-auto w-75">
        
        <section id="header-title" class="text-center">
          <h1 class="my-4 fw-bold text-primary-emphasis">
            <img src="/images/logo.svg" style="height:2em"/>
            Transport Request Form
          </h1>
        </section>

        <section id="section-start" class="steps d-none">
          <h2 class="mb-5 fw-light">Hello! Let's start by getting your email address.</h2>
          <input type="hidden" autocomplete="false">
          <div class="form-floating mb-3">
            <input type="email" class="form-control form-control-lg" id="requestor-email">
            <label for="requestor-email">Email address</label>
          </div>
      
          <div class="mt-4 d-flex">
            <button id="btn-step-start-next" class="px-5 btn btn-primary btn-lg ms-auto">Next</button>
          </div>
        </section>

        <section id="section-get-acquainted-form" class="steps d-none">
          <h2 class="mb-5 fw-light">Let's get to know you a little better shall we?</h2>
          <div class="row">
            <div class="col">
              <div class="mb-3">
                <label for="requestor-first-name" class="form-label">First Name</label>
                <input type="text" class="form-control form-control-lg" id="requestor-first-name" placeholder="Your first name">
              </div>
            </div>
            <div class="col">
              <div class="mb-3">
                <label for="requestor-last-name" class="form-label">Last Name</label>
                <input type="text" class="form-control form-control-lg" id="requestor-last-name" placeholder="Your last name">
              </div>
            </div>
          </div>

          <div class="row">
            <div class="col">
              <div class="mb-3">
                <label for="requestor-phone-number" class="form-label">Phone number</label>
                <input type="tel" class="form-control form-control-lg" id="requestor-phone-number" placeholder="Your phone number">
              </div>
            </div>
            <div class="col">
              <div class="mb-3">
                <label for="requestor-position" class="form-label">Position</label>
                <input type="text" class="form-control form-control-lg" id="requestor-position" placeholder="Your position in your department">
              </div>
            </div>
          </div>

          <div class="mt-4 d-flex">
            <button id="btn-get-acquainted-next" class="px-5 btn btn-primary btn-lg ms-auto">Next</button>
          </div>
        </section>

        <section id="section-select-route" class="steps d-none">
          <h2 class="mb-5 fw-light">Okay <span class="requestor-name"></span>, please select the option below that best describes your request:</h2>
          <div class="row">
            <div class="col">
              <select class="form-select form-select-lg mb-3" id="route-option">
                <option>Make your selection</option>
                <option value="1">Pick up a person/persons/group from the airport</option>
                <option value="2">Drop a person/persons/group off at the airport</option>
                <option value="3">Take a person/persons from one place to another</option>
                <option value="4">Reserve a vehicle</option>
              </select>
            </div>
          </div>

          <div class="mt-4 d-flex">
            <button id="btn-select-route-next" class="px-5 btn btn-primary btn-lg ms-auto">Next</button>
          </div>
        </section>

        <section id="section-get-whom" class="steps d-none">
          <h2 class="mb-5 fw-light">Who is the person / persons / group?</h2>
          <div class="row">
            <div class="col">
              <div class="mb-3">
                <label for="whom-name" class="form-label">Name</label>
                <input type="text" class="form-control form-control-lg" id="whom-name" placeholder="Description of person/persons/group e.g. Bob and Mary Jones, or Hawaii missions group">
              </div>
            </div>
            <div class="col-4">
              <div class="mb-3">
                <label for="whom-pax" class="form-label">Number of passengers</label>
                <input type="number" class="form-control form-control-lg" id="whom-pax" placeholder="" value="1">
              </div>
            </div>
          </div>

          <div class="row">
            <div class="col">
              <div class="mb-3">
                <label for="whom-contact-person" class="form-label">Contact Person</label>
                <input type="text" class="form-control form-control-lg" id="whom-contact-person" placeholder="Name of contact person">
              </div>
            </div>
            <div class="col">
              <div class="mb-3">
                <label for="whom-contact-phone" class="form-label">Contact Phone Number</label>
                <input type="tel" class="form-control form-control-lg" id="whom-contact-phone" placeholder="Phone number">
              </div>
            </div>
          </div>

          <div class="mt-4 d-flex">
            <button id="btn-get-whom-next" class="px-5 btn btn-primary btn-lg ms-auto">Next</button>
          </div>
        </section>

        <section id="section-select-airport" class="steps d-none">
          <h2 class="mb-5 fw-light" id="airport-question">Which Airport?</h2>
          <div class="row">
            <div class="col">
              <select class="form-select form-select-lg mb-3" id="airport-option">
                <?=$airportOptions?>
              </select>
            </div>
          </div>

          <div class="mt-4 d-flex">
            <button id="btn-select-airport-next" class="px-5 btn btn-primary btn-lg ms-auto">Next</button>
          </div>
        </section>

        <section id="section-flight-info" class="steps d-none">
          <h2 class="mb-5 fw-light">Flight Information</h2>
          <div class="row">
            <div class="col">
              <label for="airline-option" class="form-label">Airline</label>
              <select class="form-select form-select-lg mb-3" id="airline-option">
                <?=$airlineOptions?>
              </select>
            </div>

            <div class="col-4">
              <div class="mb-3">
                <label for="flight-number" class="form-label">Flight Number</label>
                <div class="input-group">
                  <span class="input-group-text" id="flight-number-prefix"></span>
                  <input type="text" class="form-control form-control-lg" id="flight-number" placeholder="Flight number without the prefix">
                </div>
              </div>
            </div>
          </div>

          <div class="row">
            <div class="col-6">
              <div class="mb-3">
                <label id="flight-date-label" for="flight-date" class="form-label" data-bs-toggle="tooltip" data-bs-title="">ETA</label>
                <div
                  class="input-group log-event"
                  id="datetimepicker1"
                  data-td-target-input="nearest"
                  data-td-target-toggle="nearest">
                  <input
                    id="flight-date"
                    type="text"
                    class="form-control form-control-lg"
                    data-td-target="#datetimepicker1"/>
                  <span
                    class="input-group-text"
                    data-td-target="#datetimepicker1"
                    data-td-toggle="datetimepicker">
                    <i class="fa-duotone fa-calendar fa-2x"></i>
                  </span>
                </div>
              </div>
            </div>
          </div>

          <div class="mt-4 d-flex">
            <button id="btn-flight-next" class="px-5 btn btn-primary btn-lg ms-auto">Next</button>
          </div>
        </section>

        <section id="section-location" class="steps d-none">
          <div class="row">
            <div class="col">
              <div class="mb-3">
                <label id="location-address-label" for="location-address" class="form-label">Drop off location (place and address)</label>
                <textarea class="form-control form-control-lg" id="location-address" rows="3"></textarea>
              </div>
            </div>
          </div>

          <div class="mt-4 d-flex">
            <button id="btn-location-next" class="px-5 btn btn-primary btn-lg ms-auto">Next</button>
          </div>
        </section>

        <section id="section-summary" class="steps d-none">
          <h2 class="mb-4 fw-light">Almost done <span class="requestor-name"></span>! Does the following look correct?</h2>
          <div class="row">
            <div class="col">
              <textarea class="form-control form-control-lg font-monospace" id="summary" rows="5" disabled readonly></textarea>
            </div>
          </div>

          <div class="row mt-2">
            <div class="col">
              <div class="mb-3">
                <label id="notes-label" for="notes" class="form-label">Any additional comments or requests</label>
                <textarea class="form-control form-control-lg" id="notes" rows="5"></textarea>
              </div>
            </div>
          </div>

          <div class="mt-4 d-flex">
            <button id="btn-summary-next" class="px-5 btn btn-primary btn-lg ms-auto">Submit</button>
          </div>
        </section>

      </div>
    </div>
  </div>

  
  <script type="text/javascript">

    $(async ƒ => {

      const acceptedDomains = ['awmi.net','awmcharis.com'];
      const _state = {
        step: 'start'
      }
      const state = {
        // step: 'start',
        // step: 'select-route',
        step: 'route-2',
        name: 'Richard',
        email: 'richardobrien@awmcharis.com',
        type: 'airport-dropoff',
        whom: {
          name: 'Hawaii Missions Group', //this can be the name of a group or people
          pax: 10, // The number of people
          contactPerson: 'Richard', // The name of the contact person
          contactPhoneNumber: '(719) 425-5764', // The phone number of the contact person
        },
        airport: 'DEN',
        flight: {
          airlineId: 0, // The id of the airline in our database
          airline: 'United Airlines', // The name of our airline
          eta: '2024-12-25 05:00:00', // Formatted date/time of flight arrival (or if etd, the flight departure)
          flightNumber: '1234',
          flightNumberPrefix: 'UA'
        },
        location: 'Charis'
      };
      const pickupDateControl = new tempusDominus.TempusDominus(document.getElementById('datetimepicker1'), tempusConfigDefaults);
      pickupDateControl.updateOptions({
        allowInputToggle: true,
        promptTimeOnDateChange: true,
        useCurrent: false,
        restrictions: {
          minDate: new Date(),
        },
      }, false);

      function next() {
        console.log('Figure out the next step...');
        console.log(state);
        $('.steps').addClass('d-none');

        switch (state.step) {

          case 'start':
            $('#section-start').removeClass('d-none');
            break;

          case 'select-route':
            $('.requestor-name').html(state.name);
            $('#section-select-route').removeClass('d-none');
            break;

          case 'route-1': // Airport pick up
            if (!state.whom) {
              $('#section-get-whom').removeClass('d-none');
              return;
            }
            if (!state.airport) {
              $('#airport-question').html('Which airport are we picking up from?');
              $('#section-select-airport').removeClass('d-none');
              return;
            }
            if (!state.flight) {
              $('#flight-date-label').html('ETA');
              $('#section-flight-info').removeClass('d-none');
              return;
            }
            if (!state.location) {
              $('#location-address-label').html('Drop off location (place and address)');
              $('#section-location').removeClass('d-none');
              return;
            }
            summarize();
            $('#section-summary').removeClass('d-none');
            break;

          case 'route-2': // Airport drop off
            if (!state.whom) {
              $('#section-get-whom').removeClass('d-none');
              return;
            }
            if (!state.location) {
              $('#location-address-label').html('Pick up location (place and address)');
              $('#section-location').removeClass('d-none');
              return;
            }
            if (!state.airport) {
              $('#airport-question').html('Which airport are we dropping off at?');
              $('#section-select-airport').removeClass('d-none');
              return;
            }
            if (!state.flight) {
              $('#flight-date-label').html('ETD');
              $('#section-flight-info').removeClass('d-none');
              return;
            }
            summarize();
            $('#section-summary').removeClass('d-none');
            break;

          case 'route-3': // Point to point
            if (!state.whom) {
              $('#section-get-whom').removeClass('d-none');
              return;
            }
            break;

          case 'route-4': // Vehicle request
            break;
        }
      }

      function summarize () {
        let summary = '';
        if (state.type === 'airport-pickup') {
          summary += `${state.airport} pick up - ${state.whom.name} (${state.whom.pax})\n`;
          summary += `PU: ${state.flight.airline} ${state.flight.flightNumberPrefix}-${state.flight.flightNumber} ETA: ${state.flight.eta}\n`;
          summary += `DO: ${state.location}\n`;
          summary += `Contact: ${state.whom.contactPerson} ${state.whom.contactPhoneNumber}`;
        }
        if (state.type === 'airport-dropoff') {
          summary += `${state.airport} drop off - ${state.whom.name} (${state.whom.pax})\n`;
          summary += `PU: ${state.location}\n`;
          summary += `DO: ${state.flight.airline} ${state.flight.flightNumberPrefix}-${state.flight.flightNumber} ETD: ${state.flight.eta}\n`;
          summary += `Contact: ${state.whom.contactPerson} ${state.whom.contactPhoneNumber}`;
        }
        $('#summary').text(summary);
      }

      function letsGetAcquainted() {
        $('.steps').addClass('d-none');
        $('#section-get-acquainted-form').removeClass('d-none');
      }
      
      $('#btn-step-start-next').on('click', async e => {
        e.preventDefault();
        // Check is email address is acceptable
        email = cleanLowerVal('#requestor-email');
        const emailDomain = email.split('@')[1];
        if (acceptedDomains.includes(emailDomain)) {
          // Send an otp to the email address provided
          const resp = await get('action.send-otp.php', {email});
          if (resp?.result) {
            const otp = await input(`Great! We've sent a one-time-passcode (OTP) to your email address. Please enter it here to continue:`);
            const resp = await get('action.validate-otp.php', {email, otp});
            console.log(resp);
            if (resp?.result) {
              state.email = email;
              if (resp?.name) {
                // User is known to us
                state.name = resp.name;
                state.step = 'select-route';
                return next();
              } else {
                return letsGetAcquainted();
              }
              return;
            }
            alertError(`Doesn't seem like a match. Please try again.`, 'Error');
            return;
          }
          alertError(`Something went wrong trying to send you a one-time-password.`, 'Oh dear!');
          return;
        }
        alertError(`I'm truely sorry, but it doesn't seem like you're allowed to make a transportation request! ...unless of course you made a typo in your email address. You're welcome to check and re-submit.`, 'Oh dear!');
      });

      $('#btn-get-acquainted-next').on('click', async e => {
        e.preventDefault();
        const firstName = cleanProperVal('#requestor-first-name');
        const lastName = cleanProperVal('#requestor-last-name');
        const phoneNumber = cleanVal('#requestor-phone-number');
        const position = cleanProperVal('#requestor-position');
        const resp = await post('action.update-requestor.php', {
          firstName, lastName, phoneNumber, position, email: state.email
        });
        console.log(resp);
        if (resp?.result) {
          state.name = firstName;
          state.step = 'select-route';
          next();
        }
      });

      $('#btn-select-route-next').on('click', async e => {
        e.preventDefault();
        const option = $('#route-option').val();
        if (option !== '') {
          state.step = 'route-' + option;
          switch (option) {
            case '1':
              state.type = 'airport-pickup';
              break;
            case '2':
              state.type = 'airport-dropoff';
              break;
            case '3':
              state.type = 'point-to-point';
              break;
            case '4':
              state.type = 'vehicle-request';
              break;
          }
          return next();
        }
      });

      $('#btn-get-whom-next').on('click', async e => {
        e.preventDefault();
        const name = cleanProperVal('#whom-name');
        const contactPerson = cleanProperVal('#whom-contact-person');
        const contactPhoneNumber = cleanVal('#whom-contact-phone');
        const pax = int(cleanDigitsVal('#whom-pax'));
        if (name.length <= 0) return alertError(`We're going to need a name first...`, 'Almost');
        if (contactPerson.length <= 0) return alertError(`We're going to need a name for the contact person first...`, 'Almost');
        if (contactPhoneNumber.length <= 0) return alertError(`We're going to need a phone number for the contact person first...`, 'Almost');
        if (pax <= 0) return alertError(`We're going to need an approximate number of passenger first...`, 'Almost');
        state.whom = {
          name, contactPerson, contactPhoneNumber, pax
        }
        return next();
      });

      $('#btn-select-airport-next').on('click', async e => {
        e.preventDefault();
        const airport = $('#airport-option').val();
        if (airport) state.airport = airport;
        next();
      });

      $('#airline-option').on('change', async function(e) {
        e.preventDefault();
        const prefix = $(this).find(':selected').data('prefix');
        $('#flight-number-prefix').html(prefix);
        setTimeout(() => $('#flight-number').focus().select(), 200);
      });

      $('#btn-flight-next').on('click', async e => {
        const date = moment($('#flight-date').val(), 'MM/DD/YYYY h:mm A');
        const airlineId = $('#airline-option').val();
        const flightNumber = cleanUpperVal('#flight-number');
        if (!date.isValid()) return alertError(`Please make a valid date/time selection for the flight`, 'Almost');
        if (!airlineId) return alertError(`We're going to need you to select an airline first.`, 'Almost');
        if (flightNumber.length <= 0) return  alertError(`We're going to need you to give us a flight number.`, 'Almost');

        const airline = $('#airline-option').find(':selected').html();
        const flightNumberPrefix = $('#airline-option').find(':selected').data('prefix');
        if (state.type === 'airport-pickup') {
          state.flight = {
            airlineId, flightNumber, eta: date.format('YYYY-MM-DD HH:mm:ss'), airline, flightNumberPrefix
          }
          return next();
        }
        if (state.type === 'airport-dropoff') {
          state.flight = {
            airlineId, flightNumber, etd: date.format('YYYY-MM-DD HH:mm:ss'), airline, flightNumberPrefix
          }
          return next();
        }
      });

      $('#btn-location-next').on('click', async e => {
        const location = cleanVal('#location-address');
        if (location.length <= 5) return alertError(`We're going to need you to give us a location first.`, 'Almost');
        state.location = location;
        return next();
      });

      $('#btn-summary-next').on('click', async e => {
        const comments = cleanVal('#notes');
        state.comments = comments;
        console.log(state);
      });

      // We'll kick it off here
      next();

    });

  </script>
</body>
</html>