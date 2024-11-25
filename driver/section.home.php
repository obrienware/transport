<div class="container">
  <div class="row">
    <div class="col">
      <h1 class="fw-lighter text-bg-primary text-center py-2 mt-3">Transport</h1>
    </div>
  </div>
  <div id="main">

  <?php
    include 'inc.trips-in-progress.php';
    if ($trips) {
      $tripId = $trips[0]->id;
      include 'inc.trip-in-progress.php';
    } else {
      include 'inc.upcoming-trips.php';
    }
  ?>

  </div>
  <div class="row mt-5 d-none">
    <div class="col">
      <button class="btn btn-primary btn-logout">
        <i class="fa-solid fa-arrow-right-to-bracket"></i>
      </button>
    </div>
  </div>

</div>
