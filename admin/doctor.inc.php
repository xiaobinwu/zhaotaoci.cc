<?php
/*
	[Destoon B2B System] Copyright (c) 2008-2015 www.destoon.com
	This is NOT a freeware, use is subject to license.txt
*/
defined('DT_ADMIN') or exit('Access Denied');
$menus = array (
    array('系统体检', '?file='.$file),
    array('MySQL进程', '?file=database&action=process', ' target="_blank"'),
    array('PHP信息', '?file='.$file.'&action=phpinfo', ' target="_blank"'),
);
if($action == 'phpinfo') {
	phpinfo();
} else {
	include tpl('doctor');
}
?>