<?php
require_once 'autoload.php';

use Transport\{Snag};
use Generic\InputHandler;

$vehicleId = InputHandler::getInt(INPUT_GET, 'vehicleId');
?>
<input type="hidden" id="vehicleId" value="<?= $vehicleId ?>">

<div class="text-end">
  <button class="btn btn-sm btn-primary mb-2" onclick="$(document).trigger('snag:add', <?= $vehicleId ?>)"><i class="fa-solid fa-plus-large"></i></button>
</div>

<?php if ($rows = Snag::getSnags($vehicleId)): ?>
  <!-- SNAGLIST -->
  <div class="table-responsive">
    <table class="table table-bordered table-sm mb-0">
      <thead>
        <tr class="table-dark">
          <th class="fit">Date</th>
          <th>Description</th>
          <th class="fit">Acknowledged</th>
          <th class="fit">Resolved</th>
          <th>Comments</th>
          <th class="fit">&nbsp;</th>
          <th>&nbsp;</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($rows as $row): ?>
          <tr data-id="<?= $row->id ?>" data-images="<?= $row->image_filenames ?>">
            <td class="datetime nowrap text-center align-middle"><?= $row->logged ?></td>
            <td class="text-left align-middle">
              <div><?= $row->description ?></div>
              <div>
                <div class="badge bg-dark-subtle"><?= ucwords($row->created_by) ?></div>
              </div>
            </td>
            <td class="text-left align-middle fit">
              <?php if ($row->acknowledged): ?>
                <div>
                  <div class="tag tag-success"><?= ucwords($row->acknowledged_by) ?></div>
                </div>
              <?php endif; ?>
            </td>
            <td class="text-left align-middle fit">
              <?php if ($row->resolved): ?>
                <div><?= $row->resolution ?></div>
                <div>
                  <div class="badge bg-dark-subtle"><?= ucwords($row->resolved_by) ?></div>
                </div>
              <?php endif; ?>
            </td>
            <td>
              <div><?= is_null($row->comments) ? '' : str_replace("\n\n", '<hr class="my-1">', $row->comments) ?></div>
            </td>
            <td class="text-center align-middle fit">
              <?php if ($row->image_filenames): ?>
                <div><i class="fa-duotone fa-paperclip-vertical fa-lg pointer" onclick="$(document).trigger('showSnagImages', this)"></i></div>
              <?php endif; ?>
            </td>
            <td class="text-center align-middle">
              <i class="fa-solid fa-ellipsis fa-xl text-body-tertiary action-icon pointer hidden-content" onclick="$(document).trigger('listActionItem:snag', this)"></i>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
<?php else: ?>

  <div class="d-flex my-3">
    <div class="alert alert-info mx-auto" role="alert">
      <i class="fa-solid fa-info-circle"></i>
      There are no snags for this vehicle at this time.
    </div>
  </div>

<?php endif; ?>


