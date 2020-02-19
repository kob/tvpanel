<script>
  var leftbgColor='#112233';
  var showindex=0;
  var maxindex=0;
</script>
<?php

include_once "nav.php";

mysqli_query($GLOBALS['conn'],"alter table chzb_appdata add column autoupdate int DEFAULT 1;");
mysqli_query($GLOBALS['conn'],"alter table chzb_appdata add column updateinterval int DEFAULT 15;");
mysqli_query($GLOBALS['conn'],"alter table chzb_category add column psw varchar(16) DEFAULT '';");

if($_SESSION['channeladmin']==0){
	exit();
}

function echoJSON($category){
	$sql = "SELECT name,url FROM chzb_channels where category='$category' order by id";
	$result = mysqli_query($GLOBALS['conn'],$sql);

	while($row = mysqli_fetch_array($result)) {
		if(!in_array($row['name'],$nameArray)){
			$nameArray[]=$row['name'];
		}		
		$sourceArray[$row['name']][]=$row['url'];
	}
	mysqli_free_result($result);
	$objCategory=(Object)null;
	$objChannel=(Object)null;

	for($i=0;$i<count($nameArray);$i++) {
		$objChannel=(Object)null;
		$objChannel->name=$nameArray[$i];
		$objChannel->source=$sourceArray[$nameArray[$i]];
		$channelArray[]=$objChannel;
	}
	$objCategory->$category=$channelArray;
	return $objCategory;
}

?>

<html>
<head>
<meta charset="UTF-8"> <!-- for HTML5 -->
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<script src="https://apps.bdimg.com/libs/jquery/2.1.4/jquery.min.js"></script>
<script>
  (function($){$.session={_id:null,_cookieCache:undefined,_init:function()
  {if(!window.name){window.name=Math.random();}
  this._id=window.name;this._initCache();var matches=(new RegExp(this._generatePrefix()+"=([^;]+);")).exec(document.cookie);if(matches&&document.location.protocol!==matches[1]){this._clearSession();for(var key in this._cookieCache){try{window.sessionStorage.setItem(key,this._cookieCache[key]);}catch(e){};}}
  document.cookie=this._generatePrefix()+"="+ document.location.protocol+';path=/;expires='+(new Date((new Date).getTime()+ 120000)).toUTCString();},_generatePrefix:function()
  {return'__session:'+ this._id+':';},_initCache:function()
  {var cookies=document.cookie.split(';');this._cookieCache={};for(var i in cookies){var kv=cookies[i].split('=');if((new RegExp(this._generatePrefix()+'.+')).test(kv[0])&&kv[1]){this._cookieCache[kv[0].split(':',3)[2]]=kv[1];}}},_setFallback:function(key,value,onceOnly)
  {var cookie=this._generatePrefix()+ key+"="+ value+"; path=/";if(onceOnly){cookie+="; expires="+(new Date(Date.now()+ 120000)).toUTCString();}
  document.cookie=cookie;this._cookieCache[key]=value;return this;},_getFallback:function(key)
  {if(!this._cookieCache){this._initCache();}
  return this._cookieCache[key];},_clearFallback:function()
  {for(var i in this._cookieCache){document.cookie=this._generatePrefix()+ i+'=; path=/; expires=Thu, 01 Jan 1970 00:00:01 GMT;';}
  this._cookieCache={};},_deleteFallback:function(key)
  {document.cookie=this._generatePrefix()+ key+'=; path=/; expires=Thu, 01 Jan 1970 00:00:01 GMT;';delete this._cookieCache[key];},get:function(key)
  {return window.sessionStorage.getItem(key)||this._getFallback(key);},set:function(key,value,onceOnly)
  {try{window.sessionStorage.setItem(key,value);}catch(e){}
  this._setFallback(key,value,onceOnly||false);return this;},'delete':function(key){return this.remove(key);},remove:function(key)
  {try{window.sessionStorage.removeItem(key);}catch(e){};this._deleteFallback(key);return this;},_clearSession:function()
  {try{window.sessionStorage.clear();}catch(e){for(var i in window.sessionStorage){window.sessionStorage.removeItem(i);}}},clear:function()
  {this._clearSession();this._clearFallback();return this;}};$.session._init();})(jQuery);
</script>

<style type="text/css">
a{
text-decoration: none;
font-size:16px;
color:#0000ff;
}
#pdlist{padding-left: 0px;padding-top: 5px;}
ul li{list-style: none}
textarea{
	font-size:16px;
	font-family:Fixedsys;
	line-height: 1.5;
	width:100%;
	height: 76%;
	white-space:nowrap; 
	overflow:scroll;

}
input{
	margin:5px;
}
img{
	vertical-align: middle;
	padding-left: 5px;
}
pre{
white-space:pre-wrap;
white-space:-moz-pre-wrap;
white-space:-pre-wrap;
white-space:-o-pre-wrap;
word-wrap:break-word;
}
</style>

