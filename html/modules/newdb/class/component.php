<?php

/**
 * class Component.
 *
 * This provide following component for you.
 * - dynamic html tree menu
 * - comment thread
 *
 * manual:
 * $com = new Component();
 *
 * if($com->setLabel('label')){
 *   echo $com->getDynamicMenu(); //DHTML tree menu
 *   echo $com->showThread('3' [thread_limit]);            //Comment thread
 * }else{
 *     echo $com->error();
 * }
 */
class Component
{
    /**
     *    private.
     */
    public $db;
    public $labe;
    public $labelid;
    public $error;

    // for DHTML tree menu
    public $menu_source;
    public $tree_icon;
    public $tree_item;

    // for comment thread
    public $source;

    /**
     * Class Constructor.
     */
    public function __construct()
    {
        $this->db          = XoopsDatabaseFactory::getDatabaseConnection();
        $this->labe        = '';
        $this->labelid     = -1;
        $this->menu_source = '';
        $this->tree_icon   = '';
        $this->tree_item   = '';
        $this->error       = '';
        $this->source      = '';
    }

    /**
     * setLabel.
     * @param $label
     * @return bool
     */
    public function setLabel($label)
    {
        $sql = 'SELECT * FROM ' . $this->db->prefix('newdb_master') . " WHERE label='" . $label . "'";
        $rs  = $this->db->query($sql);
        if ($rs && $this->db->getRowsNum($rs) == 1) {
            $row           = $this->db->fetchArray($rs);
            $this->labelid = $row['label_id'];
            $this->label   = $label;

            return true;
        } else {
            $this->error = 'Label (' . $label . ') does not exist.<br>';

            return false;
        }
    }

    public function setLabelID($label_id)
    {
        $sql = 'SELECT * FROM ' . $this->db->prefix('newdb_master') . " WHERE label_id='" . $label_id . "'";
        $rs  = $this->db->query($sql);
        if ($rs && $this->db->getRowsNum($rs) == 1) {
            $row           = $this->db->fetchArray($rs);
            $this->labelid = $label_id;
            $this->label   = $row['label'];

            return true;
        } else {
            $this->error = 'Label id (' . $label_id . ') does not exist.<br>';

            return false;
        }
    }

    /**
     * getDynamicMenu (for DHTML tree menu).
     */
    public function getDynamicMenu()
    {
        if ($this->labelid == -1) {
            $this->error = 'Label (' . $label . ') is not found.<br>';

            return false;
        }

        $this->__getItems($this->labelid, '');

        $this->tree_icon = "'target'  : 'main',";
        $this->tree_icon .= "'icon_e'  : 'images/jmenu/empty.gif',";
        $this->tree_icon .= "'icon_l'  : 'images/jmenu/MiddleLine.gif',";
        $this->tree_icon .= "'icon_32' : 'images/jmenu/top.gif',";
        $this->tree_icon .= "'icon_36' : 'images/jmenu/top.gif',";
        $this->tree_icon .= "'icon_48' : 'images/jmenu/top.gif',";
        $this->tree_icon .= "'icon_52' : 'images/jmenu/top.gif',";
        $this->tree_icon .= "'icon_56' : 'images/jmenu/top.gif',";
        $this->tree_icon .= "'icon_60' : 'images/jmenu/top.gif',";
        $this->tree_icon .= "'icon_16' : 'images/jmenu/fold_closed.gif',";
        $this->tree_icon .= "'icon_20' : 'images/jmenu/fold_open.gif',";
        $this->tree_icon .= "'icon_24' : 'images/jmenu/fold_open.gif',";
        $this->tree_icon .= "'icon_28' : 'images/jmenu/fold_open.gif',";
        $this->tree_icon .= "'icon_0'  : 'images/jmenu/page.gif',";
        $this->tree_icon .= "'icon_4'  : 'images/jmenu/page.gif',";
        $this->tree_icon .= "'icon_2'  : 'images/jmenu/MiddleCrossLine.gif',";
        $this->tree_icon .= "'icon_3'  : 'images/jmenu/BottomLine.gif',";
        $this->tree_icon .= "'icon_18' : 'images/jmenu/MiddlePlus.gif',";
        $this->tree_icon .= "'icon_19' : 'images/jmenu/BottomPlus.gif',";
        $this->tree_icon .= "'icon_26' : 'images/jmenu/MiddleMinus.gif',";
        $this->tree_icon .= "'icon_27' : 'images/jmenu/BottomMinus.gif'";

        $this->menu_source = "<script language='JavaScript' src='tree.js'></script>\n";
        $this->menu_source .= "<script language='JavaScript'>\n";
        $this->menu_source .= "var TREE_ITEMS = [['',''," . $this->tree_item . "];\n";
        $this->menu_source .= 'var tree_tpl = { ' . $this->tree_icon . " };\n";
        $this->menu_source .= "new tree (TREE_ITEMS, tree_tpl);\n";
        $this->menu_source .= "</script>\n";

        return $this->menu_source;
    }

