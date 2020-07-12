<?php

/* @TODO is this worth putthing here?
ini_set("mbstring.http_input","pass");
ini_set("mbstring.http_output","pass");
ini_set('mbstring.substitute_character','');
ini_set("mbstring.encoding_translation","Off");
*/

include '../../mainfile.php';

// Load Encoder
// @TODO is this worth loading here?

if(file_exists(XOOPS_ROOT_PATH.'/modules/discuss/language/'.$GLOBALS['xoopsConfig']['language'].'/encoder.php')){
	include_once XOOPS_ROOT_PATH.'/modules/discuss/language/'.$GLOBALS['xoopsConfig']['language'].'/encoder.php';
} else {
	include_once XOOPS_ROOT_PATH.'/modules/discuss/language/english/encoder.php';	
}
?>