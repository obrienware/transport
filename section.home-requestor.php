<?php
require_once 'class.user.php';
$user = new User($_SESSION['user']->id);
?>
<div class="container-fluid">

  <h6>Hello, <?=$user->firstName?>!</h6>

  <div class="row">
    <div class="d-flex justify-content-between mb-2">
      <div>
        Views:
        <div class="btn-group" role="group" aria-label="Basic radio toggle button group">
          <input type="radio" class="btn-check" name="btnradio" id="view-calendar" autocomplete="off" checked>
          <label class="btn btn-outline-primary" for="view-calendar">Calendar</label>

          <input type="radio" class="btn-check" name="btnradio" id="opt-list-events" autocomplete="off">
          <label class="btn btn-outline-primary" for="opt-list-events">List</label>
        </div>
      </div>
      <div>
        <button id="btn-refresh-calendar" class="btn btn-outline-primary"><i class="fa-solid fa-rotate"></i></button>
        <button id="btn-request-event" class="btn btn-outline-primary">Request New Event</button>
        <button id="btn-request-trip" class="btn btn-outline-primary">Request New Trip</button>
      </div>
    </div>
    <div id="ec" class="col"></div>
    <div id="trip-text"></div>
  </div>
</div>

<script type="text/javascript">

  $(async ƒ => {
    // let vehicleResources = await get('/api/get.resource-vehicles.php');
    // let driverResources = await get('/api/get.resource-drivers.php');
    let requestorId = <?=$user->userId?>;

    const ec = new EventCalendar(document.getElementById('ec'), {
      view: 'dayGridMonth',
      editable: false,
      eventStartEditable: false,
      eventDurationEditable: false,
      eventSources: [
        {
          url: '/api/get.trips.php?requestorId=' + requestorId,
          method: 'GET',
        }, 
        {
          url: '/api/get.events.php?requestorId=' + requestorId,
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

    $('#view-calendar').on('click', () => {
      $('#trip-text').html('');
      ec.setOption('view', 'dayGridMonth');
    });

    $('#opt-list-events').on('click', () => {
      $('#trip-text').html('');
      ec.setOption('view', 'listMonth');
    });

    // $('#btn-new-trip').on('click', () => {
    //   app.openTab('new-trip', 'New Trip', `section.new-trip.php`);
    // });
    
    // $('#btn-new-event').on('click', () => {
    //   app.openTab('new-event', 'New Event', `section.edit-event.php`);
    // });
    
    $('#ec').on('click', async e => {
      if (e.target.className == 'ec-bg-events') {
        const date = $(e.target).prev().attr('datetime');
        if (date) {
          // check if the date is in the future
          if (moment().isSameOrBefore(moment(date, 'YYYY-MM-DD'), 'day')) {
            // if (await ask('Do you want to create a new trip?')) {
            //   const formatted_date = encodeURIComponent(moment(date).format('MM/DD/YYYY h:mm A'))
            //   app.openTab('new-trip', 'New Trip', `section.new-trip.php?dateHint=${formatted_date}`);
            // }
          }
        }
      }
    });

    $('#btn-refresh-calendar').on('click', e => {
      ec.refetchEvents();
    });

  });

</script>
