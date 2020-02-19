<style type="text/css">
 form{margin:0px;display: inline}
 td{padding: 8px;}
</style>

<?php
ini_set('display_errors',1);            
ini_set('display_startup_errors',1);   
error_reporting(E_ERROR);

include_once "nav.php";

if($_SESSION['author1']==0)exit();

if(isset($_POST['submitdel'])){
	foreach ($_POST['id'] as $id){
		mysqli_query($GLOBALS['conn'],"delete from chzb_users where name=$id");
		$sql="delete from chzb_users where name='$id'";
		mysqli_query($GLOBALS['conn'],$sql);			
	}
	echo "选中用户信息已删除！";		
}

if(isset($_POST['submitauthor'])){
	foreach ($_POST['id'] as $id){		
		$administrator=$_SESSION['user'];
		$nowtime=time();
		$exp=strtotime(date("Y-m-d"),time())+86400*$_POST['exp'];
		$sql="update chzb_users set status=1,exp=$exp,author='$administrator',authortime=$nowtime,marks='已授权' where name='$id'";
		mysqli_query($GLOBALS['conn'],$sql);
	}
	echo "选中用户授权成功！";		
}

if(isset($_POST['submitauthorforever'])){
	foreach ($_POST['id'] as $id){
		$exp=strtotime(date("Y-m-d"),time())+86400*999;
		$administrator=$_SESSION['user'];
		$nowtime=time();
		$sql="update chzb_users set status=999,exp=$exp,author='$administrator',authortime=$nowtime,marks='已授权' where name='$id'";
		mysqli_query($GLOBALS['conn'],$sql);
	}
	echo "选中用户已授权为永不到期！";		
}

if(isset($_POST['submitforbidden'])){
	foreach ($_POST['id'] as $id){
		$exp=strtotime(date("Y-m-d"),time());
		$administrator=$_SESSION['user'];
		$nowtime=time();
		$sql="update chzb_users set exp=$exp where name='$id'";
		mysqli_query($GLOBALS['conn'],$sql);
	}
	echo "选中用户已被禁止试用！";		
}

if(isset($_POST['submitdelonedaybefor'])){
	$onedaybefore=strtotime(date("Y-m-d"),time());
	$sql="delete from chzb_users where status=-1 and lasttime<$onedaybefore";
	mysqli_query($GLOBALS['conn'],$sql);
	echo "已删除一天前未授权用户！";
}

if(isset($_POST['submitdelall'])){
	$sql="delete from chzb_users where status=-1";
	mysqli_query($GLOBALS['conn'],$sql);
	echo "已删除所有未授权用户！";
}

if(isset($_POST['recCounts'])){
	$recCounts=$_POST['recCounts'];
	mysqli_query($GLOBALS['conn'],"update chzb_admin set showcounts=$recCounts where name='$user'");
}

if(isset($_GET['keywords'])){
	$keywords=$_GET['keywords'];
	$searchparam="and (name like '%$keywords%' or mac like '%$keywords%' or deviceid like '%$keywords%' or model like '%$keywords%' or ip like '%$keywords%' or region like '%$keywords%' or lasttime like '%$keywords%')";
}

$result=mysqli_query($GLOBALS['conn'],"select showcounts from chzb_admin where name='$user'");
if($row=mysqli_fetch_array($result)){
	$recCounts=$row['showcounts'];
}else{
	$recCounts=100;
}

$result=mysqli_query($GLOBALS['conn'],"select adtext from chzb_appdata");
if($row=mysqli_fetch_array($result)){
	$adtext=$row['adtext'];
	$userpsw=$row['userpsw'];
}else{
	$ad="";
}

if(isset($_GET['page'])){
	$page=$_GET['page'];
}else{
	$page=1;
}

if(isset($_GET['order'])){
	$order=$_GET['order'];
}else{
	$order='lasttime';
}

$result=mysqli_query($GLOBALS['conn'],"select count(*) from chzb_users where status=-1");
if($row=mysqli_fetch_array($result)){
	$userCount=$row[0];
	$pageCount=ceil($row[0]/$recCounts);
}else{
	$userCount=0;
	$pageCount=1;
}

if(isset($_POST['jumpto'])){
	$p=$_POST['jumpto'];
	if(($p<=$pageCount)&&($p>0)){
		header("location:?page=$p&order=$order");
	}
}

$todayTime=strtotime(date("Y-m-d"),time());
$result=mysqli_query($GLOBALS['conn'],"select count(*) from chzb_users where status=-1 and lasttime>$todayTime");
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

?>

