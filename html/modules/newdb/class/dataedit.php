<?php

class DataEdit{

	var $db;
	var $uid;
	var $label;
	var $labelid;
	var $list;

	function DataEdit($uid, $labelid, $label){
		$this->db =& Database::getInstance();
		$this->uid = $uid;
		$this->labelid = $labelid;
		$this->label = $label;
	}
	

	# return directory which exists in the database
	function getDirlist(){
		$this->list = "<option value='0'>Top</option>";
		$this->__getDirs($this->labelid,'', 0);
		return $this->list;
	}
	
	# return data which exists in the database
	function getDatalist($isadmin=0, $mode='all'){
		$this->list = "<table class='list_table'>";
		$this->__getItems($this->labelid,'', 0, $isadmin, $mode);
		$this->list.= "</table>";
		return $this->list;
	}
	
	# return file which exists in the trashbox dir
	function getTrashlist($base_path){
		$this->list = "<table class='list_table'>";
		$trash_path = $base_path.'/'.$this->labelid.'/trashbox';
		
	  if($handle = opendir($trash_path)){
	    while(false !== $file = readdir($handle)){
	      if($file != '.' && $file != '..'){
	        if(!is_dir($trash_path.'/'.$file)){
						$this->list.= "<tr><td class='even' style='width:5%; text-align:center'>";
						$this->list.= "<input type='checkbox' name='data[]' value='".$file."'>";
						$this->list.= "</td><td>&nbsp;";
						$this->list .= "<img src='images/jmenu/page.gif'>".$file;
						$this->list .= "</td></tr>";
					}
				}
			}
			closedir($handle);
		}

		$this->list.= "</table>";
		return $this->list;
	}

	function __getDirs($labelid, $dir, $n){
		
		$sql = "SELECT * FROM ".$this->db->prefix('newdb_item');
		$sql.= " WHERE label_id='".$labelid."' AND path='".$dir."' AND type='dir'";
		$rs = $this->db->query($sql);
		while ($row = $this->db->fetchArray($rs)){
			
			$this->list.= "<option value='".$row['item_id']."'>";
			for($i=0; $i<$n; $i++) $this->list.= "--";
			$this->list.= $row['name']."</option>";
			
			$n++;
			if($dir == ''){
				$this->list .= $this->__getDirs($labelid, $row['name'], $n);
			}else{
				$this->list .= $this->__getDirs($labelid, $dir.'/'.$row['name'], $n);
			}
			$n--;
		}
	}

	
	function __getItems($labelid, $dir, $n, $isadmin, $mode){
		
		$sql = "SELECT * FROM ".$this->db->prefix('newdb_item');
		$sql.= " WHERE label_id='".$labelid."' AND path='".$dir."' ORDER BY type";
		$rs = $this->db->query($sql);
		while ($row = $this->db->fetchArray($rs)){
			
			$iid = $row['item_id'];
			$type = $row['type'];
			$name = $row['name'];
			
			$sql2 = "SELECT uname FROM ".$this->db->prefix('users')." where uid='".$row['reg_user']."'";
			$rs2 = $this->db->query($sql2);
			$row2 = $this->db->fetchArray($rs2);
			$owner = $row2['uname'];
						
			if($row['path'] != ''){
				$path = $row['path'].'/'.$name;
			}else{
				$path = $name;
			}
			
			
			$tr = "<tr><td class='even' style='width:5%; text-align:center'>";
			$checkbox = '';
			if($this->uid == $row['reg_user'] || $isadmin){
				$checkbox = "<input type='checkbox' name='data[]' value='".$iid."'>";
			}
			$sp = '';
			for($i=0; $i<$n; $i++) $sp.= "&nbsp;&nbsp;&nbsp;";
			$td = "</td><td>&nbsp;".$sp;
						
			if($type == 'dir'){
			
				$this->list.= $tr;
				if($mode == 'all' || $mode == 'dir'){
					$this->list.= $checkbox;
				}
				$this->list.= $td;
			
				$this->list .= "<img src='images/jmenu/fold_closed.gif'>".$name;
				$this->list .= "</td><td style='width:120px'>".$owner;

				$n++;
				if($dir == ''){
					$this->list .= $this->__getItems($labelid, $name, $n, $isadmin, $mode);
				}else{
					$this->list .= $this->__getItems($labelid, $dir.'/'.$name, $n, $isadmin, $mode);
				}
				$n--;
		
				$this->list.= "</td></tr>";
	
			}elseif($type == 'file'){
				if($mode == 'all' || $mode == 'file'){
					$this->list.= $tr;
					$this->list.= $checkbox;
					$this->list.= $td;
					$this->list .= "<img src='images/jmenu/page.gif'>".$name;
					$this->list .= "</td><td style='width:120px'>".$owner;
					$this->list.= "</td></tr>";
				}
			}
		}
	}
	
	
	function moveFiles($file, $dir, $base_path){
	
		$base_path2 = $base_path.'/'.$this->labelid.'/data';
		$dpath = $base_path2.'/'.$dir;

		foreach($file as $k => $v){

			$path = $base_path2.'/'.$v;
			$f = explode('/', $v);
			$file = $f[count($f)-1];
		
			if(file_exists($path)){
				if(copy($path, $dpath.'/'.$file) && unlink($path)){
					$sql = "UPDATE ".$this->db->prefix('newdb_item')." SET path='".$dir."' WHERE item_id='".$k."'";
					$rs = $this->db->query($sql);
				}
			}
		}
	}
	
	
	function moveDirs($target, $dir, $base_path){
	
		$base_path2 = $base_path.'/'.$this->labelid.'/data';
		if(!empty($dir)){
			$dpath = $base_path2.'/'.$dir;
		}else{
			$dpath = $base_path2;
		}

		foreach($target as $k => $v){
			$from = $base_path2.'/'.$v;
			$f = explode('/', $v);
			$d = $f[count($f)-1];
			if(is_dir($from)) $this->__moveDirs($from, $dpath.'/'.$d, $base_path2);
		}
	}
	
