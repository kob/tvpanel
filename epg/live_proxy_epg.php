<?php
header("Content-Type:application/json;chartset=uft-8");
define('SELF', pathinfo(__file__, PATHINFO_BASENAME));
define('FCPATH', str_replace("\\", "/", str_replace(SELF, '', __file__)));
ini_set('display_errors',1);            
ini_set('display_startup_errors',1);   
error_reporting(E_ERROR);
include "curl.class.php";
include "caches.class.php";
include FCPATH . '../sql.php';
mysqli_query($GLOBALS['conn'],"SET NAMES 'UTF8'");

$t = time();
$start_a = "00:01";
$start_b = "00:10";
$a_t = strtotime($start_a);
$b_t = strtotime($start_b);
if( $t<$a_t || $b_t<$t ){
}else{
	$files = glob(FCPATH  . 'bak/*');
	foreach($files as $file){
        if (is_file($file)) {
            @unlink($file);
        }
    }
}

$id=!empty($_GET["id"])?$_GET["id"]:exit(json_encode(["code"=>500,"msg"=>"EPG频道参数不能为空!","name"=>$name,"date"=>null,"data"=>null],JSON_UNESCAPED_UNICODE));
echo out_epg($id);exit;

//输出EPG节目地址
function out_epg($id){
    $tvdata = channel($id);
	$tvid = $tvdata['id'];
	$epgid =  $tvdata['name'];
	
	if (!is_numeric($tvid)) {
	    return $tvid;
	}
	
	$tt=cache("time_out_chk","cache_time_out");  //获取当前时间（后天）的00:00时间戳
	if (time()>=$tt) {
	     Cache::$cache_path="./cache/";   //设置缓存路径
		 //删除除当前目录缓存文件
		 Cache::dels();
		 //重新写入当天时间缓存文件
		 cache("time_out_chk","cache_time_out");
	}
	$ejson=cache($tvid,"get_epg_data",[$tvid,$epgid,$id]);
	return $ejson;
} 

//缓存EPG节目数据
function cache($key,$f_name,$ff=[]){
    Cache::$cache_path="./cache/";   //设置缓存路径
	$val=Cache::gets($key);
	if (!$val) {
	    $data=call_user_func_array($f_name,$ff);   
		Cache::put($key,$data);
		return $data;
	}else {
	    return $val;
	}
} 

function cache_time_out(){
    date_default_timezone_set("Asia/Shanghai");
	$tt=strtotime(date("Y-m-d 00:00:00",time()))+86400;
	return $tt;
} 

