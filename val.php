<?php
session_start();

if(isset($_SESSION['user'])){
  $user=$_SESSION['user'];
}else{
  header("location:userlogin.php");
}

$result=mysqli_query($GLOBALS['conn'],"select * from chzb_admin where name='$user'");
if($row=mysqli_fetch_array($result)){
  $psw=$row['psw'];
}else{
  $psw='';
}

if(!isset($_SESSION['psw'])||$_SESSION['psw']!=md5($psw)){
  header("location:userlogin.php");
}

?>
