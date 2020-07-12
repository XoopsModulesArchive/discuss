<?php
require '../../mainfile.php';

$xoopsOption['template_main'] = 'discuss_viewlog.html';
include XOOPS_ROOT_PATH.'/header.php';

$discuss_id = !empty($_GET["did"]) ? intval($_GET["did"]) : 0;
if ($discuss_id == 0) {
	redirect_header(XOOPS_URL."/modules/discuss/index.php",1,'discuss id is not selected.');
}
$discuss_handler =& xoops_getmodulehandler("discuss");
$discuss_obj =& $discuss_handler->get($discuss_id);
if( !is_object($discuss_obj) ) {
	redirect_header(XOOPS_URL."/modules/discuss/index.php",1,'such discuss does not exist.');
}

$start = !empty($_GET['start']) ? intval($_GET['start']) : 0;
$limit = isset($_GET['perpage']) ? intval($_GET['perpage']) : 30;

$criteria =& new CriteriaCompo();
$criteria->add(new Criteria("discuss_id", $discuss_id));
$criteria->add(new Criteria("message_id",0,">"));
$criteria->setLimit($limit);
$criteria->setStart($start);

$msgobj_h =& xoops_getmodulehandler("message");
$msgobjs =& $msgobj_h->getObjects($criteria);
$count = $msgobj_h->getCount($criteria);

include_once './class/mypagenav.php';
$nav = new MyPageNav($count, $limit, $start, 'start', 'did='.$discuss_id);
$xoopsTpl->assign('pagenav', $nav->renderAuto());
$xoopsTpl->assign('pagenav2', $nav->renderNav(8));

foreach ($msgobjs as $msgobj) {
	$message_id = $msgobj->getVar("message_id");
	$message = $msgobj->getVar("message");
	$uname = $msgobj->getVar("uname");
	$timestamp = formatTimestamp($msgobj->getVar("timestamp"), "mysql");
	$xoopsTpl->append('msgobjs', array('message_id' => $message_id, 'message' => $message, 'uname' => $uname, 'timestamp' => $timestamp));
}

/*
echo "<PRE>";
print_r($msgobjs);
echo "</PRE>";
*/

include(XOOPS_ROOT_PATH.'/footer.php');
?>