</head>
<body>
<?php
ini_set('display_errors',1);            
ini_set('display_startup_errors',1);   
error_reporting(E_ERROR);

//对分类进行重新排序
$numCount=1;
$categoryname="chzb_category";
$result=mysqli_query($GLOBALS['conn'],"SELECT * from $categoryname order by id");
while ($row=mysqli_fetch_array($result)) {
	$name=$row['name'];
	mysqli_query($GLOBALS['conn'],"UPDATE $categoryname set id=$numCount where name='$name'");
	$numCount++;
}
//排序结束

if(isset($_GET['pd'])){
	$pd=$_GET['pd'];
}else{
	$result=mysqli_query($GLOBALS['conn'],"SELECT name from $categoryname order by id");
	if($row=mysqli_fetch_array($result)){
		$pd=$row['name'];
	}else{
		$pd='';
	}
}

	mysqli_query($GLOBALS['conn'],"set names utf8");

	if(isset($_POST['submit'])&&isset($_POST['pd'])&&isset($_POST['srclist'])){
		$pd=$_POST['pd'];
		$srclist=$_POST['srclist'];
		$showindex=$_POST['showindex'];
		
		mysqli_query($GLOBALS['conn'],"delete from chzb_channels where category='$pd'");
		$rows=explode("\r\n",$srclist);
		foreach($rows as $row){	
			if (strpos($row, ',') !== false){
				$ipos=strpos($row, ',');			
				//$arr_row=explode(",",$row);
				$channelname=substr($row,0,$ipos);
				$source=substr($row,$ipos+1);
				if(strpos($source,'#')!==false){
					$sources=explode("#",$source);
					foreach ($sources as $src) {
						$src2=str_replace("\"", "", $src);
						$src2=str_replace("\'", "", $src2);
						//$src2=str_replace(",", "", $src2);
						$src2=str_replace("}", "", $src2);
						$src2=str_replace("{", "", $src2);
						if($channelname!=''&&$src2!=''){
							mysqli_query($GLOBALS['conn'],"INSERT INTO chzb_channels VALUES (null,'$channelname','$src2','$pd')");
						}
					}					
				}else{
					$src2=str_replace("\"", "", $source);
					$src2=str_replace("\'", "", $src2);
					//$src2=str_replace(",", "", $src2);
					$src2=str_replace("}", "", $src2);
					$src2=str_replace("{", "", $src2);
					if($channelname!=''&&$src2!=''){
						mysqli_query($GLOBALS['conn'],"INSERT INTO chzb_channels VALUES (null,'$channelname','$src2','$pd')");
					}
				}
				
			}
		}
		echo"<script>showindex=$showindex;</script>保存成功。";
		
	}
	if(isset($_POST['submit'])&&isset($_POST['category'])){
		$category=$_POST['category'];
		$cpass=$_POST['cpass'];
		$maxindex=$_POST['maxindex'];
		if($category==""){
			echo "类别名称不能为空！";
			exit();
		}

		
		$result=mysqli_query($GLOBALS['conn'],"SELECT max(id) from $categoryname");
		if($row=mysqli_fetch_array($result)){			
			if($row[0]>0){
				$numCount=$row[0]+1;
			}
		}
		

		$sql = "SELECT name FROM $categoryname where name='$category'";
		$result = mysqli_query($GLOBALS['conn'],$sql);
		if(mysqli_fetch_array($result)){
			echo "<script>showindex=$showindex;</script>该栏目已经存在！";
		}else{
			mysqli_query($GLOBALS['conn'],"INSERT INTO $categoryname (id,name,psw) VALUES ($numCount,'$category','$cpass')");
			$result=mysqli_query($GLOBALS['conn'],"SELECT * from $categoryname");
			$showindex=mysqli_num_rows($result)-1;
			echo "<script>showindex=$showindex;</script><font color=red>增加类别$category 成功！</font>";
			$pd=$category;
		}
        
		
	}

	if(isset($_POST['submit_deltype'])&&isset($_POST['category'])){
		$category=$_POST['category'];
	    $showindex=$_POST['showindex'];
		$result=mysqli_query($GLOBALS['conn'],"SELECT id from $categoryname where name='$category'");
		if($row=mysqli_fetch_array($result)){
			$categoryid=$row[0];
			mysqli_query($GLOBALS['conn'],"UPDATE $categoryname set id=id-1 where id>$categoryid");
		}
		$sql = "delete from $categoryname where name='$category'";
		mysqli_query($GLOBALS['conn'],$sql);	
		mysqli_query($GLOBALS['conn'],"delete from chzb_channels where category='$category'");
		echo "<script>showindex=$showindex-1;</script>$category 删除成功！";
	}

	if(isset($_POST['submit_modifytype'])&&isset($_POST['category'])){
		$category=$_POST['category'];	
		$cpass=$_POST['cpass'];
		$showindex=$_POST['showindex'];
		$category0=$_POST['typename0'];
		if($category==""){
			echo "类别名称不能为空！";
			exit();
		}
		mysqli_query($GLOBALS['conn'],"update $categoryname set name='$category',psw='$cpass' where name='$category0'");
		mysqli_query($GLOBALS['conn'],"UPDATE chzb_channels set category='$category' where category='$category0'");
		echo "$category 修改成功！<script>showindex=$showindex;</script>";
		$pd=$category;
	}

	if(isset($_POST['submit_moveup'])&&isset($_POST['category'])){
		$category=$_POST['category'];
		$showindex=$_POST['showindex'];
		$result=mysqli_query($GLOBALS['conn'],"SELECT id from $categoryname where name='$category'");
		if($row=mysqli_fetch_array($result)){
			$id=$row['id'];
			if(!($id==1)){
				$preid=$id-1;
				mysqli_query($GLOBALS['conn'],"update $categoryname set id=id+1  where id=$preid");	
				mysqli_query($GLOBALS['conn'],"update $categoryname set id=id-1  where name='$category'");
               echo "<script>showindex=$showindex-1;</script>";
			}
		}
	}
	
	if(isset($_POST['submit_movedown'])&&isset($_POST['category'])){
		$category=$_POST['category'];
		$showindex=$_POST['showindex'];
		$result=mysqli_query($GLOBALS['conn'],"SELECT id from $categoryname where name='$category'");
		if($row=mysqli_fetch_array($result)){
			$id=$row['id'];	
			$nextid=$id+1;
			if(mysqli_fetch_array(mysqli_query($GLOBALS['conn'],"SELECT id from $categoryname where id=$nextid"))){
				mysqli_query($GLOBALS['conn'],"update $categoryname set id=id-1  where id=$nextid");	
				mysqli_query($GLOBALS['conn'],"update $categoryname set id=id+1  where name='$category'");
            	echo "<script>showindex=$showindex+1;</script>";
			}else{
            	echo "<script>showindex=$showindex;</script>";
      }
		}
	}
	
	if(isset($_POST['submit_movetop'])&&isset($_POST['category'])){
		$category=$_POST['category'];
		$result=mysqli_query($GLOBALS['conn'],"SELECT Min(id) from $categoryname");
		if($row=mysqli_fetch_array($result)){
			$id=$row[0]-1;				
			mysqli_query($GLOBALS['conn'],"update $categoryname set id=$id  where name='$category'");
			mysqli_query($GLOBALS['conn'],"update $categoryname set id=id+1");
			echo "<script>showindex=0;</script>";
		}
	}

	if(isset($_POST['submit'])&&isset($_POST['ver'])){
		$updateinterval=$_POST['updateinterval'];
		if(isset($_POST['autoupdate'])){			
			mysqli_query($GLOBALS['conn'],"update chzb_appdata set autoupdate=1,updateinterval=$updateinterval");
		}else{
			$ver=$_POST['ver'];
			$sql = "update chzb_appdata set dataver=$ver,autoupdate=0";
			mysqli_query($GLOBALS['conn'],$sql);	
		}
		echo "<font color=red>保存成功。</font>";
	}

	if(isset($_POST['checkpdname'])){ 
		mysqli_query($GLOBALS['conn'],"UPDATE $categoryname set enable=0");
		foreach ($_POST['enable'] as $pdenable) {				
			mysqli_query($GLOBALS['conn'],"UPDATE $categoryname set enable=1 where name='$pdenable'");		 	 
		}
	}

	$sql = "SELECT dataver,appver,autoupdate,updateinterval FROM chzb_appdata";
	$result = mysqli_query($GLOBALS['conn'],$sql);
	if($row = mysqli_fetch_array($result)) {
		$ver=$row['dataver'];
		$versionname=$row['appver'];
		$autoupdate=$row['autoupdate'];
		$updateinterval=$row['updateinterval'];
	}else{
		$ver="0";
		$autoupdate=0;
		$updateinterval=0;
	}
	if($autoupdate==1){
		$checktext="checked='true'";
	}else{
		$checktext='';
	}
