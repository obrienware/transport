<div class="container">
  <div class="row">
    <div class="col">
      <h1 class="fw-lighter text-bg-primary text-center py-2 mt-3">Transport</h1>
    </div>
  </div>

  <h2>Great! Trip Complete!</h2>
  
  <h4>About the vehicle</h4>

  <!-- About the vehicle -->
  <div class="mb-3">
    Any issues with the vehicle?
    <div>
      <div class="btn-group" role="group" aria-label="">
        <input type="radio" class="btn-check" name="trip-vehicle-issues" id="btn-trip-vehicle-issue-yes" autocomplete="off">
        <label class="btn btn-outline-primary" for="btn-trip-vehicle-issue-yes">yes</label>

        <input type="radio" class="btn-check" name="trip-vehicle-issues" id="btn-trip-vehicle-issue-no" autocomplete="off">
        <label class="btn btn-outline-primary" for="btn-trip-vehicle-issue-no">no</label>
      </div>
    </div>
  </div>

  <section class="" id="vehicle-issues-section">
    <div class="mb-3">
      <label for="vehicle-issues" class="form-label">Please describe the issue(s)</label>
      <textarea class="form-control" id="vehicle-issues" rows="2"></textarea>
    </div>
  
    <div class="mb-3">
      Is the check-engine light on?
      <div>
        <div class="btn-group" role="group" aria-label="">
          <input type="radio" class="btn-check" name="trip-vehicle-checkengine" id="btn-trip-vehicle-check-engine-yes" autocomplete="off">
          <label class="btn btn-outline-primary" for="btn-trip-vehicle-check-engine-yes">yes</label>

          <input type="radio" class="btn-check" name="trip-vehicle-checkengine" id="btn-trip-vehicle-check-engine-no" autocomplete="off">
          <label class="btn btn-outline-primary" for="btn-trip-vehicle-check-engine-no">no</label>
        </div>
      </div>
    </div>
  </section>

  <div class="mb-3">
    <label for="mileage" class="form-label">Current vehicle mileage</label>
    <input type="number" class="form-control" id="mileage" placeholder="">
  </div>

  <div class="mb-3">
    <label for="fuel-level" class="form-label">How much fuel in the vehicle?</label>
    <input type="range" class="form-range" min="0" max="100" step="10" id="fuel-level" value="0">
  </div>

  <div class="mb-3">
    Is the vehicle clean (inside)?
    <div>
      <div class="btn-group" role="group" aria-label="">
        <input type="radio" class="btn-check" name="trip-vehicle-clean" id="btn-trip-vehicle-clean-yes" autocomplete="off">
        <label class="btn btn-outline-primary" for="btn-trip-vehicle-clean-yes">yes</label>

        <input type="radio" class="btn-check" name="trip-vehicle-clean" id="btn-trip-vehicle-clean-no" autocomplete="off">
        <label class="btn btn-outline-primary" for="btn-trip-vehicle-clean-no">no</label>
      </div>
    </div>
  </div>
  
  <div class="mb-2">
    Is the vehicle clean (outside)?
    <div>
      <div class="btn-group" role="group" aria-label="">
        <input type="radio" class="btn-check" name="trip-vehicle-clean-o" id="btn-trip-vehicle-clean-o-yes" autocomplete="off">
        <label class="btn btn-outline-primary" for="btn-trip-vehicle-clean-o-yes">yes</label>

        <input type="radio" class="btn-check" name="trip-vehicle-clean-o" id="btn-trip-vehicle-clean-o-no" autocomplete="off">
        <label class="btn btn-outline-primary" for="btn-trip-vehicle-clean-o-no">no</label>
      </div>
    </div>
  </div>
  
  <div class="mb-2">
    Does the vehicle need restocking (refreshments)?
    <div>
      <div class="btn-group" role="group" aria-label="">
        <input type="radio" class="btn-check" name="trip-vehicle-refreshments" id="btn-trip-vehicle-refreshments-yes" autocomplete="off">
        <label class="btn btn-outline-primary" for="btn-trip-vehicle-refreshments-yes">yes</label>

        <input type="radio" class="btn-check" name="trip-vehicle-refreshments" id="btn-trip-vehicle-refreshments-no" autocomplete="off">
        <label class="btn btn-outline-primary" for="btn-trip-vehicle-refreshments-no">no</label>
      </div>
    </div>
  </div>

  <!-- Current location of the vehicle -->
  <div class="mb-3">
    <label for="vehicle-location" class="form-label">Vehicle Location (if deviation from the plan)</label>
    <input 
      type="text" 
      class="form-control" 
      id="vehicle-location" 
      placeholder="Current Location">
  </div>


  <h4 class="mt-5">About the trip</h4>
  <!-- About the trip -->


  <div class="mb-4">
    How are the road conditions?
    <div class="rating-container mt-1 text-center">
      <input type="hidden" id="road-conditions" class="rating"  data-filled="fa-solid fa-star fa-lg" data-empty="fa-regular fa-star fa-lg">
    </div>
  </div>

  <div class="mb-4">
    How are the weather conditions?
    <div class="rating-container mt-1 text-center">
      <input type="hidden" id="weather-conditions" class="rating"  data-filled="fa-solid fa-star fa-lg" data-empty="fa-regular fa-star fa-lg">
    </div>
  </div>

  <div class="mb-3">
    Any issues with the guest/group?
    <div>
      <div class="btn-group" role="group" aria-label="">
        <input type="radio" class="btn-check" name="trip-group-issues" id="btn-trip-guest-issue-yes" autocomplete="off">
        <label class="btn btn-outline-primary" for="btn-trip-guest-issue-yes">yes</label>

        <input type="radio" class="btn-check" name="trip-group-issues" id="btn-trip-guest-issue-no" autocomplete="off">
        <label class="btn btn-outline-primary" for="btn-trip-guest-issue-no">no</label>
      </div>
    </div>
  </div>
  <div class="mb-3 d-none" id="guest-issues-section">
    <label for="guest-issues" class="form-label">Please describe the issue(s)</label>
    <textarea class="form-control" id="guest-issues" rows="2"></textarea>
  </div>




  <div class="mb-4">
    How would you rate the trip overall
    <div class="rating-container mt-1 text-center">
      <input type="hidden" id="trip-rating" class="rating"  data-filled="fa-solid fa-star fa-lg" data-empty="fa-regular fa-star fa-lg">
    </div>
  </div>

  <div class="mb-4">
    <label for="vehicle-comments" class="form-label">Any othe comments?</label>
    <textarea class="form-control" id="vehicle-comments" rows="2"></textarea>
  </div>



  <div class="row my-5">
    <div class="col d-flex justify-content-around">
      <div>
        <button id="btn-finish-survey" class="btn btn-primary btn-lg px-5">Finish</button>
      </div>
    </div>
  </div>

  
