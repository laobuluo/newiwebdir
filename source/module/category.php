<?php
/** category path */
function get_category_path($cate_ids = 0, $separator = ' &nbsp;&rsaquo;&nbsp; ') {
	global $DB;
	
	$strpath = '';
	if (!empty($cate_ids)) {
		$sql = "SELECT cate_id, cate_name FROM ".$DB->table('categories')." WHERE cate_id IN (".$cate_ids.")";
		$categories = $DB->fetch_all($sql);
		foreach ($categories as $row) {
			$strpath .= $separator.'<a href="'.get_category_url($row['cate_id']).'">'.$row['cate_name'].'</a>';
		}
	}
	
	return $strpath;
}
	
/** category option */
function get_category_option($root_id = 0, $cate_id = 0, $level_id = 0) {
	global $DB;
		
	$sql = "SELECT cate_id, cate_name FROM ".$DB->table('categories')." WHERE root_id='$root_id' ORDER BY cate_sort ASC, cate_id ASC";
	$categories = $DB->fetch_all($sql);
	
	$optstr = '';
	if (!empty($categories) && is_array($categories)) {
		foreach ($categories as $cate) {
			$optstr .= '<option value="'.$cate['cate_id'].'"';
			if ($cate_id > 0 && $cate_id == $cate['cate_id']) $optstr .= ' selected';
			
			if ($level_id == 0) {
				$optstr .= ' style="background: #EEF3F7;">';
				$optstr .= '├'.$cate['cate_name'];
			} else {
				$optstr .= '>';
				for ($i = 2; $i <= $level_id; $i++) {
					$optstr .= '│&nbsp;&nbsp;';
				}
				$optstr .= '│&nbsp;&nbsp;├'.$cate['cate_name'];
			}
			$optstr .= '</option>';
			$optstr .= get_category_option($cate['cate_id'], $cate_id, $level_id + 1);
		}
	}
		
	return $optstr;
}

/** categories */
function get_categories($cate_id = 0, $top_num = 0) {
	global $DB;
	
	$sql = "SELECT cate_id, cate_name, cate_dir, cate_childcount, cate_postcount FROM ".$DB->table('categories')." WHERE root_id='$cate_id' ORDER BY cate_sort ASC, cate_id ASC";
	if ($top_num > 0) $sql .= " LIMIT $top_num";
	$categories = $DB->fetch_all($sql);
	
	$temp = array();
	if (!empty($categories) && is_array($categories)) {
		foreach ($categories as $row) {
			$row['cate_link'] = get_category_url($row['cate_id']);
			$temp[] = $row;
		}
	}
	
	return $temp;
}

/** best categories */
function get_best_categories($top_num = 0) {
	global $DB;
	
	$sql = "SELECT cate_id, cate_name, cate_childcount, cate_postcount FROM ".$DB->table('categories')." WHERE cate_isbest=1 ORDER BY cate_sort ASC, cate_id ASC";
	if ($top_num > 0) $sql .= " LIMIT $top_num";
	$categories = $DB->fetch_all($sql);
	
	$temp = array();
	if (!empty($categories) && is_array($categories)) {
		foreach ($categories as $row) {
			$row['cate_link'] = get_category_url($row['cate_id']);
			$temp[] = $row;
		}
	}
	
	return $temp;
}

/** all category */
function get_all_category() {
	global $DB;
	
	$categories = load_cache('categories') ? load_cache('categories') : $DB->fetch_all("SELECT cate_id, root_id, cate_name, cate_dir, cate_arrparentid, cate_arrchildid, cate_childcount, cate_postcount FROM ".$DB->table('categories')." ORDER BY cate_sort ASC, cate_id ASC");
		
	$temp = array();
	if (!empty($categories) && is_array($categories)) {
		foreach ($categories as $row) {
			$row['cate_link'] = get_category_url($row['cate_id'], $row['cate_dir']);
			$temp[$row['cate_id']] = $row;
		}
	}
	
	return $temp;
}

/** one category */
function get_one_category($cate_id) {
	global $DB;
	
	$row = load_cache('category_'.$cate_id) ? load_cache('category_'.$cate_id) : $DB->fetch_one("SELECT cate_id, root_id, cate_name, cate_dir, cate_arrparentid, cate_arrchildid, cate_childcount, cate_postcount FROM ".$DB->table('categories')." WHERE cate_id='$cate_id' LIMIT 1");
		
	return $row;
}
	
/** category name */
function get_category_name($cate_id) {
	$row = get_one_category($cate_id);
	return $row['cate_name'];
}
	
/** category count */
function get_category_count($cate_id = 0) {
	global $DB;
	
	if ($cate_id > 0) $where = array('root_id' => $cate_id);
	$count = $DB->get_count($DB->table('categories'), $where);
		
	return $count;
}
	
/** category parent ids */
function get_category_parent_ids($cate_id) {
	global $DB;
	
	$sql = "SELECT root_id FROM ".$DB->table('categories')." WHERE cate_id='$cate_id'";
	$ids = $DB->fetch_all($sql);	
		
	$idstr = '';
	if (!empty($ids) && is_array($ids)) {
		foreach ($ids as $id) {
			if ($id['root_id'] > 0) {
				$idstr .= get_category_parent_ids($id['root_id']);
				$idstr .= ','.$id['root_id'];
			} else {
				$idstr = '0';
			}
		}
	}
		
	return $idstr;
}

/** category child ids */
function get_category_child_ids($cate_id) {
	global $DB;
	
	$sql = "SELECT cate_id FROM ".$DB->table('categories')." WHERE root_id='$cate_id'";
	$ids = $DB->fetch_all($sql);	
	
	$idstr = '';
	if (!empty($ids) && is_array($ids)) {
		foreach ($ids as $id) {
			$idstr .= ','.$id['cate_id'];
			$idstr .= get_category_child_ids($id['cate_id']);
		}
	}
	
	return $idstr;
}
?>