//请求频道的EPG数据
function get_epg_data($tvid,$epgid,$name="",$date=""){
	if(strstr($epgid,"cntv") != false){
		$url = "http://api.cntv.cn/epg/epginfo?serviceId=cbox&c=".substr($epgid, 5)."&d=".date('Ymd');
		$str=curl::c()->set_ssl()->get($url);
		$re=json_decode($str,true);
		if (!empty($re[substr($epgid, 5)]['program'])){
			$data=array("code"=>200,"msg"=>"请求成功!","name"=>$re[substr($epgid, 5)]['channelName'],"tvid"=>$tvid,"date"=>date('Y-m-d'));
			foreach($re[substr($epgid, 5)]['program'] as $row){
				$data["data"][]= array("name"=> $row['t'],"starttime"=> $row['showTime']);
			}
			return json_encode($data,JSON_UNESCAPED_UNICODE);
		}
		$data=["code"=>500,"msg"=>"请求失败!","name"=>$name,"date"=>null,"data"=>null];
	    return json_encode($data,JSON_UNESCAPED_UNICODE);
	}else if(strstr($epgid,"tvsou") != false){
		$wday = intval(date('w',strtotime(date('Y-m-d'))));
	    if($wday == 0)
		    $wday = 7;
		$url = "https://www.tvsou.com/epg/".substr($epgid, 6)."/w".$wday;
		$file=curl::c()->set_ssl()->get($url);
	    $file=strstr($file,"<tbody>");
		$pos = strpos($file,"</tbody>");
	    $file=substr($file,0,$pos);
		$file =  preg_replace(array("/<script[\s\S]*?<\/script>/i","/<a .*?href='(.*?)'.*?>/is","/<tbody>/i","/<\/a>/i","/<td[\s\S]*?>/i"), '', $file);
		$file =  str_replace("</td></td></tr> ", '|', $file);
		$file =  str_replace("</td>", '#', $file);
		$file =  str_replace(array("<tr>","\r","\n","\r\n"," "), '', $file);
		$preview = substr($file,0,strlen($file)-1);
		if (!empty($preview)){
		    $data=array("code"=>200,"msg"=>"请求成功!","name"=>$name,"tvid"=>$tvid,"date"=>date('Y-m-d'));
		    $preview = explode('|',$preview);
		    foreach($preview as $row){
			    $row1 = explode('#',$row);
			    $data["data"][]= array("name"=> $row1[1],"starttime"=> $row1[0]);
		    }
		    return json_encode($data,JSON_UNESCAPED_UNICODE);;
	    }
	    $data=["code"=>500,"msg"=>"请求失败!","name"=>$name,"date"=>null,"data"=>null];
	    return json_encode($data,JSON_UNESCAPED_UNICODE);
	}else if(strstr($epgid,"51zmt") != false){
		/*
		$url = "http://epg.51zmt.top:8000/e.xml";
		$file=curl::c()->set_ssl()->get($url);
		file_put_contents('./cache/e.xml',$file) ;
        $xml = json_encode('./cache/e.xml');
        $xml = json_decode($xml,true);
		$arr=$channel=$epgdata=$result=array();
        foreach($xml['channel'] as $row){
		    $channel['data'][] = array('id'=>$row['@attributes']['id'],'name'=>$row['display-name']);
	    }
		foreach($channel['data'] as $key => $value) {
		    foreach ($value as $valu) {
			    if(substr($epgid, 6) == $valu){
				    array_push($arr,$key);
			    }
		    }
	    }
		foreach ($arr as $key => $value) {
		    if(array_key_exists($value,$channel['data'])){
			    array_push($result, $channel['data'][$value]);
		    }
	    }
		foreach($xml['programme'] as $row){
		    if(substr($row['@attributes']['start'],0,8) == date('Ymd')){
		         $epgdata[] = array('id'=>$row['@attributes']['channel'],"start"=>$row['@attributes']['start'],'title'=>$row['title']);
	        }
	    }
		if (!empty($epgdata)){
	        $data=array("code"=>200,"msg"=>"请求成功!","name"=>$name,"tvid"=>$tvid,"date"=>date('Y-m-d'));
	        foreach($epgdata as $row){
		        if($row['id'] == $result[0]['id']){
			        $data["data"][]= array("name"=> $row['title'],"starttime"=> preg_replace('{^(\d{4})(\d{2})(\d{2})(\d{2})(\d{2})(\d{2})(.*?)$}u', '$4:$5',$row["start"]));
		        }
	        }
	        return json_encode($data,JSON_UNESCAPED_UNICODE);
		}
		*/
		$data=["code"=>200,"msg"=>"51zmt接口暂不可用","name"=>$name,"date"=>null,"data"=>"51zmt接口暂不可用"];
	    return json_encode($data,JSON_UNESCAPED_UNICODE);
	}
} 

//频道映射对应表
function channel($id){
	global $con;
	$id=urldecode($id);
	$sql = "select * FROM chzb_epg where status=1 AND FIND_IN_SET('$id',content)";
	$result = mysqli_query($GLOBALS['conn'],"select * FROM chzb_epg where FIND_IN_SET('$id',content)");
	if($row=mysqli_fetch_array($result)){
		return $row;
		mysqli_close($GLOBALS['conn']);
	}else{
		$data=["code"=>500,"msg"=>"频道不存在!","name"=>null,"date"=>null,"data"=>null];
	    exit(json_encode($data,JSON_UNESCAPED_UNICODE));
	}
} 
?>
