<?php
	include '../../../include/cp_header.php';

	$mode = 'default';
	if(isset($_GET['mode'])){
		$mode = $_GET['mode'];
	}elseif(isset($_POST['mode'])){
		$mode = $_POST['mode'];
	}

	switch($mode){

    case 'keyword':
    	include 'keyword.php';
			break;
			
		case 'list':
			include 'list.php';
			break;
	
		case 'component':
			include 'component.php';
			break;
		
		case 'detail':
			include 'detail.php';
			break;
			
		case 'inherite':
			include 'inherite.php';
			break;
			
		case 'import':
			include 'import.php';
			break;
	
		default:
			xoops_cp_header();
			include 'style.css';
			
		  echo "<table class='list_table' width='100%'>";
		  echo "<tr><th style='width:160px'>"._ND_ADMIN_ITEM."</th><th>"._ND_ADMIN_ITEM_DESC."</th></tr>";

		  echo "<tr><td style='padding:5px'><b><a href='".XOOPS_URL.'/modules/system/admin.php?fct=preferences&amp;op=showmod&amp;mod='.$xoopsModule->getVar('mid')."'>"._ND_CONFIG."</a></b></td>";
		  echo "<td>"._ND_CONFIG_DESC."</td></tr>";

		  echo "<tr><td style='padding:5px'><b><a href='index.php?mode=component'>" . _ND_COMPONENT_ADMIN . "</a></b></td>";
		  echo "<td>"._ND_COMPONENT_ADMIN_DESC."</td></tr>";

		  echo "<tr><td style='padding:5px'><b><a href='index.php?mode=list'>" . _ND_LIST_ADMIN . "</a></b></td>";
		  echo "<td>"._ND_LIST_ADMIN_DESC."</td></tr>";

		  echo "<tr><td style='padding:5px'><b><a href='index.php?mode=detail'>" . _ND_DETAIL_ADMIN . "</a></b></td>";
		  echo "<td>"._ND_DETAIL_ADMIN_DESC."</td></tr>";

		  echo "<tr><td style='padding:5px'><b><a href='index.php?mode=keyword'>" . _ND_KEYWORD_ADMIN . "</a></b></td>";
		  echo "<td>"._ND_KEYWORD_ADMIN_DESC."</td></tr>";

		  echo "<tr><td style='padding:5px'><b><a href='index.php?mode=inherite'>" . _ND_INHERITE_ADMIN . "</a></b></td>";
		  echo "<td>"._ND_INHERITE_ADMIN_DESC."</td></tr>";

#		  echo "<tr><td style='padding:5px'><b><a href='index.php?mode=import'>" . _ND_IMPORT_ADMIN . "</a></b></td>";
#		  echo "<td>"._ND_IMPORT_ADMIN_DESC."</td></tr>";

		  echo"</table>";
			
			xoops_cp_footer();
		  break;
	}

?>