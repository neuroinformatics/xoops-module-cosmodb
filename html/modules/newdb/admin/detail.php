<?php

	$url = XOOPS_URL.'/modules/newdb/admin/index.php';
	if(isset($_POST['action'])){
		$action = $_POST['action'];
	}elseif(isset($_GET['action'])){
		$action = $_GET['action'];
	}else{
		$action='';
	}

	switch($action){

		case 'change':
			Change_detail($url);
			break;
	
		default:
			Detail_top($url);
			break;
	}

	function Change_detail($url){
	
		global $xoopsDB;
		$myts =& MyTextSanitizer::getInstance();

		$template = $myts->stripSlashesGPC($_POST['template']);
		$template4sql = addslashes($template);
		
		$sql = "UPDATE ".$xoopsDB->prefix('newdb_detail')." SET template='".$template4sql."'";
		$rs = $xoopsDB->query($sql);
		if($rs){
			redirect_header($url.'?mode=detail', 2, _ND_DETAIL_OK);
		}else{
			redirect_header($url.'?mode=detail', 2, _ND_DETAIL_NG);
		}
	}

	function Detail_top($url){
	
		global $xoopsDB;
		$sql = "SELECT * FROM ".$xoopsDB->prefix('newdb_detail');
		$rs = $xoopsDB->query($sql);
		$row = $xoopsDB->fetchArray($rs);
		
		xoops_cp_header();
		include 'style.css';
		echo "<center>\n";
		echo "<div class='title'>"._ND_DETAIL_ADMIN."</div>";
		echo "<div class='title_desc'>"._ND_DETAIL_ADMIN_DESC."</div>";

		echo "<form action='".$url."' method='POST' style='margin:0; padding:0'>";
		echo "<table class='list_table' style='width:500px'>";
		echo "<tr><th>"._ND_DETAIL_TEMP."</th></tr>";
		echo "<tr><td><center>";
		echo "<textarea style='width:100%; height:400px' name='template'>".$row['template']."</textarea>";
		echo "</center></td></tr>";
		echo "</table>";

		echo "<table style='margin: 20px 0 20px 0; border:0'>";
		echo "<tr><td>";
		echo "<input type='submit' value='submit'>";
		echo "<input type='hidden' value='detail' name='mode'>";
		echo "<input type='hidden' value='change' name='action'>";
		echo "</td></tr></table>";
		echo "</form>";
		echo "</center>";
		xoops_cp_footer();
	}
	
?>