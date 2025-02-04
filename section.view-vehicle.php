<?php
require_once 'autoload.php';

use Transport\Snag;
use Transport\Utils;
use Transport\Vehicle;

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT, FILTER_NULL_ON_FAILURE);
$vehicleId = $id === false ? null : $id;
$vehicle = new Vehicle($vehicleId);
?>
<div class="container">

  <div class="d-flex justify-content-between">
    <h3>
      <i class="bi bi-square-fill" style="color:<?=$vehicle->color?>"></i>
      <?=$vehicle->name?>
    </h3>
    <button onclick="app.openTab('edit-vehicle', 'Vehicle (edit)', `section.edit-vehicle.php?id=<?=$vehicleId?>`);" class="btn btn-outline-primary btn-sm align-self-center">Edit</button>
  </div>

  <table class="table table-bordered table-sm">
    <tr>
      <th class="fit px-2 bg-body-secondary">Description</th>
      <td><?=$vehicle->description?></td>
      <th class="fit px-2 bg-body-secondary">License Plate</th>
      <td class="fit px-2"><?=$vehicle->licensePlate?></td>
    </tr>
    <tr>
      <th class="fit px-2 bg-body-secondary">Passengers</th>
      <td colspan="3"><?=$vehicle->passengers?></td>
    </tr>
    <tr>
      <th class="fit px-2 bg-body-secondary">Requires a CDL driver</th>
      <td colspan="3"><?=$vehicle->requireCDL ? 'Yes' : 'No' ?></td>
    </tr>
    <tr>
      <th class="fit px-2 bg-body-secondary">Staging Location</th>
      <td colspan="3"><?=$vehicle->stagingLocation->name?></td>
    </tr>
    <tr>
      <th class="fit px-2 bg-body-secondary">Next Trip/Event</th>
      <td colspan="3" id="nextTripEventDetail"></td>
    </tr>
  </table>


  <ul class="nav nav-pills mb-3" id="pills-tab" role="tablist">
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
    <!--
    <li class="nav-item" role="presentation">
      <button class="nav-link" id="pills-disabled-tab" data-bs-toggle="pill" data-bs-target="#pills-disabled" type="button" role="tab" aria-controls="pills-disabled" aria-selected="false" disabled>Disabled</button>
    </li>
    -->
  </ul>

  <div class="tab-content" id="pills-tabContent">

    <div class="tab-pane fade show active" id="pills-status" role="tabpanel" aria-labelledby="pills-status-tab" tabindex="0"></div>

    <div class="tab-pane fade" id="pills-snags" role="tabpanel" aria-labelledby="pills-snags-tab" tabindex="0"></div>

    <div class="tab-pane fade" id="pills-maintenance" role="tabpanel" aria-labelledby="pills-maintenance-tab" tabindex="0">...</div>

    <div class="tab-pane fade" id="pills-document" role="tabpanel" aria-labelledby="pills-document-tab" tabindex="0">...</div>

    <!-- <div class="tab-pane fade" id="pills-disabled" role="tabpanel" aria-labelledby="pills-disabled-tab" tabindex="0">...</div> -->
  </div>  

</div>



<script type="module">
  import * as input from '/js/formatters.js';
  import * as ui from '/js/notifications.js';
  import * as net from '/js/network.js';
  import { reFormat } from '/js/main.js';

  $(async ƒ => {

    const vehicleId = <?=$vehicleId?>;

    $('#pills-status').load('section.vehicle-status.php?vehicleId='+vehicleId);
    $('#pills-document').load('section.vehicle-documents.php?vehicleId='+vehicleId);
    $('#pills-snags').load('section.vehicle-snags.php?vehicleId='+vehicleId);

    reFormat();

    // nextTripEventDetail
    const nextTrip = await net.get('/api/get.next-trip.php', {id: vehicleId});
    if (nextTrip.starts === null) return $('#nextTripEventDetail').html('Nothing scheduled');
    const starts = moment(nextTrip.starts, 'YYYY-MM-DD H:mm:ss');
    $('#nextTripEventDetail').html(
      `<div style="font-size: small" class="text-black-50">` +
      timeago.format(nextTrip.starts).toSentenceCase() + ' (' + starts.format('M/D h:mma') + ') ' 
      + `</div>`
      + `<div><i class="fa-solid fa-circle-right text-primary"></i> ${nextTrip.name}</div>`
    );

  });
</script>