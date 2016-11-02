<?php
	include '../../../../mainfile.php';
	$mes = "";
	
	$sql = "CREATE TABLE ".$xoopsDB->prefix('newdb_list_textsearch')."(";
	$sql.= "ref_id int(20) NOT NULL auto_increment,	";
	$sql.= "user char(32) NOT NULL, ";
	$sql.= "text char(255), ";
	$sql.= "labels text, ";
	$sql.= "primary key(ref_id))";
	$rs = $xoopsDB->queryF($sql);
	if($rs){
		$mes.= "<br>success: create table 'newdb_list_textsearch'";
	}else{
		$mes.= "<br>error: table 'newdb_list_textsearch' wasn't created.";
	}
	
	redirect_header(XOOPS_URL, 4, $mes);
?>