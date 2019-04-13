<?php
/** rewrite output */
function rewrite_output($content) {
	$search = array(
		"/href\=\"(\.*\/*)\?mod\=(index|webdir|category|update|archives|top|addurl|feedback|link|rssfeed|sitemap)?\"/ie",
		"/href\=\"(\.*\/*)\?mod\=webdir([&amp;|&]cid\=(\d+))?([&amp;|&]page\=(\d+))?\"/ie",
		"/href\=\"(\.*\/*)\?mod\=update([&amp;|&]days\=(\d+))?([&amp;|&]page\=(\d+))?\"/ie",
		"/href\=\"(\.*\/*)\?mod\=archives([&amp;|&]date\=(\d+))?([&amp;|&]page\=(\d+))?\"/ie",
		"/href\=\"(\.*\/*)\?mod\=search([&amp;|&]type\=(.+?))?([&amp;|&]query\=(.+?))?([&amp;|&]page\=(\d+))?\"/ie",
		"/href\=\"(\.*\/*)\?mod\=website[&amp;|&]url\=(\w+)\"/ie",
		"/href\=\"(\.*\/*)\?mod\=comment([&amp;|&]wid\=(\d+))?([&amp;|&]page\=(\d+))?\"/ie",
		"/href\=\"(\.*\/*)\?mod\=diypage[&amp;|&]pid\=(\d+)\"/ie",
		"/href\=\"(\.*\/*)\?mod\=rssfeed([&amp;|&]cid\=(\d+))?\"/ie",
		"/href\=\"(\.*\/*)\?mod\=sitemap([&amp;|&]cid\=(\d+))?\"/ie",
	);
		
	$replace = array(
		"rewrite_module('\\2')",
		"rewrite_webdir('\\3', '\\5')",
		"rewrite_update('\\3', '\\5')",		
		"rewrite_archives('\\3', '\\5')",
		"rewrite_search('\\3', '\\5', '\\7')",
		"rewrite_website('\\2')",
		"rewrite_comment('\\3', '\\5')",
		"rewrite_diypage('\\2')",
		"rewrite_rssfeed('\\3')",
		"rewrite_sitemap('\\3')",
	);
	
	return preg_replace($search, $replace, $content);
}

/** module */
function rewrite_module($module) {	
	return 'href="'.get_module_url($module).'"';
}

/** webdir */
function rewrite_webdir($cate_id, $page) {
	return 'href="'.get_category_url($cate_id, $page).'"';
}

/** website */
function rewrite_website($web_url) {
	return 'href="'.get_siteinfo_url($web_url).'"';
}

/** update */
function rewrite_update($days, $page) {
	return 'href="'.get_update_url($days, $page).'"';
}

/** archives */
function rewrite_archives($date, $page) {
	return 'href="'.get_archives_url($date, $page).'"';
}
	
/** search */
function rewrite_search($type = 'name', $query, $page) {
	return 'href="'.get_search_url($type, $query, $page).'"';
}

/** comment */
function rewrite_comment($web_id, $page) {
	return 'href="'.get_comment_url($web_id, $page).'"';
}

/** diypage */
function rewrite_diypage($page_id) {	
	return 'href="'.get_diypage_url($page_id).'"';
}

/** rssfeed */
function rewrite_rssfeed($cate_id) {
	return 'href="'.get_rssfeed_url($cate_id).'"';
}

/** sitemap */
function rewrite_sitemap($cate_id) {
	return 'href="'.get_sitemap_url($cate_id).'"';
}
?>