<?php
//  ------------------------------------------------------------------------ //
//                XOOPS - PHP Content Management System                      //
//                    Copyright (c) 2000 XOOPS.org                           //
//                       <http://www.xoops.org/>                             //
//  ------------------------------------------------------------------------ //
//  This program is free software; you can redistribute it and/or modify     //
//  it under the terms of the GNU General Public License as published by     //
//  the Free Software Foundation; either version 2 of the License, or        //
//  (at your option) any later version.                                      //
//                                                                           //
//  You may not change or alter any portion of this comment or credits       //
//  of supporting developers from this source code or any supporting         //
//  source code which is considered copyrighted (c) material of the          //
//  original comment or credit authors.                                      //
//                                                                           //
//  This program is distributed in the hope that it will be useful,          //
//  but WITHOUT ANY WARRANTY; without even the implied warranty of           //
//  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the            //
//  GNU General Public License for more details.                             //
//                                                                           //
//  You should have received a copy of the GNU General Public License        //
//  along with this program; if not, write to the Free Software              //
//  Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307 USA //
//  ------------------------------------------------------------------------ //

/**
 * @package MyFixedformObject
 */
include_once dirname(__FILE__).'/xoopstableobject.php';

if (!defined('MFFO_PREFIX')) define('MFFO_PREFIX', 'xo_');

