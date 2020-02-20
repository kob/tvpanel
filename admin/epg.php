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

if(isset($_GET['keywords'])){
	$keywords=$_GET['keywords'];
	$searchparam="where name like '%$keywords%' or beizhu like '%$keywords%' or content like '%$keywords%'";
}

if(isset($_POST['recCounts'])){
	$recCounts=$_POST['recCounts'];
	mysqli_query($GLOBALS['conn'],"update chzb_admin set showcounts=$recCounts where name='$user'");
}

if(isset($_GET['page'])){
	$page=$_GET['page'];
}else{
	$page=1;
}

$result=mysqli_query($GLOBALS['conn'],"select showcounts from chzb_admin where name='$user'");
if($row=mysqli_fetch_array($result)){
	$recCounts=$row['showcounts'];
}else{
	$recCounts=100;
}

$result=mysqli_query($GLOBALS['conn'],"select count(*) from chzb_epg");
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
		header("location:?page=$p");
	}

}

if(isset($_POST['qkbd'])){
	$sql = "update chzb_epg set content=null";
	if(isset($_POST['id'])){
		$sql .= " where id=".$_POST['id'];
	}
	mysqli_query($GLOBALS['conn'],$sql);
	mysqli_close($GLOBALS['conn']);
	exit("<script>javascript:alert('清除绑定频道成功!');self.location=document.referrer;</script>");

}
if(isset($_POST['bdpd'])){
	$sql="SELECT distinct name FROM chzb_channels";
	$result=mysqli_query($GLOBALS['conn'],$sql);
	if (!mysqli_num_rows($result)) {
		mysqli_free_result($result);
	    exit("<script>javascript:alert('对不起，暂时没有节目信息，无法匹配!');self.location=document.referrer;</script>");
	}
	while ($r=mysqli_fetch_array($result,MYSQLI_ASSOC)) {
		$clist[] = $r;
	}
	unset($r);
	mysqli_free_result($result);
	
	if(isset($_POST['id'])){
		if(empty($_POST["beizhu"])){exit("<script>javascript:alert('对不起，备注信息不完，无法匹配!');self.location=document.referrer;</script>");}
		foreach ($clist as $k => $v) {
			if (strstr($v['name'],$_POST['beizhu']) !==false) {
				$list[$k] = $v['name'];
			}
		}
		$a = implode(",",array_unique($list));
		if(empty($a)){exit("<script>javascript:alert('对不起，没有索引到频道列表!');self.location=document.referrer;</script>");}
		mysqli_query($GLOBALS['conn'],"update chzb_epg set content='$a' where id=".$_POST['id']);
		mysqli_close($GLOBALS['conn']);
	    exit("<script>javascript:alert('EPG信息匹配完成!');self.location=document.referrer;</script>");
	}
	
	$sql = "select id,beizhu,content from chzb_epg where beizhu != ''";
	$result=mysqli_query($GLOBALS['conn'],$sql);
	if (!mysqli_num_rows($result)) {
		mysqli_free_result($result);
	    exit("<script>javascript:alert('对不起，暂时没有EPG信息，无法匹配!');self.location=document.referrer;</script>");
	}
	while($r=mysqli_fetch_array($result,MYSQLI_ASSOC)) {
		foreach ($clist as $k => $v) {
			if (strstr($v['name'],$r['beizhu']) !==false) {
			    $list[$k] = $v['name'];
			    $a = implode(",",array_unique($list));
		        mysqli_query($GLOBALS['conn'],"update chzb_epg set content='$a' where id=".$r['id']);
			}
		}
		unset($list);
	}
	unset($r);
	mysqli_free_result($result);
	mysqli_close($GLOBALS['conn']);
	exit("<script>javascript:alert('EPG信息匹配完成!');self.location=document.referrer;</script>");
}

