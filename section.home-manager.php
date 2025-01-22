<?php
require_once 'autoload.php';

use Transport\User;

$user = new User($_SESSION['user']->id);
?>
<div class="container-fluid">

  <h6>Hello, <?=$user->firstName?>!</h6>

  <div id="trips-to-confirm"></div>
  <div id="vehicle-alerts"></div>

  <div class="row">
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
    <div id="ec" class="col bg-body py-2"></div>
  </div>
</div>


<script type="text/javascript">

  $(async ƒ => {

    let vehicleResources = await get('/api/get.resource-vehicles.php');
    let driverResources = await get('/api/get.resource-drivers.php');    

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
      eventDidMount: info => {
        const el = info.el;
        const title = info.event.title || 'untitled';
        $(el).attr('data-bs-title', title).tooltip();
      }
    });

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

    $('#btn-refresh-calendar').on('click', ec.refetchEvents);

    $(document).on('vehicleChange', async function (event, data) {
      vehicleResources = await get('/api/get.resource-vehicles.php');
      ec.refetchEvents();
    });

    $(document).on('driverChange', async function (event, data) {
      vehicleResources = await get('/api/get.resource-drivers.php');
      ec.refetchEvents();
    });

    $(document).on('tripChange', ec.refetchEvents);
    $(document).on('eventChange', ec.refetchEvents);

    $('#ec').on('click', async e => {
      if (e.target.className == 'ec-bg-events') {
        const date = $(e.target).prev().attr('datetime');
        if (date) {
          // check if the date is in the future
          if (moment().isSameOrBefore(moment(date, 'YYYY-MM-DD'), 'day')) {
            if (await ask('Do you want to create a new trip?')) {
              const formatted_date = encodeURIComponent(moment(date).format('MM/DD/YYYY h:mm A'))
              app.openTab('new-trip', 'New Trip', `section.new-trip.php?dateHint=${formatted_date}`);
            }
          }
        }
      }
    });

    // We're also going to have this auto-update every minute as well
    setInterval(() => {
      ec.refetchEvents();
      loadJITContent();
    }, 60 * 1000);
    loadJITContent();

  });

</script>
