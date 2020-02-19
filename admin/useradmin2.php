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

<?php
ini_set('display_errors',1);            
ini_set('display_startup_errors',1);   
error_reporting(E_ERROR);

include_once "nav.php";

if($_SESSION['useradmin']==0){
	exit();
}

mysqli_query($GLOBALS['conn'],"alter table chzb_users add column vip int DEFAULT 0;");

if(isset($_POST['submitdelall'])){
	$nowtime=time();
	$sql="delete from chzb_users where status=1 and exp<$nowtime";
	mysqli_query($GLOBALS['conn'],$sql);
	echo "已清空所有过期用户！";
}

if(isset($_POST['submitdel'])){
	foreach ($_POST['id'] as $id){
		mysqli_query($GLOBALS['conn'],"delete from chzb_users where name=$id");	
		mysqli_query($GLOBALS['conn'],"delete from chzb_loginrec where userid=$id");	
	}
	echo "选中用户及其登陆信息已删除！";
}

if(isset($_POST['submitmodify'])){
	$expimportmac=$_POST['exp'];
	$exp=strtotime(date("Y-m-d"),time())+86400*$_POST['exp'];
	foreach ($_POST['id'] as $id) {
		mysqli_query($GLOBALS['conn'],"update chzb_users set exp=$exp where name=$id and status=1");
		mysqli_query($GLOBALS['conn'],"update chzb_users set exp=$expimportmac where name=$id and status=2");		
	}
	echo "选中用户授权天数已修改！";	
}

if(isset($_POST['submitadddays'])){
	$expimportmac=$_POST['exp'];
	$exp=86400*$_POST['exp'];
	foreach ($_POST['id'] as $id) {
		mysqli_query($GLOBALS['conn'],"update chzb_users set exp=exp+$exp where name=$id and status=1");	
		mysqli_query($GLOBALS['conn'],"update chzb_users set exp=exp+$expimportmac where name=$id and status=2");	
	}
	echo "选中用户授权天数已增加！";	
}

if(isset($_POST['submitmodifymarks'])){
	$marks=$_POST['marks'];
	foreach ($_POST['id'] as $id) {
		mysqli_query($GLOBALS['conn'],"update chzb_users set marks='$marks' where name=$id");		
	}
	echo "选中用户备注已修改！";	
}

if(isset($_POST['submitforbidden'])){
	foreach ($_POST['id'] as $id) {
		mysqli_query($GLOBALS['conn'],"update chzb_users set status=0 where name=$id and (status=1 or status=999)");		
	}
	echo "选中用户已停用！";	
}

if(isset($_POST['submitNotExpired'])){
	foreach ($_POST['id'] as $id) {
		mysqli_query($GLOBALS['conn'],"update chzb_users set status=999 where name=$id and status=1");		
	}
	echo "选中用户已设置为永不到期！，未激活用户无法设置！";	
}

if(isset($_POST['submitCancelNotExpired'])){
	foreach ($_POST['id'] as $id) {
		mysqli_query($GLOBALS['conn'],"update chzb_users set status=1 where name=$id and status=999");		
	}
	echo "选中用户已取消永不到期权限！";	
}

if(isset($_POST['submitpermit'])){
	foreach ($_POST['id'] as $id) {
		mysqli_query($GLOBALS['conn'],"update chzb_users set status=1 where name=$id and (status=0 or status=999)");		
	}
	echo "选中用户已启用！";	
}

if(isset($_POST['submitclear'])){
	foreach ($_POST['id'] as $id) {
		mysqli_query($GLOBALS['conn'],"update chzb_users set mac='',deviceid='',model='' where name=$id");
		mysqli_query($GLOBALS['conn'],"delete chzb_loginrec from where userid=$id");
	}	
	echo "选中用户绑定信息已清除！";
}

if(isset($_POST['submitmodifyipcount'])){
	$ipcount=$_POST['ipcount'];
	mysqli_query($GLOBALS['conn'],"update chzb_appdata set ipcount=$ipcount");
}

if(isset($_POST['recCounts'])){
	$recCounts=$_POST['recCounts'];
	mysqli_query($GLOBALS['conn'],"update chzb_admin set showcounts=$recCounts where name='$user'");
}

