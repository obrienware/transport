<?php 
require_once '../autoload.php';

use Transport\DriverNote;
?>
<?php if ($rows = DriverNote::getAll()): ?>

  <div class="px-2" id="note-list">
    <ul class="list-group">
      <?php foreach ($rows as $row): ?>
        <li class="list-group-item list-group-item-action note-item" data-id="<?=$row->id?>">
          <?=$row->title?>
        </li>
      <?php endforeach; ?>
    </ul>
  </div>

  <div id="note-section" class="d-none p-3">
    <section id="note-content" class="parsedown"></section>
    <div class="hstack gap-2">
      <button id="btn-close-note-view ms-auto" class="btn btn-outline-primary">Done</button>
    </div>
  </div>


<?php else: ?>

  <div class="p-5 text-center">
    <div class="alert alert-info">No notes to show at this time</div>
  </div>

<?php endif;?>

<script>

  $(async ƒ => {

    $('.note-item').off('click').on('click', async function (e) {
      const id = $(this).data('id');
      $('#note-list').addClass('d-none');
      $('#note-section').removeClass('d-none')
      $('#note-content').html('Loading...').load(`section.note-detail.php?id=${id}`);
    });

    $('#btn-close-note-view').off('click').on('click', async function (e) {
      $('#note-list').removeClass('d-none');
      $('#note-section').addClass('d-none');
    });

  });

</script>