<?php
require_once 'class.user.php';
if (!isset($user)) $user = new User($_SESSION['user']->id);
?>
<nav data-bs-theme="dark" class="navbar navbar-expand-lg bg-body-tertiary sticky-top">
  <div class="container-fluid">
    <a class="navbar-brand" href="#">
      <img src="/images/logo.svg" style="height:1.75em" class="bg-light bg-opacity-75 p-1 rounded"/>
      Transport
    </a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse justify-content-between" id="navbarNav">
      <ul class="navbar-nav">
        <!-- <li class="nav-item">
          <a class="nav-link active" aria-current="page" href="">Home</a>
        </li> -->
        <?php if (array_search($_SESSION['view'], ['developer','manager','driver']) !== false):?>
          <li class="nav-item">
            <a class="nav-link menu-item" href="#section.list-trips.php" data-target-id="list-trips">Trips</a>
          </li>
          <li class="nav-item">
            <a class="nav-link menu-item" href="#section.list-events.php" data-target-id="list-events">Events</a>
          </li>
        <?php endif;?>
        <?php if (array_search($_SESSION['view'], ['developer','manager','driver']) !== false):?>
          <li class="nav-item">
            <a class="nav-link menu-item" href="#section.list-vehicles.php" data-target-id="list-vehicles">Vehicles</a>
          </li>
          <li class="nav-item">
            <a class="nav-link menu-item" href="#section.list-flights.php" data-target-id="list-flights">Flights</a>
          </li>
        <?php endif; ?>
        <?php if (array_search($_SESSION['view'], ['developer','manager']) !== false):?>
          <li class="nav-item">
            <a class="nav-link menu-item" href="#section.list-blockout-dates.php" data-target-id="list-block-outs">Block Outs</a>
          </li>
        <?php endif; ?>
