<?php
	include 'header.php';

	if(!isset($_GET['kw']) && !isset($_GET['type'])){
		redirect_header(XOOPS_URL, 2, _ND_NACCESS);
	}
	
	$mode = $_GET['type'];
	$kw = $myts->stripSlashesGPC($_GET['kw']);
	
	switch($mode){
		case 'comment':
			header("Location:".MOD_URL."/fts.php?kw=".$kw."&andor=0");
			break;
		
		case 'file':
			$perm = 0;
			if($uid){
				foreach($xoopsModuleConfig['access_perm'] as $group_id){
					$sql = "SELECT uid FROM ".$xoopsDB->prefix('groups_users_link');
					$sql.= " WHERE groupid='".$group_id."'";
					$rs = $xoopsDB->query($sql);
					while($row = $xoopsDB->fetchArray($rs)){
						if($row['uid'] == $uid) $perm = 1;
					}
				}
			}
			if($perm){
				header("Location:".MOD_URL."/fs.php?kw=".$kw."&andor=0");
			}else{
				redirect_header(XOOPS_URL, 2, _ND_NACCESS2);
			}
			break;
				
		case 'id':
			$kw = intval($kw);
			header("Location:".MOD_URL."/detail.php?id=".$kw);
			
		case 'dataname':
			header("Location:".MOD_URL."/list.php?system=1&text=".$kw."&tsearch=Go&item=0&sort=1&sort_method=desc&n=20&id=1");
			break;
	}
?>