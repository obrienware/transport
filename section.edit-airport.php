<?php
require_once 'class.airport.php';
$airport = new Airport($_REQUEST['id']);
?>
<?php if (isset($_REQUEST['id']) && !$airport->getAirportId()): ?>

  <div class="container-fluid text-center">
    <div class="alert alert-danger mt-5 w-50 mx-auto">
      <h1 class="fw-bold">Oh dear!</h1>
      <p class="lead">I can't seem to find that airport! <i class="fa-duotone fa-solid fa-face-thinking"></i></p>
    </div>
  </div>

<?php else: ?>

  <div class="container mt-2">
    <?php if ($airport->getAirportId()): ?>
      <h2>Edit Airport</h2>
    <?php else: ?>
      <h2>Add Airport</h2>
    <?php endif; ?>
    <div>

      <div class="row">

        <div class="col-3 mb-3">
          <label for="_iata" class="form-label">IATA</label>
          <input type="text" class="form-control" id="_iata" placeholder="Airport Code" value="<?=$airport->IATA?>">
        </div>

        <div class="col mb-3">
          <label for="_airport-name" class="form-label">Name</label>
          <input type="text" class="form-control" id="_airport-name" placeholder="Airport Name" value="<?=$airport->name?>">
        </div>

      </div>

      <div class="row">

        <div class="col-3 mb-3">
          <label for="_airport-leadtime" class="form-label">Lead Time (in minutes)</label>
          <input type="number" class="form-control" id="_airport-leadtime" placeholder="" value="<?=$airport->leadTime?>">
        </div>

        <div class="col">
          <div class="mb-3">
            <label for="_airport-staging-location" class="form-label">Staging Location</label>
            <input 
              type="text" 
              class="form-control" 
              id="_airport-staging-location" 
              placeholder="Staging Location" 
              value="<?=$airport->stagingLocation->name?>" 
              data-id="<?=$airport->stagingLocationId?>"
              data-value="<?=$airport->stagingLocation->name?>">
              <div class="invalid-feedback">Please make a valid selection</div>
          </div>
        </div>

      </div>

      <div class="row">
        <div class="col">
          <div class="mb-3">
            <label for="_airport-arrival-instructions-small" class="form-label">Arrival Instructions (3pax or less)</label>
            <textarea class="form-control" id="_airport-arrival-instructions-small" rows="7"><?=$airport->arrivalInstructions?></textarea>
          </div>
        </div>
        <div class="col">
          <div class="mb-3">
            <label for="_airport-arrival-instructions-group" class="form-label">Arrival Instructions (group)</label>
            <textarea class="form-control" id="_airport-arrival-instructions-group" rows="7"><?=$airport->arrivalInstructionsGroup?></textarea>
          </div>
        </div>
      </div>


      <div class="row my-4">
        <div class="col d-flex justify-content-between">
          <?php if ($airport->getAirportId()): ?>
            <button class="btn btn-outline-danger px-4" id="btn-delete-airport">Delete</button>
          <?php endif; ?>
          <button class="btn btn-primary px-4" id="btn-save-airport">Save</button>
        </div>
      </div>

    </div>
  </div>

  <script type="text/javascript">

    $(async ƒ => {

      const airportId = <?=$airport->getAirportId() ?: 'null'?>;

      function getData()
      {
        const data = {
          id: airportId,
          iata: cleanUpperVal('#_iata'),
          name: cleanProperVal('#_airport-name'),
          stagingLocationId: $('#_airport-staging-location').data('id'),
          leadTime: cleanDigitsVal('#_airport-leadtime'),
          arrivalInstructions: cleanVal('#_airport-arrival-instructions-small'),
          arrivalInstructionsGroup: cleanVal('#_airport-arrival-instructions-group'),
        };
        return data;
      }

      new Autocomplete(document.getElementById('_airport-staging-location'), {
        fullWidth: true,
        liveServer: true,
        server: '/api/get.autocomplete-locations.php',
        searchFields: ['label', 'short_name'],
        onSelectItem: (data) => {
          $('#_airport-staging-location')
            .data('id', data.value)
            .data('value', data.label)
            .removeClass('is-invalid');
        },
        fixed: true,
      });


      $('#btn-save-airport').off('click').on('click', async ƒ => {
        const data = getData();
        console.log(data);
        const resp = await post('/api/post.save-airport.php', data);
        if (resp?.result?.result) {
          $(document).trigger('airportChange', {airportId});
          app.closeOpenTab();
          if (airportId) return toastr.success('Airport saved.', 'Success');
          return toastr.success('Airport added.', 'Success')
        }
        toastr.error(resp .result.errors[2], 'Error');
        console.log(resp);
      });

      $('#btn-delete-airport').off('click').on('click', async ƒ => {
        if (await ask('Are you sure you want to delete this airport?')) {
          const resp = await get('/api/get.delete-airport.php', {
            id: airportId
          });
          if (resp?.result) {
            $(document).trigger('airportChange', {airportId});
            app.closeOpenTab();
            return toastr.success('Airport deleted.', 'Success')
          }
          console.log(resp);
          toastr.error('There seems to be a problem deleting airport.', 'Error');
        }
      });

    });

  </script>

<?php endif; ?>