?>

<div id="tip"></div>
<div  style="float:left;width:99%;text-align: left;">
<table>
	<tr>
		<form method="post" id='autoupdate_form'>	
		<input type="hidden" name="ver" value="<?php echo ($ver+1); ?>">
		间隔时间<input type="text" name='updateinterval' value="<?php echo $updateinterval ?>" size="5">分
		<?php echo"<input type=\"checkbox\" name=\"autoupdate\" value=\"$autoupdate\" $checktext>自动更新"?>
		<input type="submit" name="submit" value="&nbsp;&nbsp;保存设定&nbsp;&nbsp;"/>
		</form>
	</tr>
	<br>
	<tr>
		<form method="post">
		  分类名称<input id="typename" type="text" size="10" name="category" value="<?PHP echo $pd?>" />
		  分类密码<input id="typepass" type="text" size="10" name="cpass" value="<?PHP echo $cpass?>" />
  		<input type="submit" name="submit" value="增加分类">
	  	<input type="submit" name="submit_deltype" value="删除分类">
	  	<input type="submit" name="submit_modifytype" value="修改分类">
	  	<input type="submit" name="submit_moveup" value="上移分类">
	  	<input type="submit" name="submit_movedown" value="下移分类">
	  	<input type="submit" name="submit_movetop" value="移至最上">
		</form>
	</tr>
