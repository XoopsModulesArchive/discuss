<?php
function b_discuss_active_discuss()
{
	$attendee_handler =& xoops_getmodulehandler("attendee", 'discuss');
	$attendee_handler->garbageCollect(10);
	$criteria =& new CriteriaCompo(new Criteria("status", 1));
	$criteria->setSort('discuss_id');
	$attendee_objs = $attendee_handler->getObjects($criteria);
	$discuss_handler =& xoops_getmodulehandler("discuss", 'discuss');
	$msgobj_h =& xoops_getmodulehandler("message", 'discuss');

	$block = array();
	foreach ($attendee_objs as $obj){
		$discuss_id = $obj->getVar('discuss_id');
		if (!isset($block[$discuss_id])) {
			$block[$discuss_id] = array();
			$discuss_obj =& $discuss_handler->get($discuss_id);
			if( !is_object($discuss_obj) ) continue;
			$block[$discuss_id]['subject'] = $discuss_obj->getvar('subject');

			$criteria =& new CriteriaCompo();
			$criteria->add(new Criteria("discuss_id", $discuss_id));
			$criteria->setLimit(1);
			$criteria->setSort('message_id');
			$criteria->setOrder('DESC');
			$msgobjs =& $msgobj_h->getObjects($criteria);
			if ( $msgobjs ) {
				$block[$discuss_id]['message'] = $msgobjs[0]->getVar('message');
			}

			$block[$discuss_id]['unames'] = array();
		}
		$block[$discuss_id]['unames'][] = $obj->getVar('uname');
	}
	return $block;
}

?>