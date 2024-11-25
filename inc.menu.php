<nav data-bs-theme="dark" class="navbar navbar-expand-lg bg-body-tertiary sticky-top">
  <div class="container-fluid">
    <a class="navbar-brand" href="#">
      <img src="/images/logo.svg" style="height:1.75em"/>
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
        <li class="nav-item dropdown" data-bs-theme="light">
          <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
            Vehicles
          </a>
          <ul class="dropdown-menu">
            <li><a class="dropdown-item menu-item" href="#section.list-vehicles.php" data-target-id="list-vehicles" data-target-label="Vehicles">View</a></li>
            <!-- <li><hr class="dropdown-divider"></li> -->
            <li><a class="dropdown-item menu-item" href="#section.view-maintenance.php" data-target-id="view-maintenance">Maintenance</a></li>
            <li><a class="dropdown-item menu-item" href="#section.view-snags.php" data-target-id="view-snags">Snags</a></li>
          </ul>
        </li>
        <li class="nav-item">
          <a class="nav-link menu-item" href="#section.list-trips.php" data-target-id="list-trips">Trips</a>
        </li>
        <li class="nav-item">
          <a class="nav-link menu-item" href="#section.list-events.php" data-target-id="list-events">Events</a>
        </li>
<!--         
        <li class="nav-item">
          <a class="nav-link" href="#">Pricing</a>
        </li>
        <li class="nav-item">
          <a class="nav-link disabled" aria-disabled="true">Disabled</a>
        </li>
 -->

        <li class="nav-item dropdown" data-bs-theme="light">
          <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
            System
          </a>
          <ul class="dropdown-menu">
            <li><a class="dropdown-item menu-item" href="#section.list-users.php" data-target-id="list-users">Users</a></li>
            <li><a class="dropdown-item menu-item" href="#section.list-departments.php" data-target-id="list-departments">Departments</a></li>
            <li><a class="dropdown-item menu-item" href="#section.list-guests.php" data-target-id="list-guests">Guests/Groups</a></li>
            <li><a class="dropdown-item menu-item" href="#section.list-locations.php" data-target-id="list-locations">Locations</a></li>
            <li><a class="dropdown-item menu-item" href="#section.list-airlines.php" data-target-id="list-airlines">Airlines</a></li>
            <li><a class="dropdown-item menu-item" href="#section.list-airports.php" data-target-id="list-airports">Airports</a></li>
            <li>
              <div class="nav-item dropdown-submenu">
                <a href="#" role="button" data-toggle="dropdown" class="dropdown-item dropdown-toggle">Config <i class="fa-duotone fa-solid fa-caret-right"></i></a>
                <div class="nav-item dropdown-menu">
                  <a class="dropdown-item menu-item" href="#section.edit-config.php?node=system" data-target-id="edit-config" data-target-label="System (edit)">System</a>
                </div>
              </div>
            </li>
          </ul>
        </li>
      </ul>

      <ul class="navbar-nav">
        <li class="nav-item dropdown" data-bs-theme="light">
          <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
            <i class="fa-duotone fa-user fa-lg"></i>
          </a>
          <ul class="dropdown-menu dropdown-menu-end">
            <li><a class="dropdown-item menu-item" href="#section.list-my-blockout-dates.php" data-target-id="list-blockout-dates" data-target-label="Blockouts">My Blockout Dates</a></li>
            <li><a class="dropdown-item" href="mailto:support@obrienware.com">Email Support</a></li>
            <li><hr class="dropdown-divider"></li>
            <li><a class="dropdown-item" href="page.new-password.php">Change Password</a></li>
            <li><a class="dropdown-item" href="logout.php">Log out <?=$user->firstName?></a></li>
            <li><hr class="dropdown-divider"></li>
            <li><a class="dropdown-item menu-item" href="#section.release-notes.php" data-target-id="list-release-notes">Release Notes</a></li>
          </ul>
        </li>
      </ul>

    </div>
  </div>
</nav>

<script type="text/javascript">

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
