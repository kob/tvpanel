<?php
session_start();
session_unset();//free all session variable
session_destroy();//销毁一个会话中的全部数据
setcookie("rememberpass","1",time()-3600);
header("location:useradmin0.php");
?>