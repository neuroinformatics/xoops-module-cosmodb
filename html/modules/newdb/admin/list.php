<?php

$url = XOOPS_URL . '/modules/newdb/admin/index.php';
if (isset($_POST['action'])) {
    $action = $_POST['action'];
} elseif (isset($_GET['action'])) {
    $action = $_GET['action'];
} else {
    $action = '';
}

switch ($action) {
    case 'new_list':
        Add_new_list($url);
        break;

    case 'change':
        Change_list($url);
        break;

    default:
        List_top($url);
        break;
}

function Change_list($url)
{
    if (isset($_GET['id'])) {
        $id = (int)$_GET['id'];
        showForm($url, 'change', $id);
    } elseif (isset($_POST['list_id'])) {
        global $xoopsDB;
        $myts =  MyTextSanitizer::getInstance();
        $id   = (int)$_POST['list_id'];

        if (isset($_POST['del_flg']) && $_POST['del_flg'] === 'y') {
            $sql = 'DELETE FROM ' . $xoopsDB->prefix('newdb_list') . " WHERE list_id='" . $id . "'";
            $rs  = $xoopsDB->query($sql);
            if ($rs) {
                redirect_header($url . '?mode=list', 2, _ND_COMPONENT_OK);
            } else {
                redirect_header($url . '?mode=list', 2, _ND_COMPONENT_NG);
            }
        } else {
            $name  = $myts->stripSlashesGPC($_POST['name']);
            $temp  = $myts->stripSlashesGPC($_POST['temp']);
            $type  = (int)$_POST['type'];
            $onoff = (int)$_POST['onoff'];
            $sort  = (int)$_POST['sort'];

            if ($type == 1) {
                $th   = $myts->stripSlashesGPC($_POST['th']);
                $dir  = '';
                $size = '';
            } elseif ($type == 2) {
                $dir  = $myts->stripSlashesGPC($_POST['dir']);
                $size = $myts->stripSlashesGPC($_POST['size']);
                $th   = '';
            }

            if (!$name || !$temp) {
                redirect_header($url . '?mode=list', 2, _ND_COMPONENT_INPUTNG);
            }

            $name4sql = addslashes($name);
            $temp4sql = addslashes($temp);
            $dir4sql  = addslashes($dir);
            $size4sql = addslashes($size);
            $th4sql   = addslashes($th);

            $sql = 'UPDATE ' . $xoopsDB->prefix('newdb_list');
            $sql .= " SET name='" . $name4sql . "', type='" . $type . "', list_th='" . $th4sql . "', thumb_dir='" . $dir4sql . "',";
            $sql .= "thumb_size='" . $size4sql . "', template='" . $temp4sql . "', onoff='" . $onoff . "', sort='" . $sort . "'";
            $sql .= " WHERE list_id='" . $id . "'";
            $rs = $xoopsDB->query($sql);
            if ($rs) {
                redirect_header($url . '?mode=list', 2, _ND_COMPONENT_EDITOK);
            } else {
                redirect_header($url . '?mode=list', 2, _ND_COMPONENT_EDITNG);
            }
        }
    }
}

