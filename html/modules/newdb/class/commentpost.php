<?php

/**
 * class CommentPost.
 *
 * This register comment on the database.
 */

class commentpost
{
    public $myts;
    public $db;
    public $method;
    public $subject;
    public $message;
    public $lid;
    public $cid;
    public $uid;
    public $type;
    public $error;

    /**
     * Class Constructor.
     */
    public function CommentPost()
    {
        $this->myts = &MyTextSanitizer::getInstance();
        $this->db = &Database::getInstance();
        $this->method = '';
        $this->subject = '';
        $this->message = '';
        $this->lid = -1;
        $this->cid = -1;
        $this->type = 'user';
        $this->error = '';
    }

    public function setMethod($method)
    {
        $this->method = $this->myts->stripSlashesGPC($method);
    }

    public function setSubject($subject)
    {
        $this->subject = $this->myts->makeTboxData4Save($subject);
    }

    public function setMessage($message)
    {
        $this->message = $this->myts->makeTareaData4Save($message);
    }

    public function setLid($lid)
    {
        $this->lid = intval($lid);
    }

    public function setCid($cid)
    {
        $this->cid = intval($cid);
    }

    public function setUid($uid)
    {
        $this->uid = intval($uid);
    }

    public function setType($type)
    {
        $this->type = $this->myts->stripSlashesGPC($type);
    }

    public function register()
    {
        $this->error = '';

        switch ($this->method) {
            case 'new':
                if (!$this->__regNew()) {
                    $this->error = 'comment register error. (commentpost.php line '.__LINE__.')<br>';
                }
                break;

            case 'edit':
                if (!$this->__update()) {
                    $this->error = 'comment update error. (commentpost.php line '.__LINE__.')<br>';
                }
                break;

            case 'delete':
                if (!$this->__delete()) {
                    $this->error = 'comment delete error. (commentpost.php line '.__LINE__.')<br>';
                }
                break;

            default:
                break;
        }

        if ($this->error != '') {
            return false;
        }

        return true;
    }

    public function __regNew()
    {
        if (empty($this->subject)) {
            $this->subject = 'no title';
        }

        if ($this->lid > 0) {
            $sql = 'INSERT INTO '.$this->db->prefix('newdb_comment');
            $sql .= " VALUES('','','".$this->subject."','".$this->message."','".time()."','".$this->uid."')";
            $rs = $this->db->query($sql);
            if (!$rs) {
                return false;
            }
            $this->cid = $this->db->getInsertId($rs);

            $sql = 'INSERT INTO '.$this->db->prefix('newdb_comment_topic');
            $sql .= " VALUES('','".$this->lid."','".$this->cid."','".$this->type."')";
            $rs = $this->db->query($sql);
            if (!$rs) {
                return false;
            }
        } elseif ($this->cid > 0) {
            $sql = 'INSERT INTO '.$this->db->prefix('newdb_comment');
            $sql .= " VALUES('','".$this->cid."','".$this->subject."','".$this->message."','".time()."','".$this->uid."')";
            $rs = $this->db->query($sql);
            if (!$rs) {
                return false;
            }
        }

        return true;
    }

    public function __update()
    {
        $sql = 'UPDATE '.$this->db->prefix('newdb_comment');
        $sql .= " SET subject='".$this->subject."', message='".$this->message."'";
        $sql .= " WHERE com_id='".$this->cid."'";
        $rs = $this->db->query($sql);
        if (!$rs) {
            return false;
        }

        return true;
    }

    public function __delete()
    {
        $sql = 'SELECT pcom_id FROM '.$this->db->prefix('newdb_comment')." WHERE com_id='".$this->cid."'";
        $rs = $this->db->query($sql);
        if ($rs) {
            $row = $this->db->fetchArray($rs);
            // topic top comment
            if ($row['pcom_id'] == 0) {
                $sql = 'SELECT com_id FROM '.$this->db->prefix('newdb_comment')." WHERE pcom_id='".$this->cid."'";
                $rs = $this->db->query($sql);
                if (!$rs) {
                    return false;
                }

                // if this has child
                if ($this->db->getRowsNum($rs) > 0) {
                    $sql = 'UPDATE '.$this->db->prefix('newdb_comment')." SET message='this comment was deleted.' WHERE com_id='".$this->cid."'";
                    $rs = $this->db->queryF($sql);
                    if (!$rs) {
                        return false;
                    }
                } else {
                    $sql = 'DELETE FROM '.$this->db->prefix('newdb_comment')." WHERE com_id='".$this->cid."'";
                    $rs = $this->db->queryF($sql);
                    if (!$rs) {
                        return false;
                    }

                    $sql = 'DELETE FROM '.$this->db->prefix('newdb_comment_topic')." WHERE com_id='".$this->cid."'";
                    $rs = $this->db->queryF($sql);
                    if (!$rs) {
                        return false;
                    }
                }

            // child comment
            } else {
                $sql = 'DELETE FROM '.$this->db->prefix('newdb_comment')." WHERE com_id='".$this->cid."'";
                $rs = $this->db->queryF($sql);
                if (!$rs) {
                    return false;
                }
            }
        }

        return true;
    }

    /**
     * error.
     */
    public function error()
    {
        return $this->error;
    }
}
