<?php
/*
delete query does not be implemented in the case component master is deleted.
*/
	$url = XOOPS_URL.'/modules/newdb/admin/index.php';
	
	if(isset($_POST['action'])){
		$action = $_POST['action'];
	}elseif(isset($_GET['action'])){
		$action = $_GET['action'];
	}else{
		$action='';
	}
	
	switch($action){
		case 'new_component':
			Add_new_component($url);
			break;
	
		case 'change':
			Change_component($url);
			break;
			
		default:
			Component_top($url);
			break;
	}
	
	function Change_component($url){
		global $xoopsDB;

		if(isset($_GET['id'])){
			$id = intval($_GET['id']);
		}elseif(isset($_POST['comp_id'])){
			$id = intval($_POST['comp_id']);
		}
		$sql = "SELECT * FROM ".$xoopsDB->prefix('newdb_component_master')." WHERE comp_id='".$id."'";
		$rs = $xoopsDB->query($sql);
		$row = $xoopsDB->fetchArray($rs);
	
		# edit form
		if(isset($_GET['id'])){
			if($row['type'] == '1'){
				showSysForm($url, $id);
			}else{
				showForm($url, 'change', $id);
			}
		
		# do edit
		}elseif(isset($_POST['comp_id'])){
			$myts =& MyTextSanitizer::getInstance();
			$tag = $myts->stripSlashesGPC($_POST['comp_tag']);
			$onoff = intval($_POST['comp_onoff']);
			$sort = intval($_POST['comp_sort']);

			if($row['type'] == '1'){
				if(empty($tag)){
					redirect_header($url.'?mode=component', 1, _ND_COMPONENT_INPUTNG);
				}
				$tag4sql = addslashes($tag);
				$sql = "UPDATE ".$xoopsDB->prefix('newdb_component_master');
				$sql.= " SET tag='".$tag4sql."', onoff='".$onoff."', sort='".$sort."' WHERE comp_id='".$id."'";
				$rs = $xoopsDB->query($sql);
			
			}else{
				if(isset($_POST['comp_delete'])){
					$sql = "DELETE FROM ".$xoopsDB->prefix('newdb_component_master')." WHERE comp_id='".$id."'";
					$rs = $xoopsDB->query($sql);
					if($rs){
						redirect_header($url.'?mode=component', 1, _ND_COMPONENT_OK);
					}else{
						redirect_header($url.'?mode=component', 1, _ND_COMPONENT_NG);
					}
				}
	
				$name = $myts->stripSlashesGPC($_POST['comp_name']);
				$exp = $myts->stripSlashesGPC($_POST['comp_desc']);
				$select = $myts->stripSlashesGPC($_POST['comp_select']);
				$default = $myts->stripSlashesGPC($_POST['comp_default']);
				$type = intval($_POST['comp_type']);
	
				if(!$tag || !$name || !($type=='2' || $type=='3' || $type=='4') || ($type!='4' && !$select)){
					redirect_header($url.'?mode=component', 1, _ND_COMPONENT_INPUTNG);
				}
				
				$tag4sql = addslashes($tag);
				$name4sql = addslashes($name);
				$exp4sql = addslashes($exp);
				$select4sql = addslashes($select);
				$default4sql = addslashes($default);
				$sql = "UPDATE ".$xoopsDB->prefix('newdb_component_master');
				$sql.= " SET tag='".$tag4sql."', name='".$name4sql."', exp='".$exp4sql."', type='".$type."', onoff='".$onoff."', sort='".$sort."'";
				if($type=='2' || $type=='3'){
					$sql.= ", default_value='".$default4sql."', select_value='".$select4sql."'";
				}
				$sql.= " WHERE comp_id='".$id."'";
				$rs = $xoopsDB->query($sql);
			}

			if($rs){
				redirect_header($url.'?mode=component', 1, _ND_COMPONENT_EDITOK);
			}else{
				redirect_header($url.'?mode=component', 1, _ND_COMPONENT_EDITNG);
			}
		}
	}

	function Add_new_component($url){
	
		if(isset($_POST['comp_name'])){
			global $xoopsDB;
			$myts =& MyTextSanitizer::getInstance();

			$tag = $myts->stripSlashesGPC($_POST['comp_tag']);
			$name = $myts->stripSlashesGPC($_POST['comp_name']);
			$exp = $myts->stripSlashesGPC($_POST['comp_desc']);
			$type = intval($_POST['comp_type']);
			$select = $myts->stripSlashesGPC($_POST['comp_select']);
			$default = $myts->stripSlashesGPC($_POST['comp_default']);
			$onoff = intval($_POST['comp_onoff']);
			$sort = intval($_POST['comp_sort']);

			if(!$tag || !$name || !($type=='2' || $type=='3' || $type=='4') || ($type!='4' && !$select)){
				redirect_header($url.'?mode=component', 1, _ND_COMPONENT_INPUTNG);
			}
			
			$tag4sql = addslashes($tag);
			$name4sql = addslashes($name);
			$exp4sql = addslashes($exp);
			$select4sql = addslashes($select);
			$default4sql = addslashes($default);
			$sql = "INSERT INTO ".$xoopsDB->prefix('newdb_component_master');

			if($type=='2' || $type=='3'){
				$sql.= " VALUES('','".$tag4sql."','".$name4sql."','".$exp4sql."','".$type."','".$default4sql."','".$select4sql."','".$onoff."','".$sort."')";
			}else{
				$sql.= " VALUES('','".$tag4sql."','".$name4sql."','".$exp4sql."','".$type."','','','".$onoff."','".$sort."')";
			}

			$rs = $xoopsDB->query($sql);
			if($rs){
				redirect_header($url.'?mode=component', 1, _ND_COMPONENT_ADDOK);
			}else{
				redirect_header($url.'?mode=component', 1, _ND_COMPONENT_ADDNG);
			}
		
		}else{
			showForm($url, 'new_component', -1);
		}
	}

	/**
	 * showSysForm (type 1)
	 * showForm (type 2,3,4)
	 *
	 * show form for new register and edit.
	 */
	function showSysForm($url, $id){

		global $xoopsDB;
		$sql = "SELECT * FROM ".$xoopsDB->prefix('newdb_component_master')." WHERE comp_id='".$id."'";
		$rs = $xoopsDB->query($sql);
		$row = $xoopsDB->fetchArray($rs);
		$n = $row['tag'];
		$o = $row['onoff'];
		$so = $row['sort'];

		xoops_cp_header();
		include 'style.css';
		echo "<center>";
		echo "<form action='".$url."' method='POST' style='margin:0; padding:0'>";
		echo "<table class='list_table' style='width:500px;'>";
		echo "<tr><th colspan='2'>"._ND_COMPONENT_SEDIT."</th></tr>";

		echo "<tr><td class='list_odd' style='width:150px'><b>"._ND_COMPONENT_NAME."</b></td>";
		echo "<td><input type='text' name='comp_tag' size='15' value='".$n."'></td></tr>";
		
		echo "<tr><td class='list_odd'><b>"._ND_COMPONENT_SORT."</b></td><td>";
		$onoff = array(_ND_COMPONENT_YES, _ND_COMPONENT_NO);
		for($i=0; $i<2; $i++){
			echo "<input type='radio' name='comp_onoff' value='".$i."'";
			if($o == $i) echo " checked";
			echo ">".$onoff[$i]." ";
		}
		echo "</td></tr>";
	
		echo "<tr><td class='list_odd'><b>"._ND_COMPONENT_ORDER."</b></td>";
		echo "<td><input type='text' name='comp_sort' size='3' value='".$so."'></td></tr>";
		echo "</table>";
		
		echo "<br><br><input type='submit' value='submit'>";
		echo "<input type='hidden' value='component' name='mode'>";
		echo "<input type='hidden' value='change' name='action'>";
		echo "<input type='hidden' value='".$id."' name='comp_id'>";
		echo "</form>";
		echo "</center>";
		xoops_cp_footer();
	}

	function showForm($url, $act, $id){

		$tag = ''; $n = ''; $e = ''; $t = '2'; $s = ''; $d = ''; $o = '1'; $so = '0'; 
		$title = _ND_ADD_NEWCOMPONENT;
		if($act == 'change'){
			global $xoopsDB;
			$sql = "SELECT * FROM ".$xoopsDB->prefix('newdb_component_master')." WHERE comp_id='".$id."'";
			$rs = $xoopsDB->query($sql);
			$row = $xoopsDB->fetchArray($rs);
			$tag = $row['tag'];
			$n = $row['name'];
			$e = $row['exp'];
			$t = $row['type'];
			$s = $row['select_value'];
			$d = $row['default_value'];
			$o = $row['onoff'];
			$so = $row['sort'];
			$title = _ND_COMPONENT_EDIT;
		}

		xoops_cp_header();
		include 'style.css';
		echo "<center>";
		echo "<form action='".$url."' method='POST' style='margin:0; padding:0'>";
		echo "<table class='list_table' style='width:500px;'>";
		echo "<tr><th colspan='2'>".$title."</th></tr>";
		
		echo "<tr><td class='list_odd' style='width:150px'><b>"._ND_COMPONENT_NAME."</b></td>";
		echo "<td><input type='text' name='comp_tag' size='15' value='".$tag."'></td></tr>";
				
		echo "<tr><td class='list_odd' style='width:150px'><b>"._ND_COMPONENT_TEMPNAME."</b></td>";
		echo "<td><input type='text' name='comp_name' size='15' value='".$n."'></td></tr>";
		
		echo "<tr><td class='list_odd' style='width:150px'><b>"._ND_COMPONENT_ITEM_DESC."</b></td>";
		echo "<td><input type='text' name='comp_desc' style='width:70%' value='".$e."'></td></tr>";
					
		echo "<tr><td class='list_odd'><b>"._ND_COMPONENT_TYPE."</b></td><td>";
		$type = array('radio', 'checkbox', 'text');
		for($i=0; $i<3; $i++){
			echo "<input type='radio' name='comp_type' value='".($i+2)."'";
			if($t == ($i+2)) echo " checked";
			echo ">".$type[$i]." ";
		}
		echo "</td></tr>";
		
		echo "<tr><td class='list_odd'><b>"._ND_COMPONENT_SELECT_ITEM."</b><br>"._ND_COMPONENT_SELECT_ITEM_DESC."</td>";
		echo "<td><textarea name='comp_select' style='width:90%; height:60px'>".$s."</textarea></td></tr>";

		echo "<tr><td class='list_odd'><b>"._ND_COMPONENT_DEFAULT."</b><br>"._ND_COMPONENT_DEFAULT_DESC."</td>";
		echo "<td><input type='text' name='comp_default' value='".$d."'></td></tr>";

		echo "<tr><td class='list_odd'><b>"._ND_COMPONENT_SORT."</b></td><td>";
		$onoff = array(_ND_COMPONENT_YES, _ND_COMPONENT_NO);
		for($i=0; $i<2; $i++){
			echo "<input type='radio' name='comp_onoff' value='".$i."'";
			if($o == $i) echo " checked";
			echo ">".$onoff[$i]." ";
		}
		echo "</td></tr>";
		
		echo "<tr><td class='list_odd'><b>"._ND_COMPONENT_ORDER."</b></td>";
		echo "<td><input type='text' name='comp_sort' size='3' value='".$so."'></td></tr>";
		
		if($act == 'change'){
			echo "<tr><td class='list_odd'><b>"._ND_COMPONENT_DEL."</b><br>"._ND_COMPONENT_DEL_DESC."</td>";
			echo "<td><input type='checkbox' name='comp_delete'></td></tr>";
		}
		
		echo "</table>";
		
		echo "<br><br><input type='submit' value='submit'>";
		echo "<input type='hidden' value='component' name='mode'>";
		echo "<input type='hidden' value='".$act."' name='action'>";
		
		if($act == 'change'){
			echo "<input type='hidden' value='".$id."' name='comp_id'>";
		}
		echo "</form>";
		echo "</center>";
		xoops_cp_footer();
	}


	function Component_top($url){
		global $xoopsDB;

		xoops_cp_header();
		include 'style.css';
		echo "<center>";

		echo "<div class='title'>"._ND_COMPONENT_REGITEM."</div>";
		echo "<div class='title_desc'>"._ND_COMPONENT_DESC."</div>";
			
		echo "<form action='".$url."' method='POST' style='margin:0; padding:0'>";
		echo "<table class='list_table' style='width:90%'>";
		echo "<tr><th style='width:20%'>"._ND_COMPONENT_NAME."</th>";
		echo "<th style='width:20%'>"._ND_COMPONENT_TEMPNAME."</th>";
		echo "<th>"._ND_COMPONENT_VALUE."</th>";
		echo "<th style='width:70px'>"._ND_COMPONENT_TYPE."</th>";
		echo "<th style='width:50px'>"._ND_COMPONENT_SRT."</th>";
		echo "</tr>";

		$sql = "SELECT * FROM ".$xoopsDB->prefix('newdb_component_master')." ORDER BY onoff, sort";
		$rs = $xoopsDB->query($sql);
	
		while($row = $xoopsDB->fetchArray($rs)){
			$tag = "<a href='".$url."?mode=component&action=change&id=".$row['comp_id']."'>".$row['tag']."</a>";
			$value = str_replace($row['default_value'], "<span style='color:red'>".$row['default_value'].'</span>', $row['select_value']);
			echo "<tr>";
			echo "<td>".$tag."</td>";
			echo "<td>".$row['name']."</td>";
			echo "<td>".$value."</td>";
			echo "<td>";
			if($row['type'] == 1){
				echo "system";
			}elseif($row['type'] == 2){
				echo "radio";
			}elseif($row['type'] == 3){
				echo "checkbox";
			}elseif($row['type'] == 4){
				echo "text";
			}
			echo "</td><td style='text-align:center'>";
			if(!$row['onoff']){
				echo "YES</td>";
			}else{
				echo "NO</td>";
			}

			echo "</tr>";
		}
		echo "</table>";	

		echo "<table style='margin: 20px 0 20px 0; border:0'>";
		echo "<tr><td>";
		echo "<input type='submit' value='"._ND_ADD_NEWCOMPONENT."'>";
		echo "<input type='hidden' value='component' name='mode'>";
		echo "<input type='hidden' value='new_component' name='action'>";
		echo "</td></tr></table>";
		echo "</form>";
		echo "</center>";	
		xoops_cp_footer();
	}
	
?>