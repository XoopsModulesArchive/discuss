<?php
require './header.php';

$xoopsOption['template_main'] = 'discuss.html';
include XOOPS_ROOT_PATH.'/header.php';

$discuss_obj = null;
if(!empty($_GET["did"])) {
	$discuss_id = intval($_GET["did"]);
	$discuss_handler =& xoops_getmodulehandler("discuss");
	$discuss_obj =& $discuss_handler->get($discuss_id);
}
if( is_object($discuss_obj) ) {
	$xoopsTpl->assign('discuss_id', $discuss_id);
	$xoopsTpl->assign('subject', $discuss_obj->getVar('subject'));
	include(XOOPS_ROOT_PATH.'/footer.php');
} else {
	redirect_header(XOOPS_URL."/modules/discuss/", 1, 'Such discuss does not exist.');
}
?>