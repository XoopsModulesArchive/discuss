<?php

class DiscussAttendee extends XoopsObject
{
    function DiscussAttendee()
	{
        $this->XoopsObject();
        $this->initVar('attendee_id', XOBJ_DTYPE_INT, null, true);		
        $this->initVar('discuss_id', XOBJ_DTYPE_INT, null, true);
        $this->initVar('uid', XOBJ_DTYPE_INT, null, true);
        $this->initVar('uname', XOBJ_DTYPE_TXTBOX, null, true, 60);
//     	$this->initVar('session_id', XOBJ_DTYPE_TXTBOX, null, true, 32);
        $this->initVar('ip', XOBJ_DTYPE_TXTBOX, null, true, 15);
//      $this->initVar('time_registered', XOBJ_DTYPE_INT, 0, true);
        $this->initVar('updated', XOBJ_DTYPE_INT, 0, true);
        $this->initVar('status', XOBJ_DTYPE_INT, 0, true);
        }
}

class DiscussAttendeeHandler
{
    var $db;

    function DiscussAttendeeHandler(&$db)
    {
        $this->db =& $db;
    }	

    function &create()
    {
        return new DiscussAttendee();
    }
	
    function updateStatus(&$attendee)
    {
        if (strtolower(get_class($attendee)) != 'discussattendee') {
            return false;
        }
        if (!$attendee->cleanVars()) {
            return false;
        }
        foreach ($attendee->cleanVars as $k => $v) {
            ${$k} = $v;
        }
		
        if($uid == 0){
            $sql = 'SELECT COUNT(*) FROM '.$this->db->prefix('discuss_attendee').' WHERE discuss_id='.$discuss_id.' AND uid=0 AND ip='.$this->db->quoteString($ip);
            list($count) = $this->db->fetchRow($this->db->query($sql));
            if($count > 0){
                $sql = 'UPDATE '.$this->db->prefix('discuss_attendee').' SET updated='.$updated.', status=1 WHERE discuss_id='.$discuss_id.' AND uid=0 AND ip='.$this->db->quoteString($ip);
            } else {
                $sql = 'INSERT INTO '.$this->db->prefix('discuss_attendee').' (discuss_id, uid, uname, ip, updated, status) VALUES ('.$discuss_id.', '.$uid.', '.$this->db->quoteString($uname).', '.$this->db->quoteString($ip).', '.$updated.', '.$status.')';
            }
        } else {
            $sql = 'SELECT COUNT(*) FROM '.$this->db->prefix('discuss_attendee').' WHERE discuss_id='.$discuss_id.' AND uid='.$uid;
            list($count) = $this->db->fetchRow($this->db->query($sql));
            if($count > 0){
                $sql = 'UPDATE '.$this->db->prefix('discuss_attendee').' SET updated='.$updated.', status=1 WHERE discuss_id='.$discuss_id.' AND uid='.$uid;
            } else {
                $sql = 'INSERT INTO '.$this->db->prefix('discuss_attendee').' (discuss_id, uid, uname, ip, updated, status) VALUES ('.$discuss_id.', '.$uid.', '.$this->db->quoteString($uname).', '.$this->db->quoteString($ip).', '.$updated.', '.$status.')';
            }

        }

		// is secure?
        if (!$result = $this->db->queryF($sql)) {
            return false;
        }
        return true;
    }

    function garbageCollect($expire)
    {
        $mintime = time() - intval($expire);
        $sql = 'UPDATE '.$this->db->prefix('discuss_attendee').' SET status = 0 WHERE updated < '.$mintime;
        return $this->db->queryF($sql);
    }

    function &getAttendees($discuss_id, $status = 1){

        $discuss_id = intval($discuss_id);
        $status = intval($status);
        $objs = array();
		
        if ($discuss_id > 0){
			// return objects that include infomation of attendees in the discussion.
            $sql = 'SELECT * FROM '.$this->db->prefix('discuss_attendee').' WHERE discuss_id = '.$discuss_id.' AND status = '.$status;
            $result = $this->db->query($sql);
            if (!$result) {
                return $objs;
            }
            while ($myrow = $this->db->fetchArray($result)) {
                $attendee = new DiscussAttendee();
                $attendee->assignVars($myrow);
                $objs[] =& $attendee;
                unset($attendee);
            }
            return $objs;
        } else {
			// this is used to limit the number of attendees in the whole site.
            return $objs;
        }
    }

        function &getObjects($criteria = null, $id_as_key = false, $fieldlist="", $distinct = false)
        {
            $ret = array();
            $limit = $start = 0;
            $whereStr = '';
            $orderStr = '';
            if ($distinct) {
                $distinct = "DISTINCT ";
            } else {
                $distinct = "";
            }
            if ($fieldlist) {
                $sql = 'SELECT '.$distinct.$fieldlist.' FROM '.$this->db->prefix('discuss_attendee');
            } else {
                $sql = 'SELECT '.$distinct.'* FROM '.$this->db->prefix('discuss_attendee');
            }
            if (isset($criteria) && is_subclass_of($criteria, 'criteriaelement')) {
                $whereStr = $criteria->renderWhere();
                $sql .= ' '.$whereStr;
            }
            if (isset($criteria) && (is_subclass_of($criteria, 'criteriaelement')||get_class($criteria)=='criteriaelement')) {
                if ($criteria->getGroupby() != ' GROUP BY ') {
                    $sql .= ' '.$criteria->getGroupby();
                }
                if ((is_array($criteria->getSort()) && count($criteria->getSort()) > 0)) {
                    $orderStr = 'ORDER BY ';
                    $orderDelim = "";
                    foreach ($criteria->getSort() as $sortVar) {
                        $orderStr .= $orderDelim . $sortVar.' '.$criteria->getOrder();
                        $orderDelim = ",";
                    }
                    $sql .= ' '.$orderStr;
                } elseif ($criteria->getSort() != '') {
                    $orderStr = 'ORDER BY '.$criteria->getSort().' '.$criteria->getOrder();
                    $sql .= ' '.$orderStr;
                }
                $limit = $criteria->getLimit();
                $start = $criteria->getStart();
            }
            $result =& $this->db->query($sql, $limit, $start);
            if (!$result) {
                return $ret;
            }
            $records = array();
            while ($myrow = $this->db->fetchArray($result)) {
                $record =& $this->create(false);
                $record->assignVars($myrow);
                if (!$id_as_key) {
                    $records[] =& $record;
                } else {
                    $records[intval($myrow['attendee_id'])] =& $record;
                }
                unset($record);
            }
            return $records;
        }
}
?>