<?php
require_once 'autoload.php';

use Transport\Database;
use Generic\Utils;

$db = Database::getInstance();
$query = "
  SELECT 
  	t.id, t.driver_id, t. vehicle_id, t.airline_id,
  	t.summary, t.start_date, t.end_date, t.eta, t.etd, t.iata, t.flight_number, t.confirmed,
    t.started, t.completed, t.cancellation_requested,
  	a.name AS airline, a.flight_number_prefix,
    d.username AS driver_username,
  	CONCAT(d.first_name, ' ', SUBSTRING(d.last_name,1,1), '.') AS driver, d.phone_number,
  	v.name AS vehicle, v.description, v.color AS vehicle_color,
  	CONCAT(g.first_name, ' ', g.last_name) AS guest,
  	g.phone_number AS guest_contact_number,
    CASE WHEN (pu.short_name IS NOT NULL) THEN pu.short_name ELSE pu.name END AS pickup_location,
    CASE WHEN (do.short_name IS NOT NULL) THEN do.short_name ELSE do.name END AS dropoff_location
  FROM trips t
  LEFT OUTER JOIN guests g ON t.guest_id = g.id
  LEFT OUTER JOIN users d ON t.driver_id = d.id
  LEFT OUTER JOIN vehicles v ON t.vehicle_id = v.id
  LEFT OUTER JOIN airlines a ON t.airline_id = a.id
  LEFT OUTER JOIN locations pu ON t.pu_location = pu.id
  LEFT OUTER JOIN locations do ON t.do_location = do.id
  WHERE
    start_date > DATE_SUB(CURDATE(), INTERVAL 7 DAY)
    AND t.archived IS NULL
  ORDER BY start_date ASC
";
$rows = $db->get_rows($query)
?>

<div class="d-flex justify-content-between top-page-buttons">
  <button class="btn btn-sm btn-outline-primary px-2 ms-auto me-2" onclick="$(document).trigger('trip:reloadList');">
    <i class="fa-solid fa-arrow-rotate-right"></i>
    Reload
  </button>
  <button class=" btn btn-outline-primary btn-sm px-2" onclick="$(document).trigger('loadMainSection', { sectionId: 'trips', url: 'section.new-trip.php', forceReload: true });">
    New Trip
  </button>
</div>

<div class="d-flex justify-content-between mt-2">
  <h2>Trips</h2>
</div>