    /**
     * __getItems (for DHTML tree menu).
     * @param $labelid
     * @param $dir
     */
    public function __getItems($labelid, $dir)
    {
        $sql = 'SELECT * FROM ' . $this->db->prefix('newdb_item');
        $sql .= " WHERE label_id='" . $labelid . "' AND path='" . $dir . "' ORDER BY type";
        $rs = $this->db->query($sql);
        while ($row = $this->db->fetchArray($rs)) {
            $type = $row['type'];
            $name = $row['name'];
            if ($row['path'] == '') {
                $path = 'extract/' . $labelid . '/data/' . $name;
            } else {
                $path = 'extract/' . $labelid . '/data/' . $row['path'] . '/' . $name;
            }

            if (strcmp($type, 'dir') == 0) {
                $this->tree_item .= "['" . $name . "','',";
                if ($dir == '') {
                    $this->tree_item .= $this->__getItems($labelid, $name);
                } else {
                    $this->tree_item .= $this->__getItems($labelid, $dir . '/' . $name);
                }
            } elseif (strcmp($type, 'file') == 0) {
                $this->tree_item .= "['" . $name . "','" . $path . "'],";
            }
        }
        $this->tree_item .= '],';
    }

    /**
     * getAuThread (for showing comment thread).
     *
     * @param     $uid    (xoops user ID)
     * @param     $uadmin (whether mod admin user)
     * @param int $post_flg
     * @return string
     */
    public function getAuThread($uid, $uadmin, $post_flg = 0)
    {
        $myts =  MyTextSanitizer::getInstance();

        // get threads
        $message = '';
        $uname   = '';
        $time    = '';
        $thread  = array();

        $sql = 'SELECT com_id FROM ' . $this->db->prefix('newdb_comment_topic') . " WHERE label_id='" . $this->labelid . "' AND type='auth'";
        $rs  = $this->db->query($sql);
        if ($this->db->getRowsNum($rs) > 0) {
            $row = $this->db->fetchArray($rs);
            $cid = $row['com_id'];

            // get comment
            $sql = 'SELECT * FROM ' . $this->db->prefix('newdb_comment') . " WHERE com_id='" . $cid . "'";
            $rs2 = $this->db->query($sql);
            if ($rs2) {
                $row2     = $this->db->fetchArray($rs2);
                $thread[] = array('cid'     => $cid,
                                  'subject' => '',
                                  'message' => $row2['message'],
                                  'date'    => $row2['reg_date'],
                                  'user'    => $row2['reg_user']
                );

                // get reply
                $sql = 'SELECT * FROM ' . $this->db->prefix('newdb_comment') . " WHERE pcom_id='" . $cid . "' ORDER BY com_id ASC";
                $rs2 = $this->db->query($sql);
                if ($rs2) {
                    while ($row2 = $this->db->fetchArray($rs2)) {
                        $thread[] = array('cid'     => $row2['com_id'],
                                          'subject' => $row2['subject'],
                                          'message' => $row2['message'],
                                          'date'    => $row2['reg_date'],
                                          'user'    => $row2['reg_user']
                        );
                    }
                }
            } else {
                $this->error = 'comment select error. (component.php line ' . __LINE__ . ')<br>';

                return false;
            }
        } else {
            $this->error = 'topic select error. (component.php line ' . __LINE__ . ')<br>';

            return false;
        }

        $this->source = "<div style='text-align:right; margin: 0 10px 10px 0;'>";
        if (isset($cid) && $uid > 0) {
            if ($post_flg) {
                $this->source .= "<a href='comment.php?method=new&cid=" . $cid . "'>";
                $this->source .= "<img src='images/reply.gif'></a>";
            }
        }
        $this->source .= "</div><table class='list_table'>";
        $this->source .= "<tr><th colspan='2'>" . _ND_CLASS_ACOM . '</th></tr>';

        for ($i = 0, $iMax = count($thread); $i < $iMax; ++$i) {

            // get uname
            $sql = 'SELECT uname FROM ' . $this->db->prefix('users') . " WHERE uid='" . $thread[$i]['user'] . "'";
            $rs  = $this->db->query($sql);
            if ($rs) {
                $row   = $this->db->fetchArray($rs);
                $uname = $row['uname'];
            }
            if ($uname == '') {
                $uname = 'guest';
            }

            ($i == 0) ? $subject = '' : $subject = '<b>' . $myts->makeTboxData4Show($thread[$i]['subject']) . '</b><br>';
            $message = $myts->makeTareaData4Show($thread[$i]['message'], 0);
            $time    = date('Y-m-d H:i', $thread[$i]['date']);

            $edit   = '';
            $delete = '';
            if ($thread[$i]['user'] == $uid || $uadmin == 1) {
                $edit = "<a href='comment.php?method=edit&cid=" . $thread[$i]['cid'];
                ($i == 0) ? $edit .= "&type=auth'>" : $edit .= "'>";
                $edit .= "<img src='images/edit.gif'></a>";
                ($i == 0) ? $delete = '' :
                    $delete = "<a href='comment.php?method=delete&cid=" . $thread[$i]['cid'] . "'><img src='images/delete.gif'></a>";
            }

            $time = str_replace(' ', '<br>', $time);
            $this->source .= '<tr>';

            $this->source .= "<td  class='even' style='text-align:center; width:90px;'>";
            $this->source .= $uname . '<br>' . $time . '</td>';

            $this->source .= '<td>' . $subject . $message;
            $this->source .= "<div style='text-align:right; margin-top:20px;'>" . $edit . $delete . '</div>';
        }

        $this->source .= '</td></tr></table>';

        return $this->source;
    }