$searchparam='';
if(isset($_GET['keywords'])){
	$keywords=trim($_GET['keywords']);
	$searchparam="and (name like '%$keywords%' or deviceid like '%$keywords%' or mac like '%$keywords%' or name like '%$keywords%' or model like '%$keywords%' or ip like '%$keywords%' or region like '%$keywords%' or author like '%$keywords%' or marks like '%$keywords%')";
}

if(isset($_GET['submitsearch'])){
	$keywords=trim($_GET['keywords']);
	$searchparam="and (name like '%$keywords%' or deviceid like '%$keywords%' or mac like '%$keywords%' or name like '%$keywords%' or model like '%$keywords%' or ip like '%$keywords%' or region like '%$keywords%' or author like '%$keywords%' or marks like '%$keywords%')";
}

//获取每页显示数量
$result=mysqli_query($GLOBALS['conn'],"select showcounts from chzb_admin where name='$user'");
if($row=mysqli_fetch_array($result)){
	$recCounts=$row['showcounts'];
}else{
	$recCounts=100;
}

//获取每日允许登陆IP数量
$result=mysqli_query($GLOBALS['conn'],"select ipcount from chzb_appdata");
if($row=mysqli_fetch_array($result)){
	$ipcount=$row['ipcount'];
}else{
	$ipcount=5;
}

//获取当前页
if(isset($_GET['page'])){
	$page=$_GET['page'];
}else{
	$page=1;
}

//获取排序依据
if(isset($_GET['order'])){
	$order=$_GET['order'];
}else{
	$order='lasttime';
}

//获取用户总数并根据每页显示数量计算页数
$result=mysqli_query($GLOBALS['conn'],"select count(*) from chzb_users where status>-1");
if($row=mysqli_fetch_array($result)){
	$userCount=$row[0];
	$pageCount=ceil($row[0]/$recCounts);
}else{
	$userCount=0;
	$pageCount=1;
}

//处理跳转逻辑
if(isset($_POST['jumpto'])){
	$p=$_POST['jumpto'];
	if(($p<=$pageCount)&&($p>0)){
		header("location:?page=$p&order=$order");
	}
}

//todayTime为24小时前时间
$todayTime=strtotime(date("Y-m-d"),time());
$result=mysqli_query($GLOBALS['conn'],"select count(*) from chzb_users where status>-1 and lasttime>$todayTime");
if($row=mysqli_fetch_array($result)){
	$todayuserCount=$row[0];
}else{
	$todayuserCount=0;
}

$result=mysqli_query($GLOBALS['conn'],"select count(*) from chzb_users where status>-1 and authortime>$todayTime");
if($row=mysqli_fetch_array($result)){
	$todayauthoruserCount=$row[0];
}else{
	$todayauthoruserCount=0;
}

$nowTime=time();
$result=mysqli_query($GLOBALS['conn'],"select count(*) from chzb_users where status=1 and exp<$nowTime");
if($row=mysqli_fetch_array($result)){
	$expuserCount=$row[0];
}else{
	$expuserCount=0;
}
?>
<br>

<script type="text/javascript">
function submitForm(){
    var form = document.getElementById("recCounts");
    form.submit();
}
function quanxuan(a){
	var ck=document.getElementsByName("id[]");
	for (var i = 0; i < ck.length; i++) {
		var tr=ck[i].parentNode.parentNode;
		if(a.checked){
			ck[i].checked=true;
			tr.style.backgroundColor="#bbccdd";
		}else{
			ck[i].checked=false;
			tr.style.backgroundColor="#fff";
		}
	}
}
function changecolor(a){
	var tr=a.parentNode.parentNode;
	if(a.checked){
		tr.style.backgroundColor="#bbccdd";
	}else{
		tr.style.backgroundColor="#fff";
	}
}
</script>

<center>

<style type="text/css">
	#usersetting{
		display: none;
		position: absolute;
		left:25%;
		width: 50%;
		height: 50%;
		border-radius: 10px;
		background:#666;
		color: white;
	}
	#usersetting p{padding: 10px;}
	.button{font-size:15px;color:white;background:red;float:right;margin:10px;border-radius:5px;padding:3px;}
