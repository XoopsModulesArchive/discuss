<?php
/**
 * Class for discuss cache
 * @static
 */
class DiscussCache
{
	/**
	 * Get message from cache file
	 * @return array or false
	 */
	function getMessages()
	{
		$discuss_id = !empty($_GET["did"]) ? intval($_GET["did"]) : 0;
		if( isset($_COOKIE['discuss_key'.$discuss_id]) ) {
			
			$access_key = md5($_COOKIE['discuss_key'.$discuss_id]);
			$cachename = dirname(dirname(__FILE__)) . '/cache/'.$access_key.'.log';
			
			if (file_exists($cachename)) {
				$ret = array();
				if ( $fp = fopen($cachename , "r") ) {
					if ( $serialized = fread($fp, filesize($cachename)) ) {
						$ret = unserialize($serialized);
					}
					fclose($fp);
				}
				return $ret;
			}
		}
		return false;
	}
}

?>