    /**
     * getThread (for showing comment thread).
     *
     * @param int $limit (shown thread limit)
     * @param int $guest_flg
     * @return string
     */
    public function getThread($limit, $guest_flg = 0)
    {
        $myts =  MyTextSanitizer::getInstance();

        // get threads
        $thread = array();
        $sql    = 'SELECT com_id FROM ' . $this->db->prefix('newdb_comment_topic') . " WHERE label_id='" . $this->labelid . "' AND type='user'";
        $rs     = $this->db->query($sql);
        if ($rs) {
            while ($row = $this->db->fetchArray($rs)) {
                $cid   = $row['com_id'];
                $count = 0;
                $uname = 'Guest';

                // get comment
                $sql = 'SELECT * FROM ' . $this->db->prefix('newdb_comment') . " WHERE com_id='" . $cid . "'";
                $rs2 = $this->db->query($sql);
                if ($rs2) {
                    $row2    = $this->db->fetchArray($rs2);
                    $subject = "<a href='commentview.php?cid=" . $cid . "'>";
                    $subject .= $myts->makeTboxData4Show($row2['subject']);
                    $subject .= '</a>';
                    $date = $row2['reg_date'];
                    $user = $row2['reg_user'];

                    // get reply number
                    $sql = 'SELECT * FROM ' . $this->db->prefix('newdb_comment') . " WHERE pcom_id='" . $cid . "' ORDER BY com_id DESC";
                    $rs2 = $this->db->query($sql);
                    if ($rs2) {
                        $count = $this->db->getRowsNum($rs2);
                        while ($row2 = $this->db->fetchArray($rs2)) {
                            $date = $row2['reg_date'];
                            $user = $row2['reg_user'];
                            break;
                        }
                    }

                    // get uname
                    $sql = 'SELECT uname FROM ' . $this->db->prefix('users') . " WHERE uid='" . $user . "'";
                    $rs2 = $this->db->query($sql);
                    if ($rs2) {
                        $row2  = $this->db->fetchArray($rs2);
                        $uname = $row2['uname'];
                    }
                    if (empty($uname)) {
                        $uname = 'Guest';
                    }

                    $time          = date('Y-m-d H:i', $date);
                    $thread[$date] = '<b>' . $subject . "</b><br>&nbsp;&nbsp;Last post by $uname on $time ($count) Replys";
                } else {
                    $this->error = 'comment select error. (component.php line ' . __LINE__ . ')<br>';

                    return false;
                }
            }
        } else {
            $this->error = 'topic select error. (component.php line ' . __LINE__ . ')<br>';

            return false;
        }
        $this->source = '';
        if ($guest_flg) {
            $this->source .= "<div style='text-align:right; margin: 0 10px 10px 0;'>";
            $this->source .= "<a href='comment.php?method=new&lid=" . $this->labelid . "'>";
            $this->source .= "<img src='images/post.gif'></a></div>";
        }
        $this->source .= "<table class='list_table'>";
        $this->source .= '<tr><th>' . _ND_CLASS_UCOM . '</th></tr>';

        krsort($thread);
        $i = 0;
        foreach ($thread as $key => $value) {
            if ($limit && $i == $limit) {
                break;
            }
            ($i % 2) ? $class = 'even' : $class = '';
            $this->source .= "<tr class='" . $class . "'><td>";
            $this->source .= $value;
            $this->source .= '</td></tr>';
            ++$i;
        }

        $this->source .= '</table>';
        if ($limit && count($thread) > $limit) {
            $this->source .= "<div style='text-align:right;'>";
            $this->source .= "<a href='detail.php?id=" . $this->labelid . "&com=all'>" . _ND_CLASS_SHOWALL . '</a></div>';
        }

        return $this->source;
    }

