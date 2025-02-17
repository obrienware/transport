<div class="ec-grid-container">
  <div class="pt-2">

    <div class="pretty p-svg p-curve p-bigger">
      <input type="checkbox" value="1" id="show-calendar-history">
      <div class="state p-primary">
        <!-- svg path -->
        <svg class="svg svg-icon" viewBox="0 0 20 20">
          <path d="M7.629,14.566c0.125,0.125,0.291,0.188,0.456,0.188c0.164,0,0.329-0.062,0.456-0.188l8.219-8.221c0.252-0.252,0.252-0.659,0-0.911c-0.252-0.252-0.659-0.252-0.911,0l-7.764,7.763L4.152,9.267c-0.252-0.251-0.66-0.251-0.911,0c-0.252,0.252-0.252,0.66,0,0.911L7.629,14.566z" style="stroke: white;fill:white;"></path>
        </svg>
        <label>Include passed events</label>
      </div>
    </div>

    <div class="pretty p-svg p-curve p-bigger">
      <input type="checkbox" value="1" id="show-calendar-only-me">
      <div class="state p-primary">
        <!-- svg path -->
        <svg class="svg svg-icon" viewBox="0 0 20 20">
          <path d="M7.629,14.566c0.125,0.125,0.291,0.188,0.456,0.188c0.164,0,0.329-0.062,0.456-0.188l8.219-8.221c0.252-0.252,0.252-0.659,0-0.911c-0.252-0.252-0.659-0.252-0.911,0l-7.764,7.763L4.152,9.267c-0.252-0.251-0.66-0.251-0.911,0c-0.252,0.252-0.252,0.66,0,0.911L7.629,14.566z" style="stroke: white;fill:white;"></path>
        </svg>
        <label>Show only my events</label>
      </div>
    </div>

  </div>
  <div class="text-center">
    <div class="" style="font-size: small">View</div>
    <div class="btn-group btn-group-sm me-2" role="group" aria-label="Basic radio toggle button group">
      <input type="radio" class="btn-check" name="btnradio" id="view-calendar" autocomplete="off" checked onclick="$(document).trigger('calendarView:default');">
      <label class="btn btn-outline-primary" for="view-calendar">Calendar</label>

      <input type="radio" class="btn-check" name="btnradio" id="opt-list-events" autocomplete="off" onclick="$(document).trigger('calendarView:list');">
      <label class="btn btn-outline-primary" for="opt-list-events">List</label>

      <input type="radio" class="btn-check" name="btnradio" id="view-vehicles" autocomplete="off" onclick="$(document).trigger('calendarView:vehicles');">
      <label class="btn btn-outline-primary" for="view-vehicles">Vehicles</label>

      <input type="radio" class="btn-check" name="btnradio" id="view-drivers" autocomplete="off" onclick="$(document).trigger('calendarView:drivers');">
      <label class="btn btn-outline-primary" for="view-drivers">Drivers</label>
    </div>
  </div>
  <div class="text-center">
    <div class="d-none d-sm-block" style="font-size: small">&nbsp;</div>
    <div>
      <button class="btn btn-sm btn-outline-primary" onclick="$(document).trigger('refreshCalendar')"><i class="fa-solid fa-rotate"></i></button>
      <button class="btn btn-sm btn-outline-primary" onclick="$(document).trigger('loadMainSection', {sectionId: 'trips', url: 'section.new-trip.php', forceReload: true})">New Trip</button>
      <button class="btn btn-sm btn-outline-primary" onclick="$(document).trigger('loadMainSection', {sectionId: 'reservations', url: 'section.edit-reservation.php', forceReload: true})">New Reservation</button>
      <button class="btn btn-sm btn-outline-primary" onclick="$(document).trigger('loadMainSection', {sectionId: 'events', url: 'section.edit-event.php', forceReload: true})">New Event</button>
    </div>
  </div>
</div>

<div id="ec" class="col py-2 mt-4"></div>

