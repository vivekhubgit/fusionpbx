<?php
/*
	FusionPBX
	Version: MPL 1.1

	The contents of this file are subject to the Mozilla Public License Version
	1.1 (the "License"); you may not use this file except in compliance with
	the License. You may obtain a copy of the License at
	http://www.mozilla.org/MPL/

	Software distributed under the License is distributed on an "AS IS" basis,
	WITHOUT WARRANTY OF ANY KIND, either express or implied. See the License
	for the specific language governing rights and limitations under the
	License.

	The Original Code is FusionPBX

	The Initial Developer of the Original Code is
	Mark J Crane <markjcrane@fusionpbx.com>
	Portions created by the Initial Developer are Copyright (C) 2008-2012
	the Initial Developer. All Rights Reserved.

	Contributor(s):
	Mark J Crane <markjcrane@fusionpbx.com>
*/
include "root.php";
require_once "includes/require.php";
require_once "includes/checkauth.php";
if (permission_exists('call_center_queues_add') || permission_exists('call_center_queues_edit')) {
	//access granted
}
else {
	echo "access denied";
	exit;
}

$cmd = $_GET['cmd'];
$rdr = $_GET['rdr'];

//connect to event socket
$fp = event_socket_create($_SESSION['event_socket_ip_address'], $_SESSION['event_socket_port'], $_SESSION['event_socket_password']);
if ($fp) {
	$response = event_socket_request($fp, 'api reloadxml');
	$response = event_socket_request($fp, $cmd);
	fclose($fp);
}
else {
	$response = '';
}
if ($rdr == "false") {
	//redirect false
	echo $response;
}
else {
	header("Location: v_call_center_queue.php?savemsg=".urlencode($response));
}
?>