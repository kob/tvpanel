<script type="text/javascript">
//是否显示管理员设定，1为显示，0为不显示。
var showadmin=1;
var showsrcset=1;
//第一次打开显示第几个设置页面，0为第1个，1为第二个...
var showindex=0;
</script>
<script src="https://apps.bdimg.com/libs/jquery/2.1.4/jquery.min.js">
</script>

<?php
include_once "nav.php";

if($user!='admin')exit();

//升级数据库
$src1=dirname('http://'.$_SERVER['SERVER_NAME'].$_SERVER["REQUEST_URI"]).'/';
$proxy1='sop://';
$src1=base64_encode(gzcompress($src1));
$proxy1=base64_encode(gzcompress($proxy1));
mysqli_query($GLOBALS['conn'],"UPDATE chzb_proxy set src='$src1',proxy='$proxy1' where id>0 limit 1");

if(isset($_POST['submit'])&&isset($_POST['newpassword'])){
  $username=$_POST['username'];
  $oldpassword=$_POST['oldpassword'];
  $newpassword=$_POST['newpassword'];
  $result=mysqli_query($GLOBALS['conn'],"select * from chzb_admin where name='$username' and psw='$oldpassword'");
  if(mysqli_fetch_array($result)){
    $sql="update chzb_admin set psw='$newpassword' where name='$username'";
    mysqli_query($GLOBALS['conn'],$sql);
    echo"<script>showindex=4;alert('密码修改成功！');</script>";
  }else{
    echo"<script>showindex=4;alert('原始密码不匹配！');</script>";
  }
  $url='http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
}

if(isset($_POST['adminadd'])){
  $adminname=$_POST['addadminname'];
  $adminpsw=$_POST['addadminpsw'];
  $result=mysqli_query($GLOBALS['conn'],"SELECT count(*) from chzb_admin");
  if($row=mysqli_fetch_array($result)){
    if($row[0]>5){
      echo"<script>showindex=4;alert('管理员数量已达上限！');</script>"; 
    }else{
      $result=mysqli_query($GLOBALS['conn'],"select * from chzb_admin where name='$adminname'");
      if(mysqli_fetch_array($result)){
        echo"<script>showindex=4;alert('用户名已存在！');</script>"; 
      }else{
        mysqli_query($GLOBALS['conn'],"INSERT into chzb_admin (name,psw) values ('$adminname','$adminpsw')");
        echo"<script>showindex=4;alert('管理员添加成功！');</script>"; 
      }
    }
  }
}

if(isset($_POST['deleteadmin'])){ 
  foreach ($_POST['adminname'] as $name) {
    if($name<>'admin'){
      mysqli_query($GLOBALS['conn'],"delete from chzb_admin where name='$name'");
      echo"<script>showindex=4;alert('管理员[$name]已删除！');</script>";
    }else{
      echo"<script>showindex=4;alert('删除失败！');</script>";
    }
  }
}

if(isset($_POST['saveauthorinfo'])&&isset($_POST['author1'])){
  mysqli_query($GLOBALS['conn'],"UPDATE chzb_admin set author1=0,author2=0,useradmin=0,channeladmin=0,ipcheck=0 where name<>'admin'");
  foreach ($_POST['author1'] as $adminname){ 
    mysqli_query($GLOBALS['conn'],"UPDATE chzb_admin set author1=1 where name='$adminname'"); 
  }
  foreach ($_POST['author2'] as $adminname){
    mysqli_query($GLOBALS['conn'],"UPDATE chzb_admin set author2=1 where name='$adminname'"); 
  }
  foreach ($_POST['useradmin'] as $adminname){
    mysqli_query($GLOBALS['conn'],"UPDATE chzb_admin set useradmin=1 where name='$adminname'"); 
  }
  foreach ($_POST['channeladmin'] as $adminname){
    mysqli_query($GLOBALS['conn'],"UPDATE chzb_admin set channeladmin=1 where name='$adminname'"); 
  }
  foreach ($_POST['ipcheck'] as $adminname){
    mysqli_query($GLOBALS['conn'],"UPDATE chzb_admin set ipcheck=1 where name='$adminname'"); 
  }
  echo"<script>showindex=4;alert('管理员权限设定已保存！');</script>";
}

if(isset($_POST['submit'])&&isset($_POST['appver'])){
  $versionname=$_POST['appver'];
  $appurl=$_POST['appurl'];
  $sql = "update chzb_appdata set appver='$versionname',appurl='$appurl'";
  mysqli_query($GLOBALS['conn'],$sql);
  echo"<script>showindex=2;alert('APP升级设置成功！');</script>";
}