</style>

<div id='usersetting'>
	<div class='button' onclick="javascript:hidesetting()">关闭</div>
	<p>用户详细信息
	<div id="userinfos"></div>
</div>

<script type="text/javascript">
 function hidesetting(){
 	var ds=document.getElementById('usersetting');
 	ds.style.display='none';
 }
 function showsetting(id){
 	var ds=document.getElementById('usersetting');
 	ds.style.display='block';
	var userinfo=document.getElementById('userinfos');
	userinfo.innerHtml=id;
 }
</script>

<table border="1" bordercolor="#a0c6e5" style="border-collapse:collapse;">
	<tr><td colspan=10><b>已授权用户列表</b>&nbsp;&nbsp;&nbsp;&nbsp;
		&nbsp;&nbsp;&nbsp;全部用户：<?php echo $userCount; ?>
		&nbsp;今日上线：<?php echo $todayuserCount; ?>
		&nbsp;今日授权：<?php echo $todayauthoruserCount; ?>
		&nbsp;过期用户：<?php echo $expuserCount; ?>
		&nbsp;&nbsp;&nbsp;
		<form method="GET">
			<input type="text" name="keywords" size="16" value="<?php echo $keywords;?>">
			<input type="submit" name="submitsearch" value="搜索">
		</form>
		<br><br>
		<form method="POST" id="recCounts">
		每页
			<select id="sel" name="recCounts" onchange="submitForm();">
			<?php
			switch ($recCounts) {
				case '20':
					echo "<option value=\"20\" selected=\"selected\">20</option>";
					echo "<option value=\"50\">50</option>";
					echo "<option value=\"100\">100</option>";
					break;
				case '50':
					echo "<option value=\"20\">20</option>";
					echo "<option value=\"50\" selected=\"selected\">50</option>";
					echo "<option value=\"100\">100</option>";
					break;
				case '100':
					echo "<option value=\"20\">20</option>";
					echo "<option value=\"50\">50</option>";
					echo "<option value=\"100\" selected=\"selected\">100</option>";
					break;
				
				default:
					echo "<option value=\"20\" selected=\"selected\">20</option>";
					echo "<option value=\"50\">50</option>";
					echo "<option value=\"100\">100</option>";
					break;
			}
			?>
			</select>
			条
			<a href="<?php echo '?page=1&order='.$order.'&keywords='.$keywords?>">首页</a>&nbsp;
			<a href="<?php if($page>1){$p=$page-1;}else{$p=1;} echo '?page='.$p.'&order='.$order.'&keywords='.$keywords?>">上一页</a>&nbsp;
			<a href="<?php if($page<$pageCount){$p=$page+1;} else {$p=$page;} echo '?page='.$p.'&order='.$order.'&keywords='.$keywords?>">下一页</a>&nbsp;
			<a href="<?php echo '?page='.$pageCount.'&order='.$order.'&keywords='.$keywords?>">尾页</a>
			
			<input type="text" name="jumpto" style="text-align: center;" size=2 value="<?php echo $page?>">/
			<?php echo $pageCount?>
			<button onclick="submitForm()">跳转</button>
		</form>
		
		</td></tr>
	<form method="POST">
	<tr><td width=70><a href="?order=name">账号<?php if($order=='name')echo'↓';?></a></td>
		<td width=140><a href="?order=mac">MAC地址<?php if($order=='mac')echo'↓';?></a></td>
		<td width=140><a href="?order=deviceid">设备ID<?php if($order=='deviceid')echo'↓';?></a></td>
		<td width=140><a href="?order=model">型号<?php if($order=='model')echo'↓';?></a></td>
		<td width=120><a href="?order=ip">IP<?php if($order=='ip')echo'↓';?></a></td>
		<td width=150><a href="?order=region">地区<?php if($order=='region')echo'↓';?></a></td>
		<td width=100><a href="?order=lasttime">最后登陆<?php if($order=='lasttime')echo'↓';?></a></td>
		<td width=60><a href="?order=exp">剩余天数<?php if($order=='exp')echo'↓';?></a></td>
		<td width=65><a href="?order=author">授权人<?php if($order=='author')echo'↓';?></a></td>
		<td width=100><a href="?order=marks">备注<?php if($order=='marks')echo'↓';?></a></td></tr>
