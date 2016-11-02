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
	
	# check input data
	$required='';
	if($mode == 'info2'){
		$sql = "SELECT * FROM ".$xoopsDB->prefix('newdb_component_master');
		
		# data name		
		$dname = '';
		if($xoopsModuleConfig['dname_flg']){
		 	$rs = $xoopsDB->query($sql." WHERE comp_id='2'");
			$row = $xoopsDB->fetchArray($rs);
			if(empty($_POST['label_name'])){
				$required .= $row['tag'].' '._ND_REG26.'<br>';
			}else{
				$dname = $_POST['label_name'];
				if($row['textmax'] && strlen($dname) > $row['textmax']){
					$required.=$row['tag'].' '._ND_REG27.'<br>';
				}
			}
		}

		# text check
	 	$rs = $xoopsDB->query($sql." WHERE type='4'");
		$n = $xoopsDB->getRowsNum($rs);
		for($i=0;$i<$n;$i++){
			(isset($_POST['CT'.$i])) ? $ct[$i] = $_POST['CT'.$i] : $ct[$i] = '';
			$rs = $xoopsDB->query($sql." WHERE comp_id='".$_POST['CT'.$i.'_id']."'");
			$row = $xoopsDB->fetchArray($rs);
			if($row['nonull'] && $ct[$i]==''){
				$required.=$row['tag'].' '._ND_REG26.'<br>';
			}
			if($row['textmax'] && strlen($ct[$i]) > $row['textmax']){
				$required.=$row['tag'].' '._ND_REG27.'<br>';
			}
		}

		# checkbox check
	 	$rs = $xoopsDB->query($sql." WHERE type='3'");
		$n = $xoopsDB->getRowsNum($rs);
		for($i=0;$i<$n;$i++){
			(isset($_POST['CC'.$i])) ? $cc[$i] = $_POST['CC'.$i] : $cc[$i] = '';
			$rs = $xoopsDB->query($sql." WHERE comp_id='".$_POST['CC'.$i.'_id']."'");
			$row = $xoopsDB->fetchArray($rs);

			$flg = 0;
			if(is_array($cc[$i])){
				foreach($cc[$i] as $key => $value){
					if(!empty($value)) $flg = 1;
				}
			}
			if($row['nonull'] && !$flg){
				$required.=$row['tag'].' '._ND_REG26.'<br>';
			}
		}
				
		# radio
	 	$rs = $xoopsDB->query($sql." WHERE type='2'");
		$n = $xoopsDB->getRowsNum($rs);
		for($i=0;$i<$n;$i++){
			(isset($_POST['CR'.$i])) ? $cr[$i] = $_POST['CR'.$i] : $cr[$i] = '';
			$rs = $xoopsDB->query($sql." WHERE comp_id='".$_POST['CR'.$i.'_id']."'");
			$row = $xoopsDB->fetchArray($rs);

			if($row['nonull'] && empty($cr[$i])){
				$required.=$row['tag'].' '._ND_REG26.'<br>';
			}
		}

		
		# select
	 	$rs = $xoopsDB->query($sql." WHERE type='5'");
		$n = $xoopsDB->getRowsNum($rs);
		for($i=0;$i<$n;$i++){
			if(isset($_POST['CS'.$i])){
				$cs[$i] = $_POST['CS'.$i];
			}
		}
		
		if($required){
			$mode = 'info';
		}
	}
	
	switch($mode){
		
		# Change information
		case 'info':
			$const_mk = "<span style='color:red'>* </span>";

			include XOOPS_ROOT_PATH.'/header.php';
			include 'style.css';
			headerBar($lid, $label, $xoopsModuleConfig['dname_flg']);

			if(!empty($required)){
				echo "<div style='color:red; margin:20px'>".$required."</div>";
			}else{
				$required='';
			}

			echo "<center><form action='config.php' method='POST'>";
			echo "<table class='list_table' style='width:550px;'>\n";
			echo "<tr style='text-align:center'><th style='width:140px'>"._ND_REG28."</th><th>"._ND_REG29."</th></tr>\n";

			# label_name
			if($xoopsModuleConfig['dname_flg']){
				$sql = "SELECT * FROM ".$xoopsDB->prefix('newdb_component_master');
				$sql.= " WHERE name='Data Name'";
				$rs = $xoopsDB->query($sql);
				$row = $xoopsDB->fetchArray($rs);
				
				if($required) $label = $dname;
				echo "<tr><td class='even'>".$const_mk."<b>".$row['tag']."</b></td><td>";
				echo "<input type='text' style='width:180px' name='label_name' value='".$label."'>";
				if($row['textmax']) echo "&nbsp;&nbsp;&nbsp;".$row['textmax']." "._ND_REG30;
				echo "</td></tr>";
			}

			$sql = "SELECT * FROM ".$xoopsDB->prefix('newdb_component_master');
			$sql.= " WHERE type <> '1' ORDER BY sort";
			$rs = $xoopsDB->query($sql);
			
			$sql2 = "SELECT * FROM ".$xoopsDB->prefix('newdb_component')." WHERE label_id='".$lid."'";
				
			# ID number for custom field (custom radio, custom check...)
			$custom_id = array('CR'=>0, 'CC'=>0, 'CT'=>0, 'CS'=>0);
				
			while($row = $xoopsDB->fetchArray($rs)){

				$comp_id = $row['comp_id'];
				$textmax = $row['textmax'];
				$nonull = $row['nonull'];

				$rs2 = $xoopsDB->query($sql2." AND comp_id='".$comp_id."'");

				echo "<tr><td style='width:120px' class='even'>";

				if($nonull && ($row['type'] >= '2' && $row['type'] <= '4')){
					echo $const_mk;
				}

				echo "<b>".htmlspecialchars($row['tag'])."</b><br>";
				echo htmlspecialchars($row['exp'])."</td><td>\n";

				# CR(radio)
				if($row['type'] == '2'){
						if(!$required) $row2 = $xoopsDB->fetchArray($rs2);
						$svalue = explode(',', $row['select_value']);
						for($j=0; $j<count($svalue); $j++){
							$check='';
							if($required){
								if($svalue[$j] == $cr[$custom_id['CR']]) $check=' checked';
							}else{
								if($svalue[$j] == $row2['value']) $check=' checked';
							}
							echo "<input type='radio' name='CR".$custom_id['CR']."' value='".$svalue[$j]."' ".$check.">";
							$tmp = str_replace('{', '<img src="images/admin/', $svalue[$j]);
							$tmp = str_replace('}', '">', $tmp);
							echo $tmp."&nbsp;&nbsp;\n";
						}
						echo "<input type='hidden' name='CR".$custom_id['CR']."_id' value='".$comp_id."'>";
						$custom_id['CR']++;
			
				# CC(check)
				}elseif($row['type']=='3'){

					if(!$required){
						$v = array();
						while($row2 = $xoopsDB->fetchArray($rs2)){
							$v[] = $row2['value'];
						}
					}

					$svalue = explode(',', $row['select_value']);
					for($j=0; $j<count($svalue); $j++){
						$checked='';
						if($required){
							if(!empty($cc[$custom_id['CC']])){
								if(in_array($svalue[$j], $cc[$custom_id['CC']])) $checked = ' checked';
							}
						}else{
							if(in_array($svalue[$j], $v)) $checked=' checked';
						}
						echo "<input type='checkbox' name='CC".$custom_id['CC']."[]' value='".$svalue[$j]."' ".$checked.">";
						$tmp = str_replace('{', '<img src="images/admin/', $svalue[$j]);
						$tmp = str_replace('}', '">', $tmp);
						echo $tmp."&nbsp;&nbsp;\n";
					}
					echo "<input type='hidden' name='CC".$custom_id['CC']."_id' value='".$comp_id."'>";
					$custom_id['CC']++;
				
				# CT(text)
				}elseif($row['type']=='4'){
				
					if(!$required){
						$row2 = $xoopsDB->fetchArray($rs2);
						$ct[$custom_id['CT']] = $row2['value'];
					}
					if($textmax){
						echo "<input type='text' name='CT".$custom_id['CT']."' style='width:180px' value='".$ct[$custom_id['CT']]."'>\n";
						echo "&nbsp;&nbsp;&nbsp;".$textmax." "._ND_REG30;
					}else{
						echo "<textarea name='CT".$custom_id['CT']."' style='width:98%; height:60px'>".$ct[$custom_id['CT']]."</textarea>\n";
					}
					echo "<input type='hidden' name='CT".$custom_id['CT']."_id' value='".$comp_id."'>";
					$custom_id['CT']++;
				
				# CS(select)
				}elseif($row['type'] == '5'){
						if(!$required) $row2 = $xoopsDB->fetchArray($rs2);
						$svalue = explode(',', $row['select_value']);
						echo "<select name='CS".$custom_id['CS']."'>";
						for($j=0; $j<count($svalue); $j++){
							$check='';
							if($required){
								if($svalue[$j] == $cs[$custom_id['CS']]) $check=' selected';
							}else{
								if($svalue[$j] == $row2['value']) $check=' selected';
							}
							echo "<option value='".$svalue[$j]."' ".$check.">".$svalue[$j]."</option>\n";
						}
						echo "</select>";
						echo "<input type='hidden' name='CS".$custom_id['CS']."_id' value='".$comp_id."'>";
						$custom_id['CS']++;
				}

				echo "</td></tr>\n";
			}			
			echo "</table>";
			echo "<br><input type='submit' class='button' value='submit'>";
			echo "<input type='hidden' name='mode' value='info2'>";
			echo "<input type='hidden' name='lid' value='".$lid."'>";
			echo "</form></center>";
			include XOOPS_ROOT_PATH.'/footer.php';
			break;
			
		# Execute change information
		case 'info2':
			# label_name
			if($xoopsModuleConfig['dname_flg']){
				$label_name = $myts->stripslashesGPC($dname);
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
			
			# select
			$sql = "SELECT * FROM ".$xoopsDB->prefix('newdb_component_master')." WHERE type='5'";
			$rs = $xoopsDB->query($sql);
			$n = $xoopsDB->getRowsNum($rs);
			for($i=0; $i<$n; $i++){
				$cs = 'CS'.$i;
				if(isset($_POST[$cs])){
					$comp_id = intval($_POST[$cs.'_id']);
					$cs_value = $myts -> stripSlashesGPC($_POST[$cs]);
					$cs_value = addslashes($cs_value);

					$sql = "DELETE FROM ".$xoopsDB->prefix('newdb_component');
					$sql.= " WHERE comp_id='".$comp_id."' AND label_id='".$lid."'";
					$rs = $xoopsDB->query($sql);
					
					$sql = "INSERT INTO ".$xoopsDB->prefix('newdb_component');
					$sql.= " VALUES('".$comp_id."','".$lid."','".$cs_value."')";
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
			echo "<table class='list_table' style='width:600px'>";
			echo "<tr><td class='even'><b>"._ND_REG_THUMB1."</b></td><td><input type='text' name='dir' size='8'></td></tr>";
			echo "<tr><td class='even'><b>"._ND_REG_THUMB2."</b></td><td><input type='file' name='userfile'></td></tr>";
			echo "<tr><td
			class='even'><b>"._ND_REG_THUMB3."</b></td><td><textarea
			name='caption' style='width:98%;height:120px' wrap='hard'></textarea></td></tr>";
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
			        $caption_path = str_replace('thumbnail', 'caption', $value);
                                $dot_pos = strrpos($caption_path, '.');
                                $caption_file = substr($caption_path, 0, $dot_pos).'.txt';
				unlink($caption_file);
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

			// caption
			$mes = '';
			if (!empty($_POST['caption'])) {
			   $caption_path = EXTRACT_PATH.'/'.$lid.'/caption/'.$myts->stripSlashesGPC($_POST['dir']);
	
			   if (!is_dir($caption_path) && !mkdir($caption_path, 0777)){
			      $mes = _ND_DIR_FALSE;
			   }

                           # image file -> caption file: ex. file.jpg -> file.txt

                           $dot_pos = strrpos($fname, '.');
                           $caption_file = substr($fname, 0, $dot_pos).'.txt';
  		           $caption_path = $caption_path."/$caption_file";

			   $fp = fopen($caption_path, 'w');
			   fwrite($fp,$_POST['caption']);
			   fclose($fp);
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
						echo "<tr><th colspan='4'>".$file." directory</th></tr>";
						getThumbName($path.'/'.$file, $url.'/'.$file);
					
					}else{
						echo "<tr><td style='width:5%; text-align:center' class='even'>";
						echo "<input type='checkbox' name='thumb[]' value='".$path."/".$file."'></td>";
						echo "<td style='width:50%; text-align:center'><img src='".$url."/".$file."'></td>";
						echo "<td>".$file."</td>";

						$caption_path=str_replace('thumbnail','caption',$path);
						$dot_pos = strrpos($file, '.');
                                                $caption_file = substr($file, 0, $dot_pos).'.txt';
					        $caption_path = $caption_path."/$caption_file";

					        if (file_exists($caption_path)) {
					           $fp = fopen($caption_path, 'r');
					          
                                                   $caption = '';
						   while( !feof($fp) ) {
					              $caption = $caption.fgets($fp).'<br>';;
					           }
					           fclose($fp);
                                                } else {
						   $caption = '';
						}
						echo "<td>".$caption."</td></tr>";
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