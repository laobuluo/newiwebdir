<?php
function get_stats() {
	global $DB;
	
	$result = array();
	$result['category'] = $DB->get_count($DB->table('categories'));
	$result['website'] = $DB->get_count($DB->table('websites'));
	$result['adver'] = $DB->get_count($DB->table('advers'));
	$result['link'] = $DB->get_count($DB->table('links'));
	$result['feedback'] = $DB->get_count($DB->table('feedbacks'));
	$result['label'] = $DB->get_count($DB->table('labels'));
	$result['page'] = $DB->get_count($DB->table('pages'));
	
	return $result;
}
?>