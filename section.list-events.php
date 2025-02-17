<?php
require_once 'autoload.php';

use Transport\Database;

$db = Database::getInstance();
$query = "
  SELECT 
    e.*, 
    (
      SELECT GROUP_CONCAT(CONCAT(first_name,' ',last_name) SEPARATOR ', ') FROM users d WHERE FIND_IN_SET(d.id, e.driver_ids)
    ) AS drivers,
    (
      SELECT GROUP_CONCAT(v.name SEPARATOR ', ') FROM vehicles v WHERE FIND_IN_SET(v.id, e.vehicle_ids)
    ) AS vehicles,
    CASE WHEN (l.short_name IS NOT NULL) THEN l.short_name ELSE l.name END AS location
  FROM events e
  LEFT OUTER JOIN locations l ON l.id = e.location_id
  WHERE
    start_date > DATE_SUB(CURDATE(), INTERVAL 7 DAY)
    AND e.archived IS NULL
  ORDER BY start_date ASC
";
$rows = $db->get_rows($query)
?>

<div class="d-flex justify-content-between top-page-buttons">
  <button class="btn btn-sm btn-outline-primary px-2 ms-auto me-2" onclick="$(document).trigger('event:reloadList');">
    <i class="fa-solid fa-arrow-rotate-right"></i>
    Reload
  </button>
  <button class=" btn btn-outline-primary btn-sm px-2" onclick="$(document).trigger('loadMainSection', { sectionId: 'events', url: 'section.edit-event.php', forceReload: true });">
    New Event
  </button>
</div>


<div class="d-flex justify-content-between mt-2">
  <h2>Events</h2>
</div>

<table id="table-events" class="table align-middle table-striped">
  <thead>
    <tr class="table-dark">
      <th class="fit no-sort">Confirmed</th>
      <th>Description</th>
      <th class="fit">From</th>
      <th class="fit">To</th>
      <th>Where</th>
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
        $tdClass = 'table-secondary';
      }
      elseif (Date('Y-m-d') <= Date('Y-m-d', strtotime($row->end_date)) && Date('Y-m-d') >= Date('Y-m-d', strtotime($row->start_date)))
      {
        $tdClass = 'table-success';
      }
      ?>
      <tr data-id="<?= $row->id ?>" data-confirmed="<?=$row->confirmed?>" class="<?= $tdClass ?>">
        <td class="text-center fit" data-order="<?= $row->confirmed ?>">
          <?php if ($row->confirmed): ?>
            <i class="fa-solid fa-circle-check fa-xl text-success"></i>
          <?php else: ?>
            <i class="fa-solid fa-circle-xmark fa-xl text-black text-opacity-25"></i>
          <?php endif; ?>
        </td>
        <td>
          <?php if ($row->cancellation_requested): ?>
            <i class="badge bg-danger">Cancelled</i>
          <?php endif; ?>
          <?= $row->name ?>
        </td>
        <td class="fit datetime text-center align-middle" data-order="<?= $row->start_date ?>"><?= $row->start_date ?></td>
        <td class="fit datetime text-center align-middle" data-order="<?= $row->end_date ?>"><?= $row->end_date ?></td>
        <td><?= $row->location ?></td>
        <td class="text-center align-middle">
          <i class="fa-solid fa-ellipsis fa-xl text-body-tertiary action-icon pointer hidden-content" onclick="$(document).trigger('listActionItem:event', this)"></i>
        </td>
      </tr>
    <?php endforeach; ?>
  </tbody>
</table>


<script>

  if (!documentEventExists('event:reloadList')) {
    $(document).on('event:reloadList', async (e) => {
      $(document).trigger('loadMainSection', {
        sectionId: 'events',
        url: 'section.list-events.php',
        forceReload: true
      });
    });
  }

  if (!documentEventExists('listActionItem:event')) {
    $(document).on('listActionItem:event', async (e, el) => {
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
          <button class="dropdown-item" onclick="$(document).trigger('event:confirm', ${id})"><i class="fa-duotone fa-regular fa-file-check"></i> Confirm</button>
        `;
      }

      // Create the dropdown menu
      const dropdownMenu = `
        <div id="${myRandomId}" data-id="${id}" class="dropdown-menu show shadow" style="position: absolute; left: ${offset.top}px; top: ${offset.left}px; z-index: 1000;">
          <button class="dropdown-item" onclick="$(document).trigger('event:edit', ${id})"><i class="fa-duotone fa-regular fa-pen-to-square"></i> Edit</button>
          ${additionalItems}
          <hr class="dropdown-divider">
          <button class="dropdown-item text-danger" onclick="$(document).trigger('event:delete', ${id})"><i class="fa-duotone fa-regular fa-trash"></i> Delete</button>
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

  if (!documentEventExists('event:edit')) {
    $(document).on('event:edit', async (e, id) => {
      $(document).trigger('loadMainSection', {
        sectionId: 'events',
        url: `section.edit-event.php?id=${id}`,
        forceReload: true
      });
    });
  }

  if (!documentEventExists('event:delete')) {
    $(document).on('event:delete', async (e, id) => {
      if (await ui.ask('Are you sure you want to delete this event?')) {
        const resp = await net.get('/api/get.delete-event.php', { id });
        if (resp.result) {
          ui.toastr.success('Event deleted successfully.', 'Success');
          $(document).trigger('eventChange');
          return $(document).trigger('event:reloadList');
        }
        ui.toastr.error('Failed to delete event: ' + resp.error, 'Error');
      }
    });
  }

  if (!documentEventExists('event:confirm')) {
    $(document).on('event:confirm', async (e, id) => {
      const resp = await net.post('/api/post.confirm-event.php', { id });
      if (resp.result) {
        ui.toastr.success('Event confirmed successfully.', 'Success');
        $(document).trigger('eventChange');
        return $(document).trigger('event:reloadList');
      }
      ui.toastr.error('Failed to confirm event: ' + resp.error, 'Error');
    });
  }


  $(async ƒ => {

    const tableId = 'table-events';
    const dataTableOptions = {
      responsive: true,
      order: [[2, 'asc']],
    };
    const reloadOnEventName = 'eventChange';
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