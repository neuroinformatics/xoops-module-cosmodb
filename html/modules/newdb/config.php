<?php
	include 'header.php';
	include 'class/keyword.php';
	
	$mes='';
	if(!$uid) $mes = _ND_NACCESS;

	$lid='';
	if(isset($_GET['lid'])){
		$lid = intval($_GET['lid']);
	}elseif(isset($_POST['lid'])){
		$lid = intval($_POST['lid']);
	}else{
		$mes = _ND_NACCESS;
	}
	if(!empty($lid)){
		$rs = $xoopsDB->query("SELECT * FROM ".$xoopsDB->prefix('newdb_master')." WHERE label_id='".$lid."'");
		if($xoopsDB->getRowsNum($rs) > 0){
			$row = $xoopsDB->fetchArray($rs);
			$label = $row['label'];
			if($row['author'] != $uid && !$isadmin){
				$mes = _ND_NACCESS2;
			}
		}else{
			$mes = _ND_NODATA;
		}
	}
	if(!empty($mes)) redirect_header(XOOPS_URL, 1, $mes);

	if(isset($_GET['mode'])){
		$mode = $_GET['mode'];
	}elseif(isset($_POST['mode'])){
		$mode = $_POST['mode'];
	}else{
		$mode = 'info';
	}
	
	switch($mode){
		
		case 'info':
			include XOOPS_ROOT_PATH.'/header.php';
			include 'style.css';
			headerBar($lid, $label, $xoopsModuleConfig['dname_flg']);
			echo "<center><form action='config.php' method='POST'>";
			echo "<table class='list_table' style='width:400px;'>\n";

			# label_name
			if($xoopsModuleConfig['dname_flg']){
				$sql = "SELECT * FROM ".$xoopsDB->prefix('newdb_component_master')." WHERE name='Data Name'";
				$rs = $xoopsDB->query($sql);
				$row = $xoopsDB->fetchArray($rs);
				echo "<tr><td class='even'><b>".$row['tag']."</b></td><td>";
				echo "<input type='text' style='width:180px' name='label_name' value='".$label."'></td></tr>";
			}			
			# other component
			$sql = "SELECT * FROM ".$xoopsDB->prefix('newdb_component')." WHERE label_id='".$lid."'";
			for($i=4; $i>1; $i--){
				$sql2 = "SELECT * FROM ".$xoopsDB->prefix('newdb_component_master');
				$sql2.= " WHERE type='".$i."' ORDER BY sort";
				$rs = $xoopsDB->query($sql2);
				$type_id=0;
				while($row = $xoopsDB->fetchArray($rs)){
					$comp_id = $row['comp_id'];
					echo "<tr><td class='even'><b>".$myts->makeTboxData4Show($row['tag'])."</b></td><td>";
					
					# radio
					if($row['type'] == '2'){
						$rs2 = $xoopsDB->query($sql." AND comp_id='".$comp_id."'");
						$row2 = $xoopsDB->fetchArray($rs2);
						$svalue = explode(',', $row['select_value']);
						for($j=0; $j<count($svalue); $j++){
							($row2['value'] == $svalue[$j]) ? $check='checked' : $check='';
							echo "<input type='radio' name='CR".$type_id."' value='".$svalue[$j]."' ".$check.">";
							echo $svalue[$j]."&nbsp;&nbsp;\n";
						}
						echo "<input type='hidden' name='CR".$type_id."_id' value='".$comp_id."'>";

					# checkbox
					}elseif($row['type']=='3'){
						$svalue = explode(',', $row['select_value']);
						for($j=0; $j<count($svalue); $j++){
							$v = array(); $check='';
							$rs2 = $xoopsDB->query($sql." AND comp_id='".$comp_id."'");
							while($row2 = $xoopsDB->fetchArray($rs2)){
								$v[] = $row2['value'];
							}
							if(in_array($svalue[$j], $v)) $check='checked';
							echo "<input type='checkbox' name='CC".$type_id."[]' value='".$svalue[$j]."' ".$check.">";
							echo $svalue[$j]."&nbsp;&nbsp;\n";
						}
						echo "<input type='hidden' name='CC".$type_id."_id' value='".$comp_id."'>";
					
					# text
					}elseif($row['type']=='4'){
						$rs2 = $xoopsDB->query($sql." AND comp_id='".$comp_id."'");
						$row2 = $xoopsDB->fetchArray($rs2);								
						echo "<input type='text' name='CT".$type_id."' style='width:180px' value='".$row2['value']."'>\n";
						echo "<input type='hidden' name='CT".$type_id."_id' value='".$comp_id."'>";
					}
					echo "</td></tr>\n";
					$type_id ++;
				}
			}
			echo "</table>";
			echo "<br><input type='submit' class='button' value='submit'>";
			echo "<input type='hidden' name='mode' value='info2'>";
			echo "<input type='hidden' name='lid' value='".$lid."'>";
			echo "</form></center>";
			include XOOPS_ROOT_PATH.'/footer.php';
			break;
			
		case 'info2':
			# label_name
			if($xoopsModuleConfig['dname_flg']){
				$label_name = $myts->stripslashesGPC($_POST['label_name']);
				$sql = "UPDATE ".$xoopsDB->prefix('newdb_master');
				$sql.= " SET label='".$label_name."' WHERE label_id='".$lid."'";
				$rs = $xoopsDB->query($sql);
			}
			
			# radio
			$sql = "SELECT * FROM ".$xoopsDB->prefix('newdb_component_master')." WHERE type='2'";
			$rs = $xoopsDB->query($sql);
			$n = $xoopsDB->getRowsNum($rs);
			for($i=0; $i<$n; $i++){
				$cr = 'CR'.$i;
				if(isset($_POST[$cr])){
					$comp_id = intval($_POST[$cr.'_id']);
					$cr_value = $myts -> stripSlashesGPC($_POST[$cr]);
					$cr_value = addslashes($cr_value);

					$sql = "DELETE FROM ".$xoopsDB->prefix('newdb_component');
					$sql.= " WHERE comp_id='".$comp_id."' AND label_id='".$lid."'";
					$rs = $xoopsDB->query($sql);
					
					$sql = "INSERT INTO ".$xoopsDB->prefix('newdb_component');
					$sql.= " VALUES('".$comp_id."','".$lid."','".$cr_value."')";
					$rs = $xoopsDB->query($sql);
				}
			}
			
			# checkbox
			$sql = "SELECT * FROM ".$xoopsDB->prefix('newdb_component_master')." WHERE type='3'";
			$rs = $xoopsDB->query($sql);
			$n = $xoopsDB->getRowsNum($rs);
			for($i=0; $i<$n; $i++){
				$cc = 'CC'.$i;
				if(isset($_POST[$cc])){
					$comp_id = intval($_POST[$cc.'_id']);
					$sql = "DELETE FROM ".$xoopsDB->prefix('newdb_component');
					$sql.= " WHERE comp_id='".$comp_id."' AND label_id='".$lid."'";
					$rs = $xoopsDB->query($sql);
					
					foreach($_POST[$cc] as $key => $value){
						$cc_value = $myts -> stripSlashesGPC($value);
						$cc_value = addslashes($cc_value);
						
						$sql = "INSERT INTO ".$xoopsDB->prefix('newdb_component');
						$sql.= " VALUES ('".$comp_id."','".$lid."','".$cc_value."')";
						$rs = $xoopsDB->query($sql);
					}
				}else{
					$comp_id = intval($_POST[$cc.'_id']);
					$sql = "DELETE FROM ".$xoopsDB->prefix('newdb_component');
					$sql.= " WHERE comp_id='".$comp_id."' AND label_id='".$lid."'";
					$rs = $xoopsDB->query($sql);
				}
			}

			# text
			$sql = "SELECT * FROM ".$xoopsDB->prefix('newdb_component_master')." WHERE type='4'";
			$rs = $xoopsDB->query($sql);
			$n = $xoopsDB->getRowsNum($rs);
			for($i=0; $i<$n; $i++){
				$ct = 'CT'.$i;
				if(isset($_POST[$ct]) && isset($_POST[$ct.'_id'])){
					$comp_id = intval($_POST[$ct.'_id']);
					$ct_value = $myts -> stripSlashesGPC($_POST[$ct]);
					$ct_value = addslashes($ct_value);

					$sql = "DELETE FROM ".$xoopsDB->prefix('newdb_component');
					$sql.= " WHERE comp_id='".$comp_id."' AND label_id='".$lid."'";
					$rs = $xoopsDB->query($sql);
					
					$sql = "INSERT INTO ".$xoopsDB->prefix('newdb_component');
					$sql.= " VALUES('".$comp_id."','".$lid."','".$ct_value."')";
					$rs = $xoopsDB->query($sql);
				}
			}
			$mes = _ND_CONFIG_INFOCHANGE;
			redirect_header(MOD_URL.'/config.php?lid='.$lid.'&mode=info', 1, $mes);
			break;
			
		case 'keyword':
			$kw = new Keyword();
			$kw->setLabel($lid);
			include XOOPS_ROOT_PATH.'/header.php';
			include 'style.css';
			headerBar($lid, $label, $xoopsModuleConfig['dname_flg']);
			echo "<script language='JavaScript' src='tab.js'></script>\n";
			echo "<center><form action='config.php' method='POST'>";
			echo "<table style='width:500px'>";
			echo "<tr><td>".$kw->getCateTB();
			echo "<center><br><input type='submit' class='button' value='submit'></center>";
			echo "</td><td> </td><td>".$kw->getKeyTB()."</td></tr></table>";
			echo "<input type='hidden' name='mode' value='keyword2'>";
			echo "<input type='hidden' name='lid' value='".$lid."'>";
			echo "</form></center>";
			include XOOPS_ROOT_PATH.'/footer.php';
			break;

		case 'keyword2':
			$kw = '';
			foreach($_POST['kw'] as $value){
				$kw.= '['.intval($value).'],';
			}
			$sql = "UPDATE ".$xoopsDB->prefix('newdb_master')." SET keyword='".$kw."'";
			$sql.= " WHERE label_id='".$lid."'";
			$rs = $xoopsDB->query($sql);
			if($rs){
				$mes = _ND_CONFIG_KEYOK;
			}else{
				$mes = _ND_CONFIG_KEYNG;
			}
			redirect_header(MOD_URL.'/config.php?lid='.$lid.'&mode=keyword', 1, $mes);
			break;
			
		case 'thumb':
			include XOOPS_ROOT_PATH.'/header.php';
			include 'style.css';
			headerBar($lid, $label, $xoopsModuleConfig['dname_flg']);
			$img_path = EXTRACT_PATH.'/'.$lid.'/thumbnail';
			$img_url = MOD_URL.'/extract/'.$lid.'/thumbnail';
			
			echo "<center>";
			echo "<div class='title'>"._ND_CONFIG_NEWUP."</div>";
			echo "<div class='title_desc'>"._ND_CONFIG_NEWUP_DESC."</div>";
			echo "<form enctype='multipart/form-data' action='config.php' method='POST'>";
			echo "<table class='list_table' style='width:300px'>";
			echo "<tr><td class='even'><b>Directory</b></td><td><input type='text' name='dir' size='8'></td></tr>";
			echo "<tr><td class='even'><b>File</b></td><td><input type='file' name='userfile'></td></tr>";
			echo "</table>";
			echo "<input type='hidden' name='mode' value='thumb_new'>";
			echo "<input type='hidden' name='lid' value='".$lid."'>";
			echo "<br><input type='submit' class='button' value='submit'>";
			echo "</form>";
	
			echo "<div class='title'>"._ND_CONFIG_FILE_DEL."</div>";
			echo "<div class='title_desc'>"._ND_CONFIG_NOMES."</div></div>";
			echo "<form action='config.php' method='POST'>";
			echo "<table class='list_table'>";
			getThumbName($img_path, $img_url);
			echo "</table>";
			echo "<input type='hidden' name='mode' value='thumb_del'>";
			echo "<input type='hidden' name='lid' value='".$lid."'>";
			echo "<br><input type='submit' class='button' value='submit'>";
			echo "</form>";
			echo "</center>";
			include XOOPS_ROOT_PATH.'/footer.php';
			break;
			
		case 'thumb_del':
			foreach($_POST['thumb'] as $value){
				if(file_exists($value)){
					unlink($value);
				}
			}
			$mes = _ND_CONFIG_THUMBDEL;
			redirect_header(MOD_URL.'/config.php?lid='.$lid.'&mode=thumb', 1, $mes);
			break;
			
		case 'thumb_new':
			$mes = '';
			if(!empty($_POST['dir'])){
				$dir = EXTRACT_PATH.'/'.$lid.'/thumbnail/'.$myts->stripSlashesGPC($_POST['dir']);
				if(!is_dir($dir) && !mkdir($dir, 0777)){
					$mes = _ND_DIR_FALSE;
				}
			}else{
				$mes = _ND_CONFIG_INPUTDIR;
			}
			if(!empty($mes)) redirect_header(MOD_URL.'/config.php?lid='.$lid.'&mode=thumb', 1, $mes);
			
			if(is_uploaded_file($_FILES['userfile']['tmp_name'])){
				$fname = $_FILES['userfile']['name'];
				$tmp_name = $_FILES['userfile']['tmp_name'];

				if(preg_match("/.*(\.gif)$/i", $fname) || preg_match("/.*(\.bmp)$/i", $fname) || 
				   preg_match("/.*(\.jpg)$/i", $fname) || preg_match("/.*(\.jpeg)$/i", $fname)||
				   preg_match("/.*(\.png)$/i", $fname)){
					
					if(move_uploaded_file($tmp_name, $dir.'/'.$fname)){
						$mes = _ND_CONFIG_IMGUPOK;
					}else{
						$mes = _ND_CONFIG_IMGUPNG;
					}
				}else{
					$mes = _ND_CONFIG_UPSUF;
				}
			}else{
				$mes = _ND_CONFIG_NFILESELECT;
			}
			redirect_header(MOD_URL.'/config.php?lid='.$lid.'&mode=thumb', 1, $mes);			
			break;
		
		case 'delete':
			include XOOPS_ROOT_PATH.'/header.php';
			include 'style.css';
			headerBar($lid, $label, $xoopsModuleConfig['dname_flg']);
			echo "<center>";
			echo "<div style='color:red'>"._ND_CONFIG_CONFIRM."</div><br><br>"._ND_CONFIG_CONFIRM2;
			echo "<form action='config.php' method='POST'>";
			echo "<input type='hidden' name='mode' value='do_ddel'>";
			echo "<input type='hidden' name='lid' value='".$lid."'>";
			echo "<br><input type='submit' class='button' value='YES'>";
			echo "</form></center>";
			include XOOPS_ROOT_PATH.'/footer.php';
			break;
		case 'do_ddel':		
			
			$sql = "DELETE FROM ".$xoopsDB->prefix('newdb_master')." WHERE label_id='".$lid."'";
			$rs = $xoopsDB->query($sql);
			
			$sql = "DELETE FROM ".$xoopsDB->prefix('newdb_component')." WHERE label_id='".$lid."'";
			$rs = $xoopsDB->query($sql);
			
			# file
			$sql = "DELETE FROM ".$xoopsDB->prefix('newdb_item')." WHERE label_id='".$lid."'";
			$rs = $xoopsDB->query($sql);

			# bookmark
			$sql = "DELETE FROM ".$xoopsDB->prefix('newdb_bookmark_file')." WHERE label_id='".$lid."'";
			$rs = $xoopsDB->query($sql);

			# link
			$sql = "DELETE FROM ".$xoopsDB->prefix('newdb_link')." WHERE label_id='".$lid."'";
			$rs = $xoopsDB->query($sql);
			
			$sql = "DELETE FROM ".$xoopsDB->prefix('newdb_link')." WHERE type='1' AND name='".$lid."'";
			$rs = $xoopsDB->query($sql);

			# comment
			$sql = "SELECT * FROM ".$xoopsDB->prefix('newdb_comment_topic')." WHERE label_id='".$lid."'";
			$rs = $xoopsDB->query($sql);
			while($row = $xoopsDB->fetchArray($rs)){
				$com_id = $row['com_id'];
				$sql = "SELECT * FROM ".$xoopsDB->prefix('newdb_comment')." WHERE com_id='".$com_id."'";
				$rs2 = $xoopsDB->query($sql);
				while($row2 = $xoopsDB->fetchArray($rs2)){
					$pcom_id = $row['com_id'];
					$sql = "DELETE FROM ".$xoopsDB->prefix('newdb_comment')." WHERE pcom_id='".$pcom_id."'";
					$rs3 = $xoopsDB->query($sql);
					
					$sql = "DELETE FROM ".$xoopsDB->prefix('newdb_comment')." WHERE com_id='".$pcom_id."'";
					$rs3 = $xoopsDB->query($sql);
				}
			}
			$sql = "DELETE FROM ".$xoopsDB->prefix('newdb_comment_topic')." WHERE label_id='".$lid."'";
			$rs = $xoopsDB->query($sql);
			
			include 'class/regcopy.php';
			$fcopy = new RegCopy();
			$fcopy->delDirectory(EXTRACT_PATH.'/'.$lid);

			redirect_header(MOD_URL, 1, _ND_CONFIG_DEL);
			break;
	}
	
	function getThumbName($path, $url){
	  if($handle = opendir($path)){
	    while(false !== $file = readdir($handle)){
	      if($file != "." && $file != ".."){
					if(is_dir($path.'/'.$file)){
						echo "<tr><th colspan='3'>".$file." directory</th></tr>";
						getThumbName($path.'/'.$file, $url.'/'.$file);
					
					}else{
						echo "<tr><td style='width:5%; text-align:center' class='even'>";
						echo "<input type='checkbox' name='thumb[]' value='".$path."/".$file."'></td>";
						echo "<td style='width:50%; text-align:center'><img src='".$url."/".$file."'></td>";
						echo "<td>".$file."</td></tr>";
					}
				}
			}
		}
		closedir($handle);
	}

	function headerBar($lid, $label, $dname_flg){
		echo "<div class='h' style='margin-top:0'>Config: <a href='detail.php?id=".$lid."'>";
		if($dname_flg){
			echo $label;
		}else{
			echo $lid;
		}
		echo "</a></div>";
		echo "<div style='text-align:right; margin-bottom:30px;'>";
		echo "<a href='config.php?lid=".$lid."&mode=info'>"._ND_CONFIG_INFO."</a> | ";
		echo "<a href='config.php?lid=".$lid."&mode=keyword'>"._ND_CONFIG_KEYWORD."</a> | ";
		echo "<a href='config.php?lid=".$lid."&mode=thumb'>"._ND_CONFIG_THUMB."</a> | ";
		echo "<a href='config.php?lid=".$lid."&mode=delete'>"._ND_CONFIG_DELETE."</a>";
		echo "</div>";
	}
?>