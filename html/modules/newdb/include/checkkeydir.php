<?php
	if (!defined('XOOPS_ROOT_PATH')) { exit(); }

	function checkKeyDir($label_id){
		$path = EXTRACT_PATH.'/'.$label_id;

		if(!is_dir($path) && !mkdir($path, 0777)){
			redirect_header(MOD_URL, 2, $label_id.' '._ND_DIR_FALSE);
		}
		if(!is_dir($path.'/thumbnail') && !mkdir($path.'/thumbnail', 0777)){
			redirect_header(MOD_URL, 2, 'thumbnail '._ND_DIR_FALSE);
		}
		if(!is_dir($path.'/data') && !mkdir($path.'/data', 0777)){
			redirect_header(MOD_URL, 2, 'data '._ND_DIR_FALSE);
		}
		if(!is_dir($path.'/trashbox') && !mkdir($path.'/trashbox', 0777)){
			redirect_header(MOD_URL, 2, 'trashbox '._ND_DIR_FALSE);
		}
	}

?>