<table id="table-trips" class="table table-striped align-middle table-bordered">
  <thead>
    <tr class="table-dark">
      <th class="fit no-sort" data-priority="4">Confirmed</th>
      <th class="fit" data-priority="2">When</th>
      <th data-dt-order="disable" data-priority="1">Trip Summary</th>
      <th class="text-start">Pick Up</th>
      <th data-dt-order="disable">Drop Off</th>
      <th>Driver</th>
      <th>Vehicle</th>
      <th class="fit no-sort" data-priority="3">&nbsp</th>
    </tr>
  </thead>
  <tbody>
    <?php foreach ($rows as $row): ?>
      <?php
      $tdClass = '';
      if ($row->cancellation_requested)
      {
        $tdClass = 'table-secondary';
      }
      elseif (!$row->end_date or strtotime($row->end_date) <= strtotime('now') or $row->completed)
      {
        // Trip has passed or has been completed
        $tdClass = 'table-secondary';
      }
      elseif (Date('Y-m-d') <= Date('Y-m-d', strtotime($row->end_date)) && Date('Y-m-d') >= Date('Y-m-d', strtotime($row->start_date)))
      {
        // Trip is in progress
        $tdClass = 'table-success';
      }
      ?>
      <tr data-id="<?= $row->id ?>" data-confirmed="<?= $row->confirmed ?>" class="<?= $tdClass ?>">
        <!-- Confirmed -->
        <td class="text-center fit" data-order="<?= $row->confirmed ?>">
          <?php if ($row->confirmed): ?>
            <i class="fa-solid fa-circle-check fa-xl text-success"></i>
          <?php else: ?>
            <i class="fa-solid fa-circle-xmark fa-xl text-black text-opacity-25"></i>
          <?php endif; ?>
        </td>
        <!-- When -->
        <td class="fit datetime text-center align-middle" data-order="<?= $row->start_date ?>"><?= $row->start_date ?></td>
        <!-- Trip Summary -->
        <td>
          <div class="d-flex justify-content-between">
            <?php if ($row->completed) : ?>
              <i class="fa-duotone fa-regular fa-circle-check text-success align-self-center me-2" data-bs-toggle="tooltip" data-bs-title="Trip complete."></i>
            <?php elseif ($row->started): ?>
              <i class="fa-duotone fa-solid fa-spinner-third fa-spin text-success align-self-center me-2" data-bs-toggle="tooltip" data-bs-title="Currently in progress..."></i>
            <?php endif; ?>
            <?php if ($row->cancellation_requested): ?>
              <span class="badge bg-danger align-self-center me-2" style="font-size:medium; font-weight:200">Cancelled</span>
            <?php endif; ?>
            <div><?= $row->summary ?></div>
          </div>
        </td>
        <!-- Pick Up -->
        <td class="text-nowrap text-start" data-order="<?= $row->start_date ?>">
          <div>
            <?= $row->pickup_location ?>
          </div>
          <?php if ($row->eta): ?>
            <span class="badge bg-black fs-6" style="color:gold">
              <i class="~fa-duotone fa-solid fa-plane-arrival"></i>
              <?= $row->flight_number_prefix . ' ' . $row->flight_number ?>
            </span>
            <small><?= Date('g:ia', strtotime($row->eta)) ?></small>
          <?php endif; ?>
        </td>
        <!-- Drop Off -->
        <td class="text-nowrap">
          <div><?= $row->dropoff_location ?></div>
          <?php if ($row->etd): ?>
            <span class="badge bg-black fs-6" style="color:gold">
              <i class="~fa-duotone fa-solid fa-plane-departure"></i>
              <?= $row->flight_number_prefix . ' ' . $row->flight_number ?>
            </span>
            <small><?= Date('g:ia', strtotime($row->etd)) ?></small>
          <?php endif; ?>
        </td>
        <!-- Driver -->
        <td data-order="<?= $row->driver ?>" class="p-0 text-center">
          <?php if ($row->driver): ?>
            <img src="/images/drivers/<?= $row->driver_username ?>.jpg" class="rounded" style="width: 60px; height: 60px;" alt="<?= $row->driver ?>">
          <?php else: ?>
            <div class="p-3">
              <span class="badge bg-danger" style="font-size:medium; font-weight:200">Unassigned</span>
            </div>
          <?php endif; ?>
        </td>
        <!-- Vehicle -->
        <td data-order="<?= $row->vehicle ?>" class="text-center">
          <?php if ($row->vehicle): ?>
            <span class="tag nowrap w-100" style="background-color:<?= $row->vehicle_color ?>; color:<?= Utils::getContrastColor($row->vehicle_color) ?>"><?= $row->vehicle ?></span>
          <?php else: ?>
            <span class="badge bg-danger" style="font-size:medium; font-weight:200">Unassigned</span>
          <?php endif; ?>
        </td>
        <!-- <td>
          <button class="btn hidden-content" onclick="$(document).trigger('row-edit:trip', <?= $row->id ?>)"><i class="fa-solid fa-file-pen fa-lg text-muted"></i></button>
        </td> -->
        <td class="text-center align-middle">
          <i class="fa-solid fa-ellipsis fa-xl text-body-tertiary action-icon pointer hidden-content" onclick="$(document).trigger('listActionItem:trip', this)"></i>
        </td>
      </tr>
    <?php endforeach; ?>
  </tbody>
</table>


