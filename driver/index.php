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
  <meta name="apple-mobile-web-app-capable" content="yes">

  <title>Transport</title>
  <!-- We want the full-blown jQuery lib so we can take advantage of ajax loading, etc. -->
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js" integrity="sha512-v2CJ7UaYy4JwqLDIrZUI/4hqeoQieOmAZNXBeQyjo21dadnwR+8ZaIJVT8EE2iyI61OV8e6M8PP2/4hpQINQ/g==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.3/css/bootstrap.min.css" integrity="sha512-jnSuA4Ss2PkkikSOLtYs8BlYIeeIK1h99ty4YfvRPAlzr377vr3CXDb7sb7eEEBYjDtcYj+AjBH3FLv5uSJuXg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
  <script src="https://kit.fontawesome.com/e8c53c75a8.js" crossorigin="anonymous"></script>

  <!-- we're using this for our survey -->
  <script src="js/bootstrap-rating.min.js"></script>

  <link rel="stylesheet" type="text/css" href="css/style.css?<?=filemtime('css/style.css')?>">
  <script type="text/javascript" src="js/common.js?<?=filemtime('js/common.js')?>"></script>
</head>
<body>
  <div id="app"></div>

  <script>
    $(async ƒ => {

      app.loadInitialPage = function () {
        $('#app').load('section.home.php');
      }

      app.load = function (url) {
        $('#main').load(url);
      }

      app.goHome = function () {
        $('#app').load('section.home.php');
      }

      app.postTripSurvey = function (tripId) {
        $('#app').load(`section.survey.php?tripId=${tripId}`);
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
        app.loadInitialPage();

      } else {
        // User is not logged in
        $('#app').load('section.login.php');
      }

    });
  </script>  
</body>
</html>