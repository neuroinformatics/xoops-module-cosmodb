<?php

class bookmark
{
    /**
     *	private.
     */
    public $db;
    public $uid;
    public $error;
    public $tree_item;
    public $current_dir;

    /**
     * Class Constructor.
     */
    public function Bookmark($uid)
    {
        $this->db = &Database::getInstance();
        $this->uid = $uid;
        $this->error = '';
        $this->current_dir = '';
    }

    /**
     * getDynamicMenu (for DHTML tree menu).
     */
    public function getDynamicMenu()
    {
        $this->tree_item = '';
        $this->__getItems('0');

        $tree_icon = "'target'  : 'main',";
        $tree_icon .= "'icon_e'  : 'images/jmenu/empty.gif',";
        $tree_icon .= "'icon_l'  : 'images/jmenu/MiddleLine.gif',";
        $tree_icon .= "'icon_32' : 'images/jmenu/top.gif',";
        $tree_icon .= "'icon_36' : 'images/jmenu/top.gif',";
        $tree_icon .= "'icon_48' : 'images/jmenu/top.gif',";
        $tree_icon .= "'icon_52' : 'images/jmenu/top.gif',";
        $tree_icon .= "'icon_56' : 'images/jmenu/top.gif',";
        $tree_icon .= "'icon_60' : 'images/jmenu/top.gif',";
        $tree_icon .= "'icon_16' : 'images/jmenu/fold_closed.gif',";
        $tree_icon .= "'icon_20' : 'images/jmenu/fold_open.gif',";
        $tree_icon .= "'icon_24' : 'images/jmenu/fold_open.gif',";
        $tree_icon .= "'icon_28' : 'images/jmenu/fold_open.gif',";
        $tree_icon .= "'icon_0'  : 'images/jmenu/fold_closed.gif',";
        $tree_icon .= "'icon_4'  : 'images/jmenu/fold_closed.gif',";
        $tree_icon .= "'icon_2'  : 'images/jmenu/MiddleCrossLine.gif',";
        $tree_icon .= "'icon_3'  : 'images/jmenu/BottomLine.gif',";
        $tree_icon .= "'icon_18' : 'images/jmenu/MiddlePlus.gif',";
        $tree_icon .= "'icon_19' : 'images/jmenu/BottomPlus.gif',";
        $tree_icon .= "'icon_26' : 'images/jmenu/MiddleMinus.gif',";
        $tree_icon .= "'icon_27' : 'images/jmenu/BottomMinus.gif'";

        $menu_source = "<script language='JavaScript' src='tree.js'></script>\n";
        $menu_source .= "<script language='JavaScript'>\n";
        $menu_source .= "var TREE_ITEMS = [['','',".$this->tree_item."];\n";
        $menu_source .= 'var tree_tpl = { '.$tree_icon." };\n";
        $menu_source .= "new tree (TREE_ITEMS, tree_tpl);\n";
        $menu_source .= "</script>\n";

        return $menu_source;
    }

    /**
     * __getItems (for DHTML tree menu).
     */
    public function __getItems($dir)
    {
        $sql = 'SELECT * FROM '.$this->db->prefix('newdb_bookmark_dir');
        $sql .= " WHERE uid='".$this->uid."' AND pbd_id='".$dir."' ORDER BY sort";
        $rs = $this->db->query($sql);
        while ($row = $this->db->fetchArray($rs)) {
            $sql = 'SELECT * FROM '.$this->db->prefix('newdb_bookmark_file');
            $sql .= " WHERE bd_id='".$row['bd_id']."'";
            $rs2 = $this->db->query($sql);
            $num = $this->db->getRowsNum($rs2);

            $path = 'bookmark.php?bd='.$row['bd_id'];
            $this->tree_item .= "['".$row['directory'].' ('.$num.")','".$path."',";
            $this->tree_item .= $this->__getItems($row['bd_id']);
        }
        $this->tree_item .= '],';
    }

    /**
     * getBookmark.
     *
     * @return bookmark items
     */
    public function getBookmark($bd_id)
    {
        $sql = 'SELECT * FROM '.$this->db->prefix('newdb_bookmark_dir')." WHERE bd_id='".$bd_id."'";
        $rs = $this->db->query($sql);
        $row = $this->db->fetchArray($rs);
        $this->current_dir = $row['directory'];

        $bf = array();
        $sql = 'SELECT * FROM '.$this->db->prefix('newdb_bookmark_file');
        $sql .= " WHERE uid='".$this->uid."' AND bd_id='".$bd_id."' ORDER BY label_id";
        $rs = $this->db->query($sql);
        while ($row = $this->db->fetchArray($rs)) {
            $sql = 'SELECT * FROM '.$this->db->prefix('newdb_master');
            $sql .= " WHERE label_id='".$row['label_id']."'";
            $rs2 = $this->db->query($sql);
            $row2 = $this->db->fetchArray($rs2);

            $bf[] = array('bf_id' => $row['bf_id'], 'label_id' => $row['label_id'], 'label' => $row2['label'], 'note' => $row['note']);
        }

        return $bf;
    }

