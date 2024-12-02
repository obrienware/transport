<div class="modal" tabindex="-1" id="vehicleUpdateModal">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Update Vehicle Status</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        
        <div class="container-fluid">

          <div class="row">
            <div class="col">
              <div class="mb-3">
                <label for="current-location" class="form-label">Location</label>
                <input 
                  type="text" 
                  class="form-control" 
                  id="current-location" 
                  placeholder=""/>
              </div>
            </div>
          </div>

          <div class="row">
            <div class="col">
              <div class="mb-3">
                <label for="current-mileage" class="form-label">Mileage</label>
                <input type="number" class="form-control" id="current-mileage" placeholder="Mileage" value="<?=$vehicle->mileage?>">
              </div>
            </div>
          </div>

          <div class="row">
            <div class="col">
              <div class="mb-2">
                <label for="current-fuel-level" class="form-label">Fuel Level</label>
                <input type="range" class="form-range" min="0" max="100" step="12.5" id="current-fuel-level" value="0">
              </div>
            </div>
          </div>
          

          <div class="row">
            <div class="col">
              <div class="form-check">
                <input class="form-check-input" type="checkbox" id="current-checkengine-on">
                <label class="form-check-label" for="current-checkengine-on">Check engine light is on</label>
              </div>
              <div class="form-check">
                <input class="form-check-input" type="checkbox" id="current-is-clean-interior">
                <label class="form-check-label" for="current-is-clean-interior">Vehicle is clean inside</label>
              </div>
              <div class="form-check">
                <input class="form-check-input" type="checkbox" id="current-is-clean-exterior">
                <label class="form-check-label" for="current-is-clean-exterior">Vehicle is clean outside</label>
              </div>
              <div class="form-check">
                <input class="form-check-input" type="checkbox" id="current-needs-restocking">
                <label class="form-check-label" for="current-needs-restocking">Needs restocking (refreshments)</label>
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

  if (typeof VehicleUpdateClass === 'undefined') {

    window.VehicleUpdateClass = class {
      static modalId;
      static modal;
      static onUpdate;

      constructor (modalId, options) {
        this.modalId = modalId;
        this.modal = new bootstrap.Modal(modalId);
        if (options?.onUpdate) this.onUpdate = options.onUpdate;
        new Autocomplete(document.getElementById('current-location'), {
          fullWidth: true,
          highlightTyped: true,
          liveServer: true,
          server: '/api/get.autocomplete-locations.php',
          searchFields: ['label', 'short_name'],
          onSelectItem: (data) => {
            $('#current-location')
              .data('id', data.value)
              .data('type', data.type)
              .data('value', data.label)
              .removeClass('is-invalid');
          },
          fixed: true,
        });

        $(modalId).on('show.bs.modal', e => {
          this.reset();
        });

        $('#btn-update-location').on('click', e => {
          if (typeof this.onUpdate === 'function') {
            this.modal.hide();
            this.onUpdate(e, this.getValues());
          }
        });
      }

      reset () {
        $(`${this.modalId}.modal input[type="checkbox"]`).prop('indeterminate', true).prop('checked', false);
        $(`${this.modalId}.modal input`).val('');
        $(`${this.modalId}.modal input[type="range"]`).val('0');
      }
      
      getValues () {
        const data = {};
        if ($('#current-location').val()) data.locationId = $('#current-location').data('id');
        if ($('#current-mileage').val()) data.mileage = $('#current-mileage').val();
        if ($('#current-fuel-level').val() != '0') data.fuelLevel = $('#current-fuel-level').val();
        if (!$('#current-checkengine-on').is(':indeterminate')) {
          data.checkengineOn = $('#current-checkengine-on').is(':checked');
        }
        if (!$('#current-is-clean-interior').is(':indeterminate')) {
          data.isCleanInside = $('#current-is-clean-interior').is(':checked');
        }
        if (!$('#current-is-clean-exterior').is(':indeterminate')) {
          data.isCleanOutside = $('#current-is-clean-exterior').is(':checked');
        }
        if (!$('#current-needs-restocking').is(':indeterminate')) {
          data.needsRestocking = $('#current-needs-restocking').is(':checked');
        }
        return data;
      }

      show () {
        this.modal.show();
      }

    }

  }

</script>