    public function getKeywordList()
    {
        $sql     = 'SELECT keyword FROM ' . $this->db->prefix('newdb_master') . " WHERE label_id='" . $this->labelid . "'";
        $rs      = $this->db->query($sql);
        $row     = $this->db->fetchArray($rs);
        $kw      = $row['keyword'];
        $kw      = explode(',', $kw);
        $kw_list = array();

        for ($i = 0, $iMax = count($kw); $i < $iMax; ++$i) {
            if ($kw[$i] == '') {
                continue;
            }
            $kw[$i] = str_replace('[', '', $kw[$i]);
            $kw[$i] = str_replace(']', '', $kw[$i]);

            $sql = 'SELECT * FROM ' . $this->db->prefix('newdb_keyword') . " WHERE kw_id='" . $kw[$i] . "'";
            $rs  = $this->db->query($sql);
            $row = $this->db->fetchArray($rs);

            $path = $row['path'] . $kw[$i];
            $path = explode('/', $path);

            $path4show = '';
            for ($j = 0, $iMax2 = count($path); $i < $iMax2; ++$j) {
                if ($path[$j]) {
                    $sql = 'SELECT * FROM ' . $this->db->prefix('newdb_keyword') . " WHERE kw_id='" . $path[$j] . "'";
                    $rs  = $this->db->query($sql);
                    $row = $this->db->fetchArray($rs);
                    $path4show .= $row['keyword'] . '/';
                }
            }
            $path4show = substr($path4show, 0, -1);
            $kw_list[] = $path4show;
        }

        $return_kw = '<table><tr><td>';
        $category  = '';
        for ($i = 0, $iMax = count($kw_list); $i < $iMax; ++$i) {
            $tmp = explode('/', $kw_list[$i]);
            if ($category != $tmp[0]) {
                if ($category != '') {
                    $return_kw .= '</ul>';
                }
                $return_kw .= '<i>' . $tmp[0] . "</i><ul style='margin:0;'>";
                $category = $tmp[0];
            }
            $return_kw .= "<li style='margin-left:20px;'>" . str_replace($tmp[0] . '/', '', $kw_list[$i]) . '</li>';
        }
        $return_kw .= '</td></tr></table>';

        return $return_kw;
    }

