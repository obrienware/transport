<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <link rel="apple-touch-icon" href="/apple-touch-icon.png">
  <link rel="apple-touch-icon" sizes="152x152" href="/images/logo-152.png">
  <link rel="apple-touch-icon" sizes="180x180" href="/images/logo-180.png">
  <link rel="apple-touch-icon" sizes="167x167" href="/images/logo-167.png">
  <meta name="apple-mobile-web-app-title" content="Transport">
  <meta name="mobile-web-app-capable" content="yes">

  <title>Transport</title>
  <!-- We want the full-blown jQuery lib so we can take advantage of ajax loading, etc. -->
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js" integrity="sha512-v2CJ7UaYy4JwqLDIrZUI/4hqeoQieOmAZNXBeQyjo21dadnwR+8ZaIJVT8EE2iyI61OV8e6M8PP2/4hpQINQ/g==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.3/css/bootstrap.min.css" integrity="sha512-jnSuA4Ss2PkkikSOLtYs8BlYIeeIK1h99ty4YfvRPAlzr377vr3CXDb7sb7eEEBYjDtcYj+AjBH3FLv5uSJuXg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
  <script src="https://kit.fontawesome.com/cc9f38bd60.js" crossorigin="anonymous"></script>

  <!-- Pull to refresh -->
  <script src="https://cdnjs.cloudflare.com/ajax/libs/pulltorefreshjs/0.1.22/index.umd.min.js" integrity="sha512-djmgTiVR15A/7fON+ojDzFYrRsfVkzQZu07ZVb0zLC1OhA2iISP39Lzs05GqSKF0vPjkLzL5hBC+am6po7dKpA==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>

	<!-- Moment -->
	<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.30.1/moment-with-locales.min.js" integrity="sha512-4F1cxYdMiAW98oomSLaygEwmCnIP38pb4Kx70yQYqRwLVCs3DbRumfBq82T08g/4LJ/smbFGFpmeFlQgoDccgg==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/moment-timezone/0.5.46/moment-timezone-with-data.min.js" integrity="sha512-4MAP/CJtK3ASCmbYjYxWAbHWASAx1UYMc1i83cBdQZXegqFfqSZ9WqpmkRGfvzeAI18yvKiDTlgX/TLNMpxkSQ==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
	<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/timeago.js/4.0.2/timeago.full.min.js"></script>

  <script type="text/javascript" src="/js/autocomplete.js"></script>

  <link rel="stylesheet" type="text/css" href="/css/weather-icons.min.css">
  <link rel="stylesheet" type="text/css" href="css/style.css?<?=filemtime('css/style.css')?>">
  <script type="text/javascript" src="js/common.js?<?=filemtime('js/common.js')?>"></script>

  <style>
    #app {
      left:0; right:0; top:0;
      height: 100vh;
      overflow: hidden;
      position: absolute;
    }
    #footer {
      position: absolute;
      left: 0; right: 0; bottom: 0;
      height: 80px;
      background: linear-gradient(238.26deg, #ED5050 0%, #871E8D 43.52%, #478AD9 100%);
    }
    .pane {
      position: absolute;
      top: 0; left: 0; right: 0;
      bottom: 80px;
      overflow-y: auto;
    }
    html, body {
      overscroll-behavior: contain;
    }
  </style>
