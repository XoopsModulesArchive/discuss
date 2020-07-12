<?php
function discuss_search($queryarray, $andor, $limit, $offset, $userid)
{
	global $xoopsDB;

	$sql = "SELECT * FROM ".$xoopsDB->prefix('discuss_message')." WHERE 1=1 ";
	if ( is_array($queryarray) && ($count = count($queryarray)) ) {
		$sql .= "AND (message LIKE '%$queryarray[0]%'";
		for($i=1;$i<$count;$i++){
			$sql .= " $andor message LIKE '%$queryarray[$i]%'";
		}
		$sql .= ") ";
	}
	if ($userid) {
		$sql .= 'AND (uid='.intval($userid).')';
	}
	$sql .= " ORDER BY timestamp DESC";
	$result = $xoopsDB->query($sql,$limit,$offset);

	$ret = array();
	$i = 0;
 	while($myrow = $xoopsDB->fetchArray($result)){
		$start = ($myrow['message_id'] >= 10) ? $myrow['message_id'] - 10 : 0;
		$ret[$i]['link'] = "viewlog.php?did={$myrow['discuss_id']}&start={$start}&perpage=20";
		$ret[$i]['title'] = $myrow['message'];
		$ret[$i]['time'] = $myrow['timestamp'];
		$ret[$i]['uid'] = $myrow['uid'];
		$i++;
	}
	return $ret;
}
?>