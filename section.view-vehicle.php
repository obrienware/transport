<?php
require_once 'autoload.php';

use Generic\InputHandler;
use Transport\Vehicle;

$id = InputHandler::getInt(INPUT_GET, 'id');
$vehicle = new Vehicle($id);
$vehicleId = $vehicle->getId();
?>
<!-- Back button -->
<div class="d-flex justify-content-between top-page-buttons mb-2">
  <button class="btn btn-sm btn-outline-primary px-2 me-auto" onclick="$(document).trigger('loadMainSection', { sectionId: 'vehicles', url: 'section.list-vehicles.php', forceReload: true });">
    <i class="fa-solid fa-chevron-left"></i>
    List
  </button>
</div>




  <div class="d-flex justify-content-between">
    <h3>
      <i class="fa-solid fa-square fa-lg" style="color:<?=$vehicle->color?>"></i>
      <?=$vehicle->name?>
    </h3>
    <button onclick="$(document).trigger('loadMainSection', { sectionId: 'vehicles', url: 'section.edit-vehicle.php?id=<?=$vehicleId?>', forceReload: true });" class="btn btn-outline-primary btn-sm align-self-center">Edit</button>
  </div>

  <div class="row mb-3">
    <div class="col-12 col-lg-8 col-xl-6 col-xxl-4 mb-1">
      <div class="input-group">
        <span class="input-group-text">Description</span>
        <input type="text" class="form-control" value="<?=$vehicle->description?>" readonly>
      </div>
    </div>
    <div class="col-12 col-sm-8 col-lg-6 col-xl-4 col-xxl-3 mb-1">
      <div class="input-group">
        <span class="input-group-text">License Plate</span>
        <input type="text" class="form-control" value="<?=$vehicle->licensePlate?>" readonly>
      </div>
    </div>
    <div class="col-12 col-sm-6 col-lg-4 col-xl-3 col-xxl-2 mb-1">
      <div class="input-group">
        <span class="input-group-text">Capacity</span>
        <input type="text" class="form-control" value="<?=$vehicle->passengers?>" readonly>
      </div>
    </div>
    <div class="col-12 col-sm-6 col-lg-4 col-xl-3 mb-1">
      <div class="input-group">
        <span class="input-group-text">CDL Required</span>
        <input type="text" class="form-control" value="<?=$vehicle->requireCDL ? 'Yes' : 'No' ?>" readonly>
      </div>
    </div>
    <div class="col-12 col-lg-8 col-xl-6 col-xxl-4 mb-1">
      <div class="input-group">
        <span class="input-group-text">Staging Location</span>
        <input type="text" class="form-control" value="<?=$vehicle->stagingLocation->name?>" readonly>
      </div>
    </div>
  </div>

  <div class="d-flex">
    <div class="alert alert-info px-4 py-2" role="alert">
      <div class="fw-bold fs-4 mb-3">
        Next Trip/Event
      </div>
      <div id="nextTripEventDetail"></div>
    </div>
  </div>


  <ul class="nav nav-tabs" id="pills-tab" role="tablist">
    <li class="nav-item" role="presentation">
      <button class="nav-link active" id="pills-status-tab" data-bs-toggle="pill" data-bs-target="#pills-status" type="button" role="tab" aria-controls="pills-status" aria-selected="true">
        State
      </button>
    </li>
    <li class="nav-item" role="presentation">
      <button class="nav-link position-relative" id="pills-snags-tab" data-bs-toggle="pill" data-bs-target="#pills-snags" type="button" role="tab" aria-controls="pills-snags" aria-selected="false">
        Snags
        <span id="snag-count" class="d-none position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">3</span>
      </button>
    </li>
    <li class="nav-item" role="presentation">
      <button class="nav-link" id="pills-maintenance-tab" data-bs-toggle="pill" data-bs-target="#pills-maintenance" type="button" role="tab" aria-controls="pills-maintenance" aria-selected="false">Maintenance</button>
    </li>
    <li class="nav-item" role="presentation">
      <button class="nav-link position-relative" id="pills-document-tab" data-bs-toggle="pill" data-bs-target="#pills-document" type="button" role="tab" aria-controls="pills-document" aria-selected="false">
        Documents
        <span id="document-count" class="d-none position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">3</span>
      </button>
    </li>
  </ul>

  <div class="tab-content bg-body" id="pills-tabContent">

    <div class="tab-pane p-2 border border-top-0 show active" id="pills-status" role="tabpanel" aria-labelledby="pills-status-tab" tabindex="0"></div>

    <div class="tab-pane p-2 border border-top-0" id="pills-snags" role="tabpanel" aria-labelledby="pills-snags-tab" tabindex="0"></div>

    <div class="tab-pane p-2 border border-top-0" id="pills-maintenance" role="tabpanel" aria-labelledby="pills-maintenance-tab" tabindex="0">...</div>

    <div class="tab-pane p-2 border border-top-0" id="pills-document" role="tabpanel" aria-labelledby="pills-document-tab" tabindex="0">...</div>
  </div>  




<script>

$(async ƒ => {

    const vehicleId = <?=$id?>;

    $('#pills-status').load('section.vehicle-status.php?vehicleId='+vehicleId);
    $('#pills-document').load('section.vehicle-documents.php?vehicleId='+vehicleId);
    $('#pills-snags').load('section.vehicle-snags.php?vehicleId='+vehicleId);

    // reFormat();

    // nextTripEventDetail
    const nextTrip = await net.get('/api/get.next-trip.php', {id: vehicleId});
    if (nextTrip.starts === null) return $('#nextTripEventDetail').html('Nothing scheduled at this time.');
    const starts = moment(nextTrip.starts, 'YYYY-MM-DD H:mm:ss');
    $('#nextTripEventDetail').html(
      `<div style="font-size: small" class="text-black-50">` +
      timeago.format(nextTrip.starts).toSentenceCase() + ' (' + starts.format('M/D h:mma') + ') ' 
      + `</div>`
      + `<div><i class="fa-solid fa-circle-right text-primary"></i> ${nextTrip.name}</div>`
    );

  });
</script>