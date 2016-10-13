<?php
ob_start();
header('Content-type: text/html;charset=utf-8');
/*
	此文件主要功能如下：
		1.在数据库中添加新记录
		2.返回新加记录信息。JSON格式
		3.创建上传目录
	此文件主要在数据库中添加新的记录并返回文件信息
		如果存在则在数据库中添加一条相同记录。返回添加的信息
		如果不存在，则向数据库中添加一条记录。并返回此记录ID
	控件每次计算完文件MD5时都将向信息上传到此文件中
*/
require('DbHelper.php');
require('DnFile.php');
require('DnFileInf.php');

$uid 		= $_GET["uid"];
$cbk		= $_GET["callback"];//jsonp格式用到
$fileLoc 	= $_GET["file"];
$fileLoc	= str_replace("+","%20",$fileLoc);
$fileLoc	= urldecode($fileLoc);

if (  strlen($uid) < 1)
{
	echo cbk . "({\"value\":null})";
	die();
}

$inf = new DnFileInf();


$fileArr = json_decode($fileLoc,true);
$inf->uid = (int)$uid;
$inf->nameLoc = $fileArr["nameLoc"];
$inf->pathLoc = $fileArr["pathLoc"];
$inf->fileUrl = $fileArr["fileUrl"];
$inf->lenSvr = (int)$fileArr["lenSvr"];
$inf->sizeSvr = $fileArr["sizeSvr"];


$db = new DnFile();
$inf->idSvr = (int)$db->Add($inf);

$json = json_encode($inf);
$json = urlencode($json);
$json = $cbk . "({\"value\":\"".$json."\"})";//返回jsonp格式数据。
echo $json;
header('Content-Length: ' . ob_get_length());
?>