if (!class_exists('MyFixedformObject')) {

	/**
	 * table object class with fixed form
	 * @package MyFixedformObject
	 * @abstract
	 */
	class MyFixedformObject extends XoopsTableObject
	{
		/**
		 * Caption of form
		 * @var string
		 * @access private
		 */
		var $_formCaption = '';
		
		/**
		 * constructor
		 * @access protected
		 */
		function MyFixedformObject()
		{
			$this->XoopsTableObject();
		}
		
		/**
		 * define edit form elements
		 * @abstract
		 * @return bool
		 */
		function initFormElements()
		{
			$this->setError('Form Elements are not initialized.');
			return false;
		}
		
		/**
		 * @access public
		 * @return void
		 */
		function assignFormTokenElement()
		{
			if (class_exists('XoopsMultiTokenHandler')) {
				include_once XOOPS_ROOT_PATH.'/class/xoopsform/formelement.php';
				include_once XOOPS_ROOT_PATH.'/class/xoopsform/formhidden.php';
				include_once XOOPS_ROOT_PATH.'/class/xoopsform/formtoken.php';
				$tokenhandler = new XoopsMultiTokenHandler();
				$ticket =& $tokenhandler->create(get_class($this).'_edit', 600);
				$this->_formElements['ticket'] = new XoopsFormToken($ticket);
				$this->setAttribute('ticket', $ticket->getTokenValue());
			}
		}
		
		/**
		 * @access public
		 * @return string
		 */
		function getFormCaption()
		{
			return $this->_formCaption;
		}
		
	} // End of class
	
	/**
	 * MyFixedformObject handler class
	 * @package MyFixedformObject
	 * @abstract
	 */
	class MyFixedformObjectHandler extends XoopsTableObjectHandler
	{
		/**
		 * @var string
		 * @access private
		 */
		var $_message = '';
		
		/**
		 * record that handled
		 * @var object reference to the {@link MyFixedformObject} object
		 * @access private
		 */
		var $_record = null;
		
		/**
		 * constructor
		 * @access protected
		 * @param object $db reference to the {@link XoopsDatabase} object
		 */
		function MyFixedformObjectHandler($db)
		{
			$this->XoopsTableObjectHandler($db);
		}
		
		/**
		 * check token
		 * @access private
		 * @return bool
		 */
		function _checkToken()
		{
			$ret = true;
			if (class_exists('XoopsMultiTokenHandler')) {
				$tokenhandler = new XoopsMultiTokenHandler();
				$ret = $tokenhandler->autoValidate($this->_entityClassName.'_edit');
			}
			return $ret;
		}
		
		/**
		 * @access public
		 * @param string $op	insert/save/edit/new/
		 * @param array  $keys
		 * @return bool
		 */
		function handleRecord($op='', $keys=array())
		{
			if( !$op ) $op = !empty($_POST['op']) ? $_POST['op'] : '';
			
			if ($op == 'insert' || $op == 'save') {
				$ret = false;
				$record =& $this->create();
				foreach ($record->getKeyFields() as $k => $v) {
					if (!isset($_POST[MFFO_PREFIX.$v]) && ($op == 'save' || !$record->isAutoIncrement($v))) {
						$this->setError('Record key does not post.');
						return false;
					}
				}
				
				if ($this->_checkToken())
				{
					$updateOnlyChanged = false;
					if ($op == 'save') {
						$record->unsetNew();
						$updateOnlyChanged = true;
					}
					$record->setFormVars($_POST, MFFO_PREFIX);
					
					if ($this->insert($record, false, $updateOnlyChanged)) {
						$record->unsetNew();
						$this->_record =& $record;
						$ret = true;
						$this->_message = 'Success update database.';
					}
				} else {
					$this->setError('An illegal request was detected. please, try submit again.');
				}
				unset($record);
				return $ret;
				
			} else {
				if( !$op ) $op = !empty($_GET['op']) ? $_GET['op'] : '';
				
				if ($op == 'edit' || $op == 'new' || $op == '') {
					
					$record =& $this->create();
					$recordKeys = $record->getKeyFields();
					if (gettype($keys) != 'array') {
						$keys = array($recordKeys[0] => $keys);
					}
					foreach ($recordKeys as $k => $v) {
						if (!array_key_exists($v, $keys) && isset($_GET[$v])) {
							$keys[$v] = $_GET[$v];
						}
					}
					
					if ($op == 'edit') {
						unset($record);
						if (!($record =& $this->get($keys))) {
							$this->setError('Request record does not exist.');
							return false;
						}
					} else {
						$myts =& MyTextSanitizer::getInstance();
						$criteria = new CriteriaCompo();
						$issetkey = true;
						foreach ($recordKeys as $k => $v) {
							if (array_key_exists($v, $keys)) {
								$criteria->add(new Criteria($v, $myts->addSlashes($keys[$v])));
								$record->setVar($v, $keys[$v]);
							} elseif (!$record->isAutoIncrement($v)) {
								$this->setError('Record key does not post.');
								return false;
							} else {
								$issetkey = false;
							}
						}
						
						if ($issetkey && $this->getCount($criteria)) {
							unset($record);
							if ($op == 'new') {
								$this->setError('Designate key already exist.');
								return false;
							} else {
								$record =& $this->get($keys);
							}
						}
					}
					$this->_record =& $record;
					unset($record);
					return true;
				} else {
					$this->setError('An illegal option was requested.');
				}
			}
			return false;
		}
		
		/**
		 * render edit form for record that handled "function handleRecord"
		 * @access public
		 * @param string $action
		 * @return string rendered XoopsThemeForm
		 */
		function renderEditForm($action)
		{
			if( is_object($this->_record) ) {
				$this->_record->assignFormTokenElement();
				if ($this->_record->initFormElements()) {
					return $this->_record->renderEditForm($this->_record->getFormCaption(), $this->_entityClassName, $action);
				} else {
					$this->_errors += $this->_record->getErrors();
				}
			}
			return false;
		}
		
		/**
		 * return record that handled.
		 * @access public
		 * @return object	reference to the {@link MyFixedformObject} object
		 */
		function &getRecord()
		{
			return $this->_record;
		}
		
		/**
		 * @access public
		 * @return string
		 */
		function &getMessage($html=false)
		{
			$str = $this->_message;
			if( count($this->_errors) ) {
				if( $str ) {
					$delim = $html ? "<br />\n" : "\n";
					$str .= $delim.'Error:'.$delim;
				}
				$str .= $this->getErrors($html);
			}
			return $str;
		}
		
	} // End of Class MyFixedformObjectHandler

} // End of if
?>