</table>
</div>

<div style="float:left; width:19%;height:80%;" >
<div id="cate" style="padding:5px;overflow:scroll;height: 98%;overflow-x:visible;">
<script type="text/javascript">
var pdname=[];
var psw=[];
</script>
<center>
  
<ul id="pdlist">
	<?php
			$sql = "SELECT name,psw,enable FROM $categoryname order by id";
			$result = mysqli_query($GLOBALS['conn'],$sql);
			$index=0;
				while($row = mysqli_fetch_array($result)) {
					$pdname=$row['name'];
					$enable=$row['enable'];
					$cpass=$row['psw'];
					if($enable==1){
						$check='checked=checked';
					}else{
						$check='';
					}
          if($cpass==''){
            $lockimg='';
          }else{
            $lockimg='*';
          }
          echo "<script>pdname[$index]='$pdname';psw[$index]='$cpass';</script>";
					echo "<li>
					<a href='#' onclick=\"showlist($index)\">
					<div class='pdlist' style='text-align:left;padding-left:25px;padding-top:5px;padding-bottom:5px;'>
					<input width='20px' type='checkbox' $check onclick='togglepdcheck(\"$pdname\",\"$categoryname\")'/>					
					$pdname $lockimg 
					</div>
					</a>
					</li>";
					$index++;
				}
	mysqli_close($GLOBALS['conn']);
	?>
</ul></center>
</div>
</div>
<script>
  	function togglepdcheck(pdname,catname){
		$.get("togglepd.php?pdname="+pdname+"&cat="+catname,function(data){$("#tip").html(data)});
	}
	function showlist(index){
      	$("#pdlist li div").css("fontSize","22px");
        $("#cate").css("background","#3c444d");
		$("#pdlist li").css("background","none");		
		$(".pdlist").css("color","#d6d7d9");
      	$("#pdlist li").css("border-left","3px solid #3c444d");
        $($("#pdlist li")[index]).css("background","#2c3138");
        $($("#pdlist li")[index]).css("border-left","3px solid #55ff77");
		$($(".pdlist")[index]).css("color","white");
	    $("#srclist").val("正在加载中...");
   		$("#srclist").load("getlist.php?pd="+pdname[index],function(data){
          $("#srclist").val(data);
        });
		$("#typename").val(pdname[index]);
     	$("#typename0").val(pdname[index]);
        $("#typepass").val(psw[index]);
		$("#pd").val(pdname[index]);
		$("#showindex").val(index);
     	$("#showindextype").val(index);
		showindex=index;
        $.session.set("<?php echo 'showindex';?>",showindex);
	}
    if(showindex==-1)  showindex=$.session.get("<?php echo 'showindex';?>");
    $("#cate")[0].scrollTop=$.session.get("<?php echo 'scrollTop';?>");
    $("#cate").scroll(function(){
    $.session.set("<?php echo 'scrollTop';?>", $(this)[0].scrollTop);
    });
</script>

<div  style="float:left;width:80%;text-align: center;">
<form method="post">
<input style="width:92%;" type="submit" name="submit" value="&nbsp;&nbsp;保&nbsp;&nbsp;&nbsp;&nbsp;存&nbsp;&nbsp;">
<input type="hidden" id="pd" name="pd" value=""/>
<input type="hidden" id="showindex" name="showindex" value=""/>
<textarea id="srclist" name="srclist">
  
  
</textarea>
</form>
</div>
<script type="text/javascript">
showlist(showindex);
</script>

</body>
</html>

