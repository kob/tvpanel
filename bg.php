<?php
header('Content-Type: text/json;charset=UTF-8');
$dir=dirname(__FILE__);
$dir=$dir.'/image';
$files = glob("images/*.png");
$pngs = array();
$url='http://'.$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'];
$url= dirname($url).'/';

foreach ($files as $file) {
	$pngs[]=$url.$file;
}

$rkey=array_rand($pngs);
echo $pngs[$rkey];

?>

