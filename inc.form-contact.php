<div class="modal" tabindex="-1" id="contactModal">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">New Contact</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        
        <div class="container-fluid">

          <div class="row">
            <div class="col">
              <div class="mb-3">
                <label for="contact-first-name" class="form-label">First Name</label>
                <input type="text" class="form-control" id="contact-first-name" placeholder="First Name">
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col">
              <div class="mb-3">
                <label for="contact-last-name" class="form-label">Last Name</label>
                <input type="text" class="form-control" id="contact-last-name" placeholder="Last Name">
              </div>
            </div>
          </div>

          <div class="row">
            <div class="col">
              <div class="mb-3">
                <label for="contact-phone-number" class="form-label">Phone Number</label>
                <input type="tel" class="form-control" id="contact-phone-number" placeholder="Phone Number">
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col">
              <div class="mb-3">
                <label for="contact-email-address" class="form-label">Email Address</label>
                <input type="email" class="form-control" id="contact-email-address" placeholder="Email Address">
              </div>
            </div>
          </div>
          
        </div>

      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        <button id="btn-add-contact" type="button" class="btn btn-primary">Add</button>
      </div>
    </div>
  </div>
</div>

<script type="module">
  import * as input from '/js/formatters.js';
  import * as ui from '/js/notifications.js';
  import * as net from '/js/network.js';

  if (typeof ContactClass === 'undefined') {

    window.ContactClass = class {
      static modalId;
      static modal;
      static onUpdate;

      constructor (modalId, options) {
        this.modalId = modalId;
        this.modal = new bootstrap.Modal(modalId);
        if (options?.onUpdate) this.onUpdate = options.onUpdate;
        $(modalId).on('show.bs.modal', e => {
          this.reset();
        });

        $('#btn-add-contact').on('click', e => {
          if (typeof this.onUpdate === 'function') {
            this.modal.hide();
            this.onUpdate(e, this.getValues());
          }
        });
      }

      reset () {
        $(`${this.modalId}.modal input`).val('');
      }
      
      getValues () {
        const data = {
          firstName: input.cleanVal('#contact-first-name'),
          lastName: input.cleanVal('#contact-last-name'),
          emailAddress: input.cleanVal('#contact-email-address'),
          phoneNumber: input.cleanVal('#contact-phone-number'),
        };
        return data;
      }

      show () {
        this.modal.show();
      }

    }

  }

</script>
