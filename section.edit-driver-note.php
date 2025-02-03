<?php
require_once 'autoload.php';

use Transport\DriverNote;

$id = !empty($_GET['id']) ? (int)$_GET['id'] : null;
$note = new DriverNote($id);
?>
<?php if (isset($_GET['id']) && !$note->getId()): ?>

  <div class="container-fluid text-center">
    <div class="alert alert-danger mt-5 w-50 mx-auto">
      <h1 class="fw-bold">Oh dear!</h1>
      <p class="lead">I can't seem to find that note! <i class="fa-duotone fa-solid fa-face-thinking"></i></p>
    </div>
  </div>

<?php else: ?>

  <div class="container mt-2">
    <?php if ($note->getId()): ?>
      <h2>Edit Note</h2>
    <?php else: ?>
      <h2>Add Note</h2>
    <?php endif; ?>
    <div>
      <div class="row">
        <div class="col">
          <div class="mb-3">
            <label for="note-title" class="form-label">Note Title</label>
            <input type="text" class="form-control" id="note-title" placeholder="Note Title" value="<?=$note->title?>">
          </div>
        </div>
      </div>
      <div class="row">
        <div class="col">
          <div class="mb-3">
            <label for="note-content" class="form-label">Note: (You may use Markdown syntax. <a href="https://www.markdownguide.org/cheat-sheet/" target="_blank">Click here for reference</a>)</label>
            <textarea class="form-control font-monospace" id="note-content" rows="10" placeholder="Note"><?=$note->note?></textarea>
          </div>
        </div>
      </div>

      <div class="row my-4">
        <div class="col d-flex justify-content-between">
          <?php if ($note->getId()): ?>
            <button class="btn btn-outline-danger px-4" id="btn-delete-driver-note">Delete</button>
          <?php endif; ?>
          <button class="btn btn-primary px-4" id="btn-save-driver-note">Save</button>
        </div>
      </div>

    </div>
  </div>

  <script type="module">
    import * as input from '/js/formatters.js';
    import * as ui from '/js/notifications.js';
    import * as net from '/js/network.js';

    $(async ƒ => {

      const noteId = <?=$note->getId() ?: 'null'?>;
      $('#btn-save-driver-note').off('click').on('click', async ƒ => {
        const resp = await net.post('/api/post.save-driver-note.php', {
          id: noteId,
          title: input.cleanProperVal('#note-title'),
          note: input.cleanVal('#note-content'),
        });
        if (resp?.result) {
          $(document).trigger('driverNoteChange', {noteId});
          app.closeOpenTab();
          if (noteId) return ui.toastr.success('Note saved.', 'Success');
          return ui.toastr.success('Note added.', 'Success')
        }
        ui.toastr.error(resp .result.errors[2], 'Error');
        console.log(resp);
      });

      $('#btn-delete-driver-note').off('click').on('click', async ƒ => {
        if (await ui.ask('Are you sure you want to delete this note?')) {
          const resp = await net.get('/api/get.delete-driver-note.php', {
            id: noteId
          });
          if (resp?.result) {
            $(document).trigger('driverNoteChange', {noteId});
            app.closeOpenTab();
            return ui.toastr.success('Note deleted.', 'Success')
          }
          console.log(resp);
          ui.toastr.error('There seems to be a problem deleting note.', 'Error');
        }
      });

    });

  </script>

<?php endif; ?>