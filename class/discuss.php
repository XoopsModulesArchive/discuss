<?php
/**
 * @package Discuss
 */
include_once dirname(__FILE__).'/myfixedformobject.php';

if(!class_exists('DiscussDiscuss')) {

/**
 * Class for discussion
 */
class DiscussDiscuss extends MyFixedformObject
{
	/**
	 * constructor
	 * @access public
	 */
	function DiscussDiscuss()
	{
		// call parent constructor
		$this->MyFixedformObject();
		
		// define object elements
		$this->initVar('discuss_id', XOBJ_DTYPE_INT, null, true);
//		$this->initVar('resource_id', XOBJ_DTYPE_INT, 0, false);
//		$this->initVar('conclusion_id', XOBJ_DTYPE_INT, 0, false);
		$this->initVar('subject', XOBJ_DTYPE_TXTBOX, null, true, 120);
		$this->initVar('description', XOBJ_DTYPE_TXTBOX, null, false);
		$this->initVar('open_time', XOBJ_DTYPE_INT, 0, false);
		$this->initVar('close_time', XOBJ_DTYPE_INT, 0, false);
		$this->initVar('closed', XOBJ_DTYPE_INT, 0, false); // ここ遊びどこ?
		$this->initVar('access_key', XOBJ_DTYPE_TXTBOX, '', false);
		$this->initVar('key_term', XOBJ_DTYPE_INT, 0, false);
		
		// define primary key
		$this->setKeyFields(array('discuss_id'));
		$this->setAutoIncrementField('discuss_id');
	}
	
	/**
	 * define edit form elements
	 * @access public
	 * @return bool
	 */
	function initFormElements()
	{
		
		if( !$this->isNew() ) {
			$this->assignEditFormElement('discuss_id', 'Hidden', array(MFFO_PREFIX.'discuss_id',0));
			$this->_formCaption = _EDIT;
		} else {
			$this->_formCaption = _MD_DISCUSS_CREATE;
		}
//		$this->assignEditFormElement('resource_id', 'Hidden', array(MFFO_PREFIX.'resource_id',0));
//		$this->assignEditFormElement('conclusion_id', 'Hidden', array(MFFO_PREFIX.'conclusion_id',0));
		$this->assignEditFormElement('subject', 'Text', array(_MD_DISCUSS_SUBJECT, MFFO_PREFIX.'subject', 20, 120));
		$this->assignEditFormElement('description', 'TextArea', array(_MD_DISCUSS_DESC, MFFO_PREFIX.'description'));
		
		return true;
	}
} // end of class


/**
 * Discuss discussion handler class.
 *
 * @package Discuss
 */
class DiscussDiscussHandler extends MyFixedformObjectHandler
{
	/**
	 * constructor
	 * @param object $db reference to the {@link XoopsDatabase} object
	 */
	function DiscussDiscussHandler($db)
	{
		// call parent constructor
		$this->MyFixedformObjectHandler($db);
		
		// define table name
		$this->tableName = $this->db->prefix('discuss');
	}
	
	/**
	 * get active dicuss
	 * @access public
	 * @return array
	 */
	function &getOpenDiscussions()
	{
		$criteria = new CriteriaCompo(new Criteria('closed', 0));
		
		$fieldlist = 'discuss_id, subject';
		$results =& $this->getObjects($criteria, true, $fieldlist);
		
		$ret = array();
		foreach($results as $k=>$v) {
			$ret[$k] = $v->getVar('subject');
		}
		return $ret;
	}
	
	/**
	 * @access public
	 * @param int $discuss_id
	 * @return string
	 */
	function getSubjectById($discuss_id)
	{
		$discuss =& $this->get($discuss_id);
		if (is_object($discuss)) {
			return $discuss->getVar('subject');
		}
		return false;
	}
} // end of class

} // end of if
?>