<br>
<script type="text/javascript">
function submitForm(){
    var form = document.getElementById("recCounts");
    form.submit();
}
function submitjump(){
    var form = document.getElementById("jumpto");
    form.submit();
}
function quanxuan(a){
	var ck=document.getElementsByName("id[]");
	for (var i = 0; i < ck.length; i++) {
		if(a.checked){
			ck[i].checked=true;
		}else{
			ck[i].checked=false;
		}
	}
}
</script>

<center>
<table border="1" bordercolor="#a0c6e5" style="border-collapse:collapse;">
	<tr><td colspan=8><b>待授权列表</b>
		  &nbsp;&nbsp;&nbsp;待授权用户：<?php echo $userCount; ?>
			&nbsp;今日上线：<?php echo $todayuserCount; ?>
			&nbsp;今日授权：<?php echo $todayauthoruserCount; ?>
			&nbsp;&nbsp;&nbsp;
			<form method="GET">
				<input type="text" name="keywords" value="<?php echo $keywords;?>">
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
			</form>条

			<a href="<?php echo '?keywords='.$keywords.'&page=1&order='.$order?>">首页</a>&nbsp;
			<a href="<?php if($page>1){$p=$page-1;}else{$p=1;} echo '?keywords='.$keywords.'&page='.$p.'&order='.$order?>">上一页</a>&nbsp;
			<a href="<?php if($page<$pageCount){$p=$page+1;} else {$p=$page;} echo '?keywords='.$keywords.'&page='.$p.'&order='.$order?>">下一页</a>&nbsp;
			<a href="<?php echo '?keywords='.$keywords.'&page='.$pageCount.'&order='.$order?>">尾页</a>
			<form method="post" id="jumpto">
			<input type="text" name="jumpto" style="text-align: center;" size=2 value="<?php echo $page?>">/
			<?php echo $pageCount?>
			<button onclick="submitjump()">跳转</button>
			</form>
	</td></tr>

	<tr>
	  <td width=100><a href="?order=name">账号</a></td>
		<td width=150><a href="?order=mac">MAC地址</a></td>
		<td width=150><a href="?order=deviceid">设备ID</a></td>
		<td width=100><a href="?order=model">型号</a></td>
		<td width=130><a href="?order=ip">IP</a></td>
		<td width=150><a href="?order=region">地区</a></td>
		<td width=80><a href="?order=exp">状态</a></td>
		<td width=200><a href="?order=lasttime">最后登陆</a></td>
	</tr>

<form method="POST">
<?php
$recStart=$recCounts*($page-1);
$sql="select name,mac,deviceid,model,ip,region,lasttime,exp from chzb_users where status=-1 $searchparam order by $order desc limit $recStart,$recCounts";
$result=mysqli_query($GLOBALS['conn'],$sql);
while($row=mysqli_fetch_array($result)){
	$lasttime=date("Y-m-d H:i:s",$row['lasttime']);
	$name=$row['name'];
	$deviceid=$row['deviceid'];
	$mac=$row['mac'];
	$model=$row['model'];
	$ip=$row['ip'];
	$region=$row['region'];
	$days=ceil(($row['exp']-time())/86400);
	if($days>0){$days='剩'."$days".'天';}else{$days="已禁用";}
	echo "<tr><td><input type='checkbox' value='$name' name='id[]'>".$name."</td>
	<td>".$mac."</td>
	<td>".$deviceid."</td>
	<td>".$model."</td>
	<td>" .$ip ."</td>	
	<td>" .$region ."</td>
	<td>" .$days ."</td>
	<td>" . $lasttime ."</td>
	</tr>";
}
?>
<tr><td colspan="8">
	<input type="checkbox" onclick="quanxuan(this)">全选&nbsp;&nbsp;&nbsp;
	授权天数<input type="text" name="exp" value="365" size="3">
	<input type="submit" name="submitauthor" value="&nbsp;&nbsp;授&nbsp;&nbsp;权&nbsp;&nbsp;">&nbsp;&nbsp;
	<input type="submit" name="submitauthorforever" value="&nbsp;&nbsp;永久授权&nbsp;&nbsp;">
	&nbsp;&nbsp;&nbsp;&nbsp;
    <input type="submit" name="submitforbidden" value="&nbsp;&nbsp;禁止试用&nbsp;&nbsp;">
	&nbsp;&nbsp;&nbsp;&nbsp;
	<input type="submit" name="submitdel" value="删除记录" onclick="return confirm('确定删除选中用户吗？')">
	&nbsp;&nbsp;&nbsp;&nbsp;
	<input type="submit" name="submitdelonedaybefor" value="清空一天前记录" onclick="return confirm('确认清空一天前待授权信息？')"> 
		&nbsp;&nbsp;&nbsp;&nbsp;
	<input type="submit" name="submitdelall" value="清空所有记录" onclick="return confirm('确认删除所有待授权信息？')"> 
</td></tr>
</form>
</table>
</center>