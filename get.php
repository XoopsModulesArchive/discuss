<?php

/* cache
include './class/cache.php';
$msgs = DiscussCache::getMessages();
if( $msgs !== false ) {
	include './class/response.php';
} else {
*/

require './header.php';	

// initialize response renderer
$response_handler  =& xoops_getmodulehandler("response");

/////////////
// Message //
/////////////

	$msgs = array();
	$discuss_id = !empty($_GET["did"]) ? intval($_GET["did"]) : 0;
	$msgobj_h =& xoops_getmodulehandler("message");
	if ($msgobj_h->checkDiscuss($discuss_id)) {
		$msgs =& $msgobj_h->getMessages();
	}

/* cache
}
*/

$client_message_id = isset($_GET["mid"]) ? intval($_GET["mid"]) : 0;
$response_handler->addMessageXML($msgs, $client_message_id);
	
//////////////
// Attendee //
//////////////

// setval($_GET["refresh"], "boolean") might be more safe?
//if($_GET["refresh"] == true){
	
	$attendee_handler =& xoops_getmodulehandler("attendee");
	$attendee_obj =& $attendee_handler->create();

	if(is_object($xoopsUser)){
		$uid = $xoopsUser->getVar("uid");
		$uname = $xoopsUser->getVar("uname");
	} else {
		$uid = 0;
		$uname = $xoopsConfig['anonymous'];
	}

	$attendee_obj->setVar('discuss_id', $discuss_id);
	$attendee_obj->setVar('uid', $uid);
	$attendee_obj->setVar('uname', $uname);
	$attendee_obj->setVar('ip', $_SERVER['REMOTE_ADDR']);
	$attendee_obj->setVar('updated', time());
	$attendee_obj->setVar('status', 1);

	$attendee_handler->updateStatus($attendee_obj);
	$attendee_handler->garbageCollect(10);

	$attendee_objs =& $attendee_handler->getAttendees($discuss_id);
	$response_handler->addAttendeeXML($attendee_objs);
//}

////////////////
// Render XML //
////////////////

$response_handler->render();
?>