<!--         
        <li class="nav-item">
          <a class="nav-link" href="#">Pricing</a>
        </li>
        <li class="nav-item">
          <a class="nav-link disabled" aria-disabled="true">Disabled</a>
        </li>
 -->
        <?php if (array_search($_SESSION['view'], ['developer','manager']) !== false):?>
          <li class="nav-item dropdown" data-bs-theme="light">
            <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
              Reports
            </a>
            <ul class="dropdown-menu">
              <li><a class="dropdown-item menu-item" href="#report.audit-trail.php" data-target-id="report" data-target-label="Report">Audit Trail</a></li>
              <li><a class="dropdown-item menu-item" href="#report.auth-log.php" data-target-id="report" data-target-label="Report">Authentication Log</a></li>
            </ul>
          </li>
        <?php endif; ?>

        <?php if (array_search($_SESSION['view'], ['developer','manager','admin']) !== false):?>
          <li class="nav-item dropdown" data-bs-theme="light">
            <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
              System
            </a>
            <ul class="dropdown-menu">
              <li><a class="dropdown-item menu-item" href="#section.list-users.php" data-target-id="list-users">Users</a></li>
              <li><a class="dropdown-item menu-item" href="#section.list-departments.php" data-target-id="list-departments">Departments</a></li>
              <li><a class="dropdown-item menu-item" href="#section.list-guests.php" data-target-id="list-guests">Contact List</a></li>
              <li><a class="dropdown-item menu-item" href="#section.list-locations.php" data-target-id="list-locations">Locations</a></li>
              <li><a class="dropdown-item menu-item" href="#section.list-airlines.php" data-target-id="list-airlines">Airlines</a></li>
              <li><a class="dropdown-item menu-item" href="#section.list-airports.php" data-target-id="list-airports">Airports</a></li>
              <li><a class="dropdown-item menu-item" href="#section.list-airport-locations.php" data-target-id="list-airport-locations">Airport Locations</a></li>
              <?php if ($user->hasRole(['admin','developer'])):?>
              <li>
                <div class="nav-item dropdown-submenu">
                  <a href="#" role="button" data-toggle="dropdown" class="dropdown-item dropdown-toggle">Config <i class="fa-duotone fa-solid fa-caret-right"></i></a>
                  <div class="nav-item dropdown-menu">
                    <?php if ($user->hasRole(['developer'])):?>
                      <a class="dropdown-item menu-item" href="#section.edit-config.php?node=system" data-target-id="edit-config-a" data-target-label="System (edit)">System</a>
                    <?php endif; ?>
                    <a class="dropdown-item menu-item" href="#section.edit-config.php?node=organization" data-target-id="edit-config-b" data-target-label="Org Config (edit)">Organization</a>
                  </div>
                </div>
              </li>
              <?php endif; ?>
            </ul>
          </li>
        <?php endif; ?>
      </ul>

      <ul class="navbar-nav">
        <li class="nav-item dropdown" data-bs-theme="light">
          <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
            <i class="fa-duotone fa-user fa-lg"></i>
          </a>
          <ul class="dropdown-menu dropdown-menu-end">
            <li>
              <div class="nav-item dropdown-submenu dropstart">
                <a href="#" role="button" data-toggle="dropdown" class="dropdown-item dropdown-toggle">View (<?=ucwords($_SESSION['view'])?>)</a>
                <ul class="dropdown-menu">
                  <?php if ($user->hasRole(['admin','developer'])):?>
                    <li><a class="dropdown-item" href="#" onclick="changeView('admin')">Admin</a></li>
                  <?php endif; ?>
                  <?php if ($user->hasRole(['developer'])):?>
                    <li><a class="dropdown-item" href="#" onclick="changeView('developer')">Developer</a></li>
                  <?php endif; ?>
                  <?php if ($user->hasRole(['manager','developer'])):?>
                    <li><a class="dropdown-item" href="#" onclick="changeView('manager')">Manager</a></li>
                  <?php endif; ?>
                  <?php if ($user->hasRole(['driver','developer'])):?>
                    <li><a class="dropdown-item" href="#" onclick="changeView('driver')">Driver</a></li>
                  <?php endif; ?>
                  <?php if ($user->hasRole(['requestor','developer'])):?>
                    <li><a class="dropdown-item" href="#" onclick="changeView('requestor')">Requestor</a></li>
                  <?php endif; ?>
                </ul>
              </div>
            </li>
            <?php if ($user->hasRole(['manager','driver'])):?>
              <li><a class="dropdown-item menu-item" href="#section.list-my-blockout-dates.php" data-target-id="list-blockout-dates" data-target-label="Blockouts">My Blockout Dates</a></li>
              <li><a class="dropdown-item menu-item" href="#section.edit-preferences.php" data-target-id="edit-preferences" data-target-label="Preferences">My Preferences</a></li>
            <?php endif; ?>
            <li><a class="dropdown-item" href="mailto:support@obrienware.com">Email Support</a></li>
            <li><hr class="dropdown-divider"></li>
            <li><a class="dropdown-item menu-item" href="#section.release-notes.php" data-target-id="list-release-notes">Release Notes</a></li>
            <li><hr class="dropdown-divider"></li> 
            <li><a class="dropdown-item" href="page.new-password.php">Change Password</a></li>
            <li><a class="dropdown-item" href="logout.php">Log out <?=$user->firstName?></a></li>
          </ul>
        </li>
      </ul>

    </div>
  </div>
</nav>

<script type="text/javascript">

  function changeView(view) {
    console.log('Changing view to: ', view);
    location.href = 'change-view.php?view='+view;
  }

  $(async ƒ => {

    $('.menu-item').on('click', function(e) {
      e.preventDefault();
      const targetTab = $(this).data('target-id');
      const tabLabel = $(this).data('target-label') || $(this).html();
      const sectionToLoad = $(this).attr('href').slice(1); // Remove the '#'
      app.openTab(targetTab, tabLabel, sectionToLoad);
    });

  });

</script>
