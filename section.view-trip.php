<?php
require_once 'autoload.php';

use Transport\Trip;
use Generic\{ InputHandler, Utils };

$id = InputHandler::getInt(INPUT_GET, 'id');
$trip = new Trip($id);
$sectionId = 'a7218ac8-065f-481e-a05f-1b8d0b145912';
?>
<div class="container-fluid mt-3">
  <div class="d-flex">
    <h3><?=$trip->summary?></h3>
    <div id="trip-action-buttons" class="dropdown ms-auto">
      <button class="btn btn-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
        Action
      </button>
      <ul class="dropdown-menu">
        <?php if (array_search($_SESSION['view'], ['developer','manager','driver']) !== false):?>
          <?php if ($trip->isEditable()): ?>
            <li><button id="<?=$sectionId?>-btn-edit" class="dropdown-item btn btn-secondary"><i class="fa-solid fa-pencil"></i> Edit</button></li>
            <?php if (!$trip->confirmed): ?>
              <li><button id="<?=$sectionId?>-btn-confirm" class="dropdown-item btn btn-secondary"><i class="fa-solid fa-stamp"></i> Confirm</button></li>
            <?php endif; ?>
          <?php endif;?>
          <li><button id="<?=$sectionId?>-btn-duplicate" class="dropdown-item btn btn-secondary"><i class="fa-solid fa-copy"></i> Duplicate</button></li>
          <?php if ($trip->isConfirmed()): ?>
            <li><a href="print.trip-driver-sheet.php?id=<?=$trip->getId()?>" target="_blank" id="<?=$sectionId?>-btn-print" class="dropdown-item btn btn-secondary"><i class="fa-solid fa-file-pdf"></i> Print (Driver Sheet)</a></li>
          <?php endif;?>
        <?php endif;?>
        <?php if ($trip->isConfirmed()): ?>
          <li><a href="print.trip-guest-sheet.php?id=<?=$trip->getId()?>" target="_blank" id="<?=$sectionId?>-btn-print" class="dropdown-item btn btn-secondary"><i class="fa-solid fa-file-pdf"></i> Print (Guest Sheet)</a></li>
          <li><a href="download.trip-ics.php?id=<?=$trip->getId()?>" target="_blank" id="<?=$sectionId?>-btn-ics" class="dropdown-item btn btn-secondary"><i class="fa-regular fa-calendar-circle-plus"></i> Calendar item (ICS)</a></li>
        <?php endif;?>
        <?php if (array_search($_SESSION['view'], ['requestor']) !== false):?>
          <li><button class="dropdown-item btn btn-secondary text-danger" onclick="cancelTrip(<?=$trip->getId()?>)"><i class="fa-solid fa-ban"></i> Cancel Request</button></li>
        <?php endif; ?>
      </ul>
    </div>
  </div>

  <div class="row row-cols-2">
    <div class="col mb-3">
      <div class="card h-100">
        <div class="card-header d-flex justify-content-between">
          <h5 class="my-0"><i class="fa-solid fa-up"></i> Pick Up</h5>
          <span class="badge bg-primary align-self-center"><?=is_null($trip->startDate) ? '' : Date('g:ia', strtotime($trip->startDate))?></span>
        </div>
        <div class="card-body">
          <div><?=is_null($trip->pickupDate) ? '' : Date('D M j @ g:ia', strtotime($trip->pickupDate))?></div>
          <div class="fs-3 fw-bold">
            <?=$trip->guests?> 
            <?php if ($trip->passengers): ?>
              (<?=$trip->passengers?> pax)
            <?php endif; ?>
          </div>
          <div><?=$trip->puLocation->name?></div>
          <?php if ($trip->guest): ?>
            <div>Contact: <?=$trip->guest->getName()?> <?=$trip->guest->phoneNumber?></div>
          <?php endif; ?>
        </div>
      </div>
    </div>

    <div class="col mb-3">
      <div class="card h-100">
        <div class="card-header">
          <h5 class="my-0"><i class="fa-solid fa-down"></i> Drop Off</h5>
        </div>
        <div class="card-body">
          <div><?=$trip->doLocation->name?></div>
        </div>
      </div>
    </div>

    <div class="col mb-3">
      <div class="card h-100">
        <div class="card-header">
          <h5 class="my-0"><i class="fa-duotone fa-solid fa-bus"></i> Vehicle and Driver</h5>
        </div>
        <div class="card-body d-flex justify-content-between">
          <div>
            <div>
              <?php if ($trip->vehicleId): ?>
                <span class="tag fs-5 px-4" style="background-color:<?=$trip->vehicle->color?>; color:<?=Utils::getContrastColor($trip->vehicle->color)?>"><?=$trip->vehicle->name?></span>
              <?php endif;?>
            </div>
            <div><?=$trip->vehiclePUOptions?> - <?=$trip->vehicleDOOptions?></div>
          </div>
          <?php if($trip->driverId):?>
            <div class="w-25">
              <div class="position-relative">
                <img src="/images/drivers/<?=$trip->driver->username?>.jpg" class="img-fluid">
                <div class="text-center fw-light" style="position:absolute; left:0; right: 0; bottom: 0; background-color: rgba(0,0,0,.5); color:white"><?=$trip->driverId ? $trip->driver->getName() : '' ?></div>
              </div>
            </div>
          <?php endif;?>
        </div>
      </div>
    </div>

    <?php if ($trip->flightNumber): ?>
    <div class="col mb-3">
      <div class="card h-100">
        <div class="card-header">
          <h5 class="my-0"><i class="fa-duotone fa-solid fa-plane-tail"></i> Flight Info</h5>
        </div>
        <div class="card-body d-flex">
          <div class="w-25 me-3">
            <?php if ($trip->airline->imageFilename): ?>
              <img src="/images/airlines/<?=$trip->airline->imageFilename?>" class="img-fluid" alt="<?=$trip->airline->name?>">
            <?php endif; ?>
          </div>
          <div>
            <div><?=$trip->airline->flightNumberPrefix?> <?=$trip->flightNumber?></div>
            <div>
              <?php if ($trip->ETA): ?>
                ETA: <?=is_null($trip->ETA) ? '' : Date('g:i a', strtotime($trip->ETA))?>
              <?php else: ?>
                ETD:  <?=is_null($trip->ETD) ? '' : Date('g:i a', strtotime($trip->ETD))?>
              <?php endif; ?>
            </div>
          </div>
        </div>
      </div>
    </div>
    <?php endif; ?>
    
  </div>

  <div class="d-flex justify-content-between">
    <table class="table table-sm table-bordered w-auto border-dark-subtle">
      <tr><th class="bg-dark-subtle px-3">Requestor:</th><td class="px-3"><?=$trip->requestor ? $trip->requestor->getName() : ''?></td></tr>
    </table>

    <?php if ($trip->isCancelled()):?>
      <table class="table table-sm table-bordered w-auto border-danger">
        <tr><th class="bg-danger text-bg-danger px-3">Cancelled:</th><td class="px-3"><?=is_null($trip->cancelled) ? '' : Date('F j g:i a', strtotime($trip->cancelled))?></td></tr>
      </table>
    <?php endif;?>

    <?php if ($trip->isConfirmed()): ?>
      <table class="table table-sm table-bordered w-auto border-success">
        <tr><th class="bg-success text-bg-success px-3">Confirmed:</th><td class="px-3"><?=is_null($trip->confirmed) ? '' : Date('F j g:i a', strtotime($trip->confirmed))?></td></tr>
      </table>
    <?php else:?>
      <table class="table table-sm table-bordered w-auto border-dark-subtle">
        <tr><th class="bg-dark-subtle px-3">Confirmed:</th><td class="px-3">No</td></tr>
      </table>
    <?php endif;?>

    <!--
    <table class="table table-sm table-bordered w-auto border-dark-subtle">
      <tr><th class="bg-dark-subtle px-3">Started:</th><td class="px-3"><?=$trip->started ? Date('F j g:i a', strtotime($trip->started)) : '-'?></td></tr>
    </table>

    <table class="table table-sm table-bordered w-auto border-dark-subtle">
      <tr><th class="bg-dark-subtle px-3">Completed:</th><td class="px-3"><?=$trip->completed ? Date('F j g:i a', strtotime($trip->completed)) : '-'?></td></tr>
    </table>
    -->
  </div>

  <div class="row">
    <?php if ($trip->guestNotes || $trip->driverNotes || $trip->generalNotes): ?>
      <div class="col-auto d-flex ~flex-column gap-3 px-4">
        <?php if ($trip->guestNotes): ?>
          <div class="align-self-center">
            <div style="transform: rotate(-3deg)" class="w-auto">
              <div class="postit d-flex flex-column">
                <div>Guest:</div>
                <div class="mt-auto mb-auto"><?=nl2br($trip->guestNotes)?></div>
              </div>
            </div>
          </div>
        <?php endif;?>
        <?php if ($trip->driverNotes): ?>
          <div class="align-self-center">
          <div style="transform: rotate(-3deg)" class="w-auto">
              <div class="postit d-flex flex-column">
                <div>Driver:</div>
                <div class="mt-auto mb-auto"><?=nl2br($trip->driverNotes)?></div>
              </div>
            </div>
          </div>
        <?php endif;?>
        <?php if ($trip->generalNotes): ?>
          <div class="align-self-center">
            <div style="transform: rotate(-3deg)" class="w-auto">
              <div class="postit d-flex flex-column">
                <div class="mt-auto mb-auto"><?=nl2br($trip->generalNotes)?></div>
              </div>
            </div>
          </div>
        <?php endif;?>
      </div>
    <?php endif;?>

    <!-- <div class="col ms-auto" style="max-width:800px">
      <div class="bg-body position-relative border rounded" style="padding-bottom: 55px !important;">
        <h3 class="py-2 px-3">Message</h3>
        <section id="trip-chat" class="chat px-5"></section>

        <div class="input-group mb-3 position-absolute bottom-0 start-50 translate-middle-x px-3">
          <input id="trip-message" type="text" class="form-control" placeholder="Type a message" aria-label="Type a message" aria-describedby="button-send">
          <button class="btn btn-outline-primary" type="button" id="button-send">Send</button>
        </div>
      </div>
    </div> -->
  </div>