</head>
<body>
  
  <div id="app" class="d-none">

    <div id="pane-trips" class="pane active">
      <h5 class="bg-primary text-bg-primary p-3 sticky-top">Trips</h5>
      <div id="trips-content"></div>
    </div>

    <div id="pane-vehicles" class="pane d-none">
      <h5 class="bg-primary text-bg-primary p-3 sticky-top">Vehicles</h5>
      <div id="vehicles-content"></div>
    </div>

    <div id="pane-flights" class="pane d-none">
      <h5 class="bg-primary text-bg-primary p-3 sticky-top">Flights</h5>
      <div id="flights-content"></div>
    </div>

    <div id="footer" class="d-flex justify-content-around py-2">
      <div id="tab-trips" class="tab text-center text-light active" data-target="trips">
        <div><i class="fa-solid fa-road fa-xl"></i></div>
        <div style="font-size:x-small">Trips</div>
      </div>
      <div id="tab-vehicles" class="tab text-center text-light text-opacity-50" data-target="vehicles">
        <div><i class="fa-solid fa-cars fa-xl"></i></div>
        <div style="font-size:x-small">Vehicles</div>
      </div>
      <div id="tab-flights" class="tab text-center text-light text-opacity-50" data-target="flights">
        <div><i class="fa-solid fa-plane-up fa-xl"></i></div>
        <div style="font-size:x-small">Flights</div>
      </div>
    </div>
  </div>

  <div id="login-section" class="container d-none">
    <div class="row">
      <div class="col">
        <h1 class="fw-lighter text-bg-primary text-center py-2 mt-3">Transport</h1>
      </div>
    </div>

    <div class="row mb-3">
      <div class="col">
        <label for="user-username" class="form-label">Username</label>
        <input type="text" class="form-control" id="user-username" placeholder="">
      </div>
    </div>
    <div class="row mb-4">
      <div class="col">
        <label for="user-password" class="form-label">Password</label>
        <input type="password" class="form-control" id="user-password" placeholder="">
      </div>
    </div>

    <div class="row">
      <div class="col d-flex justify-content-around">
        <button id="btn-login" class="btn btn-outline-primary px-5">Log In</button>
      </div>
    </div>
  </div>  

  <script>
    $(async ƒ => {

      function selectTab(name) {
        $('.pane').addClass('d-none').removeClass('active');
        $('.tab').addClass('text-opacity-50').removeClass('active');
        $(`#pane-${name}`).removeClass('d-none').addClass('active');
        $(`#tab-${name}`).removeClass('text-opacity-50').addClass('active');
      }

      $('.tab').on('click', function (e) {
        e.preventDefault();
        e.stopPropagation();
        selectTab($(this).data('target'));
      });

      /**
       * Only call this once the user is logged in
       */
      function init() {
        $('#login-section').addClass('d-none');
        $('#app').removeClass('d-none');
        $('#trips-content').load('section.trips.php');
        $('#vehicles-content').load('section.vehicles.php');
        $('#flights-content').load('section.flights.php');
      }

      let user;
      let userString = window.localStorage.getItem('user');
      if (userString) {
        // User has logged in before
        user = JSON.parse(userString);

        // Let's set up the user session (with the server) for simplicity
        user = await get('api/get.user.php', {username: user.username});
        
        userString = JSON.stringify(user);
        window.localStorage.setItem('user', userString);
        init();

      } else {
        // User is not logged in
        $('#login-section').removeClass('d-none');
      }

      $('#user-username').on('keyup', async ƒ => {
        if (ƒ.keyCode === 13) return $('#user-password').select().focus();
      });

      $('#password').on('keyup', async ƒ => {
        if (ƒ.keyCode === 13) return $('#btn-login').click();
      });

      $('#btn-login').on('click', async ƒ => {
        const username = $.trim($('#user-username').val());
        const password = $.trim($('#user-password').val());
        const user = await post('api/post.user-login.php', {username, password});
        if (user === false) {
          return alert('Sorry, username/password is incorrect. Please try again');
        }
        userString = JSON.stringify(user);
        window.localStorage.setItem('user', userString);
        init();
      });


      const standalone = navigator.standalone || window.matchMedia("(display-mode: standalone)").matches;
      if (!standalone) return; // not standalone; no pull to refresh needed

      PullToRefresh.init({
        onRefresh() {
          // We'll need to re-look at how we want to handle this
          // location.reload();
          $('#upcoming-trips').load('section.upcoming-trips.php');
          $('#flights-content').load('section.flights.php');
        },
      });
    });


  </script>  
</body>
</html>