<?php
defined('IN_DESTOON') or exit('Access Denied');
function update_company_setting($userid, $setting) {
	global $db;
	$S = get_company_setting($userid);
	foreach($setting as $k=>$v) {
		if(!check_name($k)) continue;
		if(is_array($v)) {
			foreach($v as $i=>$j) {
				$v[$i] = str_replace(',', '', $j);
			}
			$v = implode(',', $v);
		}
		if(isset($S[$k])) {
			$db->query("UPDATE {$db->pre}company_setting SET item_value='$v' WHERE userid=$userid AND item_key='$k'");
		} else {
			$db->query("INSERT INTO {$db->pre}company_setting (userid,item_key,item_value) VALUES ('$userid','$k','$v')");
		}
	}
	return true;
}

function get_company_setting($userid, $key = '', $cache = '') {
	global $db;
	if($key) {
		$r = $db->get_one("SELECT * FROM {$db->pre}company_setting WHERE userid=$userid AND item_key='$key'", $cache);
		return $r ? $r['item_value'] : '';
	} else {
		$setting = array();
		if($cache) {
			$query = $db->query("SELECT * FROM {$db->pre}company_setting WHERE userid=$userid", $cache);
		} else {
			$query = $db->query("SELECT * FROM {$db->pre}company_setting WHERE userid=$userid", $cache);
		}
		while($r = $db->fetch_array($query)) {
			$setting[$r['item_key']] = $r['item_value'];
		}
		return $setting;
	}
}

function make_auth($username) {
	global $DT_IP;
	return strtoupper(md5(md5($username.'|'.DT_KEY.'|'.$DT_IP)));
}

function check_auth($auth) {
	global $db, $L;
	preg_match("/^[A-Z0-9]{32}$/", $auth) or dalert($L['check_auth'], DT_PATH);
	$user = $db->get_one("SELECT * FROM {$db->pre}member WHERE auth='$auth'");
	if($user && $auth == make_auth($user['username'])) return $user;
	dalert($L['check_auth'], DT_PATH);
}

function auth_time($time, $type = 0) {
	global $MOD, $DT_TIME, $L;
	$second = $type ? 600 : 86400;
	if($MOD['auth_days'] && $DT_TIME - $time > $MOD['auth_days']*$second) dalert($L['auth_time'], $MOD['linkurl']);
}

function max_sms($mobile) {
	global $DT, $L, $today_endtime, $_username, $db;
	$max = intval($DT['sms_max']);
	if($max) {
		$condition = $_username ? "editor='$_username'" : "mobile='$mobile'";
		$condition .= " AND message LIKE '%".$L['sms_code']."%' AND sendtime>$today_endtime-86400";
		$items = $db->count($db->pre.'sms', $condition);
		if($items >= $max) return true;
	}
	return false;
}
?>