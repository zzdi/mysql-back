<?php
/*
备份：db.php?act=back
导入：db.php?act=import
*/

//数据库地址
define('DB_HOST', '');
//数据库用户名
define('DB_USER', '');
//数据库密码
define('DB_PASSWORD','');
//数据库名
define('DB_NAME', '');
//数据库表前缀
define('DB_PRE', '');
define('DB_PCONNECT', 0);
//数据库编码
define('DB_CHARSET', 'utf8');
//备份或导入的sql文件
define('DB_BACK', 'back.sql');

include('./mysql.class.php');
$mysql=new mysql(DB_HOST,DB_USER,DB_PASSWORD,DB_NAME,DB_CHARSET,DB_PCONNECT);

$act = @$_GET['act'];
if($act=='back'){
	db_back();
}elseif($act=='import'){
	db_import();
}


function db_back(){
	$rel2=$GLOBALS['mysql']->fetch_asc('SHOW TABLE STATUS FROM '.DB_NAME);
	$db=array();
	foreach($rel2 as $key=>$value){
		if(substr($value['Name'],0,strlen(DB_PRE))==DB_PRE){
			$db[]=$value['Name'];
		}
	}
	$sql="";
	foreach($db as $k=>$v){
		$rel=$GLOBALS['mysql']->fetch_asc('SHOW CREATE TABLE '.$v);
		$sql.="DROP TABLE IF EXISTS `".$v."`;\n";
		$sql.=$rel[0]['Create Table'].";\n";
		$record=$GLOBALS['mysql']->fetch_asc("select * from ".$v);
		if(!empty($record)){
			$insert=array();
			foreach($record as $key=>$value){
				foreach($value as $r_k=>$r_v){
					$insert[$r_k]="'".mysql_real_escape_string($r_v)."'";
				}
				$sql.="INSERT INTO `".$v."` VALUES(".implode(',',$insert).");\n";
			}
		}
	}
	if(!@file_put_contents(DB_BACK,$sql)){
		echo '备份失败,请检查文件夹是否有足够的权限';
	}
	echo "备份成功,备份文件为".DB_BACK;
}


function db_import(){
	$data=@file_get_contents(DB_BACK);
	$data=explode(";\n",trim($data));
	if(!empty($data)){
		foreach($data as $k=>$v){
			$GLOBALS['mysql']->query($v);
		}
	}
	echo "数据还原成功";
}
