<?php
require_once 'autoload.php';

use Transport\Airline;

$id = !empty($_GET['id']) ? (int)$_GET['id'] : null;
$airline = new Airline($id);
?>
<?php if (isset($_GET['id']) && !$airline->getId()): ?>

  <div class="container-fluid text-center">
    <div class="alert alert-danger mt-5 w-50 mx-auto">
      <h1 class="fw-bold">Oh dear!</h1>
      <p class="lead">I can't seem to find that airline! <i class="fa-duotone fa-solid fa-face-thinking"></i></p>
    </div>
  </div>

<?php else: ?>

  <div class="container mt-2">
    <?php if ($airline->getId()): ?>
      <h2>Edit Airline</h2>
    <?php else: ?>
      <h2>Add Airline</h2>
    <?php endif; ?>
    <div>

      <div class="row">
        <div class="col mb-3">
          <label for="_airline-name" class="form-label">Name</label>
          <input type="text" class="form-control" id="_airline-name" placeholder="Airline Name" value="<?=$airline->name?>">
        </div>
      </div>

      <div class="row">
        <div class="col-3 mb-3">
          <label for="_airline-flight-number-prefix" class="form-label">Flight Number Prefix</label>
          <input type="text" class="form-control" id="_airline-flight-number-prefix" value="<?=$airline->flightNumberPrefix?>">
        </div>
      </div>

      <div class="row">
        <div><img id="airline-image" class="img-fluid d-none" style="max-height:40px"></div>
        <div class="mb-3">
          <label for="formFile" class="form-label">Upload Airline Image</label>
          <input class="form-control" type="file" id="airlineImage">
        </div>
      </div>

      <div class="row my-4">
        <div class="col d-flex justify-content-between">
          <?php if ($airline->getId()): ?>
            <button class="btn btn-outline-danger px-4" id="btn-delete-airline">Delete</button>
          <?php endif; ?>
          <button class="btn btn-primary px-4" id="btn-save-airline">Save</button>
        </div>
      </div>

    </div>
  </div>

  <script type="text/javascript">

    $(async ƒ => {

      const airlineId = <?=$airline->getId() ?: 'null'?>;
      let airlineImage = '<?=$airline->imageFilename?>';

      function reloadSection () {
        $('#<?=$_GET["loadedToId"]?>').load(`<?=$_SERVER['REQUEST_URI']?>`); // Refresh this page
      }

      function getData () {
        const formData = new FormData();
        if (airlineId) formData.append('id', airlineId);
        formData.append('name', cleanProperVal('#_airline-name'));
        formData.append('flightNumberPrefix', cleanUpperVal('#_airline-flight-number-prefix'));
        if ($('#airlineImage')[0].files[0]) formData.append('image', $('#airlineImage')[0].files[0]);
        return formData;
      }

      if (airlineImage !== '') {
  			const url = `/images/airlines/${airlineImage}`;
			  $('#airline-image').attr('src', url).removeClass('d-none');
		  }

      $('#btn-save-airline').off('click').on('click', async ƒ => {
        const data = getData();
        console.log(data);
        const response = await fetch('/api/post.save-airline.php', {
          method: 'POST',
          body: data
        });
        const resp = await response.json();
        console.log(`Response: ${resp}`);

        if (resp?.result) {
          $(document).trigger('airlineChange', {airlineId});
          app.closeOpenTab();
          if (airlineId) return toastr.success('Airline saved.', 'Success');
          return toastr.success('Airline added.', 'Success')
        }
        toastr.error(resp?.errors[2], 'Error');
        console.log(resp);
      });

      $('#btn-delete-airline').off('click').on('click', async ƒ => {
        if (await ask('Are you sure you want to delete this airline?')) {
          const resp = await get('/api/get.delete-airline.php', {
            id: airlineId
          });
          if (resp?.result) {
            $(document).trigger('airlineChange', {airlineId});
            app.closeOpenTab();
            return toastr.success('Airline deleted.', 'Success')
          }
          console.log(resp);
          toastr.error('There seems to be a problem deleting airline.', 'Error');
        }
      });

    });

  </script>

<?php endif; ?>