//上线操作
if ($_GET["act"]=="online") {
    $id=!empty($_GET["id"])?$_GET["id"]:exit("<script>javascript:alert('参数不能为空!');history.go(-1);</script>");
	mysqli_query($GLOBALS['conn'],"update chzb_epg set status=1 where id=".$id);
	exit("<script>javascript:alert('EPG编号 ".$id." 上线操作成功!');self.location=document.referrer;</script>");
}
//下线操作
if ($_GET["act"]=="downline") {
    $id=!empty($_GET["id"])?$_GET["id"]:exit("<script>javascript:alert('参数不能为空!');history.go(-1);</script>");
	mysqli_query($GLOBALS['conn'],"update chzb_epg set status=0 where id=".$id);
	exit("<script>javascript:alert('EPG编号 ".$id." 下线操作成功!');self.location=document.referrer;</script>");
}
//删除操作
if ($_GET["act"]=="dels") {
    $id=!empty($_GET["id"])?$_GET["id"]:exit("<script>javascript:alert('参数不能为空!');history.go(-1);</script>");
	mysqli_query($GLOBALS['conn'],"delete from chzb_epg  where id=".$id);
	exit("<script>javascript:alert('EPG编号 ".$id." 删除操作成功!');self.location=document.referrer;</script>");
}
//新增套餐
if ($_GET["act"]=="add") {
    $epg=!empty($_POST["epg"])?$_POST["epg"]:exit("<script>javascript:alert('请选择EPG来源!');history.go(-1);</script>");
	$name=!empty($_POST["name"])?$_POST["name"]:exit("<script>javascript:alert('请填写EPG名称!');history.go(-1);</script>");
	$beizhu = $_POST["beizhu"];
	$epg_name = $epg.'-'.$name;
	$result=mysqli_query($GLOBALS['conn'],"select * from chzb_epg where name='".$epg_name."'");
	//套餐是否已经同名或存在
	if (mysqli_num_rows($result)) {
		mysqli_free_result($result);
	    exit("<script>javascript:alert('EPG名为 ".$epg_name." 已存在，请不要重复新增!');self.location=document.referrer;</script>");
	}
	//新加套餐
	mysqli_query($GLOBALS['conn'],"insert into chzb_epg (name,beizhu) values ('".$epg_name."','".$beizhu."')");
	exit("<script>javascript:alert('新增加的EPG为 ".$epg_name." 新增加成功!');self.location=document.referrer;</script>");
}
//修改套餐
if ($_GET["act"]=="edits") {
    $id=!empty($_POST["id"])?$_POST["id"]:exit("<script>javascript:alert('参数不能为空!');history.go(-1);</script>");
	$epg=!empty($_POST["epg"])?$_POST["epg"]:exit("<script>javascript:alert('请选择EPG来源!');history.go(-1);</script>");
	$name=!empty($_POST["name"])?$_POST["name"]:exit("<script>javascript:alert('请填写EPG名称!');history.go(-1);</script>");
	$epg_name = $epg.'-'.$name;
	$ids = implode(",",array_unique($_POST['ids']));
	$beizhu = $_POST["beizhu"];
	mysqli_query($GLOBALS['conn'],"update chzb_epg set name='".$epg_name."',content='".$ids."',beizhu='".$beizhu."' where id=".$id);
	exit("<script>javascript:alert('EPG名为 ".$epg_name." 修改成功!');self.location='epg.php';</script>");
}
mysqli_free_result($result);
?>

