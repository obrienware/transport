<?php
require_once 'autoload.php';

use Transport\User;

$user = new User($_SESSION['user']->id);
?>
<div class="container-fluid">

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
        <button id="btn-new-event" class="btn btn-outline-primary">New Event</button>
        <button id="btn-new-trip" class="btn btn-outline-primary">New Trip</button>
      </div>
    </div>
    <div id="ec" class="col py-2"></div>
    <div id="trip-text"></div>
  </div>
</div>

<div>
  <div id="trips-to-confirm"></div>
  <div id="vehicle-alerts"></div>
</div>

<script type="module">
  import * as input from '/js/formatters.js';
  import * as ui from '/js/notifications.js';
  import * as net from '/js/network.js';

  $(async ƒ => {
    let vehicleResources = await net.get('/api/get.resource-vehicles.php');
    let driverResources = await net.get('/api/get.resource-drivers.php');

    <?php if (array_search($_SESSION['view'], ['manager']) !== false):?>
      async function loadJITContent() {
        $('#trips-to-confirm').load('inc.dash-confirm.php');
        $('#vehicle-alerts').load('inc.dash-vehicles.php');
      }
      loadJITContent();
    <?php endif; ?>

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
      },
      loading: isLoading => {
        // console.log('isLoading:', isLoading);
      },
      eventDidMount: info => {
        const el = info.el;
        const title = info.event.title || 'untitled';
        $(el).attr('data-bs-title', title).tooltip();
      }
    });

    function refreshEvents() {
      $('.ec-event').tooltip('dispose');
      ec.refetchEvents();
    }

    $('#view-vehicles').on('click', () => {
      $('#trip-text').html('');
      ec.setOption('resources', vehicleResources);
      ec.setOption('view', 'resourceTimelineMonth');
    });

    $('#view-drivers').on('click', () => {
      $('#trip-text').html('');
      ec.setOption('resources', driverResources);
      ec.setOption('view', 'resourceTimelineMonth');
    });

    $('#view-calendar').on('click', () => {
      $('#trip-text').html('');
      ec.setOption('view', 'dayGridMonth');
    });

    $('#opt-list-events').on('click', () => {
      $('#trip-text').html('');
      ec.setOption('view', 'listMonth');
    });


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

    // We're just going to have this auto-update every minute as well
    setInterval(() => {
      refreshEvents();
      loadJITContent();
    }, 60 * 1000);

    $('#btn-new-trip').on('click', () => {
      app.openTab('new-trip', 'New Trip', `section.new-trip.php`);
    });
    
    $('#btn-new-event').on('click', () => {
      app.openTab('new-event', 'New Event', `section.edit-event.php`);
    });
    
    $('#ec').on('click', async e => {
      if (e.target.className == 'ec-bg-events') {
        const date = $(e.target).prev().attr('datetime');
        if (date) {
          // check if the date is in the future
          if (moment().isSameOrBefore(moment(date, 'YYYY-MM-DD'), 'day')) {
            if (await ui.ask('Do you want to create a new trip?')) {
              const formatted_date = encodeURIComponent(moment(date).format('YYYY-MM-DD HH:mm'));
              app.openTab('new-trip', 'New Trip', `section.new-trip.php?dateHint=${formatted_date}`);
            }
          }
        }
      }
    });

    $('#btn-refresh-calendar').on('click', refreshEvents);

  });

</script>
