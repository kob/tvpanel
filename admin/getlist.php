<?php
include_once "../sql.php";
session_start();
if(isset($_SESSION['user']))$user=$_SESSION['user'];
$result=mysqli_query($GLOBALS['conn'],"select * from chzb_admin where name='$user'");
  if($row=mysqli_fetch_array($result)){
  	$psw=$row['psw'];
  }else{
    $psw='';
  }
if(!isset($_SESSION['psw'])||$_SESSION['psw']!=md5($psw)){
    exit;
}
?>

<?php
header("Content-type:text/json;charset=utf-8");

function echoSource($category){
	mysqli_query($GLOBALS['conn'],"SET NAMES 'UTF8'");
	$sql = "SELECT distinct name,url FROM chzb_channels where category='$category' order by id";
	$result = mysqli_query($GLOBALS['conn'],$sql);
	while($row = mysqli_fetch_array($result)) {
		echo $row['name'] .",". $row['url'] . "\n";
	}
}

if(isset($_GET['pd'])){
	$pd=$_GET['pd'];
}else{
	$pd='未知';
}

echoSource($pd);
?>