<?php
//套餐修改区域
if ($_GET["act"]=="edit") { 
	$id=!empty($_GET["id"])?$_GET["id"]:exit("<script>javascript:alert('参数不能为空!');history.go(-1);</script>");
	//检查套餐是否存在
	$result=mysqli_query($GLOBALS['conn'],"select name,content,beizhu from chzb_epg where id=".$id);
	if (!mysqli_num_rows($result)) {
	    mysqli_free_result($result);
	    exit("<script>javascript:alert('套餐不存在!');self.location='epg.php';</script>");
	}
	$r=mysqli_fetch_array($result,MYSQLI_ASSOC);
	$ca=$r["content"];   //套餐内容
	$bz=$r["beizhu"];
	if(strstr($r["name"],"cntv") != false){
		$na = substr($r["name"], 5);
		$epgname = '<option value="cntv" selected>CCTV官网</option><option value="tvsou">搜视网</option><option value="51zmt">51zmt</option>';
	}else if(strstr($r["name"],"tvsou") != false){
		$na = substr($r["name"], 6);
		$epgname = '<option value="cntv">CCTV官网</option><option value="tvsou"  selected>搜视网</option><option value="51zmt">51zmt</option>';
	}else if(strstr($r["name"],"51zmt") != false){
		$na = substr($r["name"], 6);
		$epgname = '<option value="cntv">CCTV官网</option><option value="tvsou">搜视网</option><option value="51zmt"  selected>51zmt</option>';
	}
	unset($r);
	mysqli_free_result($result);
	//获取套餐所有的收视内容
	$sql="SELECT distinct name FROM chzb_channels order by category,id";
	$result=mysqli_query($GLOBALS['conn'],$sql);
	if (!mysqli_num_rows($result)) {
		mysqli_free_result($result);
	    exit("<script>javascript:alert('对不起，暂时没有节目信息，无法生成!');self.location=document.referrer;</script>");
	}
?>
<script type="text/javascript">
function quanxuan(a){
	var ck=document.getElementsByName("ids[]");
	for (var i = 0; i < ck.length; i++) {
		var tr=ck[i].parentNode.parentNode;
		if(a.checked){
			ck[i].checked=true;
		}else{
			ck[i].checked=false;
		}
	}
	
}
</script>
<div>
<tr>
<table width="900" align="center" border="1" bordercolor="#a0c6e5" style="border-collapse:collapse;">
<form method="post" action="?act=edits">
<tr>
<td  align="center">
EPG来源:<select id="epg" name="epg" > <option value="">请选EPG来源</option><?=$epgname?></select>&nbsp;&nbsp;
EPG名称:<input type="text" name="name" value="<?php echo $na;?>">&nbsp;&nbsp;备注:<input type="text" name="beizhu" value="<?php echo $bz;?>"><input type="hidden" name="id" value="<?php echo $id;?>">&nbsp;&nbsp;<input type="submit" name="bdpd" value="搜索绑定频道" onclick="return confirm('自动绑定频道列表后,如果不准确请手动修改!!!')">&nbsp;&nbsp;<input type="submit" name="qkbd" value="清空绑定" onclick="return confirm('确定要清空绑定的频道列表吗？')">
</tr></td><tr><td>
<p align="left"><input type="checkbox" onclick="quanxuan(this)">全选/反选</p>
<?php
while ($r=mysqli_fetch_array($result,MYSQLI_ASSOC)) {
    $nan=$r["name"];
	//if (strpos($ca,$nan) !==false) {
	if(in_array($nan,explode(',', $ca))){
	    echo "<div style=' float: left;background: #7fff00; margin-right: 3px; margin-bottom: 3px; padding: 2px 5px;'><input type='checkbox' value='".$nan."' name='ids[]'  checked=\"checked\">".$nan."</div>";
	}else {
	    echo "&nbsp;&nbsp;<div style=' float: left;background: #E7E7E7; margin-right: 3px; margin-bottom: 3px; padding: 2px 5px;'><input type='checkbox' value='".$r["name"]."' name='ids[]' >".$r["name"]."</div>";
	}
	unset($nan);
}
unset($r);
mysqli_free_result($result);
mysqli_close($GLOBALS['conn']);
?>

</td></tr><tr align="center"><td>
<input type="submit" value="确认修改">
</td>
</tr>
</form>
</table>
</tr>
</div>
</html>
<?php exit;}?>

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
    <tr>
	    <td colspan=11><b>EPG列表</b>&nbsp;&nbsp;
		    <form method="GET">
				<input type="text" style="width:100px;" name="keywords" value="<?php echo $keywords;?>">
				<input type="submit" name="submitsearch" value="搜索">
			</form>
			<form method="POST">
			    <input type="submit" name="bdpd" value="绑定频道" onclick="return confirm('自动绑定频道列表后,如果不准确请手动修改!!!')">
				<input type="submit" name="qkbd" value="清空绑定" onclick="return confirm('确定要清空绑定的频道列表吗？')">
			</form>
		    <form method="post" action="?act=add">
		        来源:<select id="epg" name="epg"> <option value="">请选来源</option><option value="cntv">CCTV官网</option><option value="tvsou">搜视网</option><option value="51zmt">51zmt</option></select>&nbsp;
                名称:<input type="text" style="width:100px;" name="name">&nbsp; 备注:<input type="text" style="width:100px;" name="beizhu">&nbsp;<input type="submit" value="新增">&nbsp;&nbsp; <input type="reset" value="重置">
            </form>
            &nbsp;&nbsp;
		    每页
			<form method="POST" id="recCounts">
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

			<a href="<?php echo '?keywords='.$keywords.'&page=1'?>">首页</a>&nbsp;
			<a href="<?php if($page>1){$p=$page-1;}else{$p=1;} echo '?keywords='.$keywords.'&page='.$p?>">上一页</a>&nbsp;
			<a href="<?php if($page<$pageCount){$p=$page+1;} else {$p=$page;} echo '?keywords='.$keywords.'&page='.$p?>">下一页</a>&nbsp;
			<a href="<?php echo '?keywords='.$keywords.'&page='.$pageCount?>">尾页</a>
			<form method="post" id="jumpto">
			<input type="text" name="jumpto" style="text-align: center;" size=2 value="<?php echo $page?>">/
			<?php echo $pageCount?>
			<button onclick="submitjump()">跳转</button>
			</form>
		</td>
	</tr>
	<tr>
	    <td width="60" align="center" style="font-size:14px;height:35px;font-weight: bold;">epg-id</td>
        <td width="300" align="center" style="font-size:14px;font-weight: bold;">epg-name</td>
		<td width="140" align="center" style="font-size:14px;font-weight: bold;">备注</td>
		<td width="80" align="center" style="font-size:14px;font-weight: bold;">来源</td>
        <td width="80" align="center" style="font-size:14px;font-weight: bold;">状态</td>
        <td width="630" align="center" style="font-size:14px;font-weight: bold;">绑定频道</td>
        <td width="150" align="center" style="font-size:14px;font-weight: bold;">操作</td>
	</tr>
