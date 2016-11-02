<?php

/**
 * class CommentPost
 *
 * This register comment on the database.
 */
 
class CommentPost{

	var $myts;
	var $db;
	var $method;
	var $subject;
	var $message;
	var $lid;
	var $cid;
	var $uid;
	var $type;
	var $error;
	
	/**
	 * Class Constructor
	 */
	function CommentPost(){

		$this->myts =& MyTextSanitizer::getInstance();
		$this->db =& Database::getInstance();
		$this->method = '';
		$this->subject = '';
		$this->message = '';
		$this->lid = -1;
		$this->cid = -1;
		$this->type = 'user';
		$this->error = '';
	}

	function setMethod($method){
		$this->method = $this->myts->stripSlashesGPC($method);
	}
	
	function setSubject($subject){
		$this->subject = $this->myts->makeTboxData4Save($subject);
	}
	
	function setMessage($message){
		$this->message = $this->myts->makeTareaData4Save($message);
	}
	
	function setLid($lid){
		$this->lid = intval($lid);
	}
	
	function setCid($cid){
		$this->cid = intval($cid);	
	}
	
	function setUid($uid){
		$this->uid = intval($uid);
	}
	
	function setType($type){
		$this->type = $this->myts->stripSlashesGPC($type);
	}
	
	function register(){
		$this->error = '';
	
		switch($this->method){
			case 'new':
				if(!$this->__regNew())
					$this->error = 'comment register error. (commentpost.php line '.__LINE__.')<br>';
				break;
				
			case 'edit':
				if(!$this->__update())
					$this->error = 'comment update error. (commentpost.php line '.__LINE__.')<br>';
				break;
				
			case 'delete':
				if(!$this->__delete())
					$this->error = 'comment delete error. (commentpost.php line '.__LINE__.')<br>';
				break;
				
			default:
				break;
		}

		if($this->error != ''){
			return false;
		}
		return true;
	}
	
	function __regNew(){
	
		if(empty($this->subject)) $this->subject = 'no title';
	
		if($this->lid > 0){
			$sql = "INSERT INTO ".$this->db->prefix('newdb_comment');
			$sql.= " VALUES('','','".$this->subject."','".$this->message."','".time()."','".$this->uid."')";
			$rs = $this->db->query($sql);
			if(!$rs) return false;
			$this->cid = $this->db->getInsertId($rs);

			$sql = "INSERT INTO ".$this->db->prefix('newdb_comment_topic');
			$sql.= " VALUES('','".$this->lid."','".$this->cid."','".$this->type."')";
			$rs = $this->db->query($sql);
			if(!$rs) return false;
			
		}elseif($this->cid > 0){
			$sql = "INSERT INTO ".$this->db->prefix('newdb_comment');
			$sql.= " VALUES('','".$this->cid."','".$this->subject."','".$this->message."','".time()."','".$this->uid."')";
			$rs = $this->db->query($sql);
			if(!$rs) return false;
		}
		return true;
	}
	
	function __update(){
		$sql = "UPDATE ".$this->db->prefix('newdb_comment');
		$sql.= " SET subject='".$this->subject."', message='".$this->message."'";
		$sql.= " WHERE com_id='".$this->cid."'";
		$rs = $this->db->query($sql);
		if(!$rs) return false;
		return true;
	}
	
	function __delete(){
	
		$sql = "SELECT pcom_id FROM ".$this->db->prefix('newdb_comment')." WHERE com_id='".$this->cid."'";
		$rs = $this->db->query($sql);
		if($rs){
			$row = $this->db->fetchArray($rs);
			# topic top comment
			if($row['pcom_id'] == 0){
				$sql = "SELECT com_id FROM ".$this->db->prefix('newdb_comment')." WHERE pcom_id='".$this->cid."'";
				$rs = $this->db->query($sql);
				if(!$rs) return false;
				
				# if this has child
				if($this->db->getRowsNum($rs) > 0){
					$sql = "UPDATE ".$this->db->prefix('newdb_comment')." SET message='this comment was deleted.' WHERE com_id='".$this->cid."'";
					$rs = $this->db->queryF($sql);
					if(!$rs) return false;

				}else{
					$sql = "DELETE FROM ".$this->db->prefix('newdb_comment')." WHERE com_id='".$this->cid."'";
					$rs = $this->db->queryF($sql);
					if(!$rs) return false;

					$sql = "DELETE FROM ".$this->db->prefix('newdb_comment_topic')." WHERE com_id='".$this->cid."'";
					$rs = $this->db->queryF($sql);
					if(!$rs) return false;
				}
	
			# child comment
			}else{
				$sql = "DELETE FROM ".$this->db->prefix('newdb_comment')." WHERE com_id='".$this->cid."'";
				$rs = $this->db->queryF($sql);
				if(!$rs) return false;
			}
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