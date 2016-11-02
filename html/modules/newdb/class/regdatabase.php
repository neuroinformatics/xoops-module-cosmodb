<?php

class RegDatabase{

	/**
	 * private
	 */
	var $db;
	var $label;
	var $labelid;
	var $extract_path;
	var $uid;
	var $error;

	/**
	 * Class Constructor
	 */
	function RegDatabase(){
		$this->db =& Database::getInstance();
		$this->label = '';
		$this->labelid = -1;
		$this->extract_path = '';
		$this->uid = -1;
		$this->error = '';
	}
	
	/**
	 * setRegLabel
	 *
	 * @param string $label
	 * @param string $extract_path
	 * @param int $uid
	 * @access public
	 * @return bool
	 */
	function setRegLabel($label, $extract_path, $uid){

		$this->label = $label;
		$this->uid = $uid;
		$this->extract_path = $extract_path;
		if(substr($extract_path, -1) == '/')	$this->extract_path = substr($extract_path, 0, -1);

		if(!is_dir($this->extract_path)){
			$this->error = '('.$this->extract_path.') does not exist. (regdatabase.php line '.__LINE__.')<br>';
			return false;
		}

		$sql = "SELECT label_id FROM ".$this->db->prefix('newdb_master')." WHERE label='".$this->label."'";
		$rs = $this->db->query($sql);
		if($rs){
			$row = $this->db->fetchArray($rs);
			$this->labelid = $row['label_id'];
		}
		return true;
	}
	
	
	function setLabel($labelid, $uid){

		$sql = "SELECT label FROM ".$this->db->prefix('newdb_master');
		$sql.= " WHERE label_id='".$labelid."'";
		$rs = $this->db->query($sql);
		if($this->db->getRowsNum($rs) == 0) return false;
		$row = $this->db->fetchArray($rs);
		$this->label = $row['label'];
		$this->labelid = $labelid;
		$this->uid = $uid;
		return true;
	}

	/**
	 * regNewLabel
	 *
	 * @access public
	 * @return bool
	 */
	function regNewLabel(){

		# register new label on table. (newdb_master)
		$sql = "INSERT INTO ".$this->db->prefix('newdb_master');
		$sql.= " VALUES('','".$this->label."','".time()."','".$this->uid."','".$this->uid."','', '0')";
		$rs = $this->db->query($sql);

		if(!$rs){
			$this->error = 'register new label error. (regdatabase.php line '.__LINE__.')<br>';
			return false;
		}

		$this->labelid = $this->db->getInsertID();
		return $this->labelid;
	}

	function regNewItems(){
		if($this->labelid < 0){
			$this->error = '__regItems() error. (regdatabase.php line '.__LINE__.')<br>';
			return false;
		}
		if(!$this->__regItems($this->extract_path.'/'.$this->labelid.'/data')){
			$this->error = '__regItems() error. (regdatabase.php line '.__LINE__.')<br>';
			return false;
		}
		return true;
	}
	
	
	/**
	 * __regItems
	 *
	 * @param string $dir (directory path)
	 * @access private
	 * @return bool
	 *
	 * register directory on the table.
	 */
	function __regItems($dir){
	
	  if($handle = opendir($dir)){
	    while(false !== $file = readdir($handle)){
	      if($file != "." && $file != ".."){
        	$path = str_replace($this->extract_path.'/'.$this->labelid.'/data', '', $dir);
        	$path = substr($path, 1);
	      
     			# insert directory
	        if(is_dir($dir.'/'.$file)){
       			$sql = "SELECT item_id FROM ".$this->db->prefix('newdb_item');
       			$sql.= " WHERE label_id='".$this->labelid."' AND name='".$file."' AND path='".$path."'";
   					$rs = $this->db->query($sql);
   					
   					if($this->db->getRowsNum($rs) < 1){
	       			$sql = "INSERT INTO ".$this->db->prefix('newdb_item');
	       			$sql.= " VALUES('','".$this->labelid."','dir','".$file."','".$path."','".time()."','".$this->uid."')";
	   					$rs = $this->db->query($sql);
	    				if(!$rs) return false;
						}
       			$this->__regItems($dir.'/'.$file);
       			
     			# insert file
					}else{
       			$sql = "SELECT item_id FROM ".$this->db->prefix('newdb_item');
       			$sql.= " WHERE label_id='".$this->labelid."' AND name='".$file."' AND path='".$path."'";
   					$rs = $this->db->query($sql);

   					if($this->db->getRowsNum($rs) < 1){
	       			$sql = "INSERT INTO ".$this->db->prefix('newdb_item');
	       			$sql.= " VALUES('','".$this->labelid."','file','".$file."','".$path."','".time()."','".$this->uid."')";
							$rs = $this->db->query($sql);
	    				if(!$rs) return false;
						}
					}
				}
			}
		closedir($handle);
		}
		return true;
	}
	
	
	# for file upload (edata.php)
	function regFile($fname, $did){
	
		if($this->uid == -1 || $this->labelid == -1){
			$this->error = 'The label and its ID are not defined. (regdatabase.php line '.__LINE__.')<br>';
			return false;
		}
		
		if($did == 0){
			$path = '';
		}else{
			$sql = "SELECT * FROM ".$this->db->prefix('newdb_item');
			$sql.= " WHERE item_id='".$did."'";
			$rs = $this->db->query($sql);
			if($this->db->getRowsNum($rs) == 0){
				$this->error = 'This directory does not exist.';
				return false;
			}
			$row = $this->db->fetchArray($rs);
			if(!empty($row['path'])){
				$path = $row['path'].'/'.$row['name'];
			}else{
				$path = $row['name'];
			}
		}
		
		$sql = "INSERT INTO ".$this->db->prefix('newdb_item');
		$sql.= " VALUES('','".$this->labelid."','file','".$fname."','".$path."','".time()."','".$this->uid."')";
		$rs = $this->db->query($sql);
		if(!$rs){
			$this->error = 'regFile() error. (regdatabase.php line '.__LINE__.')<br>';
			return false;
		}
		return true;
	}
	
	# $this->setRegLabel() => this function
	# check files. if file doesn't registered, insert it into database
	# relative funcution : synchroData() => dataedit.php
	function synchroDatabase(){
	
		if($this->labelid == -1 || $this->extract_path == ''){
			$this->error = 'The ID or EXTRACT_PATH are not defined. (regdatabase.php line '.__LINE__.')<br>';
			return false;
		}
		
		if(!$this->__regItems($this->extract_path.'/'.$this->labelid.'/data')){
			$this->error = '__regItems() error. (regdatabase.php line '.__LINE__.')<br>';
			return false;
		}
		return true;
	}


	/**
	 * error
	 *
	 * @access public
	 */
	function error(){
		return $this->error;
	}

}
?>