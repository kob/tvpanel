<?php
include_once"dbmanager.php";

session_start();

if($_SESSION['user']!='admin')exit();

$db = new DbManage();
$db->backup("chzb_admin","./backup/","");
$db->backup("chzb_appdata","./backup/","");
$db->backup("chzb_category","./backup/","");
$db->backup("chzb_channels","./backup/","");
$db->backup("chzb_epg","./backup/","");
$db->backup("chzb_loginrec","./backup/","");
$db->backup("chzb_serialnum","./backup/","");
$db->backup("chzb_users","./backup/","");
$db->backup("chzb_proxy","./backup/","");
echo "数据全部备份完成！";

mysqli_close($GLOBALS['conn']);
?>