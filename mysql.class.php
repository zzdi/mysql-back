<?php
//数据库连接类
class mysql
{
	var $link;
	var $db_host;
	var $db_user;
	var $db_password;
	var $db;
	var $db_prefix;
	var $db_pc;
	var $parentid;
	var $twoCateid;

	function mysql($db_host,$db_user,$db_password,$db,$db_charset,$pc_connect){
		$this->db_host=$db_host;
		$this->db_user=$db_user;
		$this->db_password=$db_password;
		$this->db=$db;
		$this->db_charset=$db_charset;
		$this->db_pc=$pc_connect;
		$this->open_link();
		$this->sl_db();
	}

	function open_link(){
		$this->link=($this->db_pc)?@mysql_pconnect($this->db_host,$this->db_user,$this->db_password):@mysql_connect($this->db_host,$this->db_user,$this->db_password);
		if(!$this->link){
			echo "数据库连接错误".mysql_error();
		}
	}

	function sl_db(){
		if(!@mysql_select_db($this->db,$this->link)){
			echo "数据库".$this->db."不存在";
		}
		@mysql_unbuffered_query("set names {$this->db_charset}");
	}

	function query($sql){
		if(!$res=@mysql_query($sql,$this->link)){
			echo "操作数据库失败".mysql_error()."<br>sql:{$sql}";
		}
		return $res;
	}

	function fetch_asc($sql){
		$result=$this->query($sql);
		$arr=array();
		while($rows=mysql_fetch_assoc($result)){
			$arr[]=$rows;
		}
		mysql_free_result($result);
		return $arr;
	}
}
