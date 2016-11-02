<?php

	$url = XOOPS_URL.'/modules/newdb/admin/index.php';
	(isset($_POST['action'])) ? $action = $_POST['action'] : $action='';
	
	switch($action){
		case 'new_category':
			Add_new_category($url);
			break;
			
		case 'new_keyword':
			Add_new_keyword($url);
			break;
	
		case 'change':
			Change_keyword($url);
			break;
			
		case 'do_change':
			Change_keyword2($url);
			break;
			
		case 'delete':
			Delete_keyword($url);
			break;
			
		case 'sort':
			Sort_keyword($url);
			break;
			
		default:
			Keyword_top($url);
			break;
	}
	
	function Delete_keyword($url){
		global $xoopsDB;
		$id = intval($_POST['id']);

		$kw_list = array();
		$kw_list[] = $id;
		
		$sql = "SELECT * FROM ".$xoopsDB->prefix('newdb_keyword')." WHERE kw_id='".$id."'";
		$rs = $xoopsDB->query($sql);
		$row = $xoopsDB->fetchArray($rs);

		$target = $row['path'].$row['kw_id'].'/';
		$sql = "SELECT * FROM ".$xoopsDB->prefix('newdb_keyword')." WHERE path like '".$target."%'";
		$rs = $xoopsDB->query($sql);
		while($row = $xoopsDB->fetchArray($rs)){
			$kw_list[] = $row['kw_id'];
		}
		
		foreach($kw_list as $v){
			$sql = "SELECT * FROM ".$xoopsDB->prefix('newdb_master')." WHERE keyword like '%[".$v."]%'";
			$rs = $xoopsDB->query($sql);
			while($row = $xoopsDB->fetchArray($rs)){
				$label_id = $row['label_id'];
				$kw = $row['keyword'];
				$kw = str_replace('['.$v.'],', '', $kw);
			
				$sql = "UPDATE ".$xoopsDB->prefix('newdb_master');
				$sql.= " SET keyword='".$kw."' WHERE label_id='".$label_id."'";
				$rs2 = $xoopsDB->query($sql);
			}
			$sql = "DELETE FROM ".$xoopsDB->prefix('newdb_keyword')." WHERE kw_id='".$v."'";
			$rs = $xoopsDB->query($sql);
		}
		header("Location:".$url."?mode=keyword");
	}

	# sort keyword
	function Sort_keyword($url){
		global $xoopsDB;
		$id = intval($_POST['id']);
		$num = intval($_POST['sort_num']);
		
		$sql = "UPDATE ".$xoopsDB->prefix('newdb_keyword')." SET sort='".$num."' WHERE kw_id='".$id."'";
		$rs = $xoopsDB->query($sql);
		header("Location:".$url."?mode=keyword");
	}

	# change keyword
	function Change_keyword($url){
		global $xoopsDB;
		$id = intval($_POST['id']);

		$sql = "SELECT * FROM ".$xoopsDB->prefix('newdb_keyword')." WHERE kw_id='".$id."'";
		$rs = $xoopsDB->query($sql);
		$row = $xoopsDB->fetchArray($rs);
		$this_path = $row['path'];
		$keyword = $row['keyword'];
		
   	xoops_cp_header();
   	include 'style.css';
		echo "<form method='POST' action='".$url."'>";
		echo "<table class='list_table' width='500px' align='center'>";
		echo "<tr><th>"._ND_CHANGE_KEYWORD."</th></tr>";
		echo "<tr><td class='list_odd' style='text-align:center'>";
		
		if($this_path != '/'){
			$path = explode('/', $row['path']);
			$path_id = array($path[0]);

			$sql = "SELECT * FROM ".$xoopsDB->prefix('newdb_keyword')." WHERE path like '".$path[0]."%'";
			$rs = $xoopsDB->query($sql);
			while($row = $xoopsDB->fetchArray($rs)){
				if(!in_array($row['path'].$row['kw_id'].'/', $path_id)){
					$path_id[] = $row['path'].$row['kw_id'].'/';
				}
			}
			
			$path_id2 = array();
			for($i=0; $i<count($path_id); $i++){
				$path = explode('/', $path_id[$i]);
				$tmp = '';
				for($j=0; $j<count($path); $j++){
					if($path[$j]){
						$sql = "SELECT * FROM ".$xoopsDB->prefix('newdb_keyword')." WHERE kw_id='".$path[$j]."'";
						$rs = $xoopsDB->query($sql);
						$row = $xoopsDB->fetchArray($rs);
						$tmp .= $row['keyword'].'/';
					}
				}
				$tmp = substr($tmp, 0, -1);
				$path_id2[] = $tmp;
			}
			
			/*
			for($i=0; $i<count($path_id); $i++){
				echo $path_id[$i]." - - ".$path_id2[$i]."<br>";
			}
			*/
			
			$path_id3 = array();
			for($i=0; $i<count($path_id); $i++){
				if(!strstr($path_id[$i], $this_path.$id.'/') && $path_id2[$i] != ''){
					$path_id3[] = $path_id2[$i].'#_#'.$path_id[$i];
				}
			}
			
			sort($path_id3, SORT_STRING);

			echo "<select name='path'>";
			for($i=0; $i<count($path_id3); $i++){
				$p = explode('#_#', $path_id3[$i]);
				($p[1] == $this_path) ? $s="selected" : $s='';
				echo "<option value='".$p[1]."' ".$s.">".$p[0]."/</option>";
			}
			echo "</select>\n";
		}			
		echo " <input type='text' name='keyword' value='".$keyword."'>";
	
		echo "</td></tr></table>";
		echo "<center><br><input type='submit' value='submit'></center>";
		echo "<input type='hidden' value='keyword' name='mode'>";
		echo "<input type='hidden' value='do_change' name='action'>";
		echo "<input type='hidden' value='".$id."' name='id'>";
		echo "</form>";
   	xoops_cp_footer();
	}
	
	function Change_keyword2($url){
		global $xoopsDB;
		$myts =& MyTextSanitizer::getInstance();

		$id = intval($_POST['id']);
		$keyword = $myts->addSlashes($_POST['keyword']);

		# change category name				
		if(!isset($_POST['path'])){
			$sql = "UPDATE ".$xoopsDB->prefix('newdb_keyword');
			$sql.= " SET keyword='".$keyword."' WHERE kw_id='".$id."'";
			$rs = $xoopsDB->query($sql);
		
		# change keywords
		}else{
			$path = $myts->addSlashes($_POST['path']);
		
			$sql = "SELECT * FROM ".$xoopsDB->prefix('newdb_keyword')." WHERE kw_id='".$id."'";
			$rs = $xoopsDB->query($sql);
			$row = $xoopsDB->fetchArray($rs);

			$old_path = $row['path'].$id.'/';
			$new_path = $path.$id.'/';

			$sql = "UPDATE ".$xoopsDB->prefix('newdb_keyword');
			$sql.= " SET keyword='".$keyword."', path='".$path."' WHERE kw_id='".$id."'";
			$rs = $xoopsDB->query($sql);
			
			$sql = "SELECT * FROM ".$xoopsDB->prefix('newdb_keyword')." WHERE path like '".$old_path."%'";
			$rs = $xoopsDB->query($sql);
			while($row = $xoopsDB->fetchArray($rs)){
				$path2 = str_replace($old_path, $new_path, $row['path']);
				$sql = "UPDATE ".$xoopsDB->prefix('newdb_keyword');
				$sql.= " SET path='".$path2."' WHERE kw_id='".$row['kw_id']."'";
				$rs2 = $xoopsDB->query($sql);
			}
		}
		redirect_header(XOOPS_URL.'/modules/newdb/admin/index.php?mode=keyword', 1, _ND_KEYWORD_CHANGED);
	}
	
	

	# new keyword
	function Add_new_keyword($url){
	
		global $xoopsDB;
		$id = intval($_POST['id']);

		# get path
		$sql = "SELECT * FROM ".$xoopsDB->prefix('newdb_keyword')." WHERE kw_id='".$id."'";
		$rs = $xoopsDB->query($sql);
		if($rs){
			$row = $xoopsDB->fetchArray($rs);
			$path = $row['path'].$row['kw_id'].'/';
		}else{
			redirect_header(XOOPS_URL.'/modules/newdb/admin/index.php?mode=keyword', 1, _ND_DB_ERROR);
		}
		
		# register keyword
		if(isset($_POST['keyword_name'])){
			$myts =& MyTextSanitizer::getInstance();
			
			$name = $myts->stripSlashesGPC($_POST['keyword_name']);
			$name4sql = addslashes($name);
			$sql = "INSERT INTO ".$xoopsDB->prefix('newdb_keyword')." VALUES('','".$name4sql."','".$path."','0')";
			$rs = $xoopsDB->query($sql);
			if($rs){
				redirect_header(XOOPS_URL.'/modules/newdb/admin/index.php?mode=keyword', 1, _ND_KEYWORD_ADDED);
			}else{
				redirect_header(XOOPS_URL.'/modules/newdb/admin/index.php?mode=keyword', 1, _ND_KEYWORD_NONADDED);
			}

		# form
		}else{
			$path = explode('/', $path);
			$path4show = '';
			for($i=0; $i<count($path); $i++){
				if($path[$i]){
					$sql = "SELECT * FROM ".$xoopsDB->prefix('newdb_keyword')." WHERE kw_id='".$path[$i]."'";
					$rs = $xoopsDB->query($sql);
					$row = $xoopsDB->fetchArray($rs);
					$path4show .= $row['keyword'].'/';
				}
			}
    	xoops_cp_header();
    	include 'style.css';
			echo "<form method='POST' action='".$url."'>";
			echo "<table class='list_table' width='500px' align='center'>";
			echo "<tr><th>"._ND_ADD_NEYKEYWORD."</th></tr>";
			echo "<tr><td class='list_odd' style='text-align:center'>";
			echo $path4show." <input type='text' name='keyword_name'>";
			echo "</td></tr></table>";
			echo "<center><br><input type='submit' value='submit'></center>";
			echo "<input type='hidden' value='keyword' name='mode'>";
			echo "<input type='hidden' value='new_keyword' name='action'>";
			echo "<input type='hidden' value='".$id."' name='id'>";
			echo "</form>";
  		xoops_cp_footer();
		}
	}

	# new category
	function Add_new_category($url){

		if(isset($_POST['cate_name'])){
			global $xoopsDB;
			$myts =& MyTextSanitizer::getInstance();
			
			$name = $myts->stripSlashesGPC($_POST['cate_name']);
			$name4sql = addslashes($name);
			
			$sql = "INSERT INTO ".$xoopsDB->prefix('newdb_keyword')." VALUES('','".$name4sql."','/','0')";
			$rs = $xoopsDB->query($sql);
			if($rs){
				redirect_header(XOOPS_URL.'/modules/newdb/admin/index.php?mode=keyword', 1, _ND_CATEGORY_ADDED);
			}else{
				redirect_header(XOOPS_URL.'/modules/newdb/admin/index.php?mode=keyword', 1, _ND_CATEGORY_NONADDED);
			}
		
		}else{
    	xoops_cp_header();
    	include 'style.css';
			echo "<form method='POST' action='".$url."'>";
			echo "<table class='list_table' width='500px' align='center'>";
			echo "<tr><th>"._ND_ADD_NEYCATEGORY."</th></tr>";
			echo "<tr><td class='list_odd' style='text-align:center'>";
			echo _ND_CATEGORY_NAME." : <input type='text' name='cate_name'>";
			echo "</td></tr></table>";
			echo "<center><br><input type='submit' value='submit'></center>";
			echo "<input type='hidden' value='keyword' name='mode'>";
			echo "<input type='hidden' value='new_category' name='action'>";
			echo "</form>";
  		xoops_cp_footer();
		}
	}


	# keyword top page
	function Keyword_top($url){
	
		global $xoopsDB;
   	xoops_cp_header();
 		include 'style.css';

 		echo "<script language='JavaScript' src='../tab.js'></script>\n";
		echo "<center>";
		
		# category
		echo "<div class='title'>"._ND_CATEGORY."</div>";
		echo "<div class='title_desc'>"._ND_CATEGORY_DESC."</div>";
		
		echo "<table class='list_table' style='width:500px; text-align:left'>";
		echo "<tr><th>"._ND_CATEGORY."</th><th> </th><th> </th><th> </th></tr>";
		
		$sql = "SELECT * FROM ".$xoopsDB->prefix('newdb_keyword')." WHERE path='/' ORDER BY sort";
		$rs = $xoopsDB->query($sql);
		$i=0;
		if($xoopsDB->getRowsNum($rs) > 0){
			while($row = $xoopsDB->fetchArray($rs)){
							
				echo "<form action='".$url."' method='POST' style='margin:0;'><tr>";
				echo "<td><a href=\"javascript:seltab('box', 'head', 10, ".($i+1).")\">".$row['keyword']."</a></td>";

				echo "<td class='list_odd' style='width:50px; text-align:center'>";
				echo "<input type='text' size='2' name='sort_num' value='".$row['sort']."'></td>";

				echo "<td style='width:100px'>";
				echo "<select name='action'>";
				echo "<option value='sort' selected>"._ND_SORT_KEYWORD;
				echo "<option value='change'>"._ND_CHANGE_KEYWORD;
				echo "<option value='new_keyword'>"._ND_ADD_NEYKEYWORD;
				echo "<option value='delete'>"._ND_DELETE_KEYWORD;
				echo "</select></td>";

				echo "<td class='list_odd' style='width:50px; text-align:center'>";
				echo "<input type='submit' value='Go'>";
				echo "<input type='hidden' name='id' value='".$row['kw_id']."'>";
				echo "<input type='hidden' value='keyword' name='mode'></td>";
				
				echo "</tr></form>";
				$i++;
			}
		}
		echo "</table>";
		
		echo "<table style='margin: 20px 0 20px 0; border:0'>";
		echo "<tr><td>";
		echo "<form action='".$url."' method='POST' style='margin:0; padding:0'>";
		echo "<input type='submit' value='"._ND_ADD_NEYCATEGORY."'>";
		echo "<input type='hidden' value='keyword' name='mode'>";
		echo "<input type='hidden' value='new_category' name='action'>";
		echo "</form>";
		echo "</td></tr></table>";

		# keyword
		
		$sql = "SELECT * FROM ".$xoopsDB->prefix('newdb_keyword')." WHERE path='/' ORDER BY sort";
		$rs = $xoopsDB->query($sql);
		if($xoopsDB->getRowsNum($rs) > 0){
			$box=1;
			while($row = $xoopsDB->fetchArray($rs)){
				echo "<div id='box".$box."' style='display:none;'>";
				echo "<div class='title'>"._ND_KEYWORD."</div>";
				echo "<div class='title_desc'>"._ND_KEYWORD_DESC."</div>";

				$box++;
				
				$category = $row['keyword'];
				$path = $row['path'].$row['kw_id'].'/';
				$id = $row['kw_id'];

				echo "<table class='list_table' style='width:550px; text-align:left'>";
				echo "<tr><th colspan='4'>".$category."</th></tr>";
				
				Make_tree('/'.$id.'/', $category, $url);
				
				echo "</table></div>";
			}
		}
		echo "</center>";
		xoops_cp_footer();
	}
	
	
	function Make_tree($path, $category, $url){
	
		global $xoopsDB;
		
		$sql = "SELECT * FROM ".$xoopsDB->prefix('newdb_keyword')." WHERE path ='".$path."' ORDER BY sort";
		$rs = $xoopsDB->query($sql);
		if($xoopsDB->getRowsNum($rs) > 0){
			while($row = $xoopsDB->fetchArray($rs)){
				

				$path = explode('/', $row['path']);
				$path4show = '';
				for($i=0; $i<count($path); $i++){
					if($path[$i]){
						$sql = "SELECT * FROM ".$xoopsDB->prefix('newdb_keyword')." WHERE kw_id='".$path[$i]."'";
						$rs2 = $xoopsDB->query($sql);
						$row2 = $xoopsDB->fetchArray($rs2);
						$path4show .= $row2['keyword'].'/';
					}
				}
				$path4show = str_replace($category.'/', '', $path4show.$row['keyword']);
				$path = $row['path'].$row['kw_id'].'/';

				echo "<form action='".$url."' method='POST' style='margin:0; text-align:right;'>";
				echo "<tr><td>".$path4show."</td>";
				echo "<td style='width:50px; text-align:center' class='list_odd'>";
				echo "<input type='text' size='2' name='sort_num' value='".$row['sort']."'>";
				echo "</td><td style='width:100px'>";
				echo "<select name='action'>";
				echo "<option value='sort' selected>"._ND_SORT_KEYWORD;
				echo "<option value='change'>"._ND_CHANGE_KEYWORD;
				echo "<option value='new_keyword'>"._ND_ADD_NEYKEYWORD;
				echo "<option value='delete'>"._ND_DELETE_KEYWORD;
				echo "</select>";
				echo "</td><td style='width:50px; text-align:center' class='list_odd'>";
				echo "<input type='submit' value='Go'>";
				echo "<input type='hidden' name='id' value='".$row['kw_id']."'>";
				echo "<input type='hidden' value='keyword' name='mode'>";
				echo "</td></tr></form>";

				$sql = "SELECT * FROM ".$xoopsDB->prefix('newdb_keyword')." WHERE path like'".$path."%' ORDER BY sort";
				$rs2 = $xoopsDB->query($sql);
				if($xoopsDB->getRowsNum($rs2) > 0){
					Make_tree($path, $category, $url);
				}
			}
		}
		return;
	}
	
?>