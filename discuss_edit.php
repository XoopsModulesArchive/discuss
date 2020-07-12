<?php
require('../../mainfile.php');
//require dirname( __FILE__ ).'/class/grouppermform.php';

if( !$xoopsUserIsAdmin ) {
    redirect_header(XOOPS_URL."/modules/discuss/index.php",1,_NOPERM);
}

$xoopsOption['template_main'] = "discuss_edit.html";
include( XOOPS_ROOT_PATH.'/header.php' );

$discuss_handler =& xoops_getmodulehandler("discuss");

$discuss_handler->handleRecord();
$form = $discuss_handler->renderEditForm(XOOPS_URL.'/modules/discuss/discuss_edit.php');
$message = $discuss_handler->getMessage(true);

$xoopsTpl->assign('form', $form);
$xoopsTpl->assign('message', $message);

//$gperm_form = new DiscussGroupPermForm();
//$xoopsTpl->assign('gperm_form', $gperm_form->renderForm());

include(XOOPS_ROOT_PATH.'/footer.php');
?>