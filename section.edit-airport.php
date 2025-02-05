<?php
require_once 'autoload.php';

use Transport\Airport;
use Generic\Utils;

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT, FILTER_NULL_ON_FAILURE);
$id = $id === false ? null : $id;
$airport = new Airport($id);

if (!is_null($id) && !$airport->getId()) {
  exit(Utils::showResourceNotFound());
}
?>

<div class="container mt-2">
  <?php if ($airport->getId()): ?>
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

      <div class="col-3 mb-3">
        <label for="_airport-traveltime" class="form-label">Travel Time (in minutes)</label>
        <input type="number" class="form-control" id="_airport-traveltime" placeholder="" value="<?=$airport->travelTime?>">
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
        <?php if ($airport->getId()): ?>
          <button class="btn btn-outline-danger px-4" id="btn-delete-airport">Delete</button>
        <?php endif; ?>
        <button class="btn btn-primary px-4" id="btn-save-airport">Save</button>
      </div>
    </div>

  </div>
</div>

<script type="module">
  import * as input from '/js/formatters.js';
  import * as ui from '/js/notifications.js';
  import * as net from '/js/network.js';

  $(async ƒ => {

    const airportId = <?=$airport->getId() ?: 'null'?>;

    function getData()
    {
      const data = {
        id: airportId,
        iata: input.cleanUpperVal('#_iata'),
        name: input.cleanProperVal('#_airport-name'),
        stagingLocationId: $('#_airport-staging-location').data('id'),
        leadTime: input.cleanDigitsVal('#_airport-leadtime'),
        travelTime: input.cleanDigitsVal('#_airport-traveltime'),
        arrivalInstructions: input.cleanVal('#_airport-arrival-instructions-small'),
        arrivalInstructionsGroup: input.cleanVal('#_airport-arrival-instructions-group'),
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


    $('#btn-save-airport').on('click', async ƒ => {
      const data = getData();
      const resp = await net.post('/api/post.save-airport.php', data);
      if (resp?.result) {
        $(document).trigger('airportChange', {airportId});
        app.closeOpenTab();
        if (airportId) return ui.toastr.success('Airport saved.', 'Success');
        return ui.toastr.success('Airport added.', 'Success')
      }
      ui.toastr.error(resp .result.errors[2], 'Error');
    });

    $('#btn-delete-airport').on('click', async ƒ => {
      if (await ui.ask('Are you sure you want to delete this airport?')) {
        const resp = await net.get('/api/get.delete-airport.php', {
          id: airportId
        });
        if (resp?.result) {
          $(document).trigger('airportChange', {airportId});
          app.closeOpenTab();
          return ui.toastr.success('Airport deleted.', 'Success')
        }
        ui.toastr.error('There seems to be a problem deleting airport.', 'Error');
      }
    });

  });

</script>
