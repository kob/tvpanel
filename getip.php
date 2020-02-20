<?php
header("Content-type: text/json; charset=utf-8");

$ip=$_GET['ip'];
$ipjson=file_get_contents("http://ip.taobao.com/service/getIpInfo.php?ip=$ip");
$ipobj=strtr($ipjson, array("X" => ''));
$tbObj=json_decode($ipobj);
$nettype=$tbObj->data->isp;
$loc=$tbObj->data->region;
if( !empty($nettype)) {
  $region=$loc . $tbObj->data->city . ',' . $nettype;
} else{
  $region=$loc . $tbObj->data->city ;
}
$obj=(Object)null;
$obj->region=$region;
$obj->nettype=$nettype;
$obj->loc=$loc;

echo json_encode($obj);
?>