<?php
$recStart=$recCounts*($page-1);

if($order!='exp')$order=$order.' desc';

if($user=='admin'){
$sql="select status,name,mac,deviceid,model,ip,region,lasttime,exp,author,marks,vpn,vip from chzb_users where status>-1 $searchparam order by $order  limit $recStart,$recCounts";
}else{
	$sql="select status,name,mac,deviceid,model,ip,region,lasttime,exp,author,marks,vpn,vip from chzb_users where status>-1 and author='$user' $searchparam order by $order limit $recStart,$recCounts";
}

$result=mysqli_query($GLOBALS['conn'],$sql);
while($row=mysqli_fetch_array($result)){
	$status=$row['status'];
	$lasttime=$status==2?'MAC导入未激活':date("Y-m-d H:i:s",$row['lasttime']);
	$days=ceil(($row['exp']-time())/86400);
	$expdate="到期时间：".date("Y-m-d H:i:s",$row['exp']);
	$name=$row['name'];
	$deviceid=$row['deviceid'];
	$mac=$row['mac'];
	$model=$row['model'];
	$ip=$row['ip'];
	$region=$row['region'];
	$author=$row['author'];
	$marks=$row['marks'];
  $vpn=$row['vpn'];
  $vip=$row['vip'];
	if($row['exp']<time()){
		$days='过期';
	}
	if($status==0){
		$days='停用';
        if($vpn>0){
     	   $days="禁用[".$vpn."]";
    	}
	}
	if($status==2){
		$days=$row['exp'];
		$expdate='MAC导入未激活';
	}
	if($status==999){
		$days='永不到期';
		$expdate=$days;
	}
	if($vip==1){
		$fontcolor='red';
	}else{
		$fontcolor='black';
	}
	echo "<tr><td><input type='checkbox' value='$name' name='id[]' onchange=\"changecolor(this)\">
		<a href=# onclick=\"showsetting()\">
			<font color='$fontcolor'>$name </font></a></td>
	     <td>$mac</td>
	     <td>".$deviceid."</td>
		 <td>$model</td>
	     <td>".$ip."</td>
	     <td>".$region."</td>
	     <td>".$lasttime ."</td>
	     <td title='$expdate'>".$days."</td>
	     <td>".$author."</td>
	     <td>$marks</td></tr>";
}
?>

<tr><td colspan="10">
	<input type="checkbox" onclick="quanxuan(this)">全选
	<input type="submit" name="submitforbidden" value="&nbsp;停&nbsp;&nbsp;用&nbsp;">
	&nbsp;&nbsp;&nbsp;
	<input type="submit" name="submitpermit" value="&nbsp;启&nbsp;&nbsp;用&nbsp;">
	&nbsp;&nbsp;&nbsp;
	<input type="submit" name="submitdel" value="&nbsp;删&nbsp;&nbsp;除&nbsp;" onclick="return confirm('确定删除选中用户吗？')">
	&nbsp;&nbsp;&nbsp;
	<input type="text" name="marks" value="已授权" size="15">
	<input type="submit" name="submitmodifymarks" value="修改备注">
	&nbsp;&nbsp;&nbsp;	
	<input type="text" name="exp" value="365" size="3">	
	<input type="submit" name="submitmodify" value="修改天数">
	<input type="submit" name="submitadddays" value="增加天数">
	&nbsp;&nbsp;&nbsp;
	<input type="submit" name="submitclear" value="设备解绑">
	<br><br>
	<input type="submit" name="submitNotExpired" value="设为永不到期">
	&nbsp;&nbsp;&nbsp;
	<input type="submit" name="submitCancelNotExpired" value="取消永不到期">
	&nbsp;&nbsp;&nbsp;
	<input type="submit" name="submitdelall" value="清空过期用户" onclick="return confirm('确认删除所有已过期授权信息？')">
</td></tr>
</form>
</table>
</center>