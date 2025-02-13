<?php
require_once 'autoload.php';

use Transport\User;

$user = new User($_SESSION['user']->id);
?>
<section class="container-fluid">

  <h6>Hello, <?=$user->firstName?>!</h6>

  <div class="row mb-4">
    <div class="d-flex justify-content-between mb-2">
      <div>
        <div class="btn-group me-2" role="group" aria-label="Basic radio toggle button group">
          <input type="radio" class="btn-check" name="btnradio" id="view-calendar" autocomplete="off" checked>
          <label class="btn btn-outline-primary" for="view-calendar">Calendar</label>

          <input type="radio" class="btn-check" name="btnradio" id="opt-list-events" autocomplete="off">
          <label class="btn btn-outline-primary" for="opt-list-events">List</label>

          <input type="radio" class="btn-check" name="btnradio" id="view-vehicles" autocomplete="off">
          <label class="btn btn-outline-primary" for="view-vehicles">Vehicles</label>

          <input type="radio" class="btn-check" name="btnradio" id="view-drivers" autocomplete="off">
          <label class="btn btn-outline-primary" for="view-drivers">Drivers</label>
        </div>
        View
      </div>
      <div>
        <button id="btn-refresh-calendar" class="btn btn-outline-primary"><i class="fa-solid fa-rotate"></i></button>
        <button id="btn-new-event" class="btn btn-outline-primary" onclick="app.openTab('new-event', 'New Event', 'section.edit-event.php')">New Event</button>
        <button id="btn-new-trip" class="btn btn-outline-primary" onclick="app.openTab('new-trip', 'New Trip', 'section.new-trip.php')">New Trip</button>
      </div>
    </div>
    <div id="ec" class="col py-2"></div>
  </div>

</section>

<section>
  <div id="trips-to-confirm"></div>
  <div id="vehicle-alerts"></div>
</section>


<script type="module">
  import * as input from '/js/formatters.js';
  import * as ui from '/js/notifications.js';
  import * as net from '/js/network.js';

  $(async ƒ => {

    let vehicleResources = await net.get('/api/get.resource-vehicles.php');
    let driverResources = await net.get('/api/get.resource-drivers.php');

    const ec = new EventCalendar(document.getElementById('ec'), {
      view: 'dayGridMonth',
      editable: false,
      eventStartEditable: false,
      eventDurationEditable: false,
      eventSources: [
        {
          url: '/api/get.trips.php',
          method: 'GET',
        }, 
        {
          url: '/api/get.events.php',
          method: 'GET'
        },
        {
          url: '/api/get.reservations.php',
          method: 'GET'
        },
      ],
      eventClick: data => {
        const start = moment(data.event.start).format('ddd Do h:mma');
        const startDate = moment(data.event.start).format('ddd Do');
        
        if (data?.event?.extendedProps?.type == 'trip') {
          app.openTab('view-trip', 'Trip (view)', `section.view-trip.php?id=${data.event.id}`);
        }

        if (data?.event?.extendedProps?.type == 'event') {
          app.openTab('view-event', 'Event (view)', `section.view-event.php?id=${data.event.id}`);
        }

        if (data?.event?.extendedProps?.type == 'reservation') {
          app.openTab('edit-reservation', 'Reservation (edit)', `section.edit-reservation.php?id=${data.event.id}`);
        }
      },
      eventDidMount: info => {
        const el = info.el;
        let title = info.event.title || 'untitled';

        if (!info.event?.extendedProps?.confirmed) {
          title = 'Unconfirmed: ' + title;
        }
        $(el).attr('data-confirmed', `${info.event?.extendedProps?.confirmed}`);
        $(el).attr('data-bs-title', title).tooltip();
      },
      eventAllUpdated: ƒ => {
        $('.ec-event').filter((i, el) => $(el).attr('data-confirmed') == 'null').each((i, el) => {
          $(el).find('h4.ec-event-title').prepend('<i class="fa-solid fa-pencil ~fa-lg align-content-center me-1"></i>');
        });
        $('.ec-event').tooltip('dispose');
        $('.ec-event').tooltip();
        // console.log($('.ec-event').filter((i, el) => $(el).attr('data-confirmed') !== 'null'));
      }
    });

    function refreshEvents() {
      $('.ec-event').tooltip('dispose');
      ec.refetchEvents();
    }

    async function loadJITContent() {
      <?php if (array_search($_SESSION['view'], ['manager']) !== false):?>
        $('#trips-to-confirm').load('inc.dash-confirm.php');
        $('#vehicle-alerts').load('inc.dash-vehicles.php');
      <?php endif; ?>
    }

    $('#view-vehicles').on('click', ƒ => {
      ec.setOption('resources', vehicleResources);
      ec.setOption('view', 'resourceTimelineMonth');
    });

    $('#view-drivers').on('click', ƒ => {
      ec.setOption('resources', driverResources);
      ec.setOption('view', 'resourceTimelineMonth');
    });

    $('#view-calendar').on('click', ƒ => {
      ec.setOption('view', 'dayGridMonth');
    });

    $('#opt-list-events').on('click', ƒ => {
      ec.setOption('view', 'listMonth');
    });

    $('#btn-refresh-calendar').on('click', refreshEvents);

    $(document).on('vehicleChange', async function (event, data) {
      vehicleResources = await net.get('/api/get.resource-vehicles.php');
      refreshEvents();
    });

    $(document).on('driverChange', async function (event, data) {
      vehicleResources = await net.get('/api/get.resource-drivers.php');
      refreshEvents();
    });

    $(document).on('tripChange', refreshEvents);
    $(document).on('eventChange', refreshEvents);
    $(document).on('reservationChange', refreshEvents);

    $('#ec').on('click', async e => {
      if (e.target.className == 'ec-bg-events') {
        const date = $(e.target).prev().attr('datetime');
        if (date) {
          // check if the date is in the future
          if (moment().isSameOrBefore(moment(date, 'YYYY-MM-DD'), 'day')) {
            if (await ui.ask('Do you want to create a new trip?')) {
              // const formatted_date = encodeURIComponent(moment(date).format('MM/DD/YYYY h:mm A'))
              const formatted_date = encodeURIComponent(moment(date).format('YYYY-MM-DD HH:mm'));
              app.openTab('new-trip', 'New Trip', `section.new-trip.php?dateHint=${formatted_date}`);
            }
          }
        }
      }
    });

    // We're also going to have this auto-update every minute as well
    setInterval(() => {
      refreshEvents();
      loadJITContent();
    }, 60 * 1000);
    loadJITContent();

  });

</script>