function Add_new_list($url)
{
    if (isset($_POST['name'])) {
        global $xoopsDB;
        $myts =  MyTextSanitizer::getInstance();

        $name  = $myts->stripSlashesGPC($_POST['name']);
        $temp  = $myts->stripSlashesGPC($_POST['temp']);
        $type  = (int)$_POST['type'];
        $onoff = (int)$_POST['onoff'];
        $sort  = (int)$_POST['sort'];

        if ($type == 1) {
            $th   = $myts->stripSlashesGPC($_POST['th']);
            $dir  = '';
            $size = '';
        } elseif ($type == 2) {
            $dir  = $myts->stripSlashesGPC($_POST['dir']);
            $size = $myts->stripSlashesGPC($_POST['size']);
            $th   = '';
        }

        if (!$name || !$temp) {
            redirect_header($url . '?mode=list', 2, _ND_COMPONENT_INPUTNG);
        }

        $name4sql = addslashes($name);
        $temp4sql = addslashes($temp);
        $dir4sql  = addslashes($dir);
        $size4sql = addslashes($size);
        $th4sql   = addslashes($th);

        $sql = 'INSERT INTO ' . $xoopsDB->prefix('newdb_list');
        $sql .= " VALUES('','" . $name4sql . "','" . $type . "','" . $th4sql . "','" . $dir4sql . "',";
        $sql .= "'" . $size4sql . "','" . $temp4sql . "','" . $onoff . "','" . $sort . "')";

        $rs = $xoopsDB->query($sql);
        if ($rs) {
            redirect_header($url . '?mode=list', 2, _ND_COMPONENT_ADDOK);
        } else {
            redirect_header($url . '?mode=list', 2, _ND_COMPONENT_ADDNG);
        }
    } else {
        showForm($url, 'new_list', -1);
    }
}

/**
 * showForm
 * show form for new register and edit.
 * @param $url
 * @param $act
 * @param $id
 */
