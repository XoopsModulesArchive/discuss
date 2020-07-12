<?php
require './header.php';

$xoopsOption['template_main'] = 'discuss_list.html';
include XOOPS_ROOT_PATH.'/header.php';

$discuss_handler =& xoops_getmodulehandler("discuss");

$discussions = array();
$discuss_objs =& $discuss_handler->getObjects();
foreach ($discuss_objs as $obj) {
	$discuss = array();
	$discuss['id'] = $obj->getVar('discuss_id');
	$discuss['subject'] = $obj->getVar('subject');
	$discuss['description'] = $obj->getVar('description');
	$xoopsTpl->append('discussions', $discuss);
	unset($discuss);
}

include(XOOPS_ROOT_PATH.'/footer.php');
?>