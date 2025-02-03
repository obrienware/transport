<?php
require_once 'autoload.php';

use Transport\Snag;
use Transport\Utils;
use Transport\Vehicle;

$vehicleId = isset($_GET['vehicleId']) ? (int)$_GET['vehicleId'] : null;
// $vehicle = new Vehicle($vehicleId);
?>
<!-- SNAGLIST -->
<?php if ($rows = Snag::getSnags($vehicleId)): ?>
  <div class="text-end">
    <button class="btn btn-primary mb-2">Add Snag</button>
  </div>
  <table class="table table-bordered table-sm table-striped mb-0">
    <thead>
      <tr>
        <th class="fit">Date</th>
        <th>Description</th>
        <th>Acknowledged</th>
        <th>Resolution</th>
        <th>Comments</th>
        <th>&nbsp;</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($rows as $row): ?>
        <tr>
          <td class="datetime short" style="font-size:small"><?=$row->logged?></td>
          <td>
            <div><?=$row->description?></div>
            <div><div class="badge bg-dark-subtle"><?=$row->created_by?></div></div>
          </td>
          <td class="text-center align-middle">
            <?php if ($row->acknowledged): ?>
              <div><div class="tag tag-success"><?=$row->acknowledged_by?></div></div>
            <?php endif; ?>
          </td>
          <td>
            <?php if ($row->resolved): ?>
              <div><?=$row->resolution?></div>
              <div><div class="badge bg-dark-subtle"><?=$row->resolved_by?></div></div>
            <?php endif; ?>
          </td>
          <td>
            <div><?=is_null($row->comments) ? '' : nl2br($row->comments)?></div>
          </td>
          <td class="text-center align-middle">
            <i class="fa-solid fa-ellipsis fa-xl text-body-tertiary action-icon pointer hidden-content" data-id="<?=$row->id?>"></i>
          </td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
<?php else: ?>
  <div class="card-body text-center">
    There are no snags logged yet for this vehicle
  </div>
<?php endif; ?>


<script type="module">
  import * as input from '/js/formatters.js';
  import * as ui from '/js/notifications.js';
  import * as net from '/js/network.js';

  if (typeof window.acknowledgeSnag !== 'function') {
    window.acknowledgeSnag = async function(snagId) {
      const resp = await net.get('/api/get.snag-acknowledge.php', {snagId});
      $('#pills-snags').load('section.vehicle-snags.php?vehicleId=<?=$_GET['vehicleId']?>');
    };
  }

  if (typeof window.commentSnag !== 'function') {
    window.commentSnag = async function(snagId) {
      const text = await ui.getText('Snag Comment:');
      if (text == undefined) return;
      console.log('Comment:', text);
      const resp = await net.get('/api/get.snag-comment.php', {snagId, text});
      $('#pills-snags').load('section.vehicle-snags.php?vehicleId=<?=$_GET['vehicleId']?>');
      // console.log('Action B', snagId);
    };
  }

  if (typeof window.addPhotoSnag !== 'function') {
    window.addPhotoSnag = async function(snagId) {
      const file = await ui.getFile('Upload Photo:');
      console.log('File:', file);
      // const resp = await net.get('/api/get.snag-photo.php', {snagId});
      // $('#pills-snags').load('section.vehicle-snags.php?vehicleId=<?=$_GET['vehicleId']?>');
    };
  }

  $(async ƒ => {

    const vehicleId = '<?=$_GET['vehicleId']?>';
    <?php $count = count($rows); ?>
    <?php if ($count > 0): ?>
      $('#snag-count').html('<?=$count?>').removeClass('d-none');
    <?php endif; ?>

    $('.action-icon').on('click', function(event) {
      event.preventDefault();
      event.stopPropagation();
      const snagId = $(this).data('id');

      const myRandomId = Math.random().toString(36).substring(7);

      // Remove any existing dropdown menus
      // $('.dropdown-menu').remove();
      $(document).trigger('click');

      // Create the dropdown menu
      const dropdownMenu = `
        <div id="${myRandomId}" data-snag-id="${snagId}" class="dropdown-menu show" style="position: absolute; left: ${event.pageX}px; top: ${event.pageY}px;">
          <button class="dropdown-item" onclick="acknowledgeSnag(${snagId})">Acknowledge</button>
          <button class="dropdown-item" onclick="commentSnag(${snagId})">Comment</button>
          <button class="dropdown-item" onclick="addPhotoSnag(${snagId})">Attach Photo</button>
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

      // Remove the dropdown menu when clicking outside
      $(document).on('click', function() {
        $('#' + myRandomId).remove();
      });
    });

  });

</script>