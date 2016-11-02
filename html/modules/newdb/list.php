<?php
	include 'header.php';
	include 'class/listmanager.php';
	include XOOPS_ROOT_PATH.'/header.php';
	include 'style.css';

	$list_id = intval($_GET['id']);
	if(!$list_id) redirect_header(MOD_URL, 2, _ND_NACCESS);
	
	$lm = new ListManager();
	if(!$lm->setListId($list_id)) redirect_header(MOD_URL, 2, $lm->error());

	if(isset($_GET['user'])){
		$lm->setUser(intval($_GET['user']));
	}else{
		$lm->setUser(time());
	}

	if(isset($_GET['kws'])) $lm->setKwsFlg();
	(isset($_GET['sort'])) ? $s_tg = $_GET['sort'] : $s_tg = '1';
	(isset($_GET['sort_method'])) ? $s_mt = $_GET['sort_method'] : $s_mt = 'desc';
	$lm->setSort($s_tg, $s_mt);

	if(isset($_GET['size'])) $lm->changeSize($_GET['size']);
	if(isset($_GET['item'])) $lm->setPage($_GET['item'], $_GET['n']);
	if(isset($_GET['refine'])){

		if(isset($_GET['all'])){
			$lm->refine_flg = 0;

		}elseif($_GET['refine'] == 'usedb'){
			$lm->setRefineFromDB();
					
		}else{
			$author = '';
			if(isset($_GET['author'])){
				foreach($_GET['author'] as $value){
					if($author != '') $author.=',';
					$author.= intval($value);	
				}
			}
			$from = strtotime(intval($_GET['year1']).'/'.intval($_GET['month1']).'/'.intval($_GET['day1']));
			$to = strtotime(intval($_GET['year2']).'/'.intval($_GET['month2']).'/'.intval($_GET['day2']));
			
			$component = array();
			for($i=2; $i<4; $i++){
				$rs = $xoopsDB->query("SELECT * FROM ".$xoopsDB->prefix('newdb_component_master')." WHERE type='".$i."'");
				$n = $xoopsDB->getRowsNum($rs);
				for($j=0; $j<$n; $j++){
					($i == 3) ? $k = 'CC'.$j : $k = 'CR'.$j;
					if(isset($_GET[$k])){
						$comp_id = intval($_GET[$k.'_id']);
						foreach($_GET[$k] as $value){
							$value = $myts -> stripSlashesGPC($value);
							$component[] = array($comp_id, $value);
						}
					}
				}
			}

			if(isset($_GET['more'])){
				$lm->setRefine($author, $from, $to, $component, 1);
			}else{
				$lm->setRefine($author, $from, $to, $component, 0);
			}
		}
	}

	# keyword refine(search)
	if(isset($_GET['kws'])){
		$kw = '';
		if(!empty($_GET['kw'])){
			foreach($_GET['kw'] as $value){
				if($kw != '') $kw.=',';
				$kw.= intval($value);
			}
		}
		$andor = $_GET['andor'];
		$notkws = '';
		if(!empty($_GET['notkws'])) $notkws = $myts->stripSlashesGPC($_GET['notkws']);
		$lm->setKLabels($kw, $andor, $notkws);
	}

	$label_list = array();
	$label_list = $lm->getLabels();
	
	switch($lm->type){
		# LIST
		case '1':
			$pl = $lm->getPagelink();
			echo "<script language='JavaScript' src='tab.js'></script>\n";
			echo "<script language='JavaScript' src='clipboard.js'></script>\n";
			echo "<div id='copy'></div>";
			echo "<div style='text-align:right'>".$lm->getListlink()."</div>";
			echo "<table style='width:100%; margin:0 0 5px 0; border-bottom:1px solid #eeeeee'><tr>";
			echo "<td>".$pl."</td>";
			echo "</tr></table>";
			echo "<table style='width:100%; margin-bottom:20px'><tr>";
			echo "<td style='text-align:right'>".$lm->getSortbox()."</td>";
			echo "</tr></table>";

			echo "<table class='list_table'>";
			echo "<tr>".$lm->list_th."</tr>";

			for($i=0; $i<count($label_list); $i++){
				$sql = "SELECT * FROM ".$xoopsDB->prefix('newdb_master')." WHERE label_id='".$label_list[$i]."'";
				$rs = $xoopsDB->query($sql);
				$row = $xoopsDB->fetchArray($rs);
				$gv = $lm->getValues($row['label_id'], $row['label'], $row['author'], $row['reg_date'], $row['views'], $xoopsModuleConfig['dname_flg']);
				echo "<tr class='listrow'>".$gv."</tr>";
			}
			echo "</table><br>".$pl;

			# refine box
			echo "<br><center><a href=\"javascript:seltab('rbox', 'head', 10, 1)\">"._ND_LIST_REFINE."</a></center>";
			echo "<div id='rbox1' style='display:none; margin:0'><center>";
			echo "<form method='GET' action='list.php' style='margin-top:0'>";
			echo $lm->getRefinebox();
			echo "</form>";
			echo "<form method='POST' action='kws.php' style='margin:0px'>";
			echo "<input type='submit' value='"._ND_LIST_KEYREFINE."' name='srefine' class='button'> ";
			if($uid && $lm->refine_flg) echo "<input type='submit' value='"._ND_LIST_SAVELIST."' name='ssave' class='button'>";
			echo "<input type='hidden' value='".$lm->uid."' name='user'>";
			echo "</form></center></div>";
			break;

		# THUMBNAIL
		case '2':		
			$pl = $lm->getPagelink();
			echo "<script language='JavaScript' src='tab.js'></script>\n";
			echo "<script language='JavaScript' src='border.js'></script>\n";
			echo "<script language='JavaScript' src='clipboard.js'></script>\n";
			echo "<div id='copy'></div>";
			echo "<div style='text-align:right'>".$lm->getListlink()."</div>";
			echo "<table style='width:100%; margin:0 0 5px 0; border-bottom:1px solid #eeeeee'><tr>";
			echo "<td>".$pl."</td>";
			echo "</tr></table>";
			echo "<table style='width:100%; margin-bottom:20px'><tr>";
			echo "<td style='text-align:right'>".$lm->getSortbox()."</td>";
			echo "</tr></table>";
		
			echo "<table><tr>";
			$cnt = 0;
			for($i=0; $i<count($label_list); $i++){
				$sql = "SELECT * FROM ".$xoopsDB->prefix('newdb_master')." WHERE label_id='".$label_list[$i]."'";
				$rs = $xoopsDB->query($sql);
				$row = $xoopsDB->fetchArray($rs);
				$thumb_temp = $lm->getValues($row['label_id'], $row['label'], $row['author'], $row['reg_date'], $row['views'], $xoopsModuleConfig['dname_flg']);
				
				# get image
				$img = '';
				$img_path = EXTRACT_PATH.'/'.$row['label_id'].'/thumbnail/'.$lm->thumb_dir.'/';
				$img_url = MOD_URL.'/extract/'.$row['label_id'].'/thumbnail/'.$lm->thumb_dir.'/';
				$img_cnt = 0;

				# get image size
				if($lm->thumb_active_size[1] && $lm->thumb_active_size[2]){
					$img_size = "style='width:".$lm->thumb_active_size[1]."px; height:".$lm->thumb_active_size[2]."px; ";

				}elseif($lm->thumb_active_size[1]){
					$img_size = "style='width:".$lm->thumb_active_size[1]."px; ";

				}elseif($lm->thumb_active_size[2]){
					$img_size = "style='height:".$lm->thumb_active_size[2]."px; ";

				}else{
					$img_size = '';
				}
				$img_size.= " border:2px solid white'";
				
				# show images
				if(is_dir($img_path)){
				  if($handle = opendir($img_path)){
				    while(false !== $file = readdir($handle)){
				      if($file != "." && $file != ".."){
									
								$img_tag = "<img src='".$img_url.$file."' ".$img_size." alt='".$file."' ";
								$img_tag.= "id='".$cnt."' onmouseover=\"show('".$cnt."')\" onmouseout=\"hide('".$cnt."')\">";
								$img_tag = "<a href='".$img_url.$file."' target='_blank'>".$img_tag."</a>";
								$thumb_temp2 = $thumb_temp;
								$thumb_temp2 = str_replace('{Image}', $img_tag, $thumb_temp);
								
								if($cnt != 0 && !($cnt % $lm->thumb_active_size[3])) echo "</tr><tr>";
								echo "<td>".$thumb_temp2."</td>";
								$cnt++;
								$img_cnt++;
							}
						}
					}
					closedir($handle);
				}
				
				# no image
				if(!$img_cnt){
					$margin = ($lm->thumb_active_size[2] - 20) / 2;
					if(!$lm->thumb_active_size[2]) $margin = 10;

					$img_tag = "<img src='".MOD_URL."/images/noimage.gif' style='margin:".$margin."px 0 ".$margin."px 0;'>";
					$thumb_temp2 = $thumb_temp;
					$thumb_temp2 = str_replace('{Image}', $img_tag, $thumb_temp);

					if($cnt != 0 && !($cnt % $lm->thumb_active_size[3])) echo "</tr><tr>";
					echo "<td>".$thumb_temp2."</td>";
					$cnt++;
				}
			}			
			
			echo "</tr></table><br>".$pl;

			# refine box
			echo "<br><center><a href=\"javascript:seltab('rbox', 'head', 10, 1)\">"._ND_LIST_REFINE."</a></center>";
			echo "<div id='rbox1' style='display:none; margin:0'><center>";
			echo "<form method='GET' action='list.php' style='margin-top:0'>";
			echo $lm->getRefinebox();
			echo "</form>";
			echo "<form method='POST' action='kws.php' style='margin:0px'>";
			echo "<input type='submit' value='"._ND_LIST_KEYREFINE."' name='srefine' class='button'> ";
			if($uid && $lm->refine_flg) echo "<input type='submit' value='"._ND_LIST_SAVELIST."' name='ssave' class='button'>";
			echo "<input type='hidden' value='".$lm->uid."' name='user'>";
			echo "</form></center></div>";
			break;
	}
	
	include XOOPS_ROOT_PATH.'/footer.php';
?>