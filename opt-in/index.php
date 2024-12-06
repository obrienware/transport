<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="utf-8">

	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	<meta name="description" content="">
	<meta name="author" content="Richard O'Brien">
	<link rel="icon" type="image/png" href="/Icon.png" />

	<title>CBC/Charis Transportation</title>

	<!-- Fontawesome - necessary for icons -->
	<script src="https://kit.fontawesome.com/cc9f38bd60.js" crossorigin="anonymous"></script>

	<!-- Necessary Javascript that should come before anything else -->
	<!-- Still rely heavily on jQuery. It serves our purpose quite well -->
	<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js" integrity="sha512-v2CJ7UaYy4JwqLDIrZUI/4hqeoQieOmAZNXBeQyjo21dadnwR+8ZaIJVT8EE2iyI61OV8e6M8PP2/4hpQINQ/g==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
	<!-- And of course our Bootstrap javascript -->
	<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.3/js/bootstrap.bundle.min.js" integrity="sha512-7Pi/otdlbbCR+LnW+F7PwFcSDJOuUJB3OxtEHbg4vSMvzvJjde4Po1v4BR9Gdc9aXNUNFVUY+SK51wWT8WF0Gg==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>

	<!-- Moment -->
	<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.30.1/moment-with-locales.min.js" integrity="sha512-4F1cxYdMiAW98oomSLaygEwmCnIP38pb4Kx70yQYqRwLVCs3DbRumfBq82T08g/4LJ/smbFGFpmeFlQgoDccgg==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/moment-timezone/0.5.46/moment-timezone-with-data.min.js" integrity="sha512-4MAP/CJtK3ASCmbYjYxWAbHWASAx1UYMc1i83cBdQZXegqFfqSZ9WqpmkRGfvzeAI18yvKiDTlgX/TLNMpxkSQ==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
	<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/timeago.js/4.0.2/timeago.full.min.js"></script>

	<script src="https://cdnjs.cloudflare.com/ajax/libs/lodash.js/4.17.21/lodash.min.js" integrity="sha512-WFN04846sdKMIP5LKNphMaWzU7YpMyCU245etK3g/2ARYbPK9Ub18eG+ljU96qKRCWh+quCY7yefSmlkQw1ANQ==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>

	<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

	<!-- All our customized javascript -->
	<script type="text/javascript" src="/js/common.js?<?=filemtime('js/common.js')?>"></script>

	<!-- Stylesheets -->
	<!-- Our main (custom) Bootstrap theme -->
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.3/css/bootstrap.min.css" integrity="sha512-jnSuA4Ss2PkkikSOLtYs8BlYIeeIK1h99ty4YfvRPAlzr377vr3CXDb7sb7eEEBYjDtcYj+AjBH3FLv5uSJuXg==" crossorigin="anonymous" referrerpolicy="no-referrer" />

	<!-- And of course our own styling -->
	<link rel="stylesheet" type="text/css" href="/css/style.css?<?=filemtime('css/style.css')?>">

</head>

<body>
  <div class="container-login100">
    <div class="row w-100">
      <div class="bg-white rounded d-flex flex-column text-center shadow-lg p-4 mx-auto" style="max-width:375px">
        <h1 class="my-4 fw-bold text-primary-emphasis">
					<img src="/images/logo.svg" style="height:2em"/>
					Transport
				</h1>
        <h2 class="fw-light">Opt-In to <strong>SMS/TEXT</strong> updates</h2>
        <p class="mb-4">
					By submitting your phone number below, you agree to receive informational SMS/text messages regaring your trip(s) with us. <br/>(Data rates may apply.)
				</p>
        <div class="form-floating mb-3">
          <input type="phone" class="form-control" id="phone">
          <label for="phone">Phone Number</label>
        </div>
        <div class="mt-4">
          <button id="btn-ok" class="px-5 btn btn-primary btn-lg">Okay</button>
        </div>
      </div>
    </div>
  </div>

  
  <script type="text/javascript">

    $(async ƒ => {

			$('#btn-ok').on('click', async ƒ => {
        const phone = cleanVal('#phone');
        const resp = await get('get.opt-in.php', {phone});
        if (resp) {
          return alertSuccess(
            'You have successfully opted in receive information updates.',
            'Success'
          );
        }
      });


    });

  </script>
</body>
</html>