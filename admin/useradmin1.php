<style type="text/css">
 form{margin:0px;display: inline}
 td{padding: 8px;}
</style>

<?php
ini_set('display_errors',1);            
ini_set('display_startup_errors',1);   
error_reporting(E_ERROR);

include_once "nav.php";

if($_SESSION['author2']==0)exit();

function genName(){
	$name=rand(10000000,99999999);
	$result = mysqli_query($GLOBALS['conn'],"SELECT * from chzb_users where name=$name");
	if($row=mysqli_fetch_array($result)){
		genName();
	}else{
		$result = mysqli_query($GLOBALS['conn'],"SELECT * from chzb_serialnum where sn=$name");
		if($row=mysqli_fetch_array($result)){
			genName();
		}else{
			return $name;
		}
	}
}
//生成SN
if(isset($_POST['submitsn2'])){
	$sn=$_POST['snNumber'];
	$exp=$_POST['exp'];
	$marks=$_POST['marks'];
	$snall='';
	$nowtime=time();
	$result = mysqli_query($GLOBALS['conn'],"SELECT * from chzb_users where name=$sn");
	if($row=mysqli_fetch_array($result)){
		echo "<font color=red>该账号已经存在！</font>";
	}else{
		$result = mysqli_query($GLOBALS['conn'],"SELECT * from chzb_serialnum where sn=$sn");
		if($row=mysqli_fetch_array($result)){
			echo "<font color=red>该账号已经存在！</font>";
		}else{
			$sql="INSERT into chzb_serialnum (sn,exp,author,createtime,marks) values($sn,$exp,'$user',$nowtime,'$marks')";
			mysqli_query($GLOBALS['conn'],$sql);
			echo "<font color=red>账号$sn 已生成！</font>";
		}
	}
}

//批量生成SN
if(isset($_POST['submitsn'])){
	$snCount=$_POST['snCount'];
	$exp=$_POST['exp'];
	$marks=$_POST['marks'];
	$snall='';
	for ($i=0; $i <$snCount ; $i++) { 
		$sn=genName();
		$nowtime=time();
		$snall=$snall.$sn;
		$sql="INSERT into chzb_serialnum (sn,exp,author,createtime,marks) values($sn,$exp,'$user',$nowtime,'$marks')";
		mysqli_query($GLOBALS['conn'],$sql);
	}
	echo "<font color=red>账号已生成，此次共计生成$snCount 个账号 ！</font>";
}

if(isset($_POST['submitdel'])){
	foreach ($_POST['sn'] as $sn){
		mysqli_query($GLOBALS['conn'],"delete from chzb_serialnum where sn=$sn");		
	}
	echo "选中授权号已删除！";
}

if(isset($_POST['submitclear'])){
		mysqli_query($GLOBALS['conn'],"delete from chzb_serialnum where enable=1");		
	echo "已清空激活授权号！";
}

if(isset($_POST['submitadddays'])){
	$days=$_POST['exp'];
	foreach ($_POST['sn'] as $sn){
		mysqli_query($GLOBALS['conn'],"UPDATE chzb_serialnum set exp=$days where sn=$sn and enable=0");		
	}		
	echo "选中用户天数已修改！";
}

if(isset($_POST['submitmodifymarks'])){
	$marks=$_POST['marks'];
	foreach ($_POST['sn'] as $sn){
		mysqli_query($GLOBALS['conn'],"UPDATE chzb_serialnum set marks='$marks' where sn=$sn and enable=0");		
	}
	echo "选中用户备注已修改！";
}

if(isset($_GET['submitsearch'])||isset($_GET['keyword'])){
	$keywords=$_GET['keyword'];
	$searchparam="and sn like '%$keywords%' or regtime like '%$keywords%' or regid like '%$keywords%' or exp like '%$keywords%' or author like '%$keywords%' or createtime like '%$keywords%' or marks like '%$keywords%'";
}

//处理显示数量
if(isset($_POST['recCounts'])){
	$recCounts=$_POST['recCounts'];
	mysqli_query($GLOBALS['conn'],"update chzb_admin set showcounts=$recCounts where name='$user'");
}

//获取显示数量
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

if(isset($_GET['page'])){
	$page=$_GET['page'];
}else{
	$page=1;
}

if(isset($_GET['order'])){
	$order=$_GET['order'];
}else{
	$order='id';
}

$result=mysqli_query($GLOBALS['conn'],"select count(*) from chzb_serialnum");
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
	var ck=document.getElementsByName("sn[]");
	for (var i = 0; i < ck.length; i++) {
		if(a.checked){
			ck[i].checked=true;
		}else{
			ck[i].checked=false;
		}
	}
}
function copytoclip(){
	var ck=document.getElementsByName("sn[]");
	var clipBoardContent="";
	for (var i = 0; i < ck.length; i++) {
		if(ck[i].checked){
			clipBoardContent+=ck[i].value+"\r\n";
		}
	}
    var oInput = document.createElement('textarea');
    oInput.value = clipBoardContent;
    document.body.appendChild(oInput);
    oInput.select(); // 选择对象
    document.execCommand("Copy"); // 执行浏览器复制命令
    oInput.className = 'oInput';
    document.body.removeChild(oInput);
	alert("复制成功，请粘贴到记事本");
}
</script>

