<?php
	include 'header.php';
	include 'class/dataedit.php';

	$mes='';
	if(!$xoopsModuleConfig['use_datafunc']){
		$mes = _ND_NACCESS;
	}

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
		}else{
			$mes = _ND_NODATA;
		}
	}
	if(!empty($mes)) redirect_header(XOOPS_URL, 1, $mes);
	$de = new DataEdit($uid, $lid, $label);

/*
	$sql = "SELECT * FROM ".$xoopsDB->prefix('newdb_master')." WHERE label_id='".$lid."'";
	$rs = $xoopsDB->query($sql);
	$row = $xoopsDB->fetchArray($rs);
	if($row['author'] == $uid){
		$isadmin = 1;
	}	
*/

	if(isset($_GET['mode'])){
		$mode = $_GET['mode'];
	}elseif(isset($_POST['mode'])){
		$mode = $_POST['mode'];
	}else{
		$mode = 'add';
	}
	
	switch($mode){

		case 'add':
			include 'include/getreglist.php';
			$uplimit = floor(MAX_UPLOAD_SIZE/1000/1000)." MB";
			$list = array();
			getRegList(UPLOAD_PATH.'/'.$xoopsUser->uname(), $list, 1);
			uasort($list, 'strcmp');

			include XOOPS_ROOT_PATH.'/header.php';
			include 'style.css';
			headerBar($lid, $label, $xoopsModuleConfig['dname_flg']);
			echo "<center>";
			echo "<div class='title'>"._ND_EDATA_MKDIR."</div>\n";
			echo "<div class='title_desc'></div>\n";
			echo "<form method='POST' action='edata.php'>";
			echo "<center>";
			echo "<table class='list_table' style='width:400px; margin-top:20px'>";
			echo "<tr><td class='even'><b>"._ND_EDATA_UPPERD."</b></td>";
			echo "<td><select name='dir'>".$de->getDirlist()."</select></td></tr>";
			echo "<tr><td class='even'><b>"._ND_EDATA_DNAME."</b></td>";
			echo "<td><input type='text' name='newdir'></td></tr></table>";
			echo "<input type='hidden' name='mode' value='addir'>";
			echo "<input type='hidden' name='lid' value='".$lid."'><br>";
			echo "<input type='submit' value='submit' class='button'>";
			echo "</form>";

			echo "<div class='title'>"._ND_EDATA_ADDFILE."</div>\n";
			echo "<div class='title_desc'>"._ND_EDATA_ADDFILE_DESC."</div>\n";
			echo "<form enctype='multipart/form-data' action='edata.php' method='POST'>\n";
			echo "<table class='list_table' style='width:400px'>";
			echo "<tr><td class='even' style='width:170px'><b>"._ND_EDATA_TARGETD."</b></td>";
			echo "<td><select name='dir'>".$de->getDirlist()."</select></td></tr>";
			echo "<tr><td class='even'><b>"._ND_EDATA_FILE."</b></td>";
			echo "<td><input type='file' name='userfile'></td></tr></table>";
			echo "<input type='hidden' name='mode' value='add_new'>";
			echo "<input type='hidden' name='lid' value='".$lid."'><br>";
			echo "<input type='submit' value='submit' class='button'>";
			echo "</form>";

			echo "<div class='title'>"._ND_EDATA_FD."</div>\n";
			echo "<div class='title_desc'>"._ND_REG19." (limit ".$uplimit.")</div>\n";
			echo "<form enctype='multipart/form-data' action='edata.php' method='POST'>\n";
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
			echo "<input type='hidden' name='mode' value='add_new2'>";
			echo "<input type='hidden' name='lid' value='".$lid."'><br>";
			echo "<input type='submit' value='submit' class='button'>";
			echo "</form>";		
			echo "<center>";
			include XOOPS_ROOT_PATH.'/footer.php';
			break;
		
		case 'addir':
			if(empty($_POST['newdir'])){
				$mes = _ND_EDATA_NDIRNAME;
			}else{
				$newdir = $myts->stripSlashesGPC($_POST['newdir']);
				$dir = intval($_POST['dir']);
	
				if($dir == 0){
					$path = '';
					$ab_path = EXTRACT_PATH.'/'.$lid.'/data/'.$newdir;
				}else{
					$sql = "SELECT * FROM ".$xoopsDB->prefix('newdb_item');
					$sql.= " WHERE item_id='".$dir."' AND type='dir'";
					$rs = $xoopsDB->query($sql);
					if($xoopsDB->getRowsNum($rs) > 0){
						$row = $xoopsDB->fetchArray($rs);
						if(!empty($row['path'])){
							$path = $row['path'].'/'.$row['name'];
						}else{
							$path = $row['name'];
						}
						$ab_path = EXTRACT_PATH.'/'.$lid.'/data/'.$path.'/'.$newdir;
					}
				}

				if(!empty($ab_path) && !is_dir($ab_path)){
					if(mkdir($ab_path, 0777)){
						$sql = "INSERT INTO ".$xoopsDB->prefix('newdb_item');
						$sql.= " VALUES('','".$lid."','dir','".$newdir."','".$path."','".time()."','".$uid."')";
						$rs = $xoopsDB->query($sql);
						if($rs) $mes = _ND_BK_MKDIROK;
					}
				}
				if(empty($mes))	$mes = _ND_DIR_FALSE;
			}
			redirect_header(MOD_URL.'/edata.php?lid='.$lid.'&mode=add', 1, $mes);
			break;

		### Add new data file
		
		case 'add_new':
			$lid = intval($_POST['lid']);
			$did = intval($_POST['dir']);
			include 'class/regdatabase.php';
			$rd = new RegDatabase();
			$rd->setLabel($lid, $uid);
			if($rd->error()) redirect_header(MOD_URL, 2, $rd->error());
			
			if($did == 0){
				$path = EXTRACT_PATH.'/'.$lid.'/data';
			}else{
				$sql = "SELECT * FROM ".$xoopsDB->prefix('newdb_item')." WHERE item_id='".$did."'";
				$rs = $xoopsDB->query($sql);
				$row = $xoopsDB->fetchArray($rs);
				if(!empty($row['path'])){
					$path = EXTRACT_PATH.'/'.$lid.'/data/'.$row['path'].'/'.$row['name'];
				}else{
					$path = EXTRACT_PATH.'/'.$lid.'/data/'.$row['name'];
				}
			}
			
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

			if(is_uploaded_file($_FILES['userfile']['tmp_name'])){
				$fname = $_FILES['userfile']['name'];
				$tmp_name = $_FILES['userfile']['tmp_name'];

				if(filesize($tmp_name) > MAX_UPLOAD_SIZE){
					$mes = _ND_REG2;
					redirect_header(MOD_URL.'/detail.php?id='.$lid, 2, $mes);
					break;
				}
				if(file_exists($path.'/'.$fname)){
					$mes = _ND_EDATA_FEXIST;
					redirect_header(MOD_URL.'/detail.php?id='.$lid, 2, $mes);
					break;
				}
				# white list
				if(!empty($suf)){
					$suffix = array();
					$suffix = explode('|', $suf);
				
					$tmp = explode('.', $fname);
					$tmp_suf = $tmp[count($tmp)-1];
					if(in_array($tmp_suf, $suffix)){
						if(move_uploaded_file($tmp_name, $path.'/'.$fname)){
							if(!$rd->regFile($fname, $did)) $mes = $rd->error();
						}else{
							$mes = _ND_EDATA_ADDFILENG;
						}	
					}else{
						$mes = _ND_EDATA_NSUF;
					}
				# all ok
				}else{
					if(move_uploaded_file($tmp_name, $path.'/'.$fname)){
						if(!$rd->regFile($fname, $did)) $mes = $rd->error();
					}else{
						$mes = _ND_EDATA_ADDFILENG;
					}	
				}
			}else{
				$mes = _ND_EDATA_NFILE;
			}
			if(empty($mes)) $mes = _ND_EDATA_ADDFILEOK;
			redirect_header(MOD_URL.'/detail.php?id='.$lid, 2, $mes);
			break;
			
		### Add new files / directories
		
		case 'add_new2':
			include 'class/tarextractor.php';
			include 'class/regcopy.php';
			include 'class/regdatabase.php';
			
			$lid = intval($_POST['lid']);
			$sql = "SELECT * FROM ".$xoopsDB->prefix('newdb_master')." WHERE label_id='".$lid."'";
			$rs = $xoopsDB->query($sql);
			if($xoopsDB->getRowsNum($rs) == 0){
				$mes = _ND_EDATA_NODATA;
				redirect_header(MOD_URL, 2, $mes);
			}
			$row = $xoopsDB->fetchArray($rs);
			$label = $row['label'];
			
			if(!isset($_POST['use_upload']) && !isset($_POST['uploaded_data'])){
				$mes = _ND_EDATA_NSDATA;
				redirect_header(MOD_URL.'/detail.php?id='.$lid, 2, $mes);
			}

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

			# use tar
			if(isset($_POST['use_upload'])){
				if(is_uploaded_file($_FILES['userfile']['tmp_name'])){
					$tmp_name = $_FILES['userfile']['tmp_name'];
					$fname = $_FILES['userfile']['name'];
					$fname = str_replace('.TAR', '.tar', $fname);

					if(!preg_match("/.*(\.tar)$/i", $fname)){
						$mes = _ND_REG4;
						redirect_header(MOD_URL.'/detail.php?id='.$lid, 2, $mes);
						break;
					}
					if(filesize($tmp_name) > MAX_UPLOAD_SIZE){
						$mes = _ND_REG2;
						redirect_header(MOD_URL.'/detail.php?id='.$lid, 2, $mes);
						break;
					}
					if(file_exists(UPLOAD_PATH.'/'.$fname)){
						$mes = _ND_EDATA_RETRY;
						redirect_header(MOD_URL.'/detail.php?id='.$lid, 2, $mes);
						break;
					}

					if(move_uploaded_file($tmp_name, UPLOAD_PATH.'/'.$fname)){
						$tr = new TarExtractor();
						if(!$tr->doExtract(UPLOAD_PATH.'/'.$fname, EXTRACT_PATH.'/'.$lid.'/data', $suf)){
							$mes = $tr->error();
						}else{
							$rd = new RegDatabase();
							$rd->setLabel($lid, $uid);
							$rd->extract_path = EXTRACT_PATH;
							if(!$rd->synchroDatabase()) $mes = $rd->error();
						}
						unlink(UPLOAD_PATH.'/'.$fname);
					}else{
						$mes = _ND_EDATA_ADDFILENG;
					}
				}else{
					$mes = _ND_EDATA_NFILE;
				}
			
			# use uploaded files
			}elseif(isset($_POST['uploaded_data'])){
				$data_name = $myts->stripSlashesGPC($_POST['uploaded_data']);
				if(!is_dir(UPLOAD_PATH.'/'.$xoopsUser->uname().'/'.$data_name)){
					$mes = _ND_EDATA_NODIR;
					redirect_header(MOD_URL.'/detail.php?id='.$lid, 2, $mes);
				}	
			  $from_path = UPLOAD_PATH.'/'.$xoopsUser->uname().'/'.$data_name;
				$rc = new RegCopy();
				if(!$rc->doCopy($from_path, EXTRACT_PATH.'/'.$lid.'/data', $suf)){
					$mes = _ND_EDATA_ADDFILENG;
				}
				$rd = new RegDatabase();
				$rd->setLabel($lid, $uid);
				$rd->extract_path = EXTRACT_PATH;
				if(!$rd->synchroDatabase()) $mes = $rd->error();
				$rc->delDirectory($from_path);
			}

			if(empty($mes)) $mes = _ND_EDATA_DATAADD;
			redirect_header(MOD_URL.'/detail.php?id='.$lid, 2, $mes);
			break;
		
		### Move directories
		
		case 'mvd':
			include XOOPS_ROOT_PATH.'/header.php';
			include 'style.css';
			headerBar($lid, $label, $xoopsModuleConfig['dname_flg']);
			echo "- "._ND_CONFIG_NOMES."<br>";
			echo "- "._ND_EDATA_IFMES."<br><br>";
			echo "<form method='POST' action='edata.php'>";
			echo $de->getDatalist($isadmin, 'dir');
			echo "<div style='text-align:center; margin-top:20px'>";
			echo "<select name='dir'>".$de->getDirlist()."</select> "._ND_EDATA_MOVETO;
			echo "</div>";
			echo "<input type='hidden' name='mode' value='mvd2'>";
			echo "<input type='hidden' name='lid' value='".$lid."'><br>";
			echo "<center><input type='submit' value='submit' class='button'></center>";
			include XOOPS_ROOT_PATH.'/footer.php';
			break;
			
		case 'mvd2':
			$dir = intval($_POST['dir']);
			$toid = 0;
			
			$sql = "SELECT * FROM ".$xoopsDB->prefix('newdb_item');
			$sql.= " WHERE item_id='".$dir."' AND type='dir'";
			$rs = $xoopsDB->query($sql);
			if($xoopsDB->getRowsNum($rs) == 0){
				$dpath = '';
			}else{
				$row = $xoopsDB->fetchArray($rs);
				if(!empty($row['path'])){
					$dpath = $row['path'].'/'.$row['name'];
				}else{
					$dpath = $row['name'];
				}
				$toid = $row['item_id'];
			}

			$target = array();
			foreach($_POST['data'] as $value){
				$sql = "SELECT * FROM ".$xoopsDB->prefix('newdb_item');
				$sql.= " WHERE item_id='".intval($value)."'";
				$rs = $xoopsDB->query($sql);
				if($xoopsDB->getRowsNum($rs) == 0) continue;
				$row = $xoopsDB->fetchArray($rs);
				if($row['reg_user'] == $uid || $isadmin){
					if($row['type'] == 'dir'){

						# check directory hierarchy
						if($row['item_id'] != $toid){
							$target_path = $row['path'];
							
							# top to top
							if(empty($target_path) && $toid != 0){
								if(!strstr($dpath, $row['name'])){
									$target[$row['item_id']] = $row['name'];
								}
							
							}elseif(!empty($target_path)){
								$p = $target_path.'/'.$row['name'];
								
								# OK: 1/2/3 -> 1/2/4
								if(count(explode('/', $p)) == count(explode('/', $dpath))){
									$target[$row['item_id']] = $p;
								# NG: 1/2/3 -> 1/2 , 1/2 -> 1/2/3
								}elseif(!strstr($dpath, $target_path) && !strstr($dpath, $p)){
									$target[$row['item_id']] = $p;
								}
							}							
						}	
					}
				}
			}
			asort($target);
			
			#print_r($target);
			#echo $dpath;
			
			if(count($target)){
				$de->moveDirs($target, $dpath, EXTRACT_PATH);
				$mes = _ND_EDATA_DIRMOVED;
			}else{
				$mes = _ND_EDATA_NDIRMOVED;
			}
			redirect_header(MOD_URL.'/edata.php?lid='.$lid.'&mode=mvd', 1, $mes);
			break;

		### Move files
					
		case 'mvf':
			include XOOPS_ROOT_PATH.'/header.php';
			include 'style.css';
			headerBar($lid, $label, $xoopsModuleConfig['dname_flg']);
			echo "- "._ND_CONFIG_NOMES."<br><br>";
			echo "<form method='POST' action='edata.php'>";
			echo $de->getDatalist($isadmin, 'file');
			echo "<div style='text-align:center; margin-top:20px'>";
			echo "<select name='dir'>".$de->getDirlist()."</select> "._ND_EDATA_MOVETO;
			echo "</div>";
			echo "<input type='hidden' name='mode' value='mvf2'>";
			echo "<input type='hidden' name='lid' value='".$lid."'><br>";
			echo "<center><input type='submit' value='submit' class='button'></center>";
			include XOOPS_ROOT_PATH.'/footer.php';
			break;

		case 'mvf2':
			$dir = intval($_POST['dir']);
			
			$sql = "SELECT * FROM ".$xoopsDB->prefix('newdb_item');
			$sql.= " WHERE item_id='".$dir."' AND type='dir'";
			$rs = $xoopsDB->query($sql);
			if($xoopsDB->getRowsNum($rs) == 0){
				$dpath = '';
			}else{
				$row = $xoopsDB->fetchArray($rs);
				if(!empty($row['path'])){
					$dpath = $row['path'].'/'.$row['name'];
				}else{
					$dpath = $row['name'];
				}
			}
			
			$file = array();
			foreach($_POST['data'] as $value){
				$sql = "SELECT * FROM ".$xoopsDB->prefix('newdb_item');
				$sql.= " WHERE item_id='".intval($value)."'";
				$rs = $xoopsDB->query($sql);
				if($xoopsDB->getRowsNum($rs) == 0) continue;
				$row = $xoopsDB->fetchArray($rs);
				if($row['reg_user'] == $uid || $isadmin){
					if($row['type'] == 'file'){
						if(!empty($row['path'])){
							$file[$row['item_id']] = $row['path'].'/'.$row['name'];
						}else{
							$file[$row['item_id']] = $row['name'];
						}
					}
				}
			}
			asort($file);
			$de->moveFiles($file, $dpath, EXTRACT_PATH);
			$mes = _ND_EDATA_FMOVED;
			redirect_header(MOD_URL.'/edata.php?lid='.$lid.'&mode=mvf', 1, $mes);
			break;
		
		### Delete files
		
		case 'del':
			include XOOPS_ROOT_PATH.'/header.php';
			include 'style.css';
			headerBar($lid, $label, $xoopsModuleConfig['dname_flg']);
			echo "- "._ND_EDATA_TRASH1."<br>";
			echo "- "._ND_CONFIG_NOMES."<br>";
			echo "- "._ND_EDATA_TRASH2."<br><br>";
			echo "<form method='POST' action='edata.php'>";
			echo $de->getDatalist($isadmin);
			echo "<input type='hidden' name='mode' value='del2'>";
			echo "<input type='hidden' name='lid' value='".$lid."'><br>";
			echo "<center><input type='submit' value='submit' class='button'></center>";
			include XOOPS_ROOT_PATH.'/footer.php';
			break;
		
		case 'del2':
			$dir = array();
			$file = array();
			foreach($_POST['data'] as $value){
				$sql = "SELECT * FROM ".$xoopsDB->prefix('newdb_item');
				$sql.= " WHERE item_id='".intval($value)."'";
				$rs = $xoopsDB->query($sql);
				if($xoopsDB->getRowsNum($rs) == 0) continue;
				$row = $xoopsDB->fetchArray($rs);
				if($row['reg_user'] == $uid || $isadmin){
					if($row['type'] == 'dir'){
						$dir[$row['name']] = $row['path'];
					}else{
						$file[$row['name']] = $row['path'];
					}
				}
			}
			asort($dir);
			asort($file);
			$de->toTrash($dir, $file, EXTRACT_PATH);
			
			$mes = _ND_EDATA_DATADELETED;
			redirect_header(MOD_URL.'/edata.php?lid='.$lid.'&mode=del', 1, $mes);
			break;
		
		### Trashbox
		
		case 'trash':
			include XOOPS_ROOT_PATH.'/header.php';
			include 'style.css';
			headerBar($lid, $label, $xoopsModuleConfig['dname_flg']);
			echo "- "._ND_EDATA_TRASH3."<br><br>";
			echo "<form method='POST' action='edata.php'>";
			echo $de->getTrashlist(EXTRACT_PATH);
			echo "<input type='hidden' name='mode' value='trash2'>";
			echo "<input type='hidden' name='lid' value='".$lid."'><br>";
			echo "<center>";
			echo "<input type='radio' name='method' value='delete' checked> "._ND_EDATA_DODEL."&nbsp;&nbsp;";
			echo "<input type='radio' name='method' value='restore'> ";
			echo "<select name='dir'>";
			echo $de->getDirlist();
			echo "</select>";
			echo _ND_EDATA_RESTORE;
			echo "<br><input type='submit' value='submit' class='button'></center>";
			include XOOPS_ROOT_PATH.'/footer.php';
			break;
			
		case 'trash2':
			
			if(!isset($_POST['data'])){
				$mes = _ND_EDATA_NSDATA;
			
			}elseif($_POST['method'] == 'delete'){
				foreach($_POST['data'] as $v){
					$file = EXTRACT_PATH.'/'.$lid.'/trashbox/'.$myts->stripSlashesGPC($v);
					if(file_exists($file)) unlink($file);
				}
				$mes = _ND_EDATA_DATADIS;

			}elseif($_POST['method'] == 'restore'){
				$dir = intval($_POST['dir']);
				$file = array();
				foreach($_POST['data'] as $v){
					$file[] = $myts->stripSlashesGPC($v);
				}
				if($de->fromTrash($dir, $file, EXTRACT_PATH)){
					$mes = _ND_EDATA_DATARESTORED;
				}else{
					$mes = _ND_EDATA_DATARESTOREDNG;
				}
			}

			redirect_header(MOD_URL.'/edata.php?lid='.$lid.'&mode=trash', 1, $mes);
			break;
	}
	
	
	function headerBar($lid, $label, $dname_flg){
		echo "<div class='h' style='margin-top:0'>File Manager: <a href='detail.php?id=".$lid."'>";
		if($dname_flg){
			echo $label;
		}else{
			echo $lid;
		}
		echo "</a></div>";
		echo "<div style='text-align:right;	margin-bottom:30px;'>";
		echo "<a href='edata.php?lid=".$lid."&mode=add'>"._ND_EDATA_AF."</a> | ";
		echo "<a href='edata.php?lid=".$lid."&mode=mvd'>"._ND_EDATA_MD."</a> | ";
		echo "<a href='edata.php?lid=".$lid."&mode=mvf'>"._ND_EDATA_MF."</a> | ";
		echo "<a href='edata.php?lid=".$lid."&mode=del'>"._ND_EDATA_DF."</a> | ";
		echo "<a href='edata.php?lid=".$lid."&mode=trash'>"._ND_EDATA_TR."</a>";
		echo "</div>";
	}
?>