    /**
     * @param $ext_path
     * @param $xoops_url
     * @param $target = target directory -> ('psd|gif|...') or ('ALL') or ('')
     * @param $option = image size -> [0] width, [1] height, [2] rows
     * @return string
     */
    public function getThumbnail($ext_path, $xoops_url, $target, $option)
    {
        $return_value = "<table><tr><td>\n";

        $img_path = $ext_path . '/' . $this->labelid . '/thumbnail/';
        $img_url  = $xoops_url . '/modules/newdb/extract/' . $this->labelid . '/thumbnail';

        $option   = explode('|', $option);
        $img_size = "style='";
        if ($option[0] != 0) {
            $img_size .= 'width:' . $option[0] . '; ';
        }
        if ($option[1] != 0) {
            $img_size .= 'height:' . $option[1] . '; ';
        }
        $img_size .= "border:2px solid white'";

        $dir = array();
        if (is_dir($img_path)) {
            if ($handle = opendir($img_path)) {
                while (false !== $file = readdir($handle)) {
                    if ($file !== '.' && $file !== '..') {
                        if (is_dir($img_path . $file)) {
                            $dir[] = $file;
                        }
                    }
                }
            }
            closedir($handle);
        }

        if ($target === 'ALL' || $target == '') {
            $target = $dir;
        } else {
            $target = explode('|', $target);
        }

        $return_value .= "<table style='margin-top:10px;'>";
        $cnt = 1;
        for ($i = 0, $iMax = count($target); $i < $iMax; ++$i) {
            $target[$i] = trim($target[$i]);
            if ($target[$i] == '') {
                continue;
            }
            if (!is_dir($img_path . $target[$i])) {
                continue;
            }

            $img_array = array();
            if ($handle = opendir($img_path . $target[$i] . '/')) {
                while (false !== $file = readdir($handle)) {
                    if ($file !== '.' && $file !== '..') {
                        $img4show                     = $img_url . '/' . $target[$i] . '/' . $file;
                        $img_array[strtolower($file)] = $img4show;
                    }
                }
                closedir($handle);
            }
            ksort($img_array);

            foreach ($img_array as $key => $v) {
                $file = basename($v);
                $return_value .= "<td style='text-align:center; padding:5px;'>";
                $return_value .= "<a href='" . $v . "' target='_blank'>";
                $return_value .= "<img src='" . $v . "' " . $img_size . " alt='" . $file . "' id='" . $file . "' ";
                $return_value .= "onmouseover=\"javascript:show('" . $file . "')\" onmouseout=\"javascript:hide('" . $file . "')\"";
                $return_value .= "'></a><br>";

                // for Kanzaki Lab.
                if (strstr($v, '/phy/')) {
                    $f = explode('.', $file);
                    $f = $f[0];

                    $sql = 'SELECT * FROM ' . $this->db->prefix('newdb_item');
                    $sql .= " WHERE label_id='" . $this->labelid . "' AND name='" . $f . ".eps' OR name='" . $f . ".dat'";
                    $rs = $this->db->query($sql);
                    if ($this->db->getRowsNum($rs) > 0) {
                        while ($row = $this->db->fetchArray($rs)) {
                            if (!empty($row['path'])) {
                                $p = $row['path'] . '/';
                            } else {
                                $p = '';
                            }
                            $return_value .= "<a href='" . $xoops_url . '/modules/newdb/extract/' . $this->labelid . '/data/' . $p . $row['name']
                                             . "'>" . $row['name'] . '</a>&nbsp;&nbsp;&nbsp;';
                        }
                    } else {
                        $return_value .= $file;
                    }
                    $return_value .= "</td>\n";

                    //--------------------
                } else {
                    // image directory -> caption directory: thumbnail -> caption
                    $tmp_path     = $img_path . "$target[$i]";
                    $caption_path = str_replace('thumbnail', 'caption', $tmp_path);

                    // image file -> caption file: ex. file.jpg -> file.txt
                    $dot_pos      = strrpos($file, '.');
                    $caption_file = substr($file, 0, $dot_pos) . '.txt';
                    $caption_path = $caption_path . "/$caption_file";

                    if (file_exists($caption_path)) {
                        $fp      = fopen($caption_path, 'r');
                        $caption = '';
                        while (!feof($fp)) {
                            $caption = $caption . fgets($fp) . '<br>';
                        }
                        fclose($fp);
                        $return_value .= $caption . "</td>\n";
                    }
                }

                if (!($cnt % $option[2])) {
                    $return_value .= '</tr><tr>';
                }
                ++$cnt;
            }
        }
        $return_value .= '</tr></table>';

        return $return_value . "</td></tr></table>\n";
    }

