<?php 
defined('IN_DESTOON') or exit('Access Denied');
login();
require DT_ROOT.'/module/'.$module.'/common.inc.php';
require DT_ROOT.'/include/post.func.php';
$avatar = useravatar($_userid, 'large', 0, 2);
switch($action) {
	case 'update':
		$t = $avatar ? 1 : 0;
		$db->query("UPDATE {$DT_PRE}member SET avatar=$t WHERE userid=$_userid");
		dheader('?itemid='.$DT_TIME);
	break;
	case 'upload':
		$_FILES['upfile']['size'] or dheader('?itemid='.$DT_TIME);
		require DT_ROOT.'/include/upload.class.php';
		$ext = file_ext($_FILES['upfile']['name']);
		$name = 'avatar'.$_userid.'.'.$ext;
		$file = DT_ROOT.'/file/temp/'.$name;
		if(is_file($file)) file_del($file);
		$upload = new upload($_FILES, 'file/temp/', $name, 'jpg|gif|png');
		$upload->adduserid = false;
		if($upload->save()) {
			$file = DT_ROOT.'/file/temp/'.$name;
			$img_info = @getimagesize($file);
			if(!$img_info || $img_info[0] < 128 || $img_info[1] < 128) file_del($file);
			$img_info or message($L['avatar_img_t']);
			$img_info[0] >= 128 or message($L['avatar_img_w']);
			$img_info[1] >= 128 or message($L['avatar_img_h']);
			$par = 'auth='.encrypt($_userid.'|'.$_username, DT_KEY.'AVATAR');
			require DT_ROOT.'/include/image.class.php';
			$image = new image($file);
			$image->thumb(128, 128);
			$par .= '&pic1='.urlencode((base64_encode(file_get($file))));
			$image = new image($file);
			$image->thumb(48, 48);
			$par .= '&pic2='.urlencode(base64_encode(file_get($file)));
			$image = new image($file);
			$image->thumb(20, 20);
			$par .= '&pic3='.urlencode(base64_encode(file_get($file)));
			file_del($file);
			$arr = dcurl(DT_PATH.'api/avatar/upload.php', $par);
			if(strpos($arr, ':1}') !== false) dheader('?itemid='.$DT_TIME);
			dalert($L['avatar_img_e'].$arr, '?itemid='.$DT_TIME);
		} else {
			message($upload->errmsg);
		}
	break;
	case 'delete':
		if($avatar) {
			$img = array();
			$img[1] = useravatar($_userid, 'large', 0, 2);
			$img[2] = useravatar($_userid, '', 0, 2);
			$img[3] = useravatar($_userid, 'small', 0, 2);
			$img[4] = useravatar($_username, 'large', 1, 2);
			$img[5] = useravatar($_username, '', 1, 2);
			$img[6] = useravatar($_username, 'small', 1, 2);
			foreach($img as $i) {
				file_del($i);
			}
			if($DT['ftp_remote'] && $DT['remote_url']) {
				require DT_ROOT.'/include/ftp.class.php';
				$ftp = new dftp($DT['ftp_host'], $DT['ftp_user'], $DT['ftp_pass'], $DT['ftp_port'], $DT['ftp_path'], $DT['ftp_pasv'], $DT['ftp_ssl']);
				if($ftp->connected) {
					foreach($img as $i) {
						$t = explode("/file/", $i);
						$ftp->dftp_delete($t[1]);
					}
				}
			}
		}
		$db->query("UPDATE {$DT_PRE}member SET avatar=0 WHERE userid=$_userid");
		dmsg($L['avatar_delete'], 'avatar.php?itemid='.$DT_TIME);
	break;
	default:
		$auth = encrypt($_userid.'|'.$_username, DT_KEY.'AVATAR');
		$head_title = $L['avatar_title'];	
	break;
}
include template('avatar', $module);
?>