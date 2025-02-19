<?php
require_once 'autoload.php';

use Transport\{VehicleDocument};
use Generic\InputHandler;

$vehicleId = InputHandler::getInt(INPUT_GET, 'vehicleId');
?>
<input type="hidden" id="vehicleId" value="<?= $vehicleId ?>">

<div class="text-end">
  <button class="btn btn-sm btn-primary mb-2" onclick="$(document).trigger('vehicleDocument:add', <?= $vehicleId ?>)"><i class="fa-solid fa-plus-large"></i></button>
</div>


<?php if ($rows = VehicleDocument::getDocuments($vehicleId)): ?>

  <table class="table table-bordered table-striped">
    <?php foreach ($rows as $row): ?>
      <tr data-id="<?= $row->id ?>">
        <td>
          <div class="d-flex justify-content-between">
            <div>
              <?php if ($row->file_type == 'application/pdf'): ?>
                <i class="fa-solid fa-file-pdf me-2 fa-xl"></i>
              <?php elseif ($row->file_type == 'image/png'): ?>
                <i class="fa-solid fa-file-png me-2 fa-xl"></i>
              <?php elseif ($row->file_type == 'image/jpg' || $row->file_type == 'image/jpeg'): ?>
                <i class="fa-solid fa-file-jpg me-2 fa-xl"></i>
              <?php else: ?>
                <i class="fa-solid fa-file me-2 fa-xl"></i>
              <?php endif; ?>
              <a class="text-reset text-decoration-none text-capitalize" href="/documents/<?= $row->filename ?>" target="_blank"><?= $row->name ?></a>
            </div>
            <div><?= Date('m/d/Y', strtotime($row->created)) ?></div>
          </div>
        </td>
        <td class="text-center align-middle">
          <i class="fa-solid fa-ellipsis fa-xl text-body-tertiary action-icon pointer hidden-content" onclick="$(document).trigger('listActionItem:vehicleDocument', this)"></i>
        </td>

      </tr>
    <?php endforeach; ?>
  </table>

<?php else: ?>

  <div class="d-flex my-3">
    <div class="alert alert-info mx-auto" role="alert">
      <i class="fa-solid fa-info-circle"></i>
      There are no documents for this vehicle at this time.
    </div>
  </div>

<?php endif; ?>


<script>
  if (!documentEventExists('listActionItem:vehicleDocument')) {
    $(document).on('listActionItem:vehicleDocument', async function(e, el) {
      e.stopPropagation();
      e.stopImmediatePropagation();

      const id = $(el).closest('tr').data('id');
      const offset = $(el).offset();
      const myRandomId = Math.random().toString(36).substring(7);

      // Remove any existing dropdown menus
      $(document).trigger('click');

      let additionalItems = '';

      // Create the dropdown menu
      const dropdownMenu = `
        <div id="${myRandomId}" data-id="${id}" class="dropdown-menu show shadow" style="position: absolute; left: ${offset.top}px; top: ${offset.left}px; z-index: 1000;">
          <button class="dropdown-item text-danger" onclick="$(document).trigger('vehicleDocument:delete', ${id})"><i class="fa-duotone fa-regular fa-trash"></i> Delete</button>
        </div>
      `;

      // Append the dropdown menu to the body
      $('body').append(dropdownMenu);

      // Calculate the position of the dropdown menu
      const dropdownElement = $('#' + myRandomId);
      const dropdownWidth = dropdownElement.outerWidth();
      const leftPosition = event.pageX - dropdownWidth;

      // Set the position of the dropdown menu
      dropdownElement.css({
        left: `${leftPosition}px`,
        top: `${event.pageY}px`
      });

      console.log('dropdownElement:', dropdownElement);

      // Remove the dropdown menu when clicking outside
      setTimeout(() => {
        $(document).on('click', function() {
          $('#' + myRandomId).remove();
        });
      }, 100);
    });
  }

  if (!documentEventExists('vehicleDocument:add')) {
    $(document).on('vehicleDocument:add', async (e, vehicleId) => {
      const documentName = await ui.input('Please enter a name/description for this document');
      if (!documentName) return;
      const file = await ui.getFile('Please select a document to upload');
      if (!file) return;
      const formData = new FormData();
      formData.append('vehicleId', vehicleId);
      formData.append('documentName', documentName);
      formData.append('file', file);

      const response = await fetch('/api/post.vehicle-document.php', {
        method: 'POST',
        body: formData
      });
      const resp = await response.json();
      // const resp = await net.post('/api/post.vehicle-document.php', formData);
      if (resp?.result) {
        ui.toastr.success('Document uploaded', 'Success');
        $('#pills-document').load(`section.vehicle-documents.php?vehicleId=${vehicleId}`);
        return;
      }
      ui.toastr.error('Failed to upload document', 'ERROR');
    });
  }

  if (!documentEventExists('vehicleDocument:delete')) {
    $(document).on('vehicleDocument:delete', async (e, id) => {
      if (await ui.ask('Are you sure you want to delete this document?')) {
        const vehicleId = $('#vehicleId').val();
        const resp = await net.get('/api/get.delete-vehicle-document.php', {
          id
        });
        if (resp?.result) {
          ui.toastr.success('Document deleted', 'Success');
          $('#pills-document').load(`section.vehicle-documents.php?vehicleId=${vehicleId}`);
          return;
        }
        ui.toastr.error('Failed to delete document', 'ERROR');
      }
    });
  }

  <?php $count = count($rows); ?>
  <?php if ($count > 0): ?>
    $('#document-count').html('<?= $count ?>').removeClass('d-none');
  <?php else: ?>
    $('#document-count').html('').addClass('d-none');
  <?php endif; ?>
</script>