    public function getLink($uid, $isadmin = 0, $dname_flg = 0)
    {
        $myts =  MyTextSanitizer::getInstance();

        $link_in  = '';
        $link_out = '';
        $ret      = '';
        $sql      = 'SELECT * FROM ' . $this->db->prefix('newdb_link') . " WHERE label_id='" . $this->labelid . "'";
        $rs       = $this->db->query($sql);
        while ($row = $this->db->fetchArray($rs)) {
            $name    = $myts->makeTboxData4Show($row['name']);
            $href    = $myts->makeTboxData4Show($row['href']);
            $note    = $myts->makeTboxData4Show($row['note']);
            $type    = $row['type'];
            $creater = $row['uid'];
            $link_id = $row['link_id'];

            if ($uid == $creater || $isadmin) {
                $uname = "[ <a href='link.php?mode=edit&link_id=" . $link_id . "'>edit</a> ]";
            } else {
                $sql = 'SELECT uname FROM ' . $this->db->prefix('users') . " WHERE uid='" . $creater . "'";
                $rs2 = $this->db->query($sql);
                if ($this->db->getRowsNum($rs2) == 0) {
                    $uname = 'Guest';
                } else {
                    $row2  = $this->db->fetchArray($rs2);
                    $uname = $row2['uname'];
                }
            }

            if ($type == 1) {
                $href = 'detail.php?id=' . $name;
                if ($dname_flg) {
                    $sql  = 'SELECT label FROM ' . $this->db->prefix('newdb_master') . " WHERE label_id='" . $name . "'";
                    $rs2  = $this->db->query($sql);
                    $row2 = $this->db->fetchArray($rs2);
                    $name = $row2['label'];
                }
                $link_in .= "<tr><td style='width:140px;'><a href='" . $href . "'>" . $name . '</a></td>';
                $link_in .= '<td>' . $note . "</td><td style='width:100px; text-align:right;'>" . $uname . '</td></tr>';
            } else {
                $link_out .= "<tr><td style='width:140px;'><a href='" . $href . "'>" . $name . '</a></td>';
                $link_out .= '<td>' . $note . "</td><td style='width:100px; text-align:right;'>" . $uname . '</td></tr>';
            }
        }

        if (!empty($link_in)) {
            $ret .= '<div>' . _ND_CLASS_RDATA . '</div><table>' . $link_in . '</table><br>';
        }
        if (!empty($link_out)) {
            $ret .= '<div>' . _ND_CLASS_RURL . '</div><table>' . $link_out . '</table>';
        }

        return $ret;
    }

    /**
     * error.
     */
    public function error()
    {
        return $this->error;
    }
}
