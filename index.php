<?php require 'inc.user.php'; ?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

  <!-- Link the Web App Manifest -->
  <link rel="manifest" href="/manifest.json">

  <!-- iOS-Specific Meta Tags -->
  <meta name="apple-mobile-web-app-capable" content="yes">
  <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
  <meta name="apple-mobile-web-app-title" content="Admin - Transport">

  <link rel="apple-touch-icon" href="/apple-touch-icon.png">
  <link rel="apple-touch-icon" sizes="152x152" href="/images/logo-152.png">
  <link rel="apple-touch-icon" sizes="180x180" href="/images/logo-180.png">
  <link rel="apple-touch-icon" sizes="167x167" href="/images/logo-167.png">
  <meta name="apple-mobile-web-app-title" content="Admin - Transport">
  <meta name="mobile-web-app-capable" content="yes">


  <meta name="description" content="Transportation System - Management Console">
  <meta name="author" content="Richard O'Brien">
  <link rel="icon" type="image/x-icon" href="/images/logo.svg">

  <title>Transportation Management Console</title>

  <!-- Bootstrap 5 CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js" integrity="sha512-v2CJ7UaYy4JwqLDIrZUI/4hqeoQieOmAZNXBeQyjo21dadnwR+8ZaIJVT8EE2iyI61OV8e6M8PP2/4hpQINQ/g==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
  <script src="/js/jq-plugins.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.3/js/bootstrap.bundle.min.js" integrity="sha512-7Pi/otdlbbCR+LnW+F7PwFcSDJOuUJB3OxtEHbg4vSMvzvJjde4Po1v4BR9Gdc9aXNUNFVUY+SK51wWT8WF0Gg==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>

  <!-- Fontawesome - necessary for icons -->
  <script src="https://kit.fontawesome.com/cc9f38bd60.js" crossorigin="anonymous"></script>

  <!-- Calendar -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@event-calendar/build@3.7.0/event-calendar.min.css">
  <link rel="stylesheet" href="/css/ec-calendar.css">
  <script src="https://cdn.jsdelivr.net/npm/@event-calendar/build@3.7.0/event-calendar.min.js" defer></script>

  <!-- Moment -->
  <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.30.1/moment-with-locales.min.js" integrity="sha512-4F1cxYdMiAW98oomSLaygEwmCnIP38pb4Kx70yQYqRwLVCs3DbRumfBq82T08g/4LJ/smbFGFpmeFlQgoDccgg==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/moment-timezone/0.5.46/moment-timezone-with-data.min.js" integrity="sha512-4MAP/CJtK3ASCmbYjYxWAbHWASAx1UYMc1i83cBdQZXegqFfqSZ9WqpmkRGfvzeAI18yvKiDTlgX/TLNMpxkSQ==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
  <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/timeago.js/4.0.2/timeago.full.min.js"></script>

  <!-- Ace theme(s) -->
  <script src="https://cdnjs.cloudflare.com/ajax/libs/json5/2.2.3/index.min.js" integrity="sha512-44jdhc+R2TFfzBflS3/dGNEABiNUxBkkrqwO7GWTvGsj3HkQNr3GESvI9PUvAxmqxSnTosR0Ij9y3+o+6J1hig==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/ace/1.36.4/ace.min.js" integrity="sha512-9xNuS6O4ZXZdCVDekkW4ApP8MfH2SCyK7Wsd4g0l3KDmbNld2vsozYGY6kQup0VKB0iT89cLj/DiRSV7WjfUaw==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/ace/1.36.4/mode-json5.min.js" integrity="sha512-qiTdaKQmVlm7hpUZaFVX5tdDgH6oqZx2I9ChggYoeuECMjCMRT/+YBvA0RcxX5hCRW9dP1ZNRGWqOnX3MtU17w==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/ace/1.36.4/theme-xcode.min.js" integrity="sha512-GdLo/7fZFpTgzlSOPaN1qzIRRTq4NHXr5bH5sfBgiIuU/bqo+bvXAAb1+IWJ5mwYSYApoUDbFKc46Bbn883IgA==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>

  <!-- Datatables ...yes, it's better with them -->
  <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/2.2.2/css/dataTables.bootstrap5.min.css">
  <script type="text/javascript" src="https://cdn.datatables.net/2.2.2/js/dataTables.min.js"></script>
  <script type="text/javascript" src="https://cdn.datatables.net/2.2.2/js/dataTables.bootstrap5.min.js"></script>
  <!-- Data Tables - Buttons -->
  <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/buttons/3.2.0/css/buttons.bootstrap5.min.css">
  <script type="text/javascript" src="https://cdn.datatables.net/buttons/3.2.0/js/dataTables.buttons.min.js"></script>
  <script type="text/javascript" src="https://cdn.datatables.net/buttons/3.2.0/js/buttons.bootstrap5.min.js"></script>
  <!-- Data Tables - Responsive -->
  <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/responsive/3.0.3/css/responsive.bootstrap5.min.css">
  <script type="text/javascript" src="https://cdn.datatables.net/responsive/3.0.3/js/dataTables.responsive.min.js"></script>
  <script type="text/javascript" src="https://cdn.datatables.net/responsive/3.0.3/js/responsive.bootstrap5.min.js"></script>
  <script type="text/javascript" src="/js/datatables-defaults.js"></script>


  <!-- Bootstrap Select -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-select@1.14.0-beta3/dist/css/bootstrap-select.min.css">
  <script src="https://cdn.jsdelivr.net/npm/bootstrap-select@1.14.0-beta3/dist/js/bootstrap-select.min.js" defer></script>
  <!-- Popperjs -->
  <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js" integrity="sha256-BRqBN7dYgABqtY9Hd4ynE+1slnEw+roEPFzQ7TRRfcg=" crossorigin="anonymous"></script>

  <script type="text/javascript" src="js/autocomplete.js"></script>

  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/pretty-checkbox/3.0.3/pretty-checkbox.min.css" integrity="sha512-kz4Ae66pquz4nVE5ytJyKfPDkQyHSggaDtT1v8oLfOd8bB+ZgZXNLaxex99MNu4fdCsWmi58mhLtfGk5RgfcOw==" crossorigin="anonymous" referrerpolicy="no-referrer" />

  <!-- Lightbox2 CSS -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.11.3/css/lightbox.min.css">
  <!-- Lightbox2 JS -->
  <script src="https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.11.3/js/lightbox.min.js"></script>

  <script src="/js/layout.js" defer></script>
  <link rel="stylesheet" href="/css/layout.css">
  <link rel="stylesheet" href="/css/style-alt.css">

  <script type="text/javascript" src="js/type-extensions.js"></script>

  <script type="text/javascript" src="js/upload-modal.js" defer></script>
