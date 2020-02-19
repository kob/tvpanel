<?php
include_once"aes.php";
include_once "sql.php";
mysqli_query($GLOBALS['conn'],"SET NAMES 'UTF8'");
$channelNumber=1;

function echoJSON($category,$alisname,$psw){
    global $channelNumber;

    $sql = "SELECT name,url FROM chzb_channels where category='$category' order by id";
    $result = mysqli_query($GLOBALS['conn'],$sql);
    $nameArray = array();
    while($row = mysqli_fetch_array($result)) {
      if(!in_array($row['name'],$nameArray)){
        $nameArray[]=$row['name'];
      } 
      $sourceArray[$row['name']][]=$row['url'];
    }
    mysqli_free_result($result);
    $objCategory=(Object)null;
    $objChannel=(Object)null;

    $channelArray=array();
    for($i=0;$i<count($nameArray);$i++) {
      $objChannel=(Object)null;
      $objChannel->num=$channelNumber;
      $objChannel->name=$nameArray[$i];
      $objChannel->source=$sourceArray[$nameArray[$i]];
      $channelArray[]=$objChannel;
      $channelNumber++;
    }
    $objCategory->name=$alisname;
    $objCategory->psw=$psw;
    $objCategory->data=$channelArray;

    return $objCategory;
  }

  if(isset($_POST['data'])){
    $obj=json_decode($_POST['data']);
    $region=$obj->region;
    $mac=$obj->mac;
    $androidid=$obj->androidid;
    $model=$obj->model;
    $nettype=$obj->nettype;
    $appname=$obj->appname;
    $randkey=$obj->rand;

  $contents[]= echoJSON($pdname,"我的收藏",''); 

  //添加默认频道数据
  $sql = "SELECT name,id,psw FROM chzb_category where enable=1 order by id";
  $result = mysqli_query($GLOBALS['conn'],$sql);
  while($row = mysqli_fetch_array($result)) {
    $pdname=$row['name'];
    $psw=$row['psw'];
    $contents[]= echoJSON($pdname,$pdname,$psw); 
  }

  $str=json_encode($contents,JSON_UNESCAPED_UNICODE);
  $str=stripslashes($str);
  $str=base64_encode(gzcompress($str));

  $result=mysqli_query($GLOBALS['conn'],"select dataver from chzb_appdata");
  $ver=3;
  if($row=mysqli_fetch_array($result)){
    $ver=$row[0];
  }
  $key=md5($key.$randkey);
  $key=substr($key,7,16);

  $aes = new Aes($key);
  $encrypted =$aes->encrypt($str);

  $encrypted=str_replace("f", "&", $encrypted);
  $encrypted=str_replace("b", "f", $encrypted);
  $encrypted=str_replace("&", "b", $encrypted);

  $encrypted=str_replace("t", "#", $encrypted);
  $encrypted=str_replace("y", "t", $encrypted);
  $encrypted=str_replace("#", "y", $encrypted);

  $coded=substr($encrypted,44,128);
  $coded=strrev($coded);

  $str=$coded.$encrypted;

  echo $str;
  mysqli_close($GLOBALS['conn']);
}else{
  mysqli_close($GLOBALS['conn']);
  exit();
}


?>