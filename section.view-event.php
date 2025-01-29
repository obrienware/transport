<?php
require_once 'autoload.php';

use Transport\Event;
use Transport\User;
use Transport\Vehicle;

$prefix = bin2hex(random_bytes(10 / 2));
$id = !empty($_GET['id']) ? (int)$_GET['id'] : null;
$event = new Event($id);
?>
<div class="container-fluid mt-3">
  <div class="d-flex">
    <h3><?=$event->name?></h3>
    <div id="<?=$prefix?>-action-buttons" class="dropdown ms-auto">
      <button class="btn btn-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
        Action
      </button>
      <ul class="dropdown-menu">
        <?php if (array_search($_SESSION['view'], ['developer','manager','driver']) !== false):?>
          <?php if ($event->isEditable()): ?>
            <li><button id="<?=$prefix?>-btn-edit" class="dropdown-item btn btn-secondary"><i class="fa-solid fa-pencil"></i> Edit</button></li>
            <?php if (!$event->isConfirmed()): ?>
              <li><button id="<?=$prefix?>-btn-confirm" class="dropdown-item btn btn-secondary"><i class="fa-solid fa-stamp"></i> Confirm</button></li>
            <?php endif; ?>
          <?php endif;?>
        <?php endif;?>
        <?php if (array_search($_SESSION['view'], ['manager','requestor']) !== false):?>
          <li><button class="dropdown-item btn btn-secondary text-danger" onclick="cancelEvent(<?=$event->getId()?>)"><i class="fa-solid fa-ban"></i> Cancel Request</button></li>
        <?php endif; ?>
      </ul>
    </div>
  </div>


  <div class="row">
      <div class="col-3">
        <div class="mb-3">
          <label for="event-start-date" class="form-label">Starts</label>
          <input type="text" class="form-control" id="event-start-date" value="<?=($event->startDate) ? Date('m/d/Y h:i A', strtotime($event->startDate)) : '' ?>" readonly disabled>
        </div>
      </div>

      <div class="col-3">
        <div class="mb-3">
          <label for="event-end-date" class="form-label">Ends</label>
          <input type="text" class="form-control" id="event-start-date" value="<?=($event->endDate) ? Date('m/d/Y h:i A', strtotime($event->endDate)) : '' ?>" readonly disabled>
        </div>
      </div>

      <div class="col">
        <div class="mb-3">
          <label for="event-name" class="form-label">Description</label>
          <input type="text" class="form-control" id="event-name" value="<?=$event->name?>" readonly disabled>
        </div>
      </div>
    </div>

    <div class="row">
      <div class="col">
        <div class="mb-3">
          <label for="event-location" class="form-label">Location</label>
          <input type="text" class="form-control" value="<?=$event->location->name?>" readonly disabled>
        </div>
      </div>
    </div>

    <div class="row">
      <div class="col">
        <div class="mb-3">
          <label for="event-drivers" class="form-label">Drivers</label>
          <div>
            <?php foreach ($event->drivers as $driverId): ?>
              <span class="tag tag-primary fs-6 fw-light"><?=$driverId ? (new User($driverId))->getName() : ''?></span>
            <?php endforeach; ?>
          </div>
        </div>
      </div>

      <div class="col">
        <div class="mb-3">
          <label for="event-vehicles" class="form-label">Vehicles</label>
          <div>
            <?php foreach ($event->vehicles as $vehicleId): ?>
              <span class="tag tag-primary fs-6 fw-light"><?=$vehicleId ? (new Vehicle($vehicleId))->name : ''?></span>
            <?php endforeach; ?>
          </div>
        </div>
      </div>
    </div>

    <div class="row">
      <div class="col">
        <div class="mb-3">
          <label for="event-notes" class="form-label">Notes</label>
          <textarea class="form-control" id="event-notes" rows="7" readonly disabled><?=$event->notes?></textarea>
        </div>
      </div>
    </div>







  <div class="d-flex justify-content-between">
    <table class="table table-sm table-bordered w-auto border-dark-subtle">
      <tr><th class="bg-dark-subtle px-3">Requestor:</th><td class="px-3"><?=$event->requestor ? $event->requestor->getName() : ''?></td></tr>
    </table>

    <?php if ($event->isCancelled()):?>
      <table class="table table-sm table-bordered w-auto border-danger">
        <tr><th class="bg-danger text-bg-danger px-3">Cancelled:</th><td class="px-3"><?=Date('F j g:i a', strtotime($event->cancelled))?></td></tr>
      </table>
    <?php endif;?>

    <?php if ($event->isConfirmed()):?>
      <table class="table table-sm table-bordered w-auto border-success">
        <tr><th class="bg-success text-bg-success px-3">Confirmed:</th><td class="px-3"><?=Date('F j g:i a', strtotime($event->confirmed))?></td></tr>
      </table>
    <?php else: ?>
      <table class="table table-sm table-bordered w-auto border-dark-subtle">
        <tr><th class="bg-dark-subtle px-3">Confirmed:</th><td class="px-3">No</td></tr>
      </table>
    <?php endif;?>
  </div>

  <div class="row">
    <div class="col ms-auto" style="max-width:800px">
      <div class="bg-body position-relative border rounded" style="padding-bottom: 55px !important;">
        <h3 class="py-2 px-3">Message</h3>
        <section id="event-chat" class="chat px-5"></section>

        <div class="input-group mb-3 position-absolute bottom-0 start-50 translate-middle-x px-3">
          <input id="event-message" type="text" class="form-control" placeholder="Type a message" aria-label="Type a message" aria-describedby="button-send">
          <button class="btn btn-outline-primary" type="button" id="<?=$prefix?>-button-send">Send</button>
        </div>
      </div>
    </div>
  </div>





</div>

<script type="text/javascript">

  window.cancelEvent = async function (eventId) {
    if (await ask('Are you sure you want to cancel this event?')) {
      const resp = await post('/api/post.cancel-event.php', {eventId});
      if (resp?.result) {
        $(document).trigger('eventChange', {eventId});
        app.closeOpenTab();
        return toastr.success('Event cancellation request submitted.', 'Success');
      }
      return toastr.error('Seems to be a problem cancelling this event!', 'Error');
    }
  }

  $(async ƒ => {
    const prefix = '<?=$prefix?>';
    const eventId = <?=$event->getId()?>;

    function loadConversation() {
      $('#event-chat').load('section.chat.php', {eventId});
    }
    clearInterval(window.loadInterval_2);
    window.loadInterval_2 = setInterval(loadConversation, 5000);
    loadConversation();

    $('#event-message').on('keyup', async ƒ => {
			if (ƒ.keyCode === 13) {
        $('#<?=$prefix?>-button-send').click();
      }
    });
    $('#<?=$prefix?>-button-send').on('click', async ƒ => {
      const message = $('#event-message').val();
      if (message) {
        const resp = await post('/api/post.send-message.php', {eventId, message});
        if (resp?.result) {
          $('#event-chat').load('section.chat.php', {eventId});
          $('#event-message').val('').focus();
        }
      }
    });

    $(`#${prefix}-btn-edit`).off('click').on('click', async e => {
      app.closeOpenTab();
      app.openTab('edit-event', 'Event (edit)', `section.edit-event.php?id=${eventId}`);
    });

    $(`#${prefix}-btn-confirm`).off('click').on('click', async e => {
      const resp = await post('/api/post.confirm-event.php', {id: eventId});
      if (resp?.result) {
        $(document).trigger('eventChange', {eventId});
        return toastr.success('Event confirmed.', 'Success');
      }
      return toastr.error('Seems to be a problem confirming this event!', 'Error');
    });


  });

</script>