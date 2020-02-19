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
if(isset($_GET['pdname']) && isset($_GET['cat'])){
  $pdname=$_GET['pdname'];
  $categoryname=$_GET['cat'];
  $result=mysqli_query($GLOBALS['conn'],"select enable from $categoryname where name='$pdname'");
  if($row=mysqli_fetch_array($result)){
    if($row['enable']==1){
      mysqli_query($GLOBALS['conn'],"UPDATE $categoryname set enable=0 where name='$pdname'");
      echo "$pdname 已禁用";
    }else{
      mysqli_query($GLOBALS['conn'],"UPDATE $categoryname set enable=1 where name='$pdname'");
      echo "$pdname 已启用";
    }
  }else{
    echo "$pdname 操作失败！";
  }
}else{
  echo "参数错误";
}
?>