<center>
<div style="display: none;" id="gensn">
<form method="post">
	生产数量<input type="text" name="snCount" value="10" size="20" />&nbsp;&nbsp;
	授权天数<input type="text" name="exp" value="365" size="5" />&nbsp;&nbsp;
	备注<input type="text" name="marks" value="" size="15" />&nbsp;&nbsp;
	<input type="submit" name="submitsn" value="确定" onclick="{document.getElementById('gensn').style.display='none';}" >
</form><br><br></div>

<div style="display: none;" id="gensn2">
<form method="post">
	输入账号<input type="text" name="snNumber" value="" size="20" />&nbsp;&nbsp;
	授权天数<input type="text" name="exp" value="365" size="5" />&nbsp;&nbsp;
	备注<input type="text" name="marks" value="" size="15" />&nbsp;&nbsp;
	<input type="submit" name="submitsn2" value="确定" onclick="{document.getElementById('gensn').style.display='none';}" >
</form><br><br></div>

<table border="1" bordercolor="#a0c6e5" style="border-collapse:collapse;">
	<tr><td colspan=7><b>账号列表</b>
			&nbsp;&nbsp;&nbsp;授权总数：<?php echo $userCount; ?>
			&nbsp;今日新增：<?php echo $todayuserCount; ?>
			&nbsp;今日授权：<?php echo $todayauthoruserCount; ?>&nbsp;&nbsp;&nbsp;
			<form method="GET">
			<input type="text" name="keyword" value="<?php echo $keywords;?>">
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
			</select>条
			</form>
			<a href="<?php echo '?keyword='.$keywords.'&page=1&order='.$order?>">首页</a>&nbsp;
			<a href="<?php if($page>1){$p=$page-1;}else{$p=1;} echo '?keyword='.$keywords.'&page='.$p.'&order='.$order?>">上一页</a>&nbsp;
			<a href="<?php if($page<$pageCount){$p=$page+1;} else {$p=$page;} echo '?keyword='.$keywords.'&page='.$p.'&order='.$order?>">下一页</a>&nbsp;
			<a href="<?php echo '?keyword='.$keywords.'&page='.$pageCount.'&order='.$order?>">尾页</a>
			<form method="post" id="jumpto">
			<input type="text" name="jumpto" style="text-align: center;" size=2 value="<?php echo $page?>">/
			<?php echo $pageCount?>
			<button onclick="submitjump()">跳转</button>
			</form>
			&nbsp;&nbsp;			
			<input type="button" name="button" value="生成账号" onclick="document.getElementById('gensn2').style.display='block'" />
			&nbsp;&nbsp;
			<input type="button" name="button" value="批量生成" onclick="document.getElementById('gensn').style.display='block'" />
		</td></tr>

	<tr>
	  <td width=150><a href="?order=sn">账号<?php if($order=='sn') echo'↓'?></a></td>
		<td width=250><a href="?order=regtime">激活时间<?php if($order=='regtime') echo'↓'?></a></td>
		<td width=200><a href="?order=exp">授权天数<?php if($order=='exp') echo'↓'?></a></td>
		<td width=100><a href="?order=author">管理员<?php if($order=='author') echo'↓'?></a></td>
		<td width=50><a href="?order=enable">已激活<?php if($order=='enable') echo'↓'?></a></td>
		<td width=200><a href="?order=createtime">生成时间<?php if($order=='createtime') echo'↓'?></a></td>
		<td width=200><a href="?order=marks">备注<?php if($order=='marks') echo'↓'?></a></td>
	</tr>

<form method="POST">
<?php
$recStart=$recCounts*($page-1);
if($user=='admin'){
	$sql="select sn,regtime,exp,author,createtime,enable,marks from chzb_serialnum where 1=1 $searchparam order by $order desc limit $recStart,$recCounts";
}else{
	$sql="select sn,regtime,exp,author,createtime,enable,marks from chzb_serialnum where author='$user' $searchparam order by $order desc limit $recStart,$recCounts";
}

$result=mysqli_query($GLOBALS['conn'],$sql);
while($row=mysqli_fetch_array($result)){
	$days=$row['exp'];
	$sn=$row['sn'];
	$regtime=$row['regtime']==0?'':date("Y-m-d H:i:s",$row['regtime']);
	$author=$row['author'];
	$createtime=date("Y-m-d H:i:s",$row['createtime']);
	$marks=$row['marks'];
	$isactived=$row['enable']==0?'否':'是';
	if($days==999)$days="永不到期";
	echo "<tr><td><input type='checkbox' value='$sn' name='sn[]'><font color='black'>".$sn."</font></td><td>".$regtime."</td><td>".$days. "</td><td>".$author. "</td><td>" .$isactived."</td><td>$createtime</td><td>$marks</td></tr>";
}
?>

<tr><td colspan="7">
	<input type="checkbox" onclick="quanxuan(this)">全选
	&nbsp;&nbsp;
	<input type="button" onclick="copytoclip()" value="复制账号">
	&nbsp;&nbsp;
	<input type="submit" name="submitdel" value="删除选中" onclick="return confirm('确定删除选中用户吗？')">
	&nbsp;&nbsp;
	<input type="text" name="exp" value="365" size="5">
	<input type="submit" name="submitadddays" value="修改天数">
	&nbsp;&nbsp;
	<input type="text" name="marks" value="">
	<input type="submit" name="submitmodifymarks" value="修改备注">
	&nbsp;&nbsp;
	<input type="submit" name="submitclear" value="清空已激活" onclick="return confirm('确定清空已激活用户吗？')">
</td></tr>
</form>
</table>
</center>