    /**
     * checkDir
     * make new directory when no directory exist.
     */
    public function checkDir()
    {
        $sql = 'SELECT * FROM '.$this->db->prefix('newdb_bookmark_dir');
        $sql .= " WHERE uid='".$this->uid."'";
        $rs = $this->db->query($sql);
        if ($this->db->getRowsNum($rs) == 0) {
            $sql = 'INSERT INTO '.$this->db->prefix('newdb_bookmark_dir');
            $sql .= " VALUES('', '', 'unclassified', '".$this->uid."', '0')";
            $rs = $this->db->queryF($sql);
        }
    }

    /**
     * checkChildDir.
     *
     * @return child directory id list
     */
    public function checkChildDir($bd_id, $bd_list)
    {
        $sql = 'SELECT bd_id FROM '.$this->db->prefix('newdb_bookmark_dir');
        $sql .= " WHERE pbd_id='".$bd_id."'";
        $rs = $this->db->query($sql);
        if ($this->db->getRowsNum($rs) > 0) {
            while ($row = $this->db->fetchArray($rs)) {
                $bd_list .= $row['bd_id'].',';
                $bd_list = $this->checkChildDir($row['bd_id'], $bd_list);
            }
        }

        return $bd_list;
    }

    /**
     * File functions.
     */
    public function regNewFile($bd_id, $lid, $note)
    {
        $sql = 'INSERT INTO '.$this->db->prefix('newdb_bookmark_file');
        $sql .= " VALUES('','".$bd_id."','".$lid."','".$note."','".$this->uid."')";
        $rs = $this->db->query($sql);
        if ($rs) {
            return $this->db->getInsertId();
        } else {
            return false;
        }
    }

    public function updateFile($bf_id, $note, $bd_id)
    {
        for ($i = 0, $j = 0; $i < count($bf_id); ++$i) {
            $sql = 'UPDATE '.$this->db->prefix('newdb_bookmark_file');
            $sql .= " SET note='".$note[$i]."'";
            if ($bd_id != '') {
                $sql .= " ,bd_id='".$bd_id."'";
            }
            $sql .= " WHERE bf_id='".$bf_id[$i]."'";
            $rs = $this->db->query($sql);
            if ($rs) {
                ++$j;
            }
        }

        return $j;
    }

    public function deleteFile($bf_id)
    {
        for ($i = 0, $j = 0; $i < count($bf_id); ++$i) {
            $sql = 'DELETE FROM '.$this->db->prefix('newdb_bookmark_file');
            $sql .= " WHERE bf_id='".$bf_id[$i]."'";
            $rs = $this->db->query($sql);
            if ($rs) {
                ++$j;
            }
        }

        return $j;
    }

    /**
     * Directory functions.
     */
    public function regNewDir($pbd_id, $dirname, $sort)
    {
        $sql = 'INSERT INTO '.$this->db->prefix('newdb_bookmark_dir');
        $sql .= " VALUES('','".$pbd_id."','".$dirname."','".$this->uid."','".$sort."')";
        $rs = $this->db->query($sql);
        if ($rs) {
            return $this->db->getInsertId();
        } else {
            return false;
        }
    }

    public function updateDir($set, $where)
    {
        $sql = 'UPDATE '.$this->db->prefix('newdb_bookmark_dir');
        $sql .= ' SET '.$set.' WHERE '.$where;
        $rs = $this->db->query($sql);
        if ($rs) {
            return true;
        } else {
            return false;
        }
    }

    public function deleteDir($bd_id)
    {
        $sql = 'DELETE FROM '.$this->db->prefix('newdb_bookmark_file');
        $sql .= " WHERE bd_id='".$bd_id."'";
        $rs = $this->db->query($sql);
        if ($rs) {
            $sql = 'DELETE FROM '.$this->db->prefix('newdb_bookmark_dir');
            $sql .= " WHERE bd_id='".$bd_id."'";
            $rs = $this->db->query($sql);
            if ($rs) {
                return true;
            }
        }

        return false;
    }

    /**
     * error.
     */
    public function error()
    {
        return $this->error;
    }
}