<script>
  if (!documentEventExists('showSnagImages')) {
    $(document).on('showSnagImages', async (e, el) => {
      console.log('showSnagImages');
      e.stopPropagation();
      e.stopImmediatePropagation();

      const images = $(el).closest('tr').data('images').split(', ');
      console.log('images:', images);
      if (images.length === 0) return;

      const modalBody = $("#modalBody");
      modalBody.empty(); // Clear previous content

      images.forEach(function(image) {
        const imgElement = `<a href="/images/library/${image}" data-lightbox="gallery">
          <img src="/images/library/${image}" class="img-thumbnail m-2" style="width: 150px; height: 100px;">
          </a>`;
        modalBody.append(imgElement);
      });

      $("#imageModal").modal('show');
    });
  }

  if (!documentEventExists('snag:add')) {
    $(document).on('snag:add', async (e, vehicleId) => {
      const description = await ui.getText('Enter a description of the snag:');
      if (description == undefined) return;
      const resp = await net.post('/api/post.snag.php', {
        vehicleId,
        description
      });
      $('#pills-snags').load('section.vehicle-snags.php?vehicleId=' + vehicleId);
    });
  }

  if (!documentEventExists('listActionItem:snag')) {
    $(document).on('listActionItem:snag', async (e, el) => {
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
          <button class="dropdown-item" onclick="$(document).trigger('snag:acknowledge', ${id})"><i class="fa-duotone fa-regular fa-pen-to-square"></i> Acknowledge</button>
          <button class="dropdown-item" onclick="$(document).trigger('snag:comment', ${id})"><i class="fa-duotone fa-regular fa-pen-to-square"></i> Comment</button>
          <button class="dropdown-item" onclick="$(document).trigger('snag:resolve', ${id})"><i class="fa-duotone fa-regular fa-pen-to-square"></i> Resolve</button>
          <button class="dropdown-item" onclick="$(document).trigger('snag:attach-photo', ${id})"><i class="fa-duotone fa-regular fa-paperclip-vertical"></i> Attach Photo</button>
          ${additionalItems}
          <hr class="dropdown-divider">
          <button class="dropdown-item text-danger" onclick="$(document).trigger('snag:delete', ${id})"><i class="fa-duotone fa-regular fa-trash"></i> Delete</button>
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

  if (!documentEventExists('snag:acknowledge')) {
    $(document).on('snag:acknowledge', async (e, id) => {
      const vehicleId = $('#vehicleId').val();
      const resp = await net.post('/api/post.snag-acknowledge.php', {
        id
      });
      if (resp?.result) {
        ui.toastr.success('Snag acknowledged', 'Success');
        $('#pills-snags').load(`section.vehicle-snags.php?vehicleId=${vehicleId}`);
        return;
      }
      ui.toastr.error('Failed to acknowledge snag', 'ERROR');
    });
  }

  if (!documentEventExists('snag:comment')) {
    $(document).on('snag:comment', async (e, id) => {
      const text = await ui.getText('Enter your comment:');
      if (text == undefined) return;
      const vehicleId = $('#vehicleId').val();
      const resp = await net.post('/api/post.snag-comment.php', {
        id,
        text
      });
      if (resp?.result) {
        ui.toastr.success('Comment added', 'Success');
        $('#pills-snags').load(`section.vehicle-snags.php?vehicleId=${vehicleId}`);
        return;
      }
      ui.toastr.error('Failed to add comment', 'ERROR');
    });
  }

  if (!documentEventExists('snag:resolve')) {
    $(document).on('snag:resolve', async (e, id) => {
      const text = await ui.getText('Enter resolution:');
      if (text == undefined) return;
      const vehicleId = $('#vehicleId').val();
      const resp = await net.post('/api/post.snag-resolve.php', {
        id,
        text
      });
      if (resp?.result) {
        ui.toastr.success('Snag resolved', 'Success');
        $('#pills-snags').load(`section.vehicle-snags.php?vehicleId=${vehicleId}`);
        return;
      }
      ui.toastr.error('Failed to resolve snag', 'ERROR');
    });
  }

  if (!documentEventExists('snag:attach-photo')) {
    $(document).on('snag:attach-photo', async (e, id) => {
      $(document).trigger('uploadPhoto', async function(resp) {
        if (resp.result === false) {
          ui.toastr.error('Failed to attach photo', 'ERROR');
          return;
        }
        const photoId = resp.result;
        const vehicleId = $('#vehicleId').val();
        resp2 = await net.post('/api/post.snag-photo.php', {
          id,
          photoId
        });
        if (resp2?.result) {
          ui.toastr.success('Photo attached', 'Success');
          $('#pills-snags').load(`section.vehicle-snags.php?vehicleId=${vehicleId}`);
          return;
        }
        ui.toastr.error('Failed to attach photo', 'ERROR');
      });
    });
  }

  if (!documentEventExists('snag:delete')) {
    $(document).on('snag:delete', async (e, id) => {
      if (await ui.ask('Are you sure you want to delete this snag?')) {
        const vehicleId = $('#vehicleId').val();
        const resp = await net.get('/api/get.delete-snag.php', {
          id
        });
        if (resp?.result) {
          ui.toastr.success('Snag deleted', 'Success');
          $('#pills-snags').load(`section.vehicle-snags.php?vehicleId=${vehicleId}`);
          return;
        }
        ui.toastr.error('Failed to delete snag', 'ERROR');
      }
    });
  }

  // if (!documentEventExists('badgeCount:snags')) {
  //   $(document).on('badgeCount:snags', async (e, count) => {
  //     $('#snag-count').html('<?= $count ?>').removeClass('d-none');
  //   }
  // }

  <?php $count = count($rows); ?>
  <?php if ($count > 0): ?>
    $('#snag-count').html('<?= $count ?>').removeClass('d-none');
  <?php else: ?>
    $('#snag-count').html('').addClass('d-none');
  <?php endif; ?>
</script>