</head>

<body>

  <?php include 'inc.upload-modal.html'; ?>
  <?php include 'inc.image-modal.html'; ?>

  <!-- Overlay for mobile menu -->
  <div class="overlay" id="overlay"></div>

  <div class="body w-100">
    <!-- Sidebar -->
    <nav class="sidebar d-flex flex-column overflow-y-auto" id="sidebar">

      <!-- Full Menu (Shown when expanded) -->
      <div class="full-menu h-100">

        <div class="text-center text-light bg-light bg-opacity-25 py-2">
          <img src="/images/logo.svg" alt="Transportation" style="height: 40px;" class="me-2">
          TRANSPORTATION
        </div>


        <button class="menu-item no-submenu active" data-rel="dashboard" onclick="$(document).trigger('menuSelect', 'dashboard')">
          <div class="d-flex align-items-center">
            <i class="fa-solid fa-chart-line menu-icon"></i>
            <span class="menu-text">Dashboard</span>
          </div>
        </button>
        <button class="menu-item no-submenu" data-rel="calendar" onclick="$(document).trigger('menuSelect', 'calendar')">
          <div class="d-flex align-items-center">
            <i class="fa-duotone fa-solid fa-calendar menu-icon"></i>
            <span class="menu-text">Calendar</span>
          </div>
        </button>

        <?php if (allowedRoles(['requestor'])) : ?>
          <button class="menu-item no-submenu" data-rel="request" onclick="$(document).trigger('menuSelect', 'request')">
          <div class="d-flex align-items-center">
            <i class="fa-solid fa-hand-point-up menu-icon"></i>
            <span class="menu-text">NEW Request</span>
          </div>
        </button>
        <?php endif; ?>

      <?php if (allowedRoles(['developer', 'manager', 'admin', 'driver'])) : ?>
        <button class="menu-item no-submenu" data-rel="trips" onclick="$(document).trigger('menuSelect', 'trips')">
          <div class="d-flex align-items-center">
            <i class="fa-duotone fa-solid fa-road-circle-check menu-icon"></i>
            <span class="menu-text">Trips</span>
          </div>
        </button>
        <button class="menu-item no-submenu" data-rel="events" onclick="$(document).trigger('menuSelect', 'events')">
          <div class="d-flex align-items-center">
            <i class="fa-duotone fa-solid fa-calendar-users menu-icon"></i>
            <span class="menu-text">Events</span>
          </div>
        </button>
        <button class="menu-item no-submenu" data-rel="reservations" onclick="$(document).trigger('menuSelect', 'reservations')">
          <div class="d-flex align-items-center">
            <i class="fa-duotone fa-solid fa-calendar-lines-pen menu-icon"></i>
            <span class="menu-text">Reservations</span>
          </div>
        </button>
        <button class="menu-item no-submenu" data-rel="vehicles" onclick="$(document).trigger('menuSelect', 'vehicles')">
          <div class="d-flex align-items-center">
            <i class="fa-duotone fa-solid fa-car menu-icon"></i>
            <span class="menu-text">Vehicles</span>
          </div>
        </button>
        <button class="menu-item no-submenu" data-rel="flights" onclick="$(document).trigger('menuSelect', 'flights')">
          <div class="d-flex align-items-center">
            <i class="fa-duotone fa-solid fa-plane-circle-check menu-icon"></i>
            <span class="menu-text">Flights</span>
          </div>
        </button>

        <!-- Reports -->
        <?php if (allowedRoles(['developer', 'manager'])) : ?>
        <a href="#submenu1" class="menu-item" data-bs-toggle="collapse">
          <div class="d-flex align-items-center">
            <i class="fa-duotone fa-solid fa-file-lines menu-icon"></i>
            <span class="menu-text">Reports</span>
          </div>
          <i class="fa-solid fa-chevron-down chevron"></i>
        </a>
        <div class="collapse submenu" id="submenu1">
          <button class="submenu-item" data-rel="auditTrail" onclick="$(document).trigger('menuSelect', 'auditTrail')">Audit Trail</button>
          <button class="submenu-item" data-rel="authenticationLog" onclick="$(document).trigger('menuSelect', 'authenticationLog')">Authentication Log</button>
        </div>
        <?php endif; ?>

        <!-- Settings -->
        <a href="#submenu2" class="menu-item" data-bs-toggle="collapse">
          <div class="d-flex align-items-center">
            <i class="fa-duotone fa-solid fa-database menu-icon"></i>
            <span class="menu-text">System</span>
          </div>
          <i class="fa-solid fa-chevron-down chevron"></i>
        </a>
        <div class="collapse submenu" id="submenu2">
          <button class="submenu-item" data-rel="driverNotes" onclick="$(document).trigger('menuSelect', 'driverNotes')">- Driver Notes</button>
          <button class="submenu-item" data-rel="guests" onclick="$(document).trigger('menuSelect', 'guests')">- Contact/Guest List</button>
          <button class="submenu-item" data-rel="locations" onclick="$(document).trigger('menuSelect', 'locations')">- Locations</button>
          <button class="submenu-item" data-rel="users" onclick="$(document).trigger('menuSelect', 'users')">- Users</button>
          <?php if (allowedRoles(['developer', 'manager'])) : ?>
          <button class="submenu-item" data-rel="blockouts" onclick="$(document).trigger('menuSelect', 'blockouts')">- Driver Block Out Dates</button>
          <button class="submenu-item" data-rel="departments" onclick="$(document).trigger('menuSelect', 'departments')">- Departments</button>
          <button class="submenu-item" data-rel="airlines" onclick="$(document).trigger('menuSelect', 'airlines')">- Airlines</button>
          <button class="submenu-item" data-rel="airports" onclick="$(document).trigger('menuSelect', 'airports')">- Airports</button>
          <button class="submenu-item" data-rel="airportLocations" onclick="$(document).trigger('menuSelect', 'airportLocations')">- Airport Locations</button>
          <button class="submenu-item" data-rel="emailTemplates" onclick="$(document).trigger('menuSelect', 'emailTemplates')">- Email Templates</button>
          <button class="submenu-item" data-rel="config" onclick="$(document).trigger('menuSelect', 'config')">- Config</button>
          <?php endif; ?>
        </div>

        <a href="#submenu3" class="menu-item" data-bs-toggle="collapse">
          <div class="d-flex align-items-center">
            <i class="fa-duotone fa-solid fa-user menu-icon"></i>
            <span class="menu-text">User</span>
          </div>
          <i class="fa-solid fa-chevron-down chevron"></i>
        </a>
        <div class="collapse submenu" id="submenu3">
          <button class="submenu-item" data-rel="myBlockouts" onclick="$(document).trigger('menuSelect', 'myBlockouts')">- Block Out Dates</button>
          <a href="page.new-password.php" class="submenu-item" onclick="location.href='page.new-password.php'">- Change Password</a>
          <a href="logout.php" class="submenu-item" onclick="location.href='logout.php'">- Log Out</a>
        </div>

        <div class="flex-fill"></div>

        <div class="text-center mt-auto pb-4">
          <button id="sidebar-menu-toggle" class="btn btn-dark" onclick="$(document).trigger('sideBarToggle');">
            <i class="fa-solid fa-arrow-left"></i>
          </button>
        </div>
      <?php endif; ?>
      <?php if (allowedRoles(['requestor']) && !allowedRoles(['developer', 'manager', 'admin', 'driver'])) : ?>
        <button class="menu-item no-submenu" onclick="location.href='logout.php'">
          <div class="d-flex align-items-center">
            <i class="fa-solid fa-left-from-bracket menu-icon"></i>
            <span class="menu-text">Log Out</span>
          </div>
        </button>
      <?php endif;?>
      </div>

      <!-- Simplified Menu (Shown when collapsed) -->
      <div class="collapsed-menu h-100">
        <img src="/images/logo.svg" alt="Transportation" style="height: 40px;" class="mt-2 mb-3">
        <button class="menu-item no-submenu active" data-rel="dashboard" onclick="$(document).trigger('menuSelect', 'dashboard')">
          <i class="fa-solid fa-chart-line menu-icon"></i>
        </button>
        <button class="menu-item no-submenu" data-rel="calendar" onclick="$(document).trigger('menuSelect', 'calendar')">
          <i class="fa-duotone fa-solid fa-calendar menu-icon"></i>
        </button>
        <?php if (allowedRoles(['developer', 'manager', 'admin', 'driver'])) : ?>
        <button class="menu-item no-submenu" data-rel="trips" onclick="$(document).trigger('menuSelect', 'trips')">
          <i class="fa-duotone fa-solid fa-road-circle-check menu-icon"></i>
        </button>
        <button class="menu-item no-submenu" data-rel="events" onclick="$(document).trigger('menuSelect', 'events')">
          <i class="fa-duotone fa-solid fa-calendar-users menu-icon"></i>
        </button>
        <button class="menu-item no-submenu" data-rel="reservations" onclick="$(document).trigger('menuSelect', 'reservations')">
          <i class="fa-duotone fa-solid fa-calendar-lines-pen menu-icon"></i>
        </button>
        <button class="menu-item no-submenu" data-rel="vehicles" onclick="$(document).trigger('menuSelect', 'vehicles')">
          <i class="fa-duotone fa-solid fa-car menu-icon"></i>
        </button>
        <button class="menu-item no-submenu" data-rel="blockouts" onclick="$(document).trigger('menuSelect', 'blockouts')">
          <i class="fa-duotone fa-solid fa-user-clock menu-icon"></i>
        </button>
        <?php endif; ?>


        <div class="flex-fill"></div>

        <div class="text-center pb-4">
          <button class="btn btn-dark" onclick="$(document).trigger('sideBarToggle');">
            <i class="fa-solid fa-arrow-right"></i>
          </button>
        </div>
      </div>

    </nav>


    <button class="btn btn-dark toggle-btn" id="toggle-btn" style="z-index: 1;">
      <i class="fa-solid fa-bars"></i>
    </button>


    <!-- Main Content -->
    <section id="main-content" class="content" style="display: block; overflow-y: auto;">

      <div id="dashboard" class="main-section">

        <style>
          #dashboard strong {
            font-weight: 900;
            color: goldenrod;
          }
        </style>

        <div>

          <h2 style="font-weight:200">Hello <span style="font-weight:800; color:goldenrod"><?= $user->firstName ?></span>!</h2>

      </div>

    </section>

  </div>

  <script type="module">
    import * as input from '/js/formatters.js';
    import * as ui from '/js/notifications.js';
    import * as net from '/js/network.js';
    import {
      initListPage
    } from '/js/listpage-helper.js';
    import {
      hexToRgba,
      luminanceColor
    } from '/js/helpers.js';

    window.input = input;
    window.ui = ui;
    window.net = net;
    window.initListPage = initListPage;
    window.hexToRgba = hexToRgba;
    window.luminanceColor = luminanceColor;
  </script>

  <script>
    debug = true;

    if (debug) {
      console.log(navigator.userAgentData);
      console.log(navigator.userAgent);
      console.log(/Mobi|Android|iPhone|iPad|iPod/i.test(navigator.userAgent));
    }

    function isMobile() {
      if (navigator.userAgentData) {
        return navigator.userAgentData.mobile;
      }
      return /Mobi|Android|iPhone|iPad|iPod/i.test(navigator.userAgent);
    }

    function documentEventExists(eventName) {
      const events = $._data(document, 'events');
      if (debug) console.log(events);
      return events && events[eventName] !== undefined;
    }

    $(document).on('loadMainSection', function(event, data) {
      const {
        sectionId,
        url,
        forceReload = false
      } = data;
      if (debug) console.log('data:', data);

      function load(sectionId, url) {
        let modifiedUrl = url;
        if (!url.includes('loadedToId')) {
          modifiedUrl = (url.includes('?')) ? `${url}&loadedToId=${sectionId}` : `${url}?loadedToId=${sectionId}`;
        }
        if (debug) console.log(`Loading ${modifiedUrl} into ${sectionId}`);
        $(`#${sectionId}`).load(modifiedUrl, function() {
          $(document).trigger('mainSectionLoaded', {
            sectionId
          });
        });
      }

      if (!$(`#${sectionId}`).length) {
        // The section does not yet exist.
        if (debug) console.log(`Creating section ${sectionId}`);
        $('.main-section').hide(); // Hide all other sections
        $('#main-content').append(`<section id="${sectionId}" class="main-section"></section>`);
        load(sectionId, url);
      } else if (forceReload) {
        if (debug) console.log(`Reloading section ${sectionId}`);
        load(sectionId, url);
      }

      if (!$(`#${sectionId}`).is(':visible')) {
        // If the section is not visible, show it.
        if (debug) console.log(`Showing section ${sectionId}`);
        $('.main-section').hide(); // Hide all other sections
        $(`#${sectionId}`).show(0, function() {
          $(document).trigger('mainSectionShown', {
            sectionId
          });
        });
      }
    });

    $(document).on('menuSelect', function(event, menuId) {
      if (debug) console.log('selectMenu:', menuId);
      switch (menuId) {
        case 'dashboard':
          $(document).trigger('loadMainSection', {
            sectionId: menuId,
            url: 'section.dashboard.php'
          });
          break;
        case 'calendar':
          $(document).trigger('loadMainSection', {
            sectionId: menuId,
            url: 'section.calendar.php'
          });
          break;
        case 'trips':
        case 'events':
        case 'reservations':
        case 'vehicles':
        case 'users':
        case 'departments':
        case 'guests':
        case 'locations':
        case 'airlines':
        case 'airports':
          $(document).trigger('loadMainSection', {
            sectionId: menuId,
            url: `section.list-${menuId}.php`
          });
          break;
        case 'flights':
          $(document).trigger('loadMainSection', {
            sectionId: menuId,
            url: `section.list-${menuId}.php`,
            forceReload: true
          });
          break;
        case 'blockouts':
          $(document).trigger('loadMainSection', {
            sectionId: menuId,
            url: 'section.list-blockout-dates.php'
          });
          break;
        case 'driverNotes':
          $(document).trigger('loadMainSection', {
            sectionId: menuId,
            url: 'section.list-driver-notes.php'
          });
          break;
        case 'airportLocations':
          $(document).trigger('loadMainSection', {
            sectionId: menuId,
            url: 'section.list-airport-locations.php'
          });
          break;
        case 'emailTemplates':
          $(document).trigger('loadMainSection', {
            sectionId: menuId,
            url: 'section.list-email-templates.php'
          });
          break;
        case 'config':
          $(document).trigger('loadMainSection', {
            sectionId: menuId,
            url: 'section.edit-config.php?node=organization',
            forceReload: true
          });
          break;
        case 'myBlockouts':
          $(document).trigger('loadMainSection', {
            sectionId: menuId,
            url: 'section.list-my-blockout-dates.php'
          });
          break;

        case 'auditTrail':
          $(document).trigger('loadMainSection', {
            sectionId: 'auditTrail',
            url: 'report.audit-trail.php',
            forceReload: true
          });
          break;
        case 'authenticationLog':
          $(document).trigger('loadMainSection', {
            sectionId: 'authenticationLog',
            url: 'report.auth-log.php',
            forceReload: true
          });
          break;

        case 'request':
          $(document).trigger('loadMainSection', {
            sectionId: 'request',
            url: 'section.request.php',
            forceReload: true
          });
          break;

        default:
          break;
      }
      setTimeout(() => {
        console.log($(`[data-rel="${menuId}"]`));
        $('.menu-item, .submenu-item').removeClass('active');
        $(`[data-rel="${menuId}"]`).addClass('active');
      }, 100);
    });

    // ping every minute
    setInterval(async () => {
      const resp = await net.get('/api/ping.php');
      if (!resp.result) location.reload();
    }, 60 * 1000);


    function buildAutoComplete(data) {
      const {
        selector,
        apiUrl,
        searchFields
      } = data;
      const options = {
        fullWidth: false,
        liveServer: true,
        server: apiUrl,
        onSelectItem: data => {
          $(`#${selector}`)
            .data('id', data.value)
            .data('value', data.label)
            .removeClass('is-invalid');
          if (data.type) $(`#${selector}`).data('type', data.type);
          $(`#${selector}`).trigger('change');
        },
        fixed: true,
      };
      if (searchFields) options.searchFields = searchFields;
      const ac = new Autocomplete(document.getElementById(selector), options);
      return ac;
    }

    $(document).ajaxStop(() => {
      $('.datetime:not(.short):not(.formatted)').toArray().forEach(item => {
        const value = moment($(item).html(), 'YYYY-MM-DD HH:mm:ss');
        if (value.isValid()) {
          // $(item).addClass('formatted').html(value.format('llll'));
          $(item).addClass('formatted').html(`<div>${value.format('ddd MMM D')}</div><div class="fw-light" style="font-size:smaller">${value.format('h:mma')}</div>`);
        }
      });
    });

  </script>

</body>

</html>