<div class="modal" tabindex="-1" id="vehicleLocationModal">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Update Vehicle Location</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">

        <div class="container-fluid">

          <div class="row">
            <div class="col">
              <div class="mb-3">
                <label for="current-location" class="form-label">Current Location</label>
                <input type="text" class="form-control" id="current-location" placeholder="" />
              </div>
            </div>
          </div>

        </div>

      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        <button id="btn-update-location" type="button" class="btn btn-primary">Update</button>
      </div>
    </div>
  </div>
</div>

<script>

$(async ƒ => {

  buildAutoComplete({
    selector: 'current-location',
    apiUrl: '/api/get.autocomplete-locations.php',
    searchFields: ['label', 'short_name'],
  });

  let updateLocationCallback = null;

  if (!documentEventExists('getVehicleLocation')) {
    $(document).on('getVehicleLocation', function(event, callback) {
      updateLocationCallback = callback;
      const modalId = '#vehicleLocationModal';
      $(`${modalId}.modal input[type="checkbox"]`).prop('indeterminate', true).prop('checked', false);
      $(`${modalId}.modal input`).val('');
      $(`${modalId}.modal input[type="range"]`).val('0');
      $(modalId).modal('show');
    });
  }

  $('#btn-update-location').off('click').on('click', function(e) {
    if ($('#current-location').val()) {
      const locationId = $('#current-location').data('id');
      if (typeof updateLocationCallback === 'function') {
        updateLocationCallback(locationId);
      }
    }
    $('#vehicleLocationModal').modal('hide');
  });

});
</script>