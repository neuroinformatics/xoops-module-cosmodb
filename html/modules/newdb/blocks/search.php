<?php

	function b_newdb_search(){

		global $xoopsDB;
		$sql = "SELECT * FROM ".$xoopsDB->prefix('newdb_component_master');
		$sql.= " WHERE name='Data Name' AND type='1'";
		$rs = $xoopsDB->query($sql);
		$row = $xoopsDB->fetchArray($rs);
		
		$form = "<form action='".XOOPS_URL."/modules/newdb/search_block.php' method='GET'>";
		$form .= "<select name='type'>";
		$form .= "<option value='comment'>"._ND_BLOCK_COMMENT."</option>";
		$form .= "<option value='file'>"._ND_BLOCK_FILE."</option>";
		$form .= "<option value='id'>ID</option>";
		$form .= "<option value='dataname'>".$row['tag']."</option>";
		$form .= "</select>";
		$form .= "<input type='text' name='kw' style='width:120px'>";
		$form .= "</form>";
			
		$block['content'] = $form;
	
		return $block;
	}

?>