</div>

<script>
  $(async ƒ => {

    const tripId = <?=$_REQUEST['tripId'] ?: 'null'?>;

    new Autocomplete(document.getElementById('vehicle-location'), {
      fullWidth: true,
      liveServer: true,
      server: '/api/get.autocomplete-locations.php',
      searchFields: ['label', 'short_name'],
      onSelectItem: (data) => {
        $('#vehicle-location')
          .data('id', data.value)
          .data('type', data.type)
          .data('value', data.label)
          .removeClass('is-invalid');
      }
    });


    $('input:hidden').rating();
    $('#vehicle-issues-section').addClass('d-none'); // We're doing it like this because the rating system seems to do wierd things with this if it's hidden from the outset

    $('#btn-trip-guest-issue-yes').off('click').on('click', function () {
      $('#guest-issues-section').removeClass('d-none')
    });
    $('#btn-trip-guest-issue-no').off('click').on('click', function () {
      $('#guest-issues-section').addClass('d-none')
    });

    $('#btn-trip-vehicle-issue-yes').off('click').on('click', function () {
      $('#vehicle-issues-section').removeClass('d-none')
    });
    $('#btn-trip-vehicle-issue-no').off('click').on('click', function () {
      $('#vehicle-issues-section').addClass('d-none')
    });

    $('#btn-finish-survey').off('click').on('click', async function (e) {
      e.preventDefault;
      const data = getData();
      const resp = await post('api/post.trip-survey.php', data);
      if (app.loadInitialPage) app.loadInitialPage();
      console.log(resp);
    });

    function getData () {
      const data = {
        tripId
      };
      data.ratingTrip = $('#trip-rating').val();
      data.ratingRoad = $('#road-conditions').val();
      data.ratingWeather = $('#weather-conditions').val();
      data.guestIssues = $('#btn-trip-guest-issue-yes').is(':checked');
      if (data.guestIssues) {
        data.guestIssue = $('#guest-issues').val();
      }
      data.vehicleIssues = $('#btn-trip-vehicle-issue-yes').is(':checked');
      if (data.vehicleIssues) {
        data.vehicleIssue = $('#vehicle-issues').val();
      }
      data.mileage = $('#mileage').val().replace(/\D/g,'');
      data.fuel = $('#fuel-level').val();
      if ($('#btn-trip-vehicle-clean-yes').is(':checked')) data.interiorClean = true;
      if ($('#btn-trip-vehicle-clean-no').is(':checked')) data.interiorClean = false;
      if ($('#btn-trip-vehicle-clean-o-yes').is(':checked')) data.exteriorClean = true;
      if ($('#btn-trip-vehicle-clean-o-no').is(':checked')) data.exteriorClean = false;
      if ($('#btn-trip-vehicle-refreshments-yes').is(':checked')) data.restock = true;

      if ($('#btn-trip-vehicle-check-engine-yes').is(':checked')) data.checkEngine = true;
      data.comments = $('#vehicle-comments').val();
      data.locationId = $('#vehicle-location').data('id');

      console.log('data:', data);
      return data;

      // TODO: We need the driver to specify if they dropped the vehicle off at a different location
    }
  });
</script>