function showForm($url, $act, $id)
{
    if ($act === 'new_list') {
        $n   = '';
        $o   = '0';
        $so  = '0';
        $dir = '';
        $t   = '3';

        $th = "<th style='width:%;'>{ID}</th>\n<th style='width:%;'>{Data Name}</th>\n";
        $th .= "<th style='width:%;'>{Author}</th>\n<th style='width:%;'>{Creation Date}</th>\n";
        $th .= "<th style='width:%;'>{Views}</th>";
        $list_temp = "<td>{ID}</td>\n<td>{Data Name}</td>\n<td>{Author}</td>\n";
        $list_temp .= "<td>{Creation Date}</td>\n<td>{Views}</td>\n";

        $thumb_temp = "<table>\n<tr>\n <td>{Data Name}</td>\n</tr><tr>\n ";
        $thumb_temp .= "<td>{Image}</td>\n</tr><tr>\n <td>\n  {Author}<br>\n";
        $thumb_temp .= "  {Creation Date}<br>\n </td>\n</tr>\n</table>";
        $size = 'S,80,80,5;M,120,120,4;L,160,160,3;Default,0,0,1';
    } elseif ($act === 'change') {
        global $xoopsDB;
        $sql = 'SELECT * FROM ' . $xoopsDB->prefix('newdb_list') . " WHERE list_id='" . $id . "'";
        $rs  = $xoopsDB->query($sql);
        $row = $xoopsDB->fetchArray($rs);

        $n  = $row['name'];
        $o  = $row['onoff'];
        $so = $row['sort'];

        $t = $row['type'];
        if ($t == 1) {
            $th        = $row['list_th'];
            $list_temp = $row['template'];
        } elseif ($t == 2) {
            $dir        = $row['thumb_dir'];
            $size       = $row['thumb_size'];
            $thumb_temp = $row['template'];
        }
    }

    xoops_cp_header();
    include __DIR__ . '/style.css';
    echo "<center>\n";

    if ($t == 3) {
        echo "<script language='JavaScript' src='../tab.js'></script>\n";
        echo "<table class='list_table' style='width:300px; margin:10px 0 20px 0;'>\n";
        echo "<tr><td style='width:150px; text-align:center;'>\n";
        echo "<a href=\"javascript:seltab('box', 'head', 10, 1)\">" . _ND_LIST_LIST . "</a>\n";
        echo "</td><td style='text-align:center;'>\n";
        echo "<a href=\"javascript:seltab('box', 'head', 10, 2)\">" . _ND_LIST_THUMB . "</a>\n";
        echo "</td></tr>\n";
        echo "</table>\n";
    }

    // list
    if ($t == 3 || $t == 1) {
        echo "<div id='box1' style='margin-bottom:1em;'>\n";
        echo "<form action='" . $url . "' method='POST' style='margin:0; padding:0;'>";
        echo "<table class='list_table' style='width:500px;'>";
        echo "<tr><th colspan='2'>" . _ND_LIST_LIST . _ND_LIST_CONFIG . '</th></tr>';

        echo "<tr><td class='list_odd' style='width:140px;'>";
        echo '<b>' . _ND_LIST_SHOWNAME . '</b><br>' . _ND_LIST_SNAME_DESC . '</td>';
        echo "<td><input type='text' name='name' value='" . $n . "'></td></tr>";

        echo "<tr><td class='list_odd'><b>" . _ND_LIST_H . '</b><br>' . _ND_LIST_H_DESC . '</td>';
        echo "<td><textarea name='th' style='width:100%; height:100px;'>" . $th . '</textarea></td></tr>';

        echo "<tr><td class='list_odd'><b>" . _ND_DETAIL_TEMP . '</b></td>';
        echo "<td><textarea name='temp' style='width:100%; height:100px;'>" . $list_temp . '</textarea></td></tr>';

        echo "<tr><td class='list_odd'><b>" . _ND_LIST_CHANGE . '</b></td><td>';
        $onoff = array(
            _ND_COMPONENT_YES,
            _ND_COMPONENT_NO
        );
        for ($i = 0; $i < 2; ++$i) {
            echo "<input type='radio' name='onoff' value='" . $i . "'";
            if ($o == $i) {
                echo ' checked';
            }
            echo '>' . $onoff[$i] . ' ';
        }
        echo '</td></tr>';

        echo "<tr><td class='list_odd'><b>" . _ND_COMPONENT_ORDER . '</b></td>';
        echo "<td><input type='text' name='sort' size='3' value='" . $so . "'></td></tr>";

        if ($t == 1) {
            echo "<tr><td class='list_odd'><b>" . _ND_COMPONENT_DEL . '</b><br>' . _ND_LIST_DEL . '</td>';
            echo "<td><input type='checkbox' name='del_flg' value='y'></td></tr>";
        }

        echo '</table>';

        echo "<br><br><input type='submit' value='submit'>";
        echo "<input type='hidden' value='1' name='type'>";
        echo "<input type='hidden' value='list' name='mode'>";
        echo "<input type='hidden' value='" . $act . "' name='action'>";
        if ($act === 'change') {
            echo "<input type='hidden' value='" . $id . "' name='list_id'>";
        }
        echo '</form>';
        echo '</div>';
    }

    // thumbnail
    if ($t == 3 || $t == 2) {
        ($t == 3) ? $display = 'display:none' : $display = '';
        echo "<div id='box2' style='margin-bottom:1em; " . $display . "'>\n";
        echo "<form action='" . $url . "' method='POST' style='margin:0; padding:0;'>";
        echo "<table class='list_table' style='width:500px;'>";
        echo "<tr><th colspan='2'>" . _ND_LIST_THUMB . _ND_LIST_CONFIG . '</th></tr>';

        echo "<tr><td class='list_odd' style='width:140px;'>";
        echo '<b>' . _ND_LIST_SHOWNAME . '</b><br>' . _ND_LIST_SNAME_DESC . '</td>';
        echo "<td><input type='text' name='name' value='" . $n . "'></td></tr>";

        echo "<tr><td class='list_odd'>";
        echo '<b>' . _ND_LIST_DIR . '</b><br>' . _ND_LIST_DIR_DESC . '</td>';
        echo "<td>Data Directory / thumbnail / <input type='text' name='dir' value='" . $dir . "'></td></tr>";

        echo "<tr><td class='list_odd'><b>" . _ND_DETAIL_TEMP . '</b></td>';
        echo "<td><textarea name='temp' style='width:100%; height:200px;'>" . $thumb_temp . '</textarea></td></tr>';

        echo "<tr><td class='list_odd'>";
        echo '<b>' . _ND_LIST_SIZE . '</b><br>' . _ND_LIST_SIZE_DESC . '</td>';
        echo "<td><input type='text' name='size' value='" . $size . "' style='width:100%;'>";
        echo _ND_LIST_SIZE_DESC2 . '</td></tr>';

        echo "<tr><td class='list_odd'><b>" . _ND_LIST_CHANGE . '</b></td><td>';
        $onoff = array(
            _ND_COMPONENT_YES,
            _ND_COMPONENT_NO
        );
        for ($i = 0; $i < 2; ++$i) {
            echo "<input type='radio' name='onoff' value='" . $i . "'";
            if ($o == $i) {
                echo ' checked';
            }
            echo '>' . $onoff[$i] . ' ';
        }
        echo '</td></tr>';

        echo "<tr><td class='list_odd'><b>" . _ND_COMPONENT_ORDER . '</b></td>';
        echo "<td><input type='text' name='sort' size='3' value='" . $so . "'></td></tr>";

        if ($t == 2) {
            echo "<tr><td class='list_odd'><b>" . _ND_COMPONENT_DEL . '</b><br>' . _ND_LIST_DEL . '</td>';
            echo "<td><input type='checkbox' name='del_flg' value='y'></td></tr>";
        }

        echo '</table>';

        echo "<br><br><input type='submit' value='submit'>";
        echo "<input type='hidden' value='2' name='type'>";
        echo "<input type='hidden' value='list' name='mode'>";
        echo "<input type='hidden' value='" . $act . "' name='action'>";
        if ($act === 'change') {
            echo "<input type='hidden' value='" . $id . "' name='list_id'>";
        }
        echo '</form>';
        echo '</div>';
    }

    echo '</center>';

    xoops_cp_footer();
}

