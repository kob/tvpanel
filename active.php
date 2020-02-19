<?php
include_once "aes.php";
include "sql.php";
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

if(isset($_POST['act'])){
	 $json=$_POST['act'];

	 $obj=json_decode($json);
	 $mac=$obj->mac;
	 $androidid=$obj->androidid;
	 $model=$obj->model;
	 $sn=$obj->sn;
	 $ip=$_SERVER['REMOTE_ADDR'];

 	 $myurl='http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
     $json=file_get_contents(dirname($myurl)."/iploc/iploc.php?ip=$ip");
     $obj=json_decode($json);
     $region=$obj->region;
     $nettype=$obj->nettype;


	$nowtime=time();

	if(!is_numeric($sn))exit('授权号必须为数字！！');
	
	$result=mysqli_query($GLOBALS['conn'],"SELECT status,name FROM chzb_users where deviceid='$androidid'");
	if($row=mysqli_fetch_array($result)){
		$id=$row['name'];
		$status=$row['status'];
	}else{
	//强制绑定当前设备	
		$name=genName();
		$id=$name;
		$status=-1;
		if(mysqli_query($GLOBALS['conn'],"INSERT into chzb_users (name,mac,deviceid,model,exp,ip,status,region,lasttime) values($name,'$mac','$androidid','$model',0,'$ip',-1,'$region',$nowtime)")){
			$msg='设备识别成功！';	
		}else{
			$msg='你的设备无法识别,请与管理员联系！';
		}
		
	}

	$result=mysqli_query($GLOBALS['conn'],"SELECT status,name,exp,marks,author,authortime FROM chzb_users where status>0 and deviceid='' and name=$sn");
	if($row=mysqli_fetch_array($result)){
		$status=$row['status'];
		$exp=$row['exp'];
		$marks=$row['exp'];
		$author=$row['author'];
		$authortime=$row['authortime'];

		//通过SN重新绑定			
		mysqli_query($GLOBALS['conn'],"UPDATE chzb_users set mac='$mac',deviceid='$androidid',model='$model' where status>0 and mac='' and deviceid='' and model='' and name=$sn");
		mysqli_query($GLOBALS['conn'],"DELETE from chzb_users where mac='$mac' and deviceid='$androidid' and model='$model' and status=-1");		
		$status=-1;
		$msg="系统已为你重新绑定成功！";	
	}else{
			$result=mysqli_query($GLOBALS['conn'],"SELECT sn,exp,author,marks,vip FROM chzb_serialnum where sn=$sn and enable=0");
			if($row=mysqli_fetch_array($result)){
				$nowtime=time();
				$exp=$row['exp']*86400+strtotime(date("Y-m-d"),$nowtime);
				$author=$row['author'];
				$marks=$row['marks'];
				$vip=$row['vip'];
				
				if($row['exp']==999){
					$status=999;
				}else{
					$status=1;
				}
				mysqli_query($GLOBALS['conn'],"update chzb_users set status=$status,exp=$exp,author='$author',authortime=$nowtime,marks='$marks',name=$sn,vip=$vip where name=$id");
				mysqli_query($GLOBALS['conn'],"update chzb_serialnum set regtime=$nowtime,regid=$id,enable=1 where sn=$sn");
				$msg="授权号绑定成功！！";
			}else{
				$result=mysqli_query($GLOBALS['conn'],"SELECT name from chzb_users where name=$sn");
				if(mysqli_fetch_array($result)){
					$msg="该授权号已绑定其他设备，请联系提供商！！";
				}else{
					$msg="授权号错误，请联系提供商！！";
				}
				
			}
	}

	echo $msg;


}else{
	exit();
}

?>