<?php
include_once "sql.php";
$id=$_GET['id'];

mysqli_query($GLOBALS['conn'],"UPDATE chzb_users set vpn=vpn+1 where name=$id");
mysqli_close($GLOBALS['conn']);

?>