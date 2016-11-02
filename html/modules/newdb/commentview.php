<?php

	include 'header.php';
	include XOOPS_ROOT_PATH.'/header.php';
	include 'style.css';

	# comment id (topic)
	if(isset($_GET['cid'])){
		$cid = intval($_GET['cid']);
	}
	
	# comment id (reply)
	$cid2 = -1;
	if(isset($_GET['cid2'])){
		$cid2 = intval($_GET['cid2']);
	}

	# 1: desc 2: asc
	$sort = 1;
	if(isset($_GET['sort'])){
		$sort = intval($_GET['sort']);
	}
	
	# 1: flat 2: thread
	$method = 1;
	if(isset($_GET['method'])){
			$method = intval($_GET['method']);
	}
	
	$sql = "SELECT * FROM ".$xoopsDB->prefix('newdb_comment_topic')." WHERE com_id='".$cid."'";
	$rs = $xoopsDB->query($sql);
	if($xoopsDB->getRowsNum($rs)){
		$row = $xoopsDB->fetchArray($rs);
		$lid = $row['label_id'];
		
		$sql = "SELECT * FROM ".$xoopsDB->prefix('newdb_master')." WHERE label_id='".$lid."'";
		$rs = $xoopsDB->query($sql);
		$row = $xoopsDB->fetchArray($rs);
		$row['label'] = $myts->makeTboxData4Show($row['label']);

		if($xoopsModuleConfig['dname_flg']){
			echo "<a href='detail.php?id=".$lid."'>".$row['label']." "._ND_COMMENTV_BACK."</a><br>";
		}else{
			echo "<a href='detail.php?id=".$lid."'>".$lid." "._ND_COMMENTV_BACK."</a><br>";
		}
	}
	
	$navi = '';
	if($sort == 1){
		$navi = "<a href='commentview.php?sort=2&method=".$method."&cid=".$cid."'>"._ND_COMMENTV_OLD."</a> | ";
	}else{
		$navi = "<a href='commentview.php?sort=1&method=".$method."&cid=".$cid."'>"._ND_COMMENTV_NEW."</a> | ";
	}
	if($method == 1){
		$navi .= "<a href='commentview.php?sort=".$sort."&method=2&cid=".$cid."'>"._ND_COMMENTV_SRED."</a><br>";
	}else{
		$navi .= "<a href='commentview.php?sort=".$sort."&method=1&cid=".$cid."'>"._ND_COMMENTV_FLAT."</a><br>";
	}
	
	$show = array();
	$sql = "SELECT * FROM ".$xoopsDB->prefix('newdb_comment')." WHERE com_id='".$cid."' OR pcom_id='".$cid."' ORDER BY com_id ";
	($sort == 1) ? $sql.= "DESC" : $sql.="ASC";
	$rs = $xoopsDB->query($sql);
	while($row = $xoopsDB->fetchArray($rs)){
	
		$child_id = $row['com_id'];
		$user = $row['reg_user'];
		$date = date("Y-m-d H:i",$row['reg_date']);
	
		# get uname
		$sql = "SELECT uname FROM ".$xoopsDB->prefix('users')." WHERE uid='".$user."'";
		$rs2 = $xoopsDB->query($sql);
		if($rs2){
			$row2 = $xoopsDB->fetchArray($rs2);
			$uname = $row2['uname'];
		}
		if($uname == '') $uname = 'Guest';

		$subject = $myts->makeTboxData4Show($row['subject']);
		$message = $myts->makeTareaData4Show($row['message'], 0);

		$delete = ''; $edit = "";
		if($user == $uid || $isadmin == 1){
			if(!$uid){
				if($xoopsModuleConfig['guest_post']){
					$delete = "<a href='comment.php?method=delete&cid=".$child_id."'><img src='images/delete.gif'></a>";
					$edit = "<a href='comment.php?method=edit&cid=".$child_id."'><img src='images/edit.gif'></a>";
				}
			}else{
				$delete = "<a href='comment.php?method=delete&cid=".$child_id."'><img src='images/delete.gif'></a>";
				$edit = "<a href='comment.php?method=edit&cid=".$child_id."'><img src='images/edit.gif'></a>";
			}
		}
		$show[$child_id] = array($uname, $date, $subject, $message, $edit.$delete);
	}

	if($method == 2 && $cid2 == -1){
		foreach($show as $key => $value){
			$cid2 = $key;
			break;
		}
	}

	# comment
	if($xoopsModuleConfig['guest_post']){
		echo "<div style='text-align:right; margin:0 10px 10px 0'>";
		echo "<a href='comment.php?method=new&cid=".$cid."'><img src='images/reply.gif'></a></div>";
	}elseif($uid && !$xoopsModuleConfig['guest_post']){
		echo "<div style='text-align:right; margin:0 10px 10px 0'>";
		echo "<a href='comment.php?method=new&cid=".$cid."'><img src='images/reply.gif'></a></div>";
	}
	echo $navi;
	echo "<table class='list_table'><tr><th colspan='2'>"._ND_COMMENTV_COM."</th></tr>";

	$cid3 = array();
	foreach($show as $key => $value){
		$cid3[] = $key;
		if($method == 2 && $cid2 !== $key) continue;
		$value[1] = str_replace(' ', '<br>', $value[1]);

		# comment body
		# [0] author, [1] date, [2] title, [3] body, [4] edit link
		echo "<tr>";
		echo "<td  class='even' style='padding:3px; text-align:center; width:90px'>";
		echo $value[0]."<br>".$value[1]."</td>";
		echo "<td><b>".$value[2]."</b><br><br>".$value[3];
		echo "<div style='text-align:right; margin-top:20px'>".$value[4]."</div>";
		echo "</td></tr>";
	}
	echo  "</table>";
	echo $navi;
	
	# before next
	if($method == 2){
		$p = array_search($cid2, $cid3);
		echo "<div style='text-align:right'>";

		if($sort == 1){
			$s1 = _ND_COMMENTV_NEXT;
			$s2 = _ND_COMMENTV_BEFORE;
			if($p+1 >=0 && $p+1 < count($cid3)){
				echo "<a href='commentview.php?sort=".$sort."&method=".$method."&cid=".$cid."&cid2=".$cid3[$p+1]."'>".$s2."</a>";
			}
			if($p-1 >=0 && $p-1 < count($cid3) && $p+1 >=0 && $p+1 < count($cid3)) echo " | ";
	
			if($p-1 >=0 && $p-1 < count($cid3)){
				echo "<a href='commentview.php?sort=".$sort."&method=".$method."&cid=".$cid."&cid2=".$cid3[$p-1]."'>".$s1."</a>";
			}

		}else{
			$s1 = _ND_COMMENTV_BEFORE;
			$s2 = _ND_COMMENTV_NEXT;
			if($p-1 >=0 && $p-1 < count($cid3)){
				echo "<a href='commentview.php?sort=".$sort."&method=".$method."&cid=".$cid."&cid2=".$cid3[$p-1]."'>".$s1."</a>";
			}
			if($p-1 >=0 && $p-1 < count($cid3) && $p+1 >=0 && $p+1 < count($cid3)) echo " | ";
			if($p+1 >=0 && $p+1 < count($cid3)){
				echo "<a href='commentview.php?sort=".$sort."&method=".$method."&cid=".$cid."&cid2=".$cid3[$p+1]."'>".$s2."</a>";
			}
		}
		echo "</div>";
	}
	
	# thread
	if($method == 2){
		echo "<br><br><table class='list_table'><tr><th>"._ND_COMMENTV_SRED2."</th><th>"._ND_COMMENTV_POSTER."</th><th>"._ND_COMMENTV_DATE."</th></tr>";
		foreach($show as $key => $value){
			echo "<tr style='padding:3px;'>";
			echo "<td><a href='commentview.php?sort=".$sort."&method=".$method."&cid=".$cid."&cid2=".$key."'>".$value[2]."</a></td>";
			echo "<td style='width:80px'>".$value[0]."</td><td style='width:130px'>".$value[1]."</td></tr>";
		}
		echo "</table>";
	}

	include XOOPS_ROOT_PATH.'/footer.php';
?>