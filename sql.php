<?php
header("Content-type: text/html; charset=utf-8");
$conn=mysqli_connect("localhost" , "user" , "password" , "tvpanel") OR die ('无法登录MYSQL服务器！');

global $conn;
mysqli_query($GLOBALS['conn'],"SET NAMES 'UTF8'");

?>