<?php
	include '../../../../mainfile.php';
	$mes = "";
	
	$rs = $xoopsDB->queryF("drop table ".$xoopsDB->prefix('newdb_fts'));
	if($rs){
		$sql = "CREATE TABLE ".$xoopsDB->prefix('newdb_fulltext_search')."(";
		$sql.= "user char(32) NOT NULL,	label_id int(10) NOT NULL,	pcom_id int(10) NOT NULL,	";
		$sql.= "subject char(100) NOT NULL,	message text NOT NULL,	info char(128) NOT NULL,	type char(6) NOT NULL)";
		$rs = $xoopsDB->queryF($sql);
		if($rs){
			$mes.= "<br>success: create table 'newdb_fulltext_search'";
		}
	}else{
		$mes.= "<br>error: drop table 'newdb_fts'";
	}
	
	$rs = $xoopsDB->queryF("drop table ".$xoopsDB->prefix('newdb_fs'));
	if($rs){
		$sql = "CREATE TABLE ".$xoopsDB->prefix('newdb_file_search')."(";
		$sql.= "user char(32) NOT NULL,	label_id int(10) NOT NULL,	name char(30) NOT NULL,	path text,	info char(128) NOT NULL)";
		$rs = $xoopsDB->queryF($sql);
		if($rs){
			$mes.= "<br>success: create table 'newdb_file_search'";
		}
	}else{
		$mes.= "<br>error: drop table 'newdb_fs'";
	}
	redirect_header(XOOPS_URL, 4, $mes);
?>