<script>
  $(async ƒ => {

    let extraParams = {
      history: $('#show-calendar-history').is(':checked'),
      onlyMe: $('#show-calendar-only-me').is(':checked'),
    };

    function updateExtraParams() {
      extraParams = {
        history: $('#show-calendar-history').is(':checked'),
        onlyMe: $('#show-calendar-only-me').is(':checked'),
      };
    }

    const eventSources = [
      {
        url: '/api/get.trips.php',
        method: 'GET',
        extraParams,
      },
      {
        url: '/api/get.events.php',
        method: 'GET',
        extraParams,
      },
      {
        url: '/api/get.reservations.php',
        method: 'GET',
        extraParams,
      },
    ];

    let vehicleResources = await net.get('/api/get.resource-vehicles.php');
    let driverResources = await net.get('/api/get.resource-drivers.php');

    function formatCalendarHeader() {
      document.querySelectorAll("h2.ec-title").forEach(el => {
        const text = el.textContent.trim();
        const match = text.match(/^([A-Za-z]+)\s(\d{4})$/); // Match "Month YYYY"

        if (match) {
          el.innerHTML = `<span class="month">${match[1]}</span> <span class="year">${match[2]}</span>`;
        }
      });

    }

    const ec = new EventCalendar(document.getElementById('ec'), {
      view: 'dayGridMonth',
      editable: false,
      eventStartEditable: false,
      eventDurationEditable: false,
      eventSources,
      eventDidMount: info => {
        const el = info.el;
        let title = info.event.title || 'untitled';
        title = $(`<tag>${title}</tag>`).text(); // This should fix the apostrophe issue on the tooltip

        const titleElement = $(el).find('h4.ec-event-title');
        $(titleElement).html($(titleElement).text()); // This should fix the apostrophe issue

        if (!info.event?.extendedProps?.confirmed) {
          title = 'Unconfirmed: ' + title;
        }
        $(el).attr('data-confirmed', `${info.event?.extendedProps?.confirmed}`);
        if ($.fn.tooltip) $(el).attr('data-bs-title', title).tooltip();
      },
      loading: isLoading => {
        if (isLoading) return;
        formatCalendarHeader();
      },
      eventAllUpdated: ƒ => {
        $('.ec-event').each((i, el) => {
          $(el).find('h4.ec-event-title').html($(el).find('h4.ec-event-title').text());
        });
        $('.ec-event').filter((i, el) => $(el).attr('data-confirmed') == 'null').each((i, el) => {
          $(el).find('h4.ec-event-title').prepend('<i class="fa-solid fa-pencil align-content-center me-1"></i>');
        });
        $('.ec-event').tooltip('dispose');
        $('.ec-event').tooltip();
      },
      viewDidMount: ƒ => {
        formatCalendarHeader();
      },
      eventClick: data => {
        const start = moment(data.event.start).format('ddd Do h:mma');
        const startDate = moment(data.event.start).format('ddd Do');

        if (data?.event?.extendedProps?.type == 'trip') {
          $(document).trigger('loadMainSection', {
            sectionId: 'trips',
            url: `section.edit-trip.php?id=${data.event.id}`,
            forceReload: true
          });
          $('.menu-item, .submenu-item').removeClass('active');
          $(`[data-rel="trips"]`).addClass('active');
        }

        if (data?.event?.extendedProps?.type == 'event') {
          $(document).trigger('loadMainSection', {
            sectionId: 'events',
            url: `section.edit-event.php?id=${data.event.id}`,
            forceReload: true
          });
          $('.menu-item, .submenu-item').removeClass('active');
          $(`[data-rel="events"]`).addClass('active');
        }

        if (data?.event?.extendedProps?.type == 'reservation') {
          $(document).trigger('loadMainSection', {
            sectionId: 'reservations',
            url: `section.edit-reservation.php?id=${data.event.id}`,
            forceReload: true
          });
          $('.menu-item, .submenu-item').removeClass('active');
          $(`[data-rel="reservations"]`).addClass('active');
        }
      },
    });

    function refreshEvents() {
      $('.ec-event').tooltip('dispose');
      ec.refetchEvents();
    }

    $(document).on('tripChange', refreshEvents);
    $(document).on('eventChange', refreshEvents);
    $(document).on('reservationChange', refreshEvents);

    $(document).on('refreshCalendar', ƒ => {
      refreshEvents();
    });

    $(document).on('calendarView:vehicles', ƒ => {
      ec.setOption('resources', vehicleResources);
      ec.setOption('view', 'resourceTimelineMonth');
    });

    $(document).on('calendarView:drivers', ƒ => {
      ec.setOption('resources', driverResources);
      ec.setOption('view', 'resourceTimelineMonth');
    });

    $(document).on('calendarView:default', ƒ => {
      ec.setOption('view', 'dayGridMonth');
    });

    $(document).on('calendarView:list', ƒ => {
      ec.setOption('view', 'listMonth');
    });

    $('#show-calendar-history, #show-calendar-only-me').on('change', ƒ => {
      updateExtraParams();
      eventSources.forEach(es => es.extraParams = extraParams);
      ec.setOption('eventSources', eventSources);
      window.localStorage.setItem('show-calendar-history', $('#show-calendar-history').is(':checked').toString());
      window.localStorage.setItem('show-calendar-only-me', $('#show-calendar-only-me').is(':checked').toString());
      refreshEvents();
    });

    if (window.localStorage.getItem('show-calendar-history') == 'true') {
      $('#show-calendar-history').prop('checked', true);
      $('#show-calendar-history').trigger('change');
    }
    if (window.localStorage.getItem('show-calendar-only-me') == 'true') {
      $('#show-calendar-only-me').prop('checked', true);
      $('#show-calendar-only-me').trigger('change');
    }

  });
</script>