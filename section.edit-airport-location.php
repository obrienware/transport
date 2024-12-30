<?php
require_once 'class.airport.php';
require_once 'class.airline.php';
require_once 'class.airport-location.php';
$airportLocation = new AirportLocation($_REQUEST['id']);
?>
<?php if (isset($_REQUEST['id']) && !$airportLocation->id): ?>

  <div class="container-fluid text-center">
    <div class="alert alert-danger mt-5 w-50 mx-auto">
      <h1 class="fw-bold">Oh dear!</h1>
      <p class="lead">I can't seem to find that airport location! <i class="fa-duotone fa-solid fa-face-thinking"></i></p>
    </div>
  </div>

<?php else: ?>

  <div class="container mt-2">
    <?php if ($airportLocation->id): ?>
      <h2>Edit Airport Location</h2>
    <?php else: ?>
      <h2>Add Airport Location</h2>
    <?php endif; ?>

    <div class="row">
      <div class="col">
        <label for="_airport" class="form-label">Airport</label>
        <select class="form-select" id="_airport">
          <option value="">Select Airport</option>
          <?php foreach (Airport::getAirports() as $item): ?>
            <option value="<?=$item->id?>" <?=($item->id == $airportLocation->airportId) ? 'selected' : ''?>><?=$item->name?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="col">
        <label for="_airline" class="form-label">Airline</label>
        <select class="form-select" id="_airline">
          <option value="">Select Airline</option>
          <?php foreach (Airline::getAirlines() as $item): ?>
            <option value="<?=$item->id?>" <?=($item->id == $airportLocation->airlineId) ? 'selected' : ''?>><?=$item->name?></option>
          <?php endforeach; ?>
        </select>
      </div>
    </div>

    <div class="row">
      <div class="col">
        <label for="_type" class="form-label">Type</label>
        <select class="form-select" id="_type">
          <option value="">Select Type</option>
          <option value="Arrival" <?=($airportLocation->type == 'Arrival') ? 'selected' : ''?>>Arrival</option>
          <option value="Departure" <?=($airportLocation->type == 'Departure') ? 'selected' : ''?>>Departure</option>
        </select>
      </div>

      <div class="col">
        <label for="_location" class="form-label">Location</label>
        <input 
          type="text" 
          class="form-control" 
          id="_location" 
          placeholder="Location" 
          value="<?=$airportLocation->location->name?>" 
          data-id="<?=$airportLocation->locationId?>"
          data-value="<?=$airportLocation->location->name?>">
      </div>
    </div>

    <div class="row mt-3">
      <div class="col d-flex justify-content-between">
        <?php if ($airportLocation->id): ?>
          <button id="btn-delete-airport-location" class="btn btn-outline-danger">Delete</button>
        <?php endif; ?>

        <button id="btn-save-airport-location" class="btn btn-outline-primary">Save</button>
      </div>
    </div>

  </div>

  <script type="text/javascript">

    $(async ƒ => {

      const airportLocationId = <?=$airportLocation->id ?: 'null'?>;

      new Autocomplete(document.getElementById('_location'), {
        fullWidth: true,
        // highlightTyped: true,
        liveServer: true,
        server: '/api/get.autocomplete-locations.php',
        searchFields: ['label', 'short_name'],
        onSelectItem: (data) => {
          $('#_location')
            .data('id', data.value)
            .data('value', data.label)
            .removeClass('is-invalid');
        },
        fixed: true,
      });

      async function getData() {
        const data = {};
        if (airportLocationId) data.id = airportLocationId;
        data.airportId = $('#_airport').val();
        data.airlineId = $('#_airline').val();
        data.type = $('#_type').val();
        data.locationId = $('#_location').data('id');
        return data;
      }

      $('#btn-save-airport-location').off('click').on('click', async ƒ => {
        const data = await getData();
        const resp = await post('/api/post.save-airport-location.php', data);
        if (resp?.result?.result) {
          $(document).trigger('airportLocationChange', {airportLocationId});
          app.closeOpenTab();
          if (airportLocationId) return toastr.success('Airport Location saved.', 'Success');
          return toastr.success('Airport Location added.', 'Success')
        }
        toastr.error(resp .result.errors[2], 'Error');
        console.log(resp);
      });

      $('#btn-delete-airport-location').off('click').on('click', async ƒ => {
        if (await ask('Are you sure you want to delete this airport location?')) {
          const resp = await get('/api/get.delete-airport-location.php', {
            id: airportLocationId
          });
          if (resp?.result) {
            $(document).trigger('airportLocationChange', {airportLocationId});
            app.closeOpenTab();
            return toastr.success('Airport Location deleted.', 'Success')
          }
          console.log(resp);
          toastr.error('There seems to be a problem deleting airport location.', 'Error');
        }
      });

    });

  </script>

<?php endif; ?>