</div>

<script type="module">
  import * as input from '/js/formatters.js';
  import * as ui from '/js/notifications.js';
  import * as net from '/js/network.js';

  window.cancelTrip = async function (tripId) {
    if (await ui.ask('Are you sure you want to cancel this trip?')) {
      const resp = await net.post('/api/post.cancel-trip.php', {tripId});
      if (resp?.result) {
        $(document).trigger('tripChange', {tripId});
        app.closeOpenTab();
        return ui.toastr.success('Trip cancellation request submitted.', 'Success');
      }
      return ui.toastr.error('Seems to be a problem cancelling this trip!', 'Error');
    }
  }

  $(async ƒ => {
    const sectionId = '<?=$sectionId?>';
    const tripId = <?=$trip->getId()?>;

    function reloadSection () {
      $('#<?=$_GET["loadedToId"]?>').load(`<?=$_SERVER['REQUEST_URI']?>`); // Refresh this page
    }

    // function loadConversation() {
    //   $('#trip-chat').load('section.chat.php', {tripId});
    // }
    // clearInterval(window.loadInterval_1);
    // window.loadInterval_1 = setInterval(loadConversation, 5000);
    // loadConversation();

    // $('#trip-message').on('keyup', async ƒ => {
		// 	if (ƒ.keyCode === 13) {
    //     $('#button-send').click();
    //   }
    // });
    // $('#button-send').on('click', async ƒ => {
    //   const message = $('#trip-message').val();
    //   if (message) {
    //     const resp = await net.post('/api/post.send-message.php', {tripId, message});
    //     if (resp?.result) {
    //       $('#trip-chat').load('section.chat.php', {tripId});
    //       $('#trip-message').val('').focus();
    //     }
    //   }
    // });


    $(`#${sectionId}-btn-edit`).off('click').on('click', async e => {
      app.closeOpenTab();
      app.openTab('edit-trip', 'Trip (edit)', `section.edit-trip.php?id=${tripId}`);
    });

    $(`#${sectionId}-btn-duplicate`).off('click').on('click', async e => {
      const resp = await net.get('/api/get.duplicate-trip.php', {id: tripId});
      const newId = resp.result;
      app.closeOpenTab();
      app.openTab('edit-trip', 'Trip (edit)', `section.edit-trip.php?id=${newId}`);
    });

    $(`#${sectionId}-btn-confirm`).off('click').on('click', async e => {
      const resp = await net.post('/api/post.confirm-trip.php', {id: tripId});
      if (resp?.result) {
        $(document).trigger('tripChange', {tripId});
        reloadSection();
        return ui.toastr.success('Trip confirmed.', 'Success');
      }
      return ui.toastr.error('Seems to be a problem confirming this trip!', 'Error');
    });

});

</script>