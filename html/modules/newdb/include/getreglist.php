<?php
	if (!defined('XOOPS_ROOT_PATH')) { exit(); }

	# list up files which exist in the upload dir.
	function getRegList($dir, &$list, $flg=1){
		if(!is_dir($dir)) return;
		global $xoopsDB;

	  if($handle = opendir($dir)){
	    while(false !== $file = readdir($handle)){
	      if($file != "." && $file != ".."){
	      	if(is_dir($dir.'/'.$file)){
	      		if(!$flg){
	 						$sql = "SELECT label_id FROM ".$xoopsDB->prefix('newdb_master');
	 						$sql.= " WHERE label='".$file."'";
							$rs = $xoopsDB->query($sql);
							if($xoopsDB->getRowsNum($rs) == 0){
		      			$size = floor(getDirSize($dir.'/'.$file, 0) / (1024));
		       			$list[$file] = $size;
		       		}
		       	}else{
	      			$size = floor(getDirSize($dir.'/'.$file, 0) / (1024));
	       			$list[$file] = $size;
		       	}
					}
				}
			}
		closedir($handle);
		}
		return;
	}
		

	/**
	 * getDirSize
	 *
	 * return directory size.
	 */
	function getDirSize($dir, $size){
	
	  if($handle = opendir($dir)){
	    while(false !== $file = readdir($handle)){
	      if($file != "." && $file != ".."){
	        if(is_dir($dir.'/'.$file)){
						$size = getDirSize($dir.'/'.$file, $size);
					}else{
						$size += filesize($dir.'/'.$file);
					}
				}
			}
		closedir($handle);
		}
		return $size;
	}
?>