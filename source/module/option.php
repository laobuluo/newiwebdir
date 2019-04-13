<?php
function get_options() {
	global $DB;
	
	$newarr = array();
	$options = $DB->fetch_all("SELECT option_name, option_value FROM ".$DB->table('options'));
	foreach ($options as $opt) {
		$newarr[$opt['option_name']] = addslashes($opt['option_value']);
	}
	
	return load_cache('options') ? load_cache('options') : $newarr;
}
?>