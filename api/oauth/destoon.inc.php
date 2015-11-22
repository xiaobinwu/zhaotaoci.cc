<?php
/*
	[Destoon B2B System] Copyright (c) 2008-2015 www.destoon.com
	This is NOT a freeware, use is subject to license.txt
*/
defined('IN_DESTOON') or exit('Access Denied');
function del_token($arr) {
	if($arr) {
		foreach($arr as $v) {
			$_SESSION[$v] = '';
		}
	}
}
if($success) {
	$U = $db->get_one("SELECT * FROM {$DT_PRE}oauth WHERE openid='$openid' AND site='$site'");
	if($_userid) {
		if($U) {
			if($U['username'] == $_username) {
				$update = '';
				if($U['nickname'] != $nickname) $update .= ",nickname='".addslashes($nickname)."'";
				if($U['avatar'] != $avatar) $update .= ",avatar='".addslashes($avatar)."'";
				if($U['url'] != $url) $update .= ",url='".addslashes($url)."'";
				if($update) {
					$update = substr($update, 1);
					$db->query("UPDATE {$DT_PRE}oauth SET {$update} WHERE itemid=$U[itemid]");
				}
				del_token($DS);
				dheader($MODULE[2]['linkurl'].'oauth.php');
			} else {
				$db->query("UPDATE {$DT_PRE}oauth SET username='$_username',nickname='".addslashes($nickname)."',avatar='".addslashes($avatar)."',url='".addslashes($url)."' WHERE itemid=$U[itemid]");
				del_token($DS);
				dheader($MODULE[2]['linkurl'].'oauth.php');
			}
		} else {
			$db->query("DELETE FROM {$DT_PRE}oauth WHERE username='$_username' AND site='$site'");
			$db->query("INSERT INTO {$DT_PRE}oauth (username,site,openid,nickname,avatar,url,addtime,logintime,logintimes) VALUES ('$_username','$site','$openid','".addslashes($nickname)."','".addslashes($avatar)."','".addslashes($url)."','$DT_TIME','$DT_TIME','1')");
			$forward = get_cookie('forward_url');
			if($forward) set_cookie('forward_url', '');
			if(strpos($forward, 'api/oauth') !== false) $forward = '';
			del_token($DS);
			dheader($forward ? $forward : $MODULE[2]['linkurl'].'oauth.php');
		}
	} else {
		if($U) {
			$update = "logintimes=logintimes+1,logintime=$DT_TIME";
			if($U['nickname'] != $nickname) $update .= ",nickname='".addslashes($nickname)."'";
			if($U['avatar'] != $avatar) $update .= ",avatar='".addslashes($avatar)."'";
			if($U['url'] != $url) $update .= ",url='".addslashes($url)."'";
			$db->query("UPDATE {$DT_PRE}oauth SET {$update} WHERE itemid=$U[itemid]");
			include load('member.lang');
			$MOD = cache_read('module-2.php');
			include DT_ROOT.'/include/module.func.php';
			include DT_ROOT.'/module/member/member.class.php';
			$do = new member;
			$user = $do->login($U['username'], '', 0, true);
			if($user) {
				$forward = get_cookie('forward_url');
				if($forward) set_cookie('forward_url', '');
				if(strpos($forward, 'api/oauth') !== false) $forward = '';
				$forward or $forward = $MODULE[2]['linkurl'];
				del_token($DS);
				$api_msg = '';
				if($MOD['passport'] == 'uc') {				
					$action = 'oauth';
					$passport = $user['passport'];
					include DT_ROOT.'/api/'.$MOD['passport'].'.inc.php';
				}
				#if($MOD['sso']) include DT_ROOT.'/api/sso.inc.php';
				if($api_msg) message($api_msg, $forward, -1);
				dheader($forward);
			} else {
				message($do->errmsg, $MODULE[2]['linkurl'].$DT['file_login']);
			}
		} else {
			if(!get_cookie('oauth_site')) {
				set_cookie('oauth_user', $nickname);
				set_cookie('oauth_site', $site);
				dheader(DT_PATH);
			}
			set_cookie('bind', 1);
			$MOD = cache_read('module-2.php');
			$forward = DT_PATH.'api/oauth/'.$site.'/';
			include template('bind', 'member');
		}
	}
} else {
	del_token($DS);
	set_cookie('oauth_user', '');
	set_cookie('oauth_site', '');
	dheader($MODULE[2]['linkurl'].$DT['file_login'].'?error=oauth&step=userinfo&site='.$site);
}
?>