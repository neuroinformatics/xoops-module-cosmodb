<?php
	include 'header.php';

	if(!$uid){
		redirect_header(MOD_URL, 2, _ND_NACCESS2);
	}

	$mode = '';
	if(isset($_GET['mode'])){
		$mode = $_GET['mode'];
	}elseif(isset($_POST['mode'])){
		$mode = $_POST['mode'];
	}

	switch($mode){
	
		case 'edit':
			$link_id = intval($_GET['link_id']);			
			$sql = "SELECT * FROM ".$xoopsDB->prefix('newdb_link')." WHERE uid='".$uid."'";
			$rs = $xoopsDB->query($sql);			
			if($xoopsDB->getRowsNum($rs) || $isadmin){
				include XOOPS_ROOT_PATH.'/header.php';
				include 'style.css';
				showEditForm($link_id);
				include XOOPS_ROOT_PATH.'/footer.php';
			}else{
				redirect_header(MOD_URL, 2, _ND_NACCESS2);
			}
			break;
			
		case 'do_edit':
			$link_id = intval($_POST['link_id']);
			$lid = intval($_POST['lid']);
			
			if(isset($_POST['link_del']) && $_POST['link_del'] == 'y'){
				$sql = "DELETE FROM ".$xoopsDB->prefix('newdb_link')." WHERE link_id='".$link_id."'";
				$rs = $xoopsDB->query($sql);
				if($rs){
					$mes = _ND_LINK_DELOK;
				}else{
					$mes = _ND_LINK_DELNG;
				}
			}else{
				$note4sql = addslashes($myts->stripSlashesGPC($_POST['note']));
				$sql = "SELECT * FROM ".$xoopsDB->prefix('newdb_link')." WHERE link_id='".$link_id."'";
				$rs = $xoopsDB->query($sql);
				$row = $xoopsDB->fetchArray($rs);

				if($row['type'] == 1){
					$sql = "UPDATE ".$xoopsDB->prefix('newdb_link');
					$sql.= " SET note='".$note4sql."' WHERE link_id='".$link_id."'";
					$rs = $xoopsDB->query($sql);
					if($rs){
						$mes = _ND_LINK_EDITOK;
					}else{
						$mes = _ND_LINK_EDITNG;
					}
				}elseif($row['type'] == 2){
					$name4sql = addslashes($myts->stripSlashesGPC($_POST['link_name']));
					$url4sql = addslashes($myts->stripSlashesGPC($_POST['link_url']));
					$sql = "UPDATE ".$xoopsDB->prefix('newdb_link');
					$sql.= " SET name='".$name4sql."', href='".$url4sql."', note='".$note4sql."' WHERE link_id='".$link_id."'";
					$rs = $xoopsDB->query($sql);
					if($rs){
						$mes = _ND_LINK_EDITOK;
					}else{
						$mes = _ND_LINK_EDITNG;
					}
				}
			}
			redirect_header(MOD_URL.'/detail.php?id='.$lid, 2, $mes);
			break;

		case 'reglink':
			$type = intval($_POST['type']);
			$lid = intval($_POST['lid']);
			$mes = '';
			
			if($type == '1'){
				$link_id = intval($_POST['link_id']);
				$note4sql = addslashes($myts->stripSlashesGPC($_POST['note']));

				$sql = "INSERT INTO ".$xoopsDB->prefix('newdb_link');
				$sql.= " VALUES('','".$lid."','1','".$uid."','".$link_id."','','".$note4sql."')";
				$rs = $xoopsDB->query($sql);
				if($rs){
					if(!empty($_POST['link_each'])){
						$mes = _ND_LINK_ADDOK;
						$sql = "INSERT INTO ".$xoopsDB->prefix('newdb_link');
						$sql.= " VALUES('','".$link_id."','1','".$uid."','".$lid."','','".$note4sql."')";
						$rs = $xoopsDB->query($sql);
						if($rs){
							$mes.= '<br>'._ND_LINK_RLINKOK;
						}else{
							$mes.= '<br>'._ND_LINK_RLINKNG;
						}
					}
				}else{
					$mes = _ND_LINK_ADDNG;
				}
				
			}elseif($type == '2'){
				$name4sql = addslashes($myts->stripSlashesGPC($_POST['link_name']));
				$url4sql = addslashes($myts->stripSlashesGPC($_POST['link_url']));
				$note4sql = addslashes($myts->stripSlashesGPC($_POST['note']));
			
				$sql = "INSERT INTO ".$xoopsDB->prefix('newdb_link');
				$sql.= " VALUES('','".$lid."','2','".$uid."','".$name4sql."','".$url4sql."','".$note4sql."')";
				$rs = $xoopsDB->query($sql);
				if($rs){
					$mes = _ND_LINK_ADDOK;
				}else{
					$mes = _ND_LINK_ADDNG;
				}
			}else{
				$mes = _ND_LINK_SELECT_TARGET;
			}
			redirect_header(MOD_URL.'/detail.php?id='.$lid, 1, $mes);
			break;
	
		default:
			$lid = intval($_GET['lid']);
			include XOOPS_ROOT_PATH.'/header.php';
			include 'style.css';
			echo "<script language='JavaScript' src='tab.js'></script>\n";

			echo "<form action='link.php' method='POST'>";
			echo "<center><table class='list_table' style='width:400px'>";
			echo "<tr><th colspan='2'>"._ND_LINK_ADDLINK."</th></tr>";
			echo "<tr><td class='even' style='width:100px'><b>"._ND_LINK_TARGET."</b></td><td>";
			echo "<input type='radio' name='type' value='1' onclick=\"javascript:seltab('box', 'head', 10, 1)\">";
			echo _ND_LINK_METHOD1."<br>";
			echo "<input type='radio' name='type' value='2' onclick=\"javascript:seltab('box', 'head', 10, 2)\">";
			echo _ND_LINK_METHOD2;
			echo "</td></tr></table>";
			
			echo "<div id='box1' style='display:none'>";
			echo "<table class='list_table' style='width:400px'>";
			echo "<tr><td class='even' style='width:100px;'><b>"._ND_LINK_TARGETID."</b></td>";
			echo "<td><input type='text' name='link_id'></td></tr>";
			echo "<tr><td class='even' style='width:100px;'><b>"._ND_LINK_RLNK."</b></td>";
			echo "<td><input type='checkbox' name='link_each' value='y'> "._ND_LINK_RLINK_DESC."</td></tr>";
			echo "</table></div>";
			
			echo "<div id='box2' style='display:none'>";
			echo "<table class='list_table' style='width:400px'>";
			echo "<tr><td class='even' style='width:100px;'><b>"._ND_LINK_NAME."</b></td>";
			echo "<td><input type='text' name='link_name'></td></tr>";
			echo "<tr><td class='even' style='width:100px;'><b>URL</b></td>";
			echo "<td><input type='text' name='link_url' style='width:98%'></td></tr>";
			echo "</table></div>";
			
			echo "<table class='list_table' style='width:400px'>";
			echo "<tr><td class='even' style='width:100px'><b>"._ND_COMMENTV_COM."</b></td>";
			echo "<td><input type='text' name='note' style='width:98%'></td></tr>";
			echo "<tr><td class='even'> </td>";
			echo "<td><input type='submit' class='button' value='submit'></td></tr>";
			echo "</table>";
			
			echo "<br><a href='detail.php?id=".$lid."'>"._ND_BACK."</a>";
			echo "</center>";
			echo "<input type='hidden' name='lid' value='".$lid."'>";
			echo "<input type='hidden' name='mode' value='reglink'>";
			echo "</form>";
			include XOOPS_ROOT_PATH.'/footer.php';
	}


	function showEditForm($link_id){
		if(!$link_id) return;
		
		global $xoopsDB;
		$sql = "SELECT * FROM ".$xoopsDB->prefix('newdb_link')." WHERE link_id='".$link_id."'";
		$rs = $xoopsDB->query($sql);
		$row = $xoopsDB->fetchArray($rs);
		$com = htmlspecialchars($row['note']);
		$url = htmlspecialchars($row['href']);
		$name = htmlspecialchars($row['name']);
		$lid = $row['label_id'];

		echo "<form action='link.php' method='POST'>";
		echo "<center><table class='list_table' style='width:400px'>";
		echo "<tr><th colspan='2'>"._ND_LINK_EDIT."</th></tr>";
		
		if($row['type'] == 2){
			echo "<tr><td class='even' style='width:100px;'><b>"._ND_LINK_NAME."</b></td>";
			echo "<td><input type='text' name='link_name' value='".$name."'></td></tr>";
			echo "<tr><td class='even' style='width:100px'><b>URL</b></td>";
			echo "<td><input type='text' name='link_url' style='width:98%' value='".$url."'></td></tr>";
		}
		
		echo "<tr><td class='even' style='width:100px'><b>"._ND_COMMENTV_COM."</b></td>";
		echo "<td><input type='text' name='note' style='width:98%' value='".$com."'></td></tr>";

		echo "<tr><td class='even' style='width:100px;'><b>"._ND_DELETE."</b></td>";
		echo "<td><input type='checkbox' name='link_del' value='y'> "._ND_LINK_DEL_DESC."</td></tr>";

		echo "<tr><td class='even'> </td>";
		echo "<td><input type='submit' class='button' value='submit'></td></tr>";
		echo "</table>";
		
		echo "<br><a href='detail.php?id=".$lid."'>"._ND_BACK."</a>";
		echo "</center>";
		echo "<input type='hidden' name='lid' value='".$lid."'>";
		echo "<input type='hidden' name='link_id' value='".$link_id."'>";
		echo "<input type='hidden' name='mode' value='do_edit'>";
		echo "</form>";
	}

?>
