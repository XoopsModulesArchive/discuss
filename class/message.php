<?php
/**
 * @package Discuss
 */
include_once dirname(__FILE__).'/xoopstableobject.php';
include_once XOOPS_ROOT_PATH.'/modules/discuss/language/'.$GLOBALS['xoopsConfig']['language'].'/encoder.php';

if(!defined('DISCUSS_SHOW_LIMIT_DEFAULT')) define('DISCUSS_SHOW_LIMIT_DEFAULT', 10);

if(!class_exists('DiscussMessage')) {

/**
 * Class for discussion message
 */
class DiscussMessage extends XoopsTableObject
{
	/**
	 * constructor
	 * @access public
	 */
	function DiscussMessage()
	{
		// call parent constructor
		$this->XoopsTableObject();
		
		// define object elements
		$this->initVar('discuss_id', XOBJ_DTYPE_INT, null, true);
		$this->initVar('message_id', XOBJ_DTYPE_INT, null, true);
		$this->initVar('uid', XOBJ_DTYPE_INT, null, false);
		$this->initVar('uname', XOBJ_DTYPE_TXTBOX, null, true, 60);
		$this->initVar('message', XOBJ_DTYPE_TXTAREA, null, true);
		$this->initVar('timestamp', XOBJ_DTYPE_INT, null, true);
		$this->initVar('color', XOBJ_DTYPE_TXTBOX, '000000', false);
		
		// define primary key
		$this->setKeyFields(array('discuss_id','message_id'));
		$this->setAutoIncrementField('message_id');
	}
	
	// checkVar
	function checkVar_color(&$value)
	{
		$colors = array("000000", "dc0000", "4cb5e8");
		if (!in_array($value, $colors)) {
            $this->setErrors('bad color posted.');
	        return false;
		}
		return true;
	}
	
	// check if discussion is open or not.
	function isOpen(){
		// assigned for yosha_01.
	}
}

/**
 * Discuss message handler class.
 *
 * @package Discuss
 */
class DiscussMessageHandler extends XoopsTableObjectHandler
{
	/**
	 * discussion object
	 * @var object {@link DiscussDiscuss} object
	 */
	var $_discuss = null;
	
	/**
	 * discussion id
	 * @var int
	 */
	var $_discuss_id = 0;
	
	/**
	 * constructor
	 * @param object $db reference to the {@link XoopsDatabase} object
	 */
	function DiscussMessageHandler($db)
	{
		// call parent constructor
		$this->XoopsTableObjectHandler($db);
		
		// define table name
		$this->tableName = $this->db->prefix('discuss_message');
	}
	
	// original methods
	
	/**
	 * @param int $discuss_id
	 * @return bool
	 */
	function checkDiscuss($discuss_id)
	{
		$this->_discuss_id = intval($discuss_id);
		if ($this->_discuss_id > 0) {
			$discuss_handler =& xoops_getmodulehandler("discuss");
			$this->_discuss =& $discuss_handler->get($this->_discuss_id);
			return true;
		}
		return false;
	}
	
	/**
	 * DB update procedure
	 * @return bool
	 */
	function processPostRequest()
	{
		if(is_object($this->_discuss)) {
			// @TODO check right
			if (is_object($GLOBALS['xoopsUser'])) {
				$uid = $GLOBALS['xoopsUser']->getVar("uid");
				$uname = $GLOBALS['xoopsUser']->getVar("uname");
			} else {
				$uid = 0;
				$uname = $GLOBALS['xoopsConfig']['anonymous'];
			}
			
			$message = isset($_POST["msg"]) ? $_POST["msg"] : '';
			$message = DiscussEncoder::fromUtf8($message);
			
			$msgobj =& $this->create();
			$msgobj->setVar('discuss_id', $this->_discuss_id);
			$msgobj->setVar('uid', $uid);
			$msgobj->setVar('uname', $uname);
			$msgobj->setVar('message', $message);
			$msgobj->setVar('timestamp', time());
			if (!empty($_POST["color"])) {
				$msgobj->setVar('color', $_POST["color"]);
			}
			return $this->insert($msgobj,true);
		}
		return false;
	}
	
	/**
	 * get message-array and make cache
	 * @return array $ret(array('message_id'=>,'uname'=>,'message'=>,'color'=>))
	 */
	function &getMessages()
	{
		$ret = array();
		if( is_object($this->_discuss) ) {
			//@TODO check perm
			$limit = isset($GLOBALS['xoopsModuleConfig']['message_limit']) ? intval($GLOBALS['xoopsModuleConfig']['message_limit']) : DISCUSS_SHOW_LIMIT_DEFAULT;
			$sql = "SELECT * FROM ".$this->db->prefix('discuss_message')." WHERE discuss_id = ".$this->_discuss_id." ORDER BY message_id DESC";
			
			$result =& $this->query($sql, false, $limit);
			if (!$result) {
				return $ret;
			}
			$ts =& MyTextSanitizer::getInstance();
			while ($myrow = $this->db->fetchArray($result)) {
				$msg = array();
				$msg['message_id'] = intval($myrow['message_id']);
				$msg['uname'] = DiscussEncoder::toUtf8(htmlspecialchars($ts->stripSlashesGPC($myrow['uname']), ENT_QUOTES));
				$msg['message'] = DiscussEncoder::toUtf8(htmlspecialchars($ts->stripSlashesGPC($myrow['message']), ENT_QUOTES));
				$msg['color'] = htmlspecialchars($myrow['color'], ENT_QUOTES);
				array_unshift($ret, $msg);
				unset($msg);
			}
			
			$access_key = $this->_discuss->getVar('access_key');
			$cachedir = dirname(dirname(__FILE__)) . '/cache/';
			$cachename = $cachedir.md5($access_key).'.log';
			if (file_exists($cachename)) @unlink($cachename);
			
			$key_term = $this->_discuss->getVar('key_term');
			if( $key_term < time() ) {
				$access_key = mt_rand();
				$key_term = time() + 600;
				$cachename = $cachedir.md5($access_key).'.log';
	        		$this->_discuss->setVar('access_key', $access_key);
	        		$this->_discuss->setVar('key_term', $key_term);
				$discuss_handler =& xoops_getmodulehandler("discuss");
			        $discuss_handler->insert($this->_discuss, true, true);
			}
			
			if ( $fp = fopen($cachename , "w") ) {
				if (flock($fp, LOCK_EX)) {
					fwrite($fp, serialize($ret));
					flock($fp, LOCK_UN);
				}
				fclose($fp);
			}
			
			$xoops_cookie_path = defined('XOOPS_COOKIE_PATH') ? XOOPS_COOKIE_PATH : preg_replace('?http://[^/]+(/.*)$?', "$1", XOOPS_URL);
			if( $xoops_cookie_path == XOOPS_URL ) $xoops_cookie_path = '/';
			setcookie('discuss_key'.$this->_discuss_id, $access_key, $key_term, $xoops_cookie_path, '', 0);
		}
		return $ret;
	}
}//end of class
}//end of if
?>