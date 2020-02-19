<?php
include_once "../sql.php";

session_start();

if(isset($_POST['username'])&&isset($_POST['password'])){
    $user=$_POST['username'];
    $psw=$_POST['password'];
    $result=mysqli_query($GLOBALS['conn'],"select * from chzb_admin where name='$user'");
    if($row=mysqli_fetch_array($result)){
        if($psw==$row['psw']){
        	$user=$row['name'];
            $_SESSION['user']=$user;
            $_SESSION['psw']=md5($psw); 
            $_SESSION['author1']=$row['author1'];
            $_SESSION['author2']=$row['author2'];
            $_SESSION['useradmin']=$row['useradmin'];
            $_SESSION['channeladmin']=$row['channeladmin'];
            $_SESSION['ipcheck']=$row['ipcheck'];
            if(isset($_POST['rememberpass'])){
            	setcookie("username",$user,time()+3600*24*7);
            	setcookie("psw",md5($psw),time()+3600*24*7);
            	setcookie("rememberpass","1",time()+3600*24*7);
            }else{
            	setcookie("rememberpass","1",time()-3600);
            }
            header("location:useradmin0.php");
        }else{
            echo "<script>alert('密码错误！');</script>";
        }
    }else{
        echo "<script>alert('用户不存在！');</script>";
    }
}

if(isset($_COOKIE['rememberpass'])){
	$user=$_COOKIE['username'];
	$psw=$_COOKIE['psw'];
	$result=mysqli_query($GLOBALS['conn'],"select * from chzb_admin where name='$user'");
  if($row=mysqli_fetch_array($result)){
  	if($psw==md5($row['psw'])){
    	$_SESSION['user']=$user;
			$_SESSION['psw']=$psw;
      $_SESSION['author1']=$row['author1'];
		  $_SESSION['author2']=$row['author2'];
		  $_SESSION['useradmin']=$row['useradmin'];
		  $_SESSION['channeladmin']=$row['channeladmin'];
		  $_SESSION['ipcheck']=$row['ipcheck'];
      header("location:useradmin0.php");
    }
  }
}
?>

<!doctype html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>欢迎您登录IPTV管理平台</title>
<link href="" type="image/x-icon" rel="shortcut icon">
<link rel="stylesheet" type="text/css" href="./css/login.css" media="all">
<link rel="stylesheet" type="text/css" href="./css/default_color.css" media="all">
</head>

<body id="login-page">
<div id="main-content">
<div class="login-body">
  <div class="login-main pr">
    <form method="post" class="login-form">
      <h3 class="welcome">IPTV管理平台</h3>
      <div id="itemBox" class="item-box">
        <div class="item">
          <i class="icon-login-user"></i>
          <input class="inputtext" type="text" name="username" placeholder="请填写用户名" autocomplete="off" />
        </div>
      <span class="placeholder_copy placeholder_un">请填写用户名</span>
      <div class="item b0">
        <i class="icon-login-pwd"></i>
        <input class="inputtext" type="password" name="password" placeholder="请填写密码" autocomplete="off" />
      </div>
      <span class="placeholder_copy placeholder_pwd">请填写密码</span>
      </div>
      <input class="check" type="checkbox" value="1" name="rememberpass">7天免登录
      <div class="login_btn_panel">
        <button class="login-btn" type="submit">
          <span class="in"><i class="icon-loading"></i>登 录 中 ...</span>
          <span class="on">登 录</span>
        </button>
        <div class="check-tips"></div>
      </div>
    </form>
  </div>
</div>
</div>
</body>
</html>