	function __moveDirs($from, $to, $base_path){

		if(!is_dir($to) && !mkdir($to, 0777)) return;
		
		$from_path = str_replace($base_path, '', $from);
		$from_name = explode('/', $from_path);
		$from_name = $from_name[count($from_name)-1];
		$from_path = str_replace($from_name, '', $from_path);
		if(substr($from_path, 0, 1) == '/') $from_path = substr($from_path, 1);
		if(substr($from_path, -1) == '/') $from_path = substr($from_path, 0, -1);
		#echo $from_name."<br>".$from_path."<br><br>";
		
		$to_path = str_replace($base_path, '', $to);
		$to_name = explode('/', $to_path);
		$to_name = $to_name[count($to_name)-1];
		$to_path = str_replace($to_name, '', $to_path);
		if(substr($to_path, 0, 1) == '/') $to_path = substr($to_path, 1);
		if(substr($to_path, -1) == '/') $to_path = substr($to_path, 0, -1);
		#echo $to_name."<br>".$to_path."<br><br>";

		$sql = "UPDATE ".$this->db->prefix('newdb_item')." SET path='".$to_path."'";
		$sql.= " WHERE name='".$from_name."' AND path='".$from_path."'";
		$sql.= " AND label_id='".$this->labelid."' AND type='dir'";
		$rs = $this->db->query($sql);
				
	  if($handle = opendir($from)){
	    while(false !== $file = readdir($handle)){
	      if($file != '.' && $file != '..'){

	        if(is_dir($from.'/'.$file)){
       			$this->__moveDirs($from.'/'.$file, $to.'/'.$file, $base_path);

					}else{
						if(copy($from.'/'.$file, $to.'/'.$file)){

							$t_path = $to_name;
							if(!empty($to_path)) $t_path = $to_path.'/'.$to_name;
	
							$f_path = $from_name;
							if(!empty($from_path)) $f_path = $from_path.'/'.$from_name;

							$sql = "UPDATE ".$this->db->prefix('newdb_item')." SET path='".$t_path."'";
							$sql.= " WHERE name='".$file."' AND path='".$f_path."'";
							$sql.= " AND label_id='".$this->labelid."' AND type='file'";
							$rs = $this->db->query($sql);
						
							unlink($from.'/'.$file);
						}
					}
				}
			}
		closedir($handle);
		}
		rmdir($from);
		return;
	}
	
	
	# move file into a trashbox
	function toTrash($dir, $file, $base_path){
	
		$base_path2 = $base_path.'/'.$this->labelid;
		foreach($dir as $k => $v){
			($v == '') ? $path = $base_path2.'/data/'.$k : $path = $base_path2.'/data/'.$v.'/'.$k;
			if(is_dir($path)) $this->__toTrash($path, $base_path2.'/trashbox');
		}
		
		foreach($file as $k => $v){
			($v == '') ? $path = $base_path2.'/data/'.$k : $path = $base_path2.'/data/'.$v.'/'.$k;
			if(file_exists($path)){
				if(copy($path, $base_path2.'/trashbox/'.$k)){
					unlink($path);
				}				
			}
		}

		$this->synchroData($base_path);
	}
	
