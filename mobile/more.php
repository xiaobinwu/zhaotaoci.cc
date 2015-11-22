<?php
require 'common.inc.php';
$head_title = $L['more_title'].$DT['seo_delimiter'].$head_title;
$foot = 'more';
include template('more', 'mobile');
if(DT_CHARSET != 'UTF-8') toutf8();
?>