<?php
require_once 'autoload.php';

use Transport\User;

if (!isset($_SESSION['view'])) {
	if (!isset($user)) $user = new User($_SESSION['user']->id);
	$_SESSION['view'] = $user->roles[0];
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="utf-8">

	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	<meta name="description" content="Transportation System - Management Console">
	<meta name="author" content="Richard O'Brien">
	<link rel="icon" type="image/x-icon" href="/images/logo.svg">

	<title>Transportation Management Console</title>

	<!-- Fontawesome - necessary for icons -->
	<script src="https://kit.fontawesome.com/cc9f38bd60.js" crossorigin="anonymous"></script>
	<!-- <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css"> -->

	
	<!-- Necessary Javascript that should come before anything else -->
	<!-- Still rely heavily on jQuery. It serves our purposes quite well -->
	<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js" integrity="sha512-v2CJ7UaYy4JwqLDIrZUI/4hqeoQieOmAZNXBeQyjo21dadnwR+8ZaIJVT8EE2iyI61OV8e6M8PP2/4hpQINQ/g==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
	<!-- And of course our Bootstrap javascript -->
	<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.3/js/bootstrap.bundle.min.js" integrity="sha512-7Pi/otdlbbCR+LnW+F7PwFcSDJOuUJB3OxtEHbg4vSMvzvJjde4Po1v4BR9Gdc9aXNUNFVUY+SK51wWT8WF0Gg==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>


	<!-- Bootstrap Select -->
	<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-select@1.14.0-beta3/dist/css/bootstrap-select.min.css">
	<script src="https://cdn.jsdelivr.net/npm/bootstrap-select@1.14.0-beta3/dist/js/bootstrap-select.min.js"></script>
	<!-- Popperjs -->
	<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js" integrity="sha256-BRqBN7dYgABqtY9Hd4ynE+1slnEw+roEPFzQ7TRRfcg=" crossorigin="anonymous"></script>

	<!-- TinyMCE for WYSIWYG editing! -->
	<!-- <script src="https://cdnjs.cloudflare.com/ajax/libs/tinymce/7.5.1/tinymce.min.js" integrity="sha512-8+JNyduy8cg+AUuQiuxKD2W7277rkqjlmEE/Po60jKpCXzc+EYwyVB8o3CnlTGf98+ElVPaOBWyme/8jJqseMA==" crossorigin="anonymous" referrerpolicy="no-referrer"></script> -->

	<script src="https://cdnjs.cloudflare.com/ajax/libs/dropzone/5.9.3/dropzone.min.js" integrity="sha512-U2WE1ktpMTuRBPoCFDzomoIorbOyUv0sP8B+INA3EzNAhehbzED1rOJg6bCqPf/Tuposxb5ja/MAUnC8THSbLQ==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/dropzone/5.9.3/dropzone.min.css" integrity="sha512-jU/7UFiaW5UBGODEopEqnbIAHOI8fO6T99m7Tsmqs2gkdujByJfkCbbfPSN4Wlqlb9TGnsuC0YgUgWkRBK7B9A==" crossorigin="anonymous" referrerpolicy="no-referrer" />
	
	<!-- Moment -->
	<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.30.1/moment-with-locales.min.js" integrity="sha512-4F1cxYdMiAW98oomSLaygEwmCnIP38pb4Kx70yQYqRwLVCs3DbRumfBq82T08g/4LJ/smbFGFpmeFlQgoDccgg==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/moment-timezone/0.5.46/moment-timezone-with-data.min.js" integrity="sha512-4MAP/CJtK3ASCmbYjYxWAbHWASAx1UYMc1i83cBdQZXegqFfqSZ9WqpmkRGfvzeAI18yvKiDTlgX/TLNMpxkSQ==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
	<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/timeago.js/4.0.2/timeago.full.min.js"></script>

	<!-- <script src="https://cdnjs.cloudflare.com/ajax/libs/lodash.js/4.17.21/lodash.min.js" integrity="sha512-WFN04846sdKMIP5LKNphMaWzU7YpMyCU245etK3g/2ARYbPK9Ub18eG+ljU96qKRCWh+quCY7yefSmlkQw1ANQ==" crossorigin="anonymous" referrerpolicy="no-referrer"></script> -->

	<!-- Ace theme(s) -->
	<script src="https://cdnjs.cloudflare.com/ajax/libs/json5/2.2.3/index.min.js" integrity="sha512-44jdhc+R2TFfzBflS3/dGNEABiNUxBkkrqwO7GWTvGsj3HkQNr3GESvI9PUvAxmqxSnTosR0Ij9y3+o+6J1hig==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/ace/1.36.4/ace.min.js" integrity="sha512-9xNuS6O4ZXZdCVDekkW4ApP8MfH2SCyK7Wsd4g0l3KDmbNld2vsozYGY6kQup0VKB0iT89cLj/DiRSV7WjfUaw==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/ace/1.36.4/mode-json5.min.js" integrity="sha512-qiTdaKQmVlm7hpUZaFVX5tdDgH6oqZx2I9ChggYoeuECMjCMRT/+YBvA0RcxX5hCRW9dP1ZNRGWqOnX3MtU17w==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/ace/1.36.4/theme-xcode.min.js" integrity="sha512-GdLo/7fZFpTgzlSOPaN1qzIRRTq4NHXr5bH5sfBgiIuU/bqo+bvXAAb1+IWJ5mwYSYApoUDbFKc46Bbn883IgA==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>

	<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

	<!-- Datatables ...yes, it's better with them -->
	<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/2.1.8/css/dataTables.bootstrap5.min.css">
	<script type="text/javascript" src="https://cdn.datatables.net/2.1.8/js/dataTables.min.js"></script>
	<script type="text/javascript" src="https://cdn.datatables.net/2.1.8/js/dataTables.bootstrap5.min.js"></script>
	<!-- Data Tables - Buttons -->
	<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/buttons/3.2.0/css/buttons.bootstrap5.min.css">
	<script type="text/javascript" src="https://cdn.datatables.net/buttons/3.2.0/js/dataTables.buttons.min.js"></script>
	<script type="text/javascript" src="https://cdn.datatables.net/buttons/3.2.0/js/buttons.bootstrap5.min.js"></script>
	<!-- Data Tables - Responsive -->
	<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/responsive/3.0.3/css/responsive.bootstrap5.min.css">
	<script type="text/javascript" src="https://cdn.datatables.net/responsive/3.0.3/js/dataTables.responsive.min.js"></script>
	<script type="text/javascript" src="https://cdn.datatables.net/responsive/3.0.3/js/responsive.bootstrap5.min.js"></script>


	<script type="text/javascript" src="js/autocomplete.js"></script>

	<!-- Calendar -->
	<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@event-calendar/build@3.7.0/event-calendar.min.css">
	<link rel="stylesheet" href="/css/ec-calendar.css">
	<script src="https://cdn.jsdelivr.net/npm/@event-calendar/build@3.7.0/event-calendar.min.js"></script>
	
	<!-- All our custom javascript -->
	<script type="text/javascript" src="js/common.js?<?=filemtime('js/common.js')?>"></script>
	<script type="text/javascript" src="js/tab-management.js"></script>

	<!-- Stylesheets -->
	<!-- Our main (custom) Bootstrap theme -->
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.3/css/bootstrap.min.css" integrity="sha512-jnSuA4Ss2PkkikSOLtYs8BlYIeeIK1h99ty4YfvRPAlzr377vr3CXDb7sb7eEEBYjDtcYj+AjBH3FLv5uSJuXg==" crossorigin="anonymous" referrerpolicy="no-referrer" />

	<!-- And of course our own styling -->
	<link rel="stylesheet" type="text/css" href="css/style.css?<?=filemtime('css/style.css')?>">
</head>

<body class="bg-body-secondary">
