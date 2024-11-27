<?php
require_once 'class.user.php';
$user = new User($_SESSION['user']->id);
?>
<div class="container-fluid">

  <h6>Hello, <?=$user->firstName?>!</h6>

  <div id="trips-to-finalize"></div>
  <div id="vehicle-alerts"></div>

  <div class="row">
    <div class="d-flex justify-content-between mb-2">
      <div>
        Views:
        <div class="btn-group" role="group" aria-label="Basic radio toggle button group">
          <input type="radio" class="btn-check" name="btnradio" id="view-calendar" autocomplete="off" checked>
          <label class="btn btn-outline-primary" for="view-calendar">Calendar</label>

          <input type="radio" class="btn-check" name="btnradio" id="opt-list-events" autocomplete="off">
          <label class="btn btn-outline-primary" for="opt-list-events">List</label>

          <input type="radio" class="btn-check" name="btnradio" id="view-vehicles" autocomplete="off">
          <label class="btn btn-outline-primary" for="view-vehicles">Vehicles</label>

          <input type="radio" class="btn-check" name="btnradio" id="view-drivers" autocomplete="off">
          <label class="btn btn-outline-primary" for="view-drivers">Drivers</label>
        </div>
      </div>
      <div>
        <button id="btn-refresh-calendar" class="btn btn-outline-primary"><i class="fa-solid fa-rotate"></i></button>
        <button id="btn-new-trip" class="btn btn-outline-primary">New Trip</button>
      </div>
    </div>
    <div id="ec" class="col"></div>
    <div id="trip-text"></div>
  </div>
</div>

<script type="text/javascript">

  $(async ƒ => {
    let vehicleResources = await get('/api/get.resource-vehicles.php');
    let driverResources = await get('/api/get.resource-drivers.php');

    async function loadJITContent() {
      $('#trips-to-finalize').load('inc.dash-finalize.php');
      $('#vehicle-alerts').load('inc.dash-vehicles.php');
    }
    loadJITContent();

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
          app.openTab('edit-trip', 'Trip (edit)', `section.edit-trip.php?id=${data.event.id}`);
        }

        if (data?.event?.extendedProps?.type == 'event') {
          app.openTab('edit-event', 'Event (edit)', `section.edit-event.php?id=${data.event.id}`);
        }
      },
      loading: isLoading => {
        // console.log('isLoading:', isLoading);
      },
      eventDidMount: info => {
        const el = info.el;
        const title = info.event.title;
        $(el).attr('data-bs-title', title).tooltip();
      }
    });

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
      vehicleResources = await get('/api/get.resource-vehicles.php');
      ec.refetchEvents();
    });

    $(document).on('driverChange', async function (event, data) {
      vehicleResources = await get('/api/get.resource-drivers.php');
      ec.refetchEvents();
    });

    $(document).on('tripChange', ec.refetchEvents);

    // We're just going to have this auto-update every minute as well
    setInterval(() => {
      $(document).on('tripChange', ec.refetchEvents);
      loadJITContent();
    }, 60 * 1000);

    $('#btn-new-trip').on('click', () => {
      app.openTab('new-trip', 'New Trip', `section.new-trip.php`);
    });
    
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

    $('#btn-refresh-calendar').on('click', e => {
      ec.refetchEvents();
    });

  });

</script>
