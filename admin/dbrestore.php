<?php
include_once "dbmanager.php";
echo "数据库正在恢复，请勿操作!";

session_start();
if($_SESSION['user']!='admin')exit();
    
$db = new DbManage();
$db->restore("./backup/chzb_admin_v1.sql");
$db->restore("./backup/chzb_appdata_v1.sql");
$db->restore("./backup/chzb_category_v1.sql");
$db->restore("./backup/chzb_channels_v1.sql");
$db->restore("./backup/chzb_epg_v1.sql");
$db->restore("./backup/chzb_loginrec_v1.sql");
$db->restore("./backup/chzb_serialnum_v1.sql");
$db->restore("./backup/chzb_users_v1.sql");
$db->restore("./backup/chzb_proxy_v1.sql");
echo "数据库恢复成功！";

mysqli_close($GLOBALS['conn']);
?>