	function __toTrash($from,$to){

	  if($handle = opendir($from)){
	    while(false !== $file = readdir($handle)){
	      if($file != '.' && $file != '..'){
	        if(is_dir($from.'/'.$file)){
       			$this->__toTrash($from.'/'.$file, $to);
					}else{
						if(copy($from.'/'.$file, $to.'/'.$file)){
							unlink($from.'/'.$file);
						}
					}
				}
			}
		closedir($handle);
		}
		rmdir($from);
		return;
	}
	
	# restore file from a trashbox
	function fromTrash($dir, $file, $base_path){
	
		if($dir == '0'){
			$dpath = '';
			$to_path = $base_path.'/'.$this->labelid.'/data';
		}else{
			$sql = "SELECT * FROM ".$this->db->prefix('newdb_item');
			$sql.= " WHERE item_id='".$dir."'";
			$rs = $this->db->query($sql);
			$row = $this->db->fetchArray($rs);
			$dpath = $row['path'].'/'.$row['name'];
			$to_path = $base_path.'/'.$this->labelid.'/data/'.$dpath;
		}
		
		$trash_path = $base_path.'/'.$this->labelid.'/trashbox';
		if(!is_dir($to_path)) return false;
		
		for($i=0; $i<count($file); $i++){
			if(!file_exists($trash_path.'/'.$file[$i])) continue;
			if(copy($trash_path.'/'.$file[$i], $to_path.'/'.$file[$i])){
				unlink($trash_path.'/'.$file[$i]);
				$sql = "INSERT INTO ".$this->db->prefix('newdb_item');
				$sql.= " VALUES('','".$this->labelid."','file','".$file[$i]."','".$dpath."','".time()."','".$this->uid."')";
				$rs = $this->db->query($sql);
			}
		}
		return true;
	}


	# check database. if file doesn't exist, delete it's record from database
	# relative funcution : synchroDatabase() => regdatabase.php
	function synchroData($base_path){
		
		$sql = "SELECT * FROM ".$this->db->prefix('newdb_item')." WHERE label_id='".$this->labelid."'";
		$rs = $this->db->query($sql);
		while($row = $this->db->fetchArray($rs)){
			$path = $base_path.'/'.$this->labelid.'/data/'.$row['path'].'/'.$row['name'];
			if(!file_exists($path)){
				$sql = "DELETE FROM ".$this->db->prefix('newdb_item')." WHERE item_id='".$row['item_id']."'";
				$rs2 = $this->db->query($sql);
			}
		}
	}
	
}

?>