function List_top($url)
{
    global $xoopsDB;

    xoops_cp_header();
    include __DIR__ . '/style.css';
    echo "<script language='JavaScript' src='../tab.js'></script>\n";
    echo "<center>\n";

    for ($i = 0; $i < 2; ++$i) {
        if (!$i) {
            $h = "<div class='title'>" . _ND_LIST_USE_ITEM . '</div>';
            $h .= "<div class='title_desc'>" . _ND_LIST_USE_ITEM_DESC . '</div>';
            $where  = " WHERE onoff='0' ";
            $submit = _ND_ADD_NEWCOMPONENT;
            $act    = 'new_list';
        } else {
            $h = "<div class='title'>" . _ND_LIST_NUSE_ITEM . '</div>';
            $h .= "<div class='title_desc'>" . _ND_LIST_NUSE_ITEM_DESC . '</div>';
            $where  = " WHERE onoff='1' ";
            $submit = '';
            $act    = '';
        }

        $sql = 'SELECT * FROM ' . $xoopsDB->prefix('newdb_list') . $where . 'ORDER BY sort';
        $rs  = $xoopsDB->query($sql);
        if ($xoopsDB->getRowsNum($rs)) {
            echo $h . "<table class='list_table' style='350px;'>";
            echo "<tr><th style='width:280px;'>" . _ND_COMPONENT_NAME . '</th></tr>';
            while ($row = $xoopsDB->fetchArray($rs)) {
                $name = $row['name'];
                $name = "<a href='" . $url . '?mode=list&action=change&id=' . $row['list_id'] . "'>" . $name . '</a>';
                echo '<tr><td>' . $name . '</td></tr>';
            }
            echo '</table>';
        }
        if (!empty($submit)) {
            echo "<form action='" . $url . "' method='POST' style='margin:0; padding:0;'>";
            echo "<table style='margin: 20px 0 20px 0; border:0;'>";
            echo '<tr><td>';
            echo "<input type='submit' value='" . $submit . "'>";
            echo "<input type='hidden' value='list' name='mode'>";
            echo "<input type='hidden' value='" . $act . "' name='action'>";
            echo '</td></tr></table>';
            echo '</form>';
        } else {
            echo '<br><br>';
        }
    }

    echo '</center>';
    xoops_cp_footer();
}
