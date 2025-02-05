<?php
require_once 'autoload.php';

use Transport\User;

// This should only be included before any output has been sent to the browser
if (!isset($_SESSION['user']) || !isset($_SESSION['user']->authenticated) || !isset($_SESSION['user']->id)) {
	// Redirect to a login page
	$request_uri = base64_encode($_SERVER['REQUEST_URI']);
	header('location: page.authenticate.php?return=' . $request_uri);
	exit();
}

$user = new User($_SESSION['user']->id);

/**
 * The following would take action if the user did not have the required role(s) - send a 403 error code
 * @param array $roles
 * @return void
 */
function allowRoles(array $roles)
{
	if (!allowedRoles($roles)) {
		http_response_code(403);
		exit('Sorry! You do not have permission to access this page. Please talk to your admin if you have questions.');
	}
}

/**
 * This function only determines whether the user has the specified role(s)
 * @param array $roles
 * @return bool
 */
function allowedRoles(array $roles)
{
	global $user;
	return $user->hasRole($roles);
}