<script>

  if (!documentEventExists('trip:reloadList')) {
    $(document).on('trip:reloadList', async (e) => {
      $(document).trigger('loadMainSection', {
        sectionId: 'trips',
        url: 'section.list-trips.php',
        forceReload: true
      });
    });
  }

  if (!documentEventExists('listActionItem:trip')) {
    $(document).on('listActionItem:trip', async (e, el) => {
      e.stopPropagation();
      e.stopImmediatePropagation();

      const id = $(el).closest('tr').data('id');
      const confirmed = $(el).closest('tr').data('confirmed');
      const offset = $(el).offset();
      const myRandomId = Math.random().toString(36).substring(7);

      // Remove any existing dropdown menus
      $(document).trigger('click');

      let additionalItems = '';
      if (!confirmed) {
        additionalItems += `
          <button class="dropdown-item" onclick="$(document).trigger('trip:confirm', ${id})"><i class="fa-duotone fa-regular fa-file-check"></i> Confirm</button>
        `;
      } else {
        additionalItems += `
          <a href="print.trip-driver-sheet.php?id=${id}" target="_blank" class="dropdown-item"><i class="fa-duotone fa-regular fa-file-pdf"></i> Print Driver Sheet</a>
          <a href="print.trip-guest-sheet.php?id=${id}" target="_blank" class="dropdown-item"><i class="fa-duotone fa-regular fa-file-pdf"></i> Print Guest Sheet</a>
          <a href="download.trip-ics.php?id=${id}" target="_blank" class="dropdown-item"><i class="fa-duotone fa-regular fa-calendar-circle-plus"></i> Calendar Item</a>
        `;
      }

      // Create the dropdown menu
      const dropdownMenu = `
        <div id="${myRandomId}" data-id="${id}" class="dropdown-menu show shadow" style="position: absolute; left: ${offset.top}px; top: ${offset.left}px; z-index: 1000;">
          <button class="dropdown-item" onclick="$(document).trigger('trip:edit', ${id})"><i class="fa-duotone fa-regular fa-pen-to-square"></i> Edit</button>
          <button class="dropdown-item" onclick="$(document).trigger('trip:duplicate', ${id})"><i class="fa-duotone fa-regular fa-copy"></i> Duplicate</button>
          ${additionalItems}
          <hr class="dropdown-divider">
          <button class="dropdown-item text-danger" onclick="$(document).trigger('trip:delete', ${id})"><i class="fa-duotone fa-regular fa-trash"></i> Delete</button>
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

  if (!documentEventExists('trip:edit')) {
    $(document).on('trip:edit', async (e, id) => {
      $(document).trigger('loadMainSection', {
        sectionId: 'trips',
        url: `section.edit-trip.php?id=${id}`,
        forceReload: true
      });
    });
  }

  if (!documentEventExists('trip:duplicate')) {
    $(document).on('trip:duplicate', async (e, id) => {
      const resp = await net.get('/api/get.duplicate-trip.php', { id });
      if (resp.result) {
        // ui.toastr.success('Trip duplicated successfully.', 'Success');
        $(document).trigger('trip:reloadList')
        $(document).trigger('tripChange');
        if (await ui.ask('Trip duplicated successfully. Would you like to edit the new copy now?')) {
          return $(document).trigger('trip:edit', resp.result);
        };
        return;
      }
      ui.toastr.error('Failed to duplicate trip: ' + resp.error, 'Error');
    });
  }

  if (!documentEventExists('trip:delete')) {
    $(document).on('trip:delete', async (e, id) => {
      if (await ui.ask('Are you sure you want to delete this trip?')) {
        const resp = await net.get('/api/get.delete-trip.php', { id });
        if (resp.result) {
          ui.toastr.success('Trip deleted successfully.', 'Success');
          $(document).trigger('tripChange');
          return $(document).trigger('trip:reloadList');
        }
        ui.toastr.error('Failed to delete trip: ' + resp.error, 'Error');
      }
    });
  }

  if (!documentEventExists('trip:confirm')) {
    $(document).on('trip:confirm', async (e, id) => {
      const resp = await net.post('/api/post.confirm-trip.php', { id });
      if (resp.result) {
        ui.toastr.success('Trip confirmed successfully.', 'Success');
        $(document).trigger('tripChange');
        return $(document).trigger('trip:reloadList');
      }
      ui.toastr.error('Failed to confirm trip: ' + resp.error, 'Error');
    });
  }


  $(async ƒ => {

    const tableId = 'table-trips';
    const dataTableOptions = {
      responsive: true,
      paging: true,
      order: [[1, 'asc']],
    };
    const reloadOnEventName = 'tripChange';
    const parentSectionId = `#<?= $_GET["loadedToId"] ?>`;
    const thisURI = `<?= $_SERVER['REQUEST_URI'] ?>`;

    initListPage({
      tableId,
      dataTableOptions,
      reloadOnEventName,
      parentSectionId,
      thisURI
    });


  });
</script>