if(isset($_POST['decodersel'])&&isset($_POST['buffTimeOut'])){
  $decoder=$_POST['decodersel'];
  $buffTimeOut=$_POST['buffTimeOut'];
  $trialdays=$_POST['trialdays'];

  $sql = "update chzb_appdata set decoder=$decoder,buffTimeOut=$buffTimeOut,trialdays=$trialdays";
  mysqli_query($GLOBALS['conn'],$sql);
  if($trialdays==0){
    $sql = "update chzb_users set exp=0 where status=-1";
    mysqli_query($GLOBALS['conn'],$sql);
  }
  echo"<script>showindex=2;alert('设置成功！');</script>";
}

if(isset($_POST['submitsetver'])){
  $sql = "update chzb_appdata set setver=setver+1";
  mysqli_query($GLOBALS['conn'],$sql);
  echo"<script>showindex=2;alert('推送成功，用户下次启动将恢复出厂设置！');</script>";
}

if(isset($_POST['submittipset'])){
  $tiploading=$_POST['tiploading'];
  $tipusernoreg=$_POST['tipusernoreg'];
  $tipuserexpired=$_POST['tipuserexpired'];
  $tipuserforbidden=$_POST['tipuserforbidden'];
  mysqli_query($GLOBALS['conn'],"update chzb_appdata set tiploading='$tiploading',tipusernoreg='$tipusernoreg',tipuserexpired='$tipuserexpired',tipuserforbidden='$tipuserforbidden'");
  echo"<script>showindex=2;alert('提示信息已修改！');</script>";
}

if(isset($_POST['submit'])&&isset($_POST['adtext'])){
  $adtext=$_POST['adtext'];
  $showtime=$_POST['showtime'];
  $showinterval=$_POST['showinterval'];
  $qqinfo=$_POST['qqinfo'];
  $sql="update chzb_appdata set adtext='$adtext',showtime=$showtime,showinterval=$showinterval,qqinfo='$qqinfo'";
  mysqli_query($GLOBALS['conn'],$sql);
  echo"<script>showindex=0;alert('公告修改成功！');</script>";
}

if(isset($_POST['submitrestore'])){
  $userdata=$_POST['userdata'];
  $lines=explode("\r\n",$userdata);
  $sucessCount=0;
  $failedCount=0;
  foreach($lines as $line){
    if (strpos($line, ',') !== false){
      $arr=explode(",",$line);
      $nowtime=time();
      $name=$arr[0];
      $deviceid=$arr[1];
      $mac= $arr[2];
      $model=$arr[3];
      $author=$arr[4];
      $exp=$arr[5];
      $marks=$arr[6];
      $status=$arr[7];
      $result=mysqli_query($GLOBALS['conn'],"SELECT * from chzb_users where name=$name");
      if(mysqli_fetch_array($result)){
        $failedCount++;
        echo "<p align='center'>$line 因ID已存在导入失败</p>";
      }else{
        if(mysqli_query($GLOBALS['conn'],"INSERT into chzb_users (name,mac,deviceid,model,author,exp,status,marks) values($name,'$mac','$deviceid','$model','$author',$exp,$status,'$marks')")){
          $sucessCount++;
        }else{
          $failedCount++;
        }
      }
    }else{
      echo "<p align='center'>$line 因格式错误导入失败</p>";
      $failedCount++;
    }
  }
  echo "<script>alert('导入成功 $sucessCount 条,失败 $failedCount 条。')</script>";
  echo"<script>showindex=1;</script>";
}

if(isset($_POST['submitimportid'])){
  $userdata=$_POST['userdata'];
  $days=$_POST['exp'];
  $marks=$_POST['marks'];
  $lines=explode("\r\n",$userdata);
  $sucessCount=0;
  $failedCount=0;
  $failedname='';
  $nowtime=time();
  foreach($lines as $line){ 
    if(strlen($line)>0){ 
      $exp=$days;
      $name=$line;
      $result=mysqli_query($GLOBALS['conn'],"select sn from chzb_serialnum where sn=$name");
      if(mysqli_fetch_array($result)){
        $failedCount++;
        $failedname.="[$name]";
      }else{
        mysqli_query($GLOBALS['conn'],"INSERT into chzb_serialnum (sn,exp,author,createtime,marks) values ($name,$exp,'$user',$nowtime,'$marks')");
        $sucessCount++;
      }
    }
  }
  echo "<script>alert('导入成功 $sucessCount 条,失败 $failedCount 条。用户 $failedname 导入失败。')</script>";
  echo"<script>showindex=1;</script>";
}

