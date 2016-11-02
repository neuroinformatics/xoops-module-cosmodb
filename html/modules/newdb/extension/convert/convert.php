<?php

	function Convert($base_path, $mod_path, $label_id){
	
		$data_dir = $base_path.'/'.$label_id.'/data';
		$thumb_dir = $base_path.'/'.$label_id.'/thumbnail';
	
		if(is_dir($data_dir)){
			if(__checkDir($thumb_dir)){
				__doConvert($data_dir, $thumb_dir, $mod_path.'/extension/convert');
			}
		}
	}
	
	function __doConvert($dir, $thumb_dir, $bat_dir){

	  if($handle = opendir($dir)){
	    while(false !== $file = readdir($handle)){
	      if($file != "." && $file != ".."){      
					if(is_dir($dir.'/'.$file)){
						__doConvert($dir.'/'.$file, $thumb_dir, $bat_dir);
						
					}else{
						$ext = '';
						if(preg_match("/.*(\.psd)$/i", $file)){
							$ext = 'mor';
							
						}elseif(preg_match("/.*(\.eps)$/i", $file)){
							$ext = 'phy';

						}elseif(preg_match("/.*(\.gif)$/i", $file)){
							if(__checkDir($thumb_dir.'/gif')){
								$new = $thumb_dir.'/gif/'.$file;
								if(!file_exists($new)){
									copy($dir.'/'.$file, $new);
								}
							}
						}
						
						if($ext != ''){
							if(__checkDir($thumb_dir.'/'.$ext)){
								$old = str_replace('/', '\\', $dir.'/'.$file);
								$new = str_replace('/', '\\', $thumb_dir.'/'.$ext.'/'.substr($file, 0, -3).'jpg');
								$iview = str_replace('/', '\\', $bat_dir.'/IrfanView/i_view32.exe');
								
								if(!file_exists($new)){
									exec("extension\\convert\\conv.bat \"".$iview."\" \"".$old."\" \"".$new."\"");
								}
							}
						}
	
					}
	      } 
	    } 
			closedir( $handle ); 
	  }
	} 

	function __checkDir($dir){
		if(!is_dir($dir)){
			$rc = mkdir($dir, 0777);
			if(!$rc) return false;
		}
		return true;
	}

?>