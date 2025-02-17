<?php
require_once 'autoload.php';

use Transport\DriverNote;
use Generic\Utils;

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT, FILTER_NULL_ON_FAILURE);
$id = $id === false ? null : $id;
$note = new DriverNote($id);

if (!is_null($id) && !$note->getId())
{
  exit(Utils::showResourceNotFound());
}
?>

<!-- Back button -->
<div class="d-flex justify-content-between top-page-buttons mb-2">
  <button class="btn btn-sm btn-outline-primary px-2 me-auto" onclick="$(document).trigger('driverNotes:reloadList');">
    <i class="fa-solid fa-chevron-left"></i>
    List
  </button>
</div>


<?php if ($note->getId()): ?>
  <h2>Edit Note</h2>
  <input type="hidden" id="note-id" value="<?= $note->getId() ?>">
<?php else: ?>
  <h2>Add Note</h2>
  <input type="hidden" id="note-id" value="">
<?php endif; ?>

<div class="row">
  <div class="col">
    <div class="mb-3">
      <label for="note-title" class="form-label">Note Title</label>
      <input type="text" class="form-control" id="note-title" placeholder="Note Title" value="<?= $note->title ?>">
    </div>
  </div>
</div>
<div class="row">
  <div class="col">
    <div class="mb-3">
      <label for="note-content" class="form-label">Note: (You may use Markdown syntax. <a href="https://www.markdownguide.org/cheat-sheet/" target="_blank">Click here for reference</a>)</label>
      <textarea class="form-control font-monospace" id="note-content" rows="10" placeholder="Note"><?= $note->note ?></textarea>
    </div>
  </div>
</div>

<div class="d-flex justify-content-between mt-3">
  <?php if ($note->getId()): ?>
    <button class="btn btn-outline-danger" onclick="$(document).trigger('buttonDelete:driverNote', <?= $blockoutId ?>)">Delete</button>
  <?php endif; ?>
  <button class="btn btn-outline-primary" onclick="$(document).trigger('buttonSave:driverNote', '<?= $blockoutId ?>')">Save</button>
</div>


<script>
  $(async ƒ => {

    function backToList() {
      $(document).trigger('loadMainSection', {
        sectionId: 'driverNotes',
        url: 'section.list-driver-notes.php',
        forceReload: true
      });
    }

    if (!documentEventExists('buttonSave:driverNote')) {
      $(document).on('buttonSave:driverNote', async (e, id) => {
        const noteId = id;
        const resp = await net.post('/api/post.save-driver-note.php', {
          id: noteId,
          title: $('#note-title').cleanProperVal(),
          note: $('#note-content').cleanVal(),
        });
        if (resp?.result) {
          $(document).trigger('driverNoteChange');
          if (noteId) {
            ui.toastr.success('Note saved.', 'Success');
            return backToList();
          }
          ui.toastr.success('Note added.', 'Success');
          return backToList();
        }
        ui.toastr.error(resp.result.errors[2], 'Error');
        console.error(resp);
      });
    }

    if (!documentEventExists('buttonDelete:driverNote')) {
      $(document).on('buttonDelete:driverNote', async (e, id) => {
        const noteId = id;
        if (await ui.ask('Are you sure you want to delete this note?')) {
          const resp = await net.get('/api/get.delete-driver-note.php', {
            id: noteId
          });
          if (resp?.result) {
            $(document).trigger('driverNoteChange');
            ui.toastr.success('Note deleted.', 'Success');
            return backToList();
          }
          console.error(resp);
          ui.toastr.error('There seems to be a problem deleting note.', 'Error');
        }
      });
    }

  });
</script>