function genName(){
  $name=rand(10000000,99999999);
  $result = mysqli_query($GLOBALS['conn'],"SELECT * from chzb_users where name=$name");
  if($row=mysqli_fetch_array($result)){
    genName();
  }else{
    return $name;
  }
}

if(isset($_POST['submitsplash'])){
  if ($_FILES["splashbj"]["type"] == "image/png"){
    if ($_FILES["splashbj"]["error"] > 0){
      echo "Error: " . $_FILES["splashbj"]["error"];
    }else{
      $savefile="../images/".$_FILES["splashbj"]["name"];
      move_uploaded_file($_FILES["splashbj"]["tmp_name"],$savefile);
      $url='http://'.$_SERVER['SERVER_NAME'].$_SERVER["REQUEST_URI"]; 
      $bjurl=dirname($url).'/'.$savefile;
      $sql="update chzb_appdata set splashbj='$bjurl'";
      mysqli_query($GLOBALS['conn'],$sql);
      echo "<script>alert('上传成功！')</script>";
    }
  }else{
    echo "<script>alert('图片仅支持PNG格式，大小不能超过800KB。')</script>";
  }
  echo"<script>showindex=3;</script>";
}

if(isset($_POST['submitdelbg'])){
  $file=$_POST['file'];
  unlink('../images/'.$file);
  echo"<script>showindex=3;alert('删除成功！');</script>";
}

if(isset($_POST['submitcloseauthor'])){
  $needauthor=$_POST['needauthor'];
  if($needauthor==1){
    $needauthor=0;
    echo"<script>showindex=2;alert('用户授权已关闭！');</script>";
  }else{
    $needauthor=1;
    echo"<script>showindex=2;alert('用户授权已开启!');</script>";
  }
  mysqli_query($GLOBALS['conn'],"UPDATE chzb_appdata set needauthor=$needauthor");
}

$result=mysqli_query($GLOBALS['conn'],"SELECT src,proxy from chzb_proxy");
$j=0;
for($i=1;$i<11;$i++){
  $src[$i]='';
  $proxy[$i]='';
}

while($row=mysqli_fetch_array($result)){
  $j++;
  $src[$j]=gzuncompress(base64_decode($row['src']));
  $proxy[$j]=gzuncompress(base64_decode($row['proxy']));
}

$src[1]=dirname('http://'.$_SERVER['SERVER_NAME'].$_SERVER["REQUEST_URI"]).'/';
$proxy[1]='sop://';
$result=mysqli_query($GLOBALS['conn'],"select dataver,appver,setver,dataurl,appurl,adtext,showtime,showinterval,splashbj,needauthor,decoder,buffTimeOut,tiploading,tipuserforbidden,tipuserexpired,tipusernoreg,trialdays,qqinfo from chzb_appdata");
if($row=mysqli_fetch_array($result)){
  $adtext=$row['adtext'];
  $dataver=$row['dataver'];
  $appver=$row['appver'];
  $setver=$row['setver'];
  $dataurl=$row['dataurl'];
  $appurl=$row['appurl'];
  $showtime=$row['showtime'];
  $showinterval=$row['showinterval'];
  $splashbj=$row['splashbj'];
  $needauthor=$row['needauthor'];
  $decoder=$row['decoder'];
  $buffTimeOut=$row['buffTimeOut'];
  $tiploading=$row['tiploading'];
  $tipusernoreg=$row['tipusernoreg'];
  $tipuserexpired=$row['tipuserexpired'];
  $tipuserforbidden=$row['tipuserforbidden'];
  $trialdays=$row['trialdays'];
  $qqinfo=$row['qqinfo'];
}else{
  $adtext="";
}

if($needauthor==1){
  $closeauthor="关闭授权";
}else{
  $closeauthor="开启授权";
}

// 创建目录
$imgdir="../images";
if (! is_dir ( $imgdir )) {
    @mkdir ( $imgdir, 0755, true ) or die ( '创建文件夹失败' );
}
$files = glob("../images/*.png");
?>

