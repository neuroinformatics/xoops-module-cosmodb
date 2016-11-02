<?php
	
	function xoops_module_install_newdb($xoopsMod){
	
		define('MOD_PATH', XOOPS_ROOT_PATH.'/modules/newdb');
		define('EXTRACT_PATH', MOD_PATH.'/extract');
		define('UPLOAD_PATH', MOD_PATH.'/upload');

		if(!is_dir(EXTRACT_PATH) && !mkdir(EXTRACT_PATH, 0777)){
			echo 'mkdir [extract] false<br>';
		}	
		if(!is_dir(UPLOAD_PATH) && !mkdir(UPLOAD_PATH, 0777)){
			echo 'mkdir [upload] false<br>';
		}
		
		global $xoopsDB;
		
		$sql = "INSERT INTO ".$xoopsDB->prefix('newdb_component_master')." VALUES ('1', 'ID', 'ID', '', '1', '', '', '0', '0','0','0','0')";
		$rs = $xoopsDB->query($sql);
		
		$sql = "INSERT INTO ".$xoopsDB->prefix('newdb_component_master')." VALUES ('2', '"._ND_INS_DATAN."', 'Data Name', '', '1', '', '', '0', '0','0','0','0')";
		$rs = $xoopsDB->query($sql);
		
		$sql = "INSERT INTO ".$xoopsDB->prefix('newdb_component_master')." VALUES ('3', '"._ND_INS_AUTHOR."', 'Author', '', '1', '', '', '0', '0','0','0','0')";
		$rs = $xoopsDB->query($sql);
		
		$sql = "INSERT INTO ".$xoopsDB->prefix('newdb_component_master')." VALUES ('4', '"._ND_INS_DATE."', 'Creation Date', '', '1', '', '', '0', '0','0','0','0')";
		$rs = $xoopsDB->query($sql);
		
		$sql = "INSERT INTO ".$xoopsDB->prefix('newdb_component_master')." VALUES ('5', '"._ND_INS_VIEWS."', 'Views', '', '1', '', '', '0', '0','0','0','0')";
		$rs = $xoopsDB->query($sql);
		
		$sql = "INSERT INTO ".$xoopsDB->prefix('newdb_component_master')." VALUES ('6', '"._ND_INS_RANK."', 'Rank', '', '2', '--', 'A,B,C,D,--', '0', '0','0','0','0')";
		$rs = $xoopsDB->query($sql);
		
		$sql = "INSERT INTO ".$xoopsDB->prefix('newdb_list')." VALUES('1', '"._ND_INS_LIST."', '1', '<th>{ID}</th>\n<th style=\'width:20%\'>{Data Name}</th>\n<th style=\'width:30%\'>"._ND_INS_DIR."</th>\n<th>{Author}</th>\n<th>{Creation Date}</th>\n<th>{Rank}</th>\n<th>{Views}</th>', '', '', '<th>{ID}</th>\n<td>{Data Name}</td>\n<td>{Dirs}</td>\n<td>{Author}</td>\n<td style=\'text-align:center\'>{Creation Date}</td>\n<td style=\'text-align:center\'>{Rank}</td>\n<td style=\'text-align:center\'>{Views}</td>', '0', '0')";
		$rs = $xoopsDB->query($sql);
		
		$sql = "INSERT INTO ".$xoopsDB->prefix('newdb_list')." VALUES('2', '"._ND_INS_THUMB."', '2', '', 'img', 'S,80,80,5;M,120,120,4;L,160,160,3;Default,0,0,1', '<table class=\'list_table\'>\n<tr>\n  <th>{Data Name}</th>\n</tr>\n<tr>\n  <td style=\'text-align:center\'>{Image}</td>\n</tr>\n<tr>\n  <td style=\'text-align:center\'>\n    {Author}<br>\n    {Creation Date}\n  </td>\n</tr>\n</table>', '0', '0')";
		$rs = $xoopsDB->query($sql);

		$sql = "INSERT INTO ".$xoopsDB->prefix('newdb_detail')." VALUES('1','<table class=\'label_header\'>\n<tr>\n <td style=\'vertical-align:bottom\'>\n {Data Name}\n </td>\n <td style=\'text-align:right\'>\n {AddBookmark} \n {AddLink} \n {Config} \n {FileManager}\n </td>\n</tr>\n</table>\n\n<div class=\'h_under\' style=\'text-align:center\'>\n{href_tab1}Information{/href_tab} | \n{href_tab2}Comment{/href_tab}\n</div>\n\n{tab1}\n{Image img 160px|160px|3}\n<table>\n <tr>\n  <td style=\'width:5%\'> </td>\n  <td style=\'width:42%\'>\n   <div class=\'h\'>Infomation</div><br>\n    <table>\n     <tr><td>ID</td><td>{ID}</td></tr>\n     <tr><td>"._ND_INS_AUTHOR."</td><td>{Author}</td></tr>\n     <tr><td>"._ND_INS_DATE."</td><td>{Creation Date}</td></tr>\n     <tr><td>"._ND_INS_RANK."</td><td>{Rank}</td></tr>\n     <tr><td>"._ND_INS_VIEWS."</td><td>{Views}</td></tr>\n   </table>\n  </td>\n  <td style=\'width:5%\'> </td>\n  <td style=\'width:42%;\' rowspan=\'2\'>\n   <div class=\'h\'>Keyword</div><br>{Keyword}\n  </td>\n  <td style=\'width:5%\'> </td>\n </tr>\n <tr>\n  <td style=\'width:5%\'> </td>\n  <td style=\'width:42%\'>\n   <div class=\'h\'>Data</div><br>{Dtree}\n  </td>\n  <td style=\'width:5%\'> </td>\n  <td style=\'width:5%\'> </td>\n </tr>\n</table>\n\n<center>\n<table style=\'width:90%\'>\n <tr>\n  <td>\n   <div class=\'h\'>News</div><br>{News}\n   <div class=\'h\'>Links</div><br>{Link}\n  </td>\n </tr>\n</table>\n</center>\n\n{/tab}\n\n{tab2}\n{Acomment}<br><br>\n{Ucomment}\n{/tab}')";
		$rs = $xoopsDB->query($sql);

		return true;
	}
?>