<?php
//获取套餐数据显示
$recStart=$recCounts*($page-1);
//print_r("select * from chzb_epg $searchparam limit $recStart,$recCounts");exit;
$result=mysqli_query($GLOBALS['conn'],"select * from chzb_epg $searchparam limit $recStart,$recCounts");
if (!mysqli_num_rows($result)) {
    echo"<tr>";
	echo"<td colspan=\"7\" align=\"center\" style=\"font-size:12px;color:red;height:35px;font-weight: bold;\">当前未有EPG数据！";
	echo"</td>";
	echo"</tr>";
	echo"</table></tr></div></html>";
	mysqli_free_result($result);
	mysqli_close($GLOBALS['conn']);
}
while ($r=mysqli_fetch_array($result,MYSQLI_ASSOC)) {
	if ($r["status"]) {
	    $stu="<font color=\"#33a996\">上线</font>";
		$stus="<a href=\"?act=downline&id=".$r["id"]."\"><font color=\"red\">下线</font></a>";
	}else {
	    $stu="<font color=\"red\">下线</font>";
		$stus="<a href=\"?act=online&id=".$r["id"]."\"><font color=\"#33a996\">上线</font></a>";
	}
	$epg = explode("-",$r['name']);
	if($epg[0] == 'cntv'){
			$epgname = 'CCTV官网';
	}else if($epg[0] == 'tvsou'){
			$epgname = '搜视网';
	}else if($epg[0] == '51zmt'){
			$epgname = '51zmt';
	}
	 echo"<tr>";
	 echo"<td width=\"60\" align=\"center\" style=\"font-size:12px;height:35px;font-weight: bold;\">".$r["id"]."</td>";
	 echo"<td width=\"300\" align=\"center\" style=\"font-size:12px;font-weight: bold;\">".$r["name"]."</td>";
	 echo"<td width=\"140\" align=\"center\" style=\"font-size:12px;font-weight: bold;\"><font color=\"red\">".$r["beizhu"]."</font></td>";
	 echo"<td width=\"80\" align=\"center\" style=\"font-size:12px;font-weight: bold;\"><font color=\"red\">".$epgname."</font></td>";
	 echo"<td width=\"80\" align=\"center\" style=\"font-size:12px;font-weight: bold;\">".$stu."</td>";
	 echo"<td width=\"630\" align=\"left\" style=\"font-size:12px;font-weight: bold;\">".$r["content"]."</td>";
	 echo"<td width=\"150\" align=\"center\" style=\"font-size:12px;font-weight: bold;\">
	 ".$stus."&nbsp;
	 <a href=\"?act=edit&id=".$r["id"]."\">编辑</a>&nbsp;
	 <a href=\"?act=dels&id=".$r["id"]."\"><font color=\"#8E388E\">删除</font></a>
	 </td>";
	 echo"</tr>";
}
unset($r);
mysqli_free_result($result);
mysqli_close($GLOBALS['conn']);
?>
</table>
</tr>
</center>

</html>