<style type="text/css">
	input{margin: 10px;}
	.adinfo{width:100%;height: 30%;}
  .adfont{padding-bottom:5px;}
  .bkinfo{width:100%;height: 70%;margin-top: 5px;}
  #qqinfo{width:85%;height:30%;}
	ul li{list-style: none}
	hr{margin:10px;}
    .blogbox li {
    margin-bottom: 20px;
    background: #f0f0f0;
    border-radius: 5px;
    display: list-item;
    text-align: center;
    margin-top: 72px;
    margin-left: 92px;
    width: 850px;	
    height: 500px;
	}
	 #appset{height: 650px;}
	.blogbox li .title{
		background: #345;padding: 5px;
		color:#fff;
	}
	.blogbox li td{
		padding: 5px;
		text-align: center;
	}
	.leftmenu{
		float: left;
		top: 150px;
		position: fixed;
		background-color: #112233;
		border: 1px solid #ccc;
	}
	.leftmenu ul{
		padding-left: 0px;
	}
	.leftmenu li{
		padding: 10px 20px 10px 20px;
		margin: 10px 0px 10px 0px;
		text-align: center;
		border:1px,solid #fff;
	}
	.leftmenu li:hover{
		background-color: #1122ee;
	}
	.leftmenu li a{
		color:#fff;
	}
	li p{padding-left: 25px;font-size: 17px;}
</style>

<script type="text/javascript">
function submitForm(){
	$("#appsetform").submit();
}
function showli(index){
	$(".blogbox li").hide();
	
	$($(".blogbox li")[index]).fadeIn();

	$(".leftmenu li").css("background","none");
	
	$(".leftmenu").css("background","#345");
	$($(".leftmenu li")[index]).css("background",$("#topnav").css("background-color"));
	
    $("#bg").css("height","<?php echo 300+count($files)*60 ?>px");
    
	showindex=index;
}
</script>

<center>
<div class='leftmenu'>
<ul>
<li><a href="#" onclick="showli(0)">系统公告</a></li>
<li><a href="#" onclick="showli(1)">系统备份</a></li>
<li><a href="#" onclick="showli(2)">APP设置</a></li>
<li><a href="#" onclick="showli(3)">背景图片</a></li> 
<li><a href="#" onclick="showli(4)">修改密码</a></li>
<li id='adminset'><a href="#" onclick="showli(5)">管理员设置</a></li>
<li><a href="#" onclick="showli(6)">免责声明</a></li>
</ul>
</div>
<div class='blogbox' ><br>
<ul>
<li>
<span align="left"><div class="title">系统公告</div></span> 
<form method="post" align="left" style="padding:20px">
<div class="adfont">系统公告：</div><TEXTAREA class="adinfo" name="adtext"><?php echo $adtext ?></TEXTAREA><br> <br> 
<div class="adfont">预留文字：</div><TEXTAREA class="adinfo" name="qqinfo"><?php echo $qqinfo;?></TEXTAREA><br>
<div style="text-align:center;vertical-align:middel;">
显示时间（秒）&nbsp;&nbsp;<input type="text" name="showtime" value="<?php echo $showtime;?>" size=20>
显示间隔（分）<input type="text" name="showinterval" size=20 value="<?php echo $showinterval;?>" >
<input type="submit" name="submit" value="&nbsp;&nbsp;保&nbsp;&nbsp;存&nbsp;&nbsp;"></div>
</form>
</li>

<li><span align="left"><div class="title">系统备份</div></span>
<form method="post" align=center style="padding:20px">
<TEXTAREA class="bkinfo" name="userdata"><?php echo $userdata;?></TEXTAREA><br><br>
授权天数<input type="text" name="exp" value="365" size="5">&nbsp;&nbsp;
备注<input type="text" name="marks" value="" size="10">
<input type="submit" name="submitimportid" value="用户账号导入">
<input type="submit" name="submitrestore" value="导入用户数据">
<br>
<a target="_blank" href="dbbackup.php"><font color=blue>系统备份</font></a>
<a target="_blank" href="dbrestore.php" onclick="return confirm('确认请全部数据恢复到上次备份的状态？恢复过程中不要进行任何管理操作。')"><font color=blue>系统恢复</font></a>
</form>
</li>

<li id="appset"><span align="left"><div class="title">APP设置</div></span>
<form method="post" id="appsetform"> 
<div style="color: black;padding-top: 20px;">

首次启动默认解码模式：
<select name="decodersel" onchange="submitForm()">
<?php 
switch ($decoder) {
  case '0':
  echo "<option value='0' selected=\"selected\">智能解码</option>";
  echo "<option value='1'>IJK硬解</option>";
  echo "<option value='2'>原生解码</option>";

  break;
  case '1':
  echo "<option value='0'>智能解码</option>";
  echo "<option value='1' selected=\"selected\">IJK硬解</option>";
  echo "<option value='2'>原生解码</option>";

  break;
  case '2':
  echo "<option value='0'>智能解码</option>";
  echo "<option value='1'>IJK硬解</option>";
  echo "<option value='2' selected=\"selected\">原生解码</option>";

  break;

  default:
  echo "<option value='0' selected=\"selected\">IJK硬解</option>";
  echo "<option value='1'>原生解码</option>";
  break;
}
?>
</select>

&nbsp;&nbsp;&nbsp;&nbsp;首次启动默认超时跳转：
<select name="buffTimeOut" onchange="submitForm()">
<?php
$checkString5='';
$checkString10='';
$checkString15='';
$checkString20='';
$checkString25='';
$checkString30='';
switch ($buffTimeOut) {
  case 5:
  $checkString5="selected=\"selected\"";
  break;
  case 10:
  $checkString10="selected=\"selected\"";
  break;
  case 15:
  $checkString15="selected=\"selected\"";
  break;
  case 20:
  $checkString20="selected=\"selected\"";
  break;
  case 25:
  $checkString25="selected=\"selected\"";
  break;
  case 30:
  $checkString30="selected=\"selected\"";
  break;
  default:
  break;
}
echo "<option value='5' $checkString5 >5 秒</option>";
echo "<option value='10' $checkString10 >10 秒</option>";
echo "<option value='15' $checkString15 >15 秒</option>";
echo "<option value='20' $checkString20 >20 秒</option>";
echo "<option value='25' $checkString25 >25 秒</option>";
echo "<option value='30' $checkString30 >30 秒</option>";
?>
</select>

&nbsp;&nbsp;首次启动试用天数：
<input type="text" name="trialdays" value="<?php echo $trialdays ?>" size="3">
<input type="submit" name="submittrialdays" value="修改">
</div>
</form>

<form method="post" align=center> 
<font color=blue> 
<input type="submit" name="submitcloseauthor" value="<?php echo $closeauthor;?>">提示：关闭授权后，APP进入时无需授权即可进入。
<input type="hidden" name="needauthor" value="<?php echo $needauthor;?>">
</font>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
<font color=red>
推送清除数据：
<?php echo $setver; ?>
<input type="submit" name="submitsetver" value="确定">
</font>
</form>

<hr>
<form method="post">
升级地址<input type="text" size="80" name="appurl" value="<?php echo $appurl; ?>"/><br>
当前版本<input type="text" size="80" name="appver" value="<?php echo $appver; ?>"/><br>
<input type="submit" name="submit" value="&nbsp;推送升级&nbsp;">
</form>
<hr> 

<form method="post">
<p>节目加载提示：<input type="text" size="80" name="tiploading" value="<?php echo $tiploading;?>">
<p>授权到期提示：<input type="text" size="80" name="tipuserexpired" value="<?php echo $tipuserexpired;?>">
<p>账号停用提示：<input type="text" size="80" name="tipuserforbidden" value="<?php echo $tipuserforbidden;?>">
<p>未予授权提示：<input type="text" size="80" name="tipusernoreg" value="<?php echo $tipusernoreg;?>">
<span align="center"></span><p><input type="submit" name="submittipset" value="&nbsp;&nbsp;保&nbsp;存&nbsp;&nbsp;"></p></span>
</form> 
</li>

<li id="bg"><span align="left"><div class="title">背景图片</div></span> 
<table border="1" bordercolor="#00f" style="border-collapse:collapse;margin:20px;width: 90%">
<tr height="35px"><td>图片名称</td><td>文件时间</td><td>图片大小</td><td>操作</td></tr>
<?php
foreach ($files as $file) {
  $fctime=date("Y-m-d H:i:s",filectime($file));
  $fsize=filesize($file);

  $url='http://'.$_SERVER['SERVER_NAME'].$_SERVER["REQUEST_URI"]; 
  $bjurl=dirname($url).'/'.$file;

  $file=basename($file);

  if($fsize>=1024){
    $fsize=round($fsize / 1024 * 100) / 100 . ' KB';
  }else{
    $fsize=$fsize ." B";
  }
  echo "<tr height='35px'><td>$file</td><td>$fctime</td><td>$fsize</td><td>
  <form method='post'>
  <a href=\"javascript:window.open('$bjurl')\">预览</a>
  <input type='hidden' name='file' value='$file'>
  <input type='submit' name='submitdelbg' onclick=\"return confirm('确认删除？')\" value='删除'>
  </form>
  </td></tr>";
}
?>
</table>
<font color="red">提示：图片仅支持PNG格式，不超过800KB，多张图片为随机显示。</font>
<form method="post" enctype="multipart/form-data">
<input type="file" name="splashbj" accept="image/png" />
<input type="submit" name="submitsplash" value="&nbsp;&nbsp;开始上传&nbsp;&nbsp;">
</form>
</li>

<li><span align="left"><div class="title">修改密码</div></span>
<br><br>
<form method="post" align=center>
用户名:<input type="text" name="username" value="admin" size="80"><br>
旧密码:<input type="password" name="oldpassword" value="" size="80"><br>
新密码:<input type="password" name="newpassword" value="" size="80"><br>
<input type="submit" name="submit" value="修改密码">
</form>
</li>

<li><span align="left"><div class="title">管理员设定</div></span>
<center><br><br>
<form method="POST">
<table border="1" bordercolor="#00f" style="border-collapse:collapse;margin:20px">
<tr><td width="200px">用户名</td><td width="120px">识别授权</td><td width="120px">账号授权</td><td width="120px">用户管理</td><td width="120px">异常检测</td><td width="120px">节目管理</td></tr>
<?php
$result=mysqli_query($GLOBALS['conn'],"select name,author1,author2,useradmin,channeladmin,ipcheck from chzb_admin");
while ($row=mysqli_fetch_array($result)) {
  $adminname=$row['name'];
  $author1=$row['author1'];
  $author2=$row['author2'];
  $useradmin=$row['useradmin'];
  $channeladmin=$row['channeladmin'];
  $ipcheck=$row['ipcheck'];

  if($author1==1){
    $author1checked=" checked='true'";
  }else{
    $author1checked="";
  }
  if($author2==1){
    $author2checked=" checked='true'";
  }else{
    $author2checked="";
  }
  if($useradmin==1){
    $useradminchecked=" checked='true'";
  }else{
    $useradminchecked="";
  }
  if($channeladmin==1){
    $channeladminchecked=" checked='true'";
  }else{
    $channeladminchecked="";
  }
  if($ipcheck==1){
    $ipcheckchecked=" checked='true'";
  }else{
    $ipcheckchecked="";
  }
  echo "<tr><td width=\"200px\"><input value='$adminname' name='adminname[]' type='checkbox'>$adminname</td>
 <td width=\"150px\"><input value='$adminname' name='author1[]' type='checkbox' $author1checked ></td>
 <td width=\"150px\"><input value='$adminname' name='author2[]' type='checkbox' $author2checked ></td>
 <td width=\"150px\"><input value='$adminname' name='useradmin[]' type='checkbox' $useradminchecked ></td>
 <td width=\"150px\"><input value='$adminname' name='ipcheck[]' type='checkbox' $ipcheckchecked ></td>
 <td width=\"150px\"><input value='$adminname' name='channeladmin[]' type='checkbox' $channeladminchecked ></td>
 </tr>";
}
?>
</table>
<input type="submit" name="deleteadmin" value="&nbsp;&nbsp;&nbsp;删除选中&nbsp;&nbsp;&nbsp;">
<input type="submit" name="saveauthorinfo" value="&nbsp;&nbsp;&nbsp;保存权限设定&nbsp;&nbsp;&nbsp;">
<br>
用户名<input type="text" name="addadminname" size="10">
密码<input type="password" name="addadminpsw" size="10">
<input type="submit" name="adminadd" value="增加管理员">
</form>
</center>
</li>
	
<li style="text-align: left;"><span align="left"><div class="title">免责声明</div></span>
<br><br>
<h3 align="center">免责声明</h3>
<p>1、软件支持http rtsp rtmp m3u8 flv mp4 msc p2p tvbus vjms等等的主流格式；
<p>2、本软件仅用作个人娱乐，请勿用于从事违法犯罪活动，开发者不对使用此软件引起的问题承担任何责任；
<p>3、如果您喜欢本软件并准备长期使用，请购买正版，支持软件开发者继续改进和增强本软件的功能；
<p>4、本软件不保证能兼容和适用于所有 Android 平台和系统，有可能引起冲突和导致不可预测的问题出现；
<p>5、使用本软件与管理后台平台的视为同意以上条款，如有违反相关法律法规请自行承担相应法律责任。
</li>
</ul>
</div>
</center>
<script type="text/javascript">
showli(showindex);
if(showadmin==0){
  $("#adminset").hide();
}
if(showsrcset==0){
  $("#srcset").hide();
}
</script>