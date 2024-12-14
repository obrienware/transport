<?php
require_once 'class.trip.php';
$trip = new Trip($_REQUEST['id']);
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
        <?php if ($trip->isEditable()): ?>
          <li><button id="<?=$sectionId?>-btn-edit" class="dropdown-item btn btn-secondary"><i class="fa-solid fa-pencil"></i> Edit</button></li>
          <?php if (!$trip->confirmed): ?>
            <li><button id="<?=$sectionId?>-btn-confirm" class="dropdown-item btn btn-secondary"><i class="fa-solid fa-stamp"></i> Confirm</button></li>
          <?php endif; ?>
        <?php endif;?>
        <li><button id="<?=$sectionId?>-btn-duplicate" class="dropdown-item btn btn-secondary"><i class="fa-solid fa-copy"></i> Duplicate</button></li>
        <li><a href="print.trip-driver-sheet.php?id=<?=$trip->tripId?>" target="_blank" id="<?=$sectionId?>-btn-print" class="dropdown-item btn btn-secondary"><i class="fa-solid fa-print"></i> Print (Driver Sheet)</a></li>
        <li><a href="print.trip-guest-sheet.php?id=<?=$trip->tripId?>" target="_blank" id="<?=$sectionId?>-btn-print" class="dropdown-item btn btn-secondary"><i class="fa-solid fa-print"></i> Print (Guest Sheet)</a></li>
        <li><a href="download.trip-ics.php?id=<?=$trip->tripId?>" target="_blank" id="<?=$sectionId?>-btn-ics" class="dropdown-item btn btn-secondary"><i class="fa-regular fa-calendar-circle-plus"></i> Add Calendar item</a></li>
      </ul>
    </div>
  </div>

  <div class="row row-cols-2">
    <div class="col mb-3">
      <div class="card h-100">
        <div class="card-header d-flex justify-content-between">
          <h5 class="my-0">Pick Up</h5>
          <span class="badge bg-primary"><?=Date('g:ia', strtotime($trip->startDate))?></span>
        </div>
        <div class="card-body">
          <div><?=Date('D M j @ g:ia', strtotime($trip->pickupDate))?></div>
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
          <h5 class="my-0">Drop Off</h5>
        </div>
        <div class="card-body">
          <div><?=$trip->doLocation->name?></div>
        </div>
      </div>
    </div>

    <div class="col mb-3">
      <div class="card h-100">
        <div class="card-header">
          <h5 class="my-0">Vehicle and Driver</h5>
        </div>
        <div class="card-body d-flex justify-content-between">
          <div>
            <div><?=$trip->vehicleId ? $trip->vehicle->name : '' ?></div>
            <div><?=$trip->vehiclePUOptions?> - <?=$trip->vehicleDOOptions?></div>
          </div>
          <div><?=$trip->driverId ? $trip->driver->getName() : '' ?></div>
        </div>
      </div>
    </div>

    <?php if ($trip->flightNumber): ?>
    <div class="col mb-3">
      <div class="card h-100">
        <div class="card-header">
          <h5 class="my-0">Flight Info</h5>
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
                ETA: <?=Date('g:i a', strtotime($trip->ETA))?>
              <?php else: ?>
                ETD:  <?=Date('g:i a', strtotime($trip->ETD))?>
              <?php endif; ?>
            </div>
          </div>
        </div>
      </div>
    </div>
    <?php endif; ?>
    
  </div>

  <div class="d-flex justify-content-between mt-4">
    <div>Requestor: <?=$trip->requestor ? $trip->requestor->getName() : ''?></div>
    <div>Confirmed: <?=$trip->confirmed ? Date('F j g:i a', strtotime($trip->confirmed)) : ''?></div>
    <div>Started: <?=$trip->started ? Date('F j g:i a', strtotime($trip->started)) : ''?></div>
    <div>Completed: <?=$trip->completed ? Date('F j g:i a', strtotime($trip->completed)) : ''?></div>
  </div>



</div>

<script type="text/javascript">

  $(async ƒ => {
    const sectionId = '<?=$sectionId?>';
    const tripId = <?=$trip->tripId?>;

    $(`#${sectionId}-btn-edit`).off('click').on('click', async e => {
      app.closeOpenTab();
      app.openTab('edit-trip', 'Trip (edit)', `section.edit-trip.php?id=${tripId}`);
    });

    $(`#${sectionId}-btn-duplicate`).off('click').on('click', async e => {
      const resp = await get('/api/get.duplicate-trip.php', {id: tripId});
      const newId = resp.result;
      app.closeOpenTab()
      app.openTab('edit-trip', 'Trip (edit)', `section.edit-trip.php?id=${newId}`);
    });

    $(`#${sectionId}-btn-confirm`).off('click').on('click', async e => {
      const resp = await post('/api/post.confirm-trip.php', {id: tripId});
      if (resp?.result) {
        $(document).trigger('tripChange', {tripId});
        return toastr.success('Trip confirmed.', 'Success');
      }
      return toastr.error('Seems to be a problem finalizing this trip!', 'Error');
    });

});

</script>