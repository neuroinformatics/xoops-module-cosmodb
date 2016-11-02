<?php

	function b_newdb_search(){
		global $xoopsDB;
		
		$form = "<center><form action='".XOOPS_URL."/modules/newdb/search_block.php' method='GET'>";
		$form .= "<input type='radio' name='type' value='comment' checked>"._ND_BLOCK_COMMENT;
		$form .= "<input type='radio' name='type' value='file'>"._ND_BLOCK_FILE;
		$form .= "<input type='radio' name='type' value='id'>ID<br><br>";
		$form .= "<input type='text' name='kw' style='width:120px'>";
		$form .= "</form></center>";
			
		$block['content'] = $form;
	
		return $block;
	}

?>