<?php
$modversion['name'] = '_MI_DISCUSS_NAME';
$modversion['version'] = 0.42;
$modversion['description'] = '_MI_DISCUSS_DESC';
$modversion['credits'] = '';
$modversion['author'] = 'argon, comodita, fugafuga, JulioNC, twodash, yosha_01';
$modversion['help'] = 'help.html';
$modversion['license'] = 'GPL see LICENSE';
$modversion['official'] = 0;
$modversion['image'] = 'images/discuss_slogo_02.png';
$modversion['dirname'] = 'discuss';

// Sql file (must contain sql generated by phpMyAdmin or phpPgAdmin)
// All tables should not have any prefix!
$modversion['sqlfile']['mysql'] = "sql/mysql.sql";
//$modversion['sqlfile']['postgresql'] = "sql/pgsql.sql";

// Tables created by sql file (without prefix!)
$modversion['tables'][0] = "discuss_message";
$modversion['tables'][1] = "discuss";
$modversion['tables'][2] = "discuss_attendee";

// Admin
$modversion['hasAdmin'] = 1;
$modversion['adminindex'] = 'admin/index.php';

// Menu
$modversion['hasMain'] = 1;
//$modversion['sub'][1]['name'] = _MI_DISCUSS_SMNAME1;
//$modversion['sub'][1]['url'] = "viewlog.php"; 

// Templates
$modversion['templates'][1]['file'] = 'discuss_viewlog.html';
$modversion['templates'][1]['description'] = 'View Discussion Log';
$modversion['templates'][2]['file'] = 'discuss_edit.html';
$modversion['templates'][2]['description'] = 'edit Discussion';
$modversion['templates'][3]['file'] = 'discuss_list.html';
$modversion['templates'][3]['description'] = 'List Discussion';
$modversion['templates'][4]['file'] = 'discuss.html';
$modversion['templates'][4]['description'] = 'Show Discussion';

$modversion['hasconfig'] = 1;
$modversion['config'][] = array(
	'name'			=> 'message_limit' ,
	'title'			=> '_MI_DISCUSS_LIMIT' ,
	'description'		=> '_MI_DISCUSS_LIMITDSC' ,
	'formtype'		=> 'select' ,
	'valuetype'		=> 'int' ,
	'default'		=> 10 ,
	'options'		=> array('10' => 10, '15' => 15, '20' => 20, '25' => 25, '30' => 30)
) ;
/*
$modversion['config'][] = array(
	'name'			=> 'message_encode' ,
	'title'			=> '_MI_DISCUSS_ENCODE' ,
	'description'		=> '_MI_DISCUSS_ENCODEDSC' ,
	'formtype'		=> 'textbox' ,
	'valuetype'		=> 'text' ,
	'default'		=> 'euc-jp' ,
) ;
*/
$modversion['hasSearch'] = 1;
$modversion['search']['file'] = "include/search.inc.php";
$modversion['search']['func'] = "discuss_search";

// Blocks
$modversion['blocks'] = array();

$modversion['blocks'][1]['file'] = 'discuss_blocks.php';
$modversion['blocks'][1]['name'] = '_MI_DISCUSS_B_LIST';
$modversion['blocks'][1]['description'] = '';
$modversion['blocks'][1]['show_func'] = 'b_discuss_active_discuss';
$modversion['blocks'][1]['template'] = 'discuss_block_active_discuss.html';
$modversion['blocks'][1]['can_clone'] = false;
?>