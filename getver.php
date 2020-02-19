<?php
include_once "sql.php";
$appver='V1.0';
$appUrl='';
$sql = "SELECT appver,appurl FROM chzb_appdata";
$result = mysqli_query($GLOBALS['conn'],$sql);

if($row = mysqli_fetch_array($result)) {
	$appver=$row['appver'];	
	$appUrl=$row['appurl'];
}

$obj=(Object)null;
$obj->appver=$appver;
$obj->appurl=$appUrl;

echo json_encode($obj);
mysqli_close($GLOBALS['conn']);

?>