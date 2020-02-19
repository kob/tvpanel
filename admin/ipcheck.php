<?php
ini_set('display_errors',1); 
ini_set('display_startup_errors',1); 
error_reporting(E_ERROR);

include "../sql.php";
include "nav.php";
include "../val.php";

if($_SESSION['ipcheck']==0){
  exit();
}

if(isset($_POST['submitunbind'])){
  $userid=$_POST['userid'];
  $result=mysqli_query($GLOBALS['conn'],"select * from chzb_users where name=$userid");
  if(mysqli_fetch_array($result)){
    mysqli_query($GLOBALS['conn'],"update chzb_users set mac='',deviceid='',model='' where name=$userid");
    echo "账号$userid 解绑成功！";
  }else{
    echo "账号不存在！";
  }
}

if(isset($_POST['clearvpn'])){
  $result=mysqli_query($GLOBALS['conn'],"UPDATE chzb_users set vpn=0");
  echo "抓包记录已清空";
}

if(isset($_POST['stopuse'])){
  $name=$_POST['name'];
  $now=time();
  $result=mysqli_query($GLOBALS['conn'],"UPDATE chzb_users set status=0 where name=$name and status>0");
  $result=mysqli_query($GLOBALS['conn'],"UPDATE chzb_users set exp=$now where name=$name and status<0");
}

if(isset($_POST['startuse'])){
  $name=$_POST['name'];
  $result=mysqli_query($GLOBALS['conn'],"UPDATE chzb_users set status=1 where name=$name and status=0");

}

if(isset($_POST['submitmodifyipcount'])){
  $ipcount=$_POST['ipcount'];
  mysqli_query($GLOBALS['conn'],"update chzb_appdata set ipcount=$ipcount");
}

if(isset($_POST['submitclearold'])){
  $oldtime=strtotime(date("Y-m-d"),time());
  mysqli_query($GLOBALS['conn'],"delete from chzb_loginrec where logintime<$oldtime");
}

if(isset($_POST['submitclearall'])){
  mysqli_query($GLOBALS['conn'],"delete from chzb_loginrec");
}

//获取每日允许登陆IP数量
$result=mysqli_query($GLOBALS['conn'],"select ipcount from chzb_appdata");
if($row=mysqli_fetch_array($result)){
  $ipcount=$row['ipcount'];
}else{
  $ipcount=5;
}

?>

<style type="text/css">
form{margin:0px;display: inline}
td{padding: 8px;}
.button {
  background-color: #008CBA; /* Green */
  border: none;
  color: white;
  padding: 5px 5px;
  text-align: center;
  text-decoration: none;
  display: inline-block;
  font-size: 16px;
  margin: 2px 2px;
  cursor: pointer;
}
</style>

<br>
<div id="loginrec">

<table border="1" bordercolor="#a0c6e5" style="border-collapse:collapse;">
<tr><td colspan="7">
<form method="POST">
<input type="text" name="ipcount" size="5" value="<?php echo $ipcount;?>">
<input type="submit" name="submitmodifyipcount" value="设定IP异常数量">
&nbsp;&nbsp;&nbsp;&nbsp;
<input type="submit" name="submitclearold" value="清空一天前记录">
&nbsp;&nbsp;&nbsp;&nbsp;
<input type="submit" name="submitclearall" value="清空所有记录">
</form>
&nbsp;&nbsp;&nbsp;&nbsp;
<form method="POST">
<input type="text" name="userid" size="15" value="">
<input type="submit" name="submitunbind" value="解绑账号">
</form>
</td></tr>

<tr>
<td width="100px">账号</td>
<td width="200px">登陆信息</td>
<td width="200px">登陆信息</td>
<td width="200px">登陆信息</td>
<td width="200px">登陆信息</td>
<td width="200px">登陆信息</td>
<td width="80px">IP数量</td>
</tr>

<?php
$pre24time=strtotime(date("Y-m-d"),time());
$result=mysqli_query($GLOBALS['conn'],"SELECT userid,deviceid,mac,model,ip,region,logintime from chzb_loginrec where logintime>$pre24time order by userid,deviceid,mac,model");
$arrLoginInfo = array();
while($row=mysqli_fetch_array($result)){
  $logintime=date("Y-m-d H:i:s",$row['logintime']);
  $userid=$row['userid'];
  $arrLoginInfo[$userid][]=$row['region']."<br>".$row['ip']."<br>".$logintime;
}
foreach ($arrLoginInfo as $key => $value) {
  if(count($value)>=$ipcount){
    echo "<tr><td>".$key."</td><td>".$arrLoginInfo[$key][0]."</td><td>".$arrLoginInfo[$key][1]."</td><td>".$arrLoginInfo[$key][2]."</td><td>".$arrLoginInfo[$key][3]."</td><td>".$arrLoginInfo[$key][4]."</td><td>".count($value)."</td></tr>";
  }
}
?>
</table>

<p style="margin-top:45px;color: red;font-weight: bold;">注意：使用VPN软件和屏蔽广告的软件，有可能会误判断为抓包。</p>

<table border="1" bordercolor="#a0c6e5" style="border-collapse:collapse;">
<tr>
<td width="100px">账号</td>
<td width="200px">抓包次数</td>
<td width="200px">型号</td>
<td width="200px">账号备注</td>
<td width="200px">用户状态</td>
<td width="300px" colspan="2">操作&nbsp;&nbsp;&nbsp;&nbsp;<form method="POST"><input type='submit' name='clearvpn' value='清空抓包数据'></form></td>
</tr>

<?php
$result=mysqli_query($GLOBALS['conn'],"SELECT status,name,model,vpn,marks,exp from chzb_users where vpn>0");
while ($row=mysqli_fetch_array($result)) {
  $vpn=$row['vpn'];
  $name=$row['name'];
  $marks=$row['marks'];
  $status=$row['status'];
  $model=$row['model'];
  $exp=ceil(($row['exp']-time())/86400);

  if($status==-1){
    $st="试用天数[$exp]";
  }elseif ($status==0) {
  $st='已停用';
}elseif($status==1){
$st='正常';
}else{
$st='永不到期';
}
echo "<tr>
<td>$name</td>
<td>$vpn</td>
<td>$model</td>
<td>$marks</td>
<td>$st</td>
<td colspan='2'>
<form method='post'>
<input type='hidden' name='name' value='$name'>
<input type='submit' name='stopuse' value='&nbsp;&nbsp;停&nbsp;用&nbsp;&nbsp;'>
<input type='submit' name='startuse' value='&nbsp;&nbsp;启&nbsp;用&nbsp;&nbsp;'>
&nbsp;&nbsp;&nbsp;&nbsp;
</form>
</td>
</tr>";
}
?>
</table>
</div>