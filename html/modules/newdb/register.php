<?php
	include 'header.php';
	include 'class/tarextractor.php';
	include 'class/regcopy.php';
	include 'class/regdatabase.php';
	include 'class/keyword.php';
	include 'class/commentpost.php';
	include 'include/getreglist.php';
	include 'include/checkkeydir.php';
	
	$perm = 0;
	if($uid){
		foreach($xoopsModuleConfig['reg_perm'] as $group_id){
			$sql = "SELECT uid FROM ".$xoopsDB->prefix('groups_users_link');
			$sql.= " WHERE groupid='".$group_id."'";
			$rs = $xoopsDB->query($sql);
			while($row = $xoopsDB->fetchArray($rs)){
				if($row['uid'] == $uid) $perm = 1;
			}
		}
	}
	if(!$perm) redirect_header(MOD_URL, 2, _ND_NACCESS2);

	
	$error = '';
	$data_name = '';
	
	if(isset($_POST['method'])){
		$method = $myts->stripSlashesGPC($_POST['method']);
	}else{
		$method = '';
	}
	
	switch($method){

		case 'do_reg':

			if($xoopsModuleConfig['dname_flg']){
				if(empty($_POST['name'])){
					redirect_header(MOD_URL.'/register.php', 2, _ND_REG1);
				}else{
					$label_name = $myts->stripSlashesGPC($_POST['name']);
				}
			}else{
				$label_name = ' --- ';
			}

			# Register label
			$rdb = new RegDatabase();
			if($rdb->setRegLabel($label_name, EXTRACT_PATH, $uid)){
				if(!$rdb -> regNewLabel()) $error = $rdb->error();
			}else{
				$error = $rdb->error();
			}
			if($error) redirect_header(MOD_URL.'/register.php', 2, $error);
			$label_id = $rdb->labelid;
						
		
			# Data upload 
			if(isset($_POST['uploaded_data'])){
				$data_name = $myts->stripSlashesGPC($_POST['uploaded_data']);

			}elseif(isset($_POST['use_upload'])){
				if(is_uploaded_file($_FILES['userfile']['tmp_name'])){
					$fname = $_FILES['userfile']['name'];
					$tmp_name = $_FILES['userfile']['tmp_name'];
									
					if(filesize($tmp_name) > MAX_UPLOAD_SIZE){
						$error = _ND_REG2;	
					
					}elseif(!preg_match("/.*(\.tar)$/i", $fname)){
						$error = _ND_REG4;
					}
				}
				
				if($error) redirect_header(MOD_URL.'/register.php', 2, $error);
				$data_name = $fname;
				move_uploaded_file($tmp_name, UPLOAD_PATH.'/'.$data_name);
			}

			if(isset($_POST['use_upload']) || isset($_POST['uploaded_data'])){
				if($xoopsModuleConfig['use_suffix']){
					$suf_array = explode('|', $xoopsModuleConfig['suffix']);
					$suf = '';
					for($i=0; $i<count($suf_array); $i++){
						if(empty($suf_array[$i])){
							continue;
						}else{
							$suf.= '|';
						}
						$suf.= strtolower($suf_array[$i]).'|'.strtoupper($suf_array[$i]);
					}
				}else{
					$suf = '';
				}
				# extract
				if(preg_match("/.*(\.tar)$/i", $data_name)){
					$archive = UPLOAD_PATH.'/'.$data_name;
					$tar = new TarExtractor();
					$tar->file_limit = MAX_UPLOAD_SIZE;
					if($tar->setArchive($archive, EXTRACT_PATH)){
						if($tar->doRegExtract($label_id, $suf)){
							unlink($archive);
						}else{
							$error = $tar->error();
						}
					}else{
						$error = $tar->error();
					}
	
				# copy
				}elseif(is_dir(UPLOAD_PATH.'/'.$xoopsUser->uname().'/'.$data_name)){
				  $from_path = UPLOAD_PATH.'/'.$xoopsUser->uname().'/'.$data_name;
					$fcopy = new RegCopy();
					if($fcopy->setPath($from_path, EXTRACT_PATH)){
						if($fcopy->doRegCopy($suf, $label_id)){
							$fcopy->delDirectory($from_path);
						}else{
							$error = $fcopy->error();
						}
					}else{
						$error = $fcopy->error();
					}
				}
			}
			if($error) redirect_header(MOD_URL.'/register.php', 2, $error);
			checkKeyDir($label_id);


			# extension program for advanced user
			#include 'extension/exregister.php';

	
			# Register files
			if(!$rdb -> regNewItems()){
				$error = $rdb->error();
				redirect_header(MOD_URL.'/register.php', 2, $error);
			}

			# Register custom field (newdb_component)
			# custom text
			$sql = "SELECT * FROM ".$xoopsDB->prefix('newdb_component_master')." WHERE type='4'";
			$rs = $xoopsDB->query($sql);
			$n = $xoopsDB->getRowsNum($rs);

			for($i=0; $i<$n ;$i++){
				$ct = 'CT'.$i;
				
				if(isset($_POST[$ct]) && isset($_POST[$ct.'_id'])){
					$comp_id = intval($_POST[$ct.'_id']);
					
					$ct_value = $myts -> stripSlashesGPC($_POST[$ct]);
					$ct_value = addslashes($ct_value);
					$sql = "INSERT INTO ".$xoopsDB->prefix('newdb_component');
					$sql.= " VALUES('".$comp_id."', '".$label_id."', '".$ct_value."')";
					$rs = $xoopsDB->query($sql);	
				}
			}
			
			# custom checkbox
			$sql = "SELECT * FROM ".$xoopsDB->prefix('newdb_component_master')." WHERE type='3'";
			$rs = $xoopsDB->query($sql);
			$n = $xoopsDB->getRowsNum($rs);

			for($i=0; $i<$n ;$i++){
				$cc = 'CC'.$i;
				if(isset($_POST[$cc])){
					$comp_id = intval($_POST[$cc.'_id']);

					foreach($_POST[$cc] as $key => $value){
						$cc_value = $myts -> stripSlashesGPC($value);
						$cc_value = addslashes($cc_value);
						
						$sql = "INSERT INTO ".$xoopsDB->prefix('newdb_component');
						$sql.= " VALUES('".$comp_id."', '".$label_id."', '".$cc_value."')";
						$rs = $xoopsDB->query($sql);
					}
				}
			}

			# custom radio
			$sql = "SELECT * FROM ".$xoopsDB->prefix('newdb_component_master')." WHERE type='2'";
			$rs = $xoopsDB->query($sql);
			$n = $xoopsDB->getRowsNum($rs);

			for($i=0; $i<$n ;$i++){
				$cr = 'CR'.$i;
				if(isset($_POST[$cr])){
					$comp_id = intval($_POST[$cr.'_id']);
					$cr_value = $myts -> stripSlashesGPC($_POST[$cr]);
					$cr_value = addslashes($cr_value);
					
					$sql = "INSERT INTO ".$xoopsDB->prefix('newdb_component');
					$sql.= " VALUES('".$comp_id."', '".$label_id."', '".$cr_value."')";
					$rs = $xoopsDB->query($sql);
				}
			}

			# Register keyword
			$kw = '';
			if(!empty($_POST['kw'])){
				foreach($_POST['kw'] as $key => $value){
					$kw.="[".$value."],";
				}
			}
			$sql = "UPDATE ".$xoopsDB->prefix('newdb_master');
			$sql.= " SET keyword='".$kw."' WHERE label_id='".$label_id."'";
			$rs = $xoopsDB->query($sql);

			# Register comment
			if(!$xoopsModuleConfig['acom_flg']){
				$com = '';
			}else{
				$com = $_POST['comment'];
			}
			$cp = new CommentPost();
			$cp->setMethod('new');
			$cp->setSubject(_ND_REG5);
			$cp->setMessage($com);
			$cp->setUid($uid);
			$cp->setLid($label_id);
			$cp->setType('auth');
			$cp->register();

			redirect_header(MOD_URL.'/detail.php?id='.$label_id, 2, _ND_REG7);
			break;


		/**
		 * register top
		 */
		default:
			include XOOPS_ROOT_PATH.'/header.php';
			include 'style.css';
			echo "<script language='JavaScript' src='tab.js'></script>\n";
			echo "<center>\n";
			echo "<form enctype='multipart/form-data' action='register.php' method='POST'>\n";

			# datasheet
			echo "<div class='title' style='margin-top:0'>"._ND_REG8."</div>\n";
			echo "<div class='title_desc'>"._ND_REG9."</div>\n";

			echo "<table class='list_table' style='width:500px;'>\n";
			echo "<tr><th colspan='2'>"._ND_REG10."</th></tr>\n";

			$sql = "SELECT * FROM ".$xoopsDB->prefix('newdb_component_master')." WHERE name='Data Name'";
			$rs = $xoopsDB->query($sql);
			$row = $xoopsDB->fetchArray($rs);
			
			if($xoopsModuleConfig['dname_flg']){
				echo "<tr><td style='width:120px'><b>".$row['tag']."</b></td>";
				echo "<td><input type='text' name='name' style='width:180px'></td></tr>\n";
			}	
			if($xoopsModuleConfig['acom_flg']){
				echo "<tr><td><b>"._ND_REG12."</b></td>";
				echo "<td><textarea name='comment' style='width:98%; height:120px'></textarea></td></tr>\n";
			}
			
			# type 4:text, 3:checkbox, 2:radio
			for($i=4; $i>1; $i--){
					
				$sql = "SELECT * FROM ".$xoopsDB->prefix('newdb_component_master');
				$sql.= " WHERE type='".$i."' ORDER BY sort";
				$rs = $xoopsDB->query($sql);
				$type_id=0;
				while($row = $xoopsDB->fetchArray($rs)){
					$comp_id = $row['comp_id'];

					echo "<tr><td><b>".htmlspecialchars($row['tag'])."</b><br>";
					echo htmlspecialchars($row['exp'])."</td><td>\n";

					if($row['type'] == '2'){
						$svalue = explode(',', $row['select_value']);
						for($j=0; $j<count($svalue); $j++){
							($svalue[$j] == $row['default_value']) ? $check=' checked' : $check='';
							echo "<input type='radio' name='CR".$type_id."' value='".$svalue[$j]."' ".$check.">";
							echo $svalue[$j]."&nbsp;&nbsp;\n";
						}
						echo "<input type='hidden' name='CR".$type_id."_id' value='".$comp_id."'>";

					}elseif($row['type']=='3'){
						$svalue = explode(',', $row['select_value']);
						for($j=0; $j<count($svalue); $j++){
							echo "<input type='checkbox' name='CC".$type_id."[]' value='".$svalue[$j]."'>";
							echo $svalue[$j]."&nbsp;&nbsp;\n";
						}
						echo "<input type='hidden' name='CC".$type_id."_id' value='".$comp_id."'>";
				
					}elseif($row['type']=='4'){
						echo "<input type='text' name='CT".$type_id."' style='width:180px'>\n";
						echo "<input type='hidden' name='CT".$type_id."_id' value='".$comp_id."'>";
					}

					echo "</td></tr>\n";
					$type_id ++;
				}
			}

			echo "<tr><td><b>"._ND_REG13."</b></td>";
			echo "<td><a href=\"javascript:seltab('box', 'head', 10, 1)\">"._ND_REG14."</a>";
			if($xoopsModuleConfig['use_datafunc']){
				echo " / <a href=\"javascript:seltab('box', 'head', 10, 2)\">"._ND_REG15."</a>";
			}
			echo "</td></tr></table>";

			# keyword
			$kw = new Keyword();
			echo "<div id='box1' style='display:none'>\n";
			echo "<div class='title'>"._ND_REG16."</div>\n";
			echo "<div class='title_desc'>"._ND_REG17."</div>\n";

			echo "<table style='width:520px;'>";
			echo "<tr><td>".$kw->getCateTB()."</td><td></td><td>".$kw->getKeyTB()."</td></tr>";
			echo "</table>";
			echo "</div>";

			if($xoopsModuleConfig['use_datafunc']){
				# data file
				$uplimit = floor(MAX_UPLOAD_SIZE/1000/1000)." MB";
				$list = array();
				getRegList(UPLOAD_PATH.'/'.$xoopsUser->uname(), $list);
				uasort($list, 'strcmp');
	
				echo "<div id='box2' style='display:none'>";
				echo "<div class='title'>"._ND_REG18."</div>\n";
				echo "<div class='title_desc'>"._ND_REG19." (limit ".$uplimit.")</div>\n";
				
				echo "<table class='list_table' style='width:300px'>";
				echo "<tr><th colspan='2'>"._ND_REG20."</th></tr>";
				echo "<tr><td class='even' style='width:30px'>";
				echo "<input type='checkbox' name='use_upload' value='y'>";
				echo "<td style='text-align:center'>";
				echo "<input type='file' name='userfile'>";
				echo "</td></tr></table><br><br>";
		
				if(count($list)){
					echo "<table class='list_table' style='width:300px'>";
					echo "<tr><th colspan='3'>"._ND_REG21."</th></tr>";
					echo "</table>";
		
					foreach($list as $name => $size){
						echo "<table class='list_table' style='width:300px'><tr>";
						echo "<td class='even' style='width:30px'>";
						echo "<input type='radio' name='uploaded_data' value='".$name."'></td>";
						echo "<td>$name</td>";
						echo "<td class='even' style='width:100px'>$size kb</td>";
						echo "</tr></table>";
					}
				}
				echo "</div>";
			}
			
			# submit
			echo "<div class='title'>"._ND_REG22."</div>\n";
			echo "<div class='title_desc'>"._ND_REG23."</div>\n";
			echo "<input type='submit' class='button' value='submit'>&nbsp;&nbsp;";
			echo "<input type='reset' class='button' value='reset'>";
			echo "<input type='hidden' name='method' value='do_reg'>";
			echo "</form></center>";
		
			include XOOPS_ROOT_PATH.'/footer.php';
			break;	
	}	
?>