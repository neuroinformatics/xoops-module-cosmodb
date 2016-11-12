<?php

include __DIR__.'/header.php';
include __DIR__.'/class/bookmark.php';
include __DIR__.'/class/component.php';

if (!$uid) {
    redirect_header(MOD_URL, 2, _ND_NACCESS2);
}

$mode = '';
if (isset($_GET['mode'])) {
    $mode = $_GET['mode'];
} elseif (isset($_POST['mode'])) {
    $mode = $_POST['mode'];
}

if (isset($_GET['bd'])) {
    $bd_id = (int) $_GET['bd'];
} elseif (isset($_POST['bd'])) {
    $bd_id = (int) $_POST['bd'];
}
if (empty($bd_id)) {
    $sql = 'SELECT * FROM '.$xoopsDB->prefix('newdb_bookmark_dir');
    $sql .= " WHERE uid='".$uid."' AND pbd_id='0' ORDER BY sort LIMIT 0, 1";
    $rs = $xoopsDB->query($sql);
    $row = $xoopsDB->fetchArray($rs);
    $bd_id = $row['bd_id'];
}

if (isset($bd_id)) {
    $sql = 'SELECT * FROM '.$xoopsDB->prefix('newdb_bookmark_dir');
    $sql .= " WHERE uid='".$uid."' AND bd_id='".$bd_id."'";
    $rs = $xoopsDB->query($sql);
    if (!$xoopsDB->getRowsNum($rs)) {
        redirect_header(MOD_URL, 2, _ND_NACCESS);
    }
}

$bm = new Bookmark($uid);
$bm->checkDir();

switch ($mode) {

    /*
     * bookmark register
     */
    case 'regbf':
        if (!isset($_GET['lid'])) {
            redirect_header(MOD_URL, 1, _ND_NACCESS);
        }
        $lid = (int) $_GET['lid'];

        include XOOPS_ROOT_PATH.'/header.php';
        include __DIR__.'/style.css';
        echo "<form action='bookmark.php' method='POST'>";
        echo "<center><table class='list_table' style='width:400px;'>";
        echo "<tr><th colspan='2'>"._ND_BK_ADD.'</th></tr>';
        echo "<tr><td class='even' style='width:100px;'><b>"._ND_BK_DIR.'</b></td>';
        echo "<td><select name='bd'>";

        $sql = 'SELECT * FROM '.$xoopsDB->prefix('newdb_bookmark_dir');
        $sql .= " WHERE uid='".$uid."' ORDER BY sort";
        $rs = $xoopsDB->query($sql);
        while ($row = $xoopsDB->fetchArray($rs)) {
            echo "<option value='".$row['bd_id']."'>".$row['directory'].'</option>';
        }
        echo '</select></td></tr>';
        echo "<tr><td class='even'><b>"._ND_COMMENTV_COM.'</b></td>';
        echo "<td><input type='text' style='width:95%;' name='note'></td></tr>";
        echo "<tr><td class='even'> </td>";
        echo "<td><input type='submit' class='button' value='submit'></td></tr>";
        echo '</table>';
        echo "<br><a href='detail.php?id=".$lid."'>"._ND_BACK.'</a>';
        echo '</center>';
        echo "<input type='hidden' name='lid' value='".$lid."'>";
        echo "<input type='hidden' name='mode' value='regbf2'>";
        echo '</form>';
        include XOOPS_ROOT_PATH.'/footer.php';
        break;

    case 'regbf2':
        $lid = (int) $_POST['lid'];
        $note4sql = addslashes($myts->stripSlashesGPC($_POST['note']));
        if ($bm->regNewFile($bd_id, $lid, $note4sql)) {
            $mes = _ND_KW_OK;
        } else {
            $mes = _ND_KW_NG;
        }
        redirect_header(MOD_URL.'/detail.php?id='.$lid, 1, $mes);
        break;

    /*
     * bookmark directory register form
     */
    case 'regbd':
        include XOOPS_ROOT_PATH.'/header.php';
        include __DIR__.'/style.css';
        echo "<form action='bookmark.php' method='POST'>";
        echo "<center><table class='list_table' style='width:400px;'>";
        echo "<tr><th colspan='2'>"._ND_BK_MKDIR.'</th></tr>';
        echo "<tr><td class='even' style='width:100px;'><b>"._ND_BK_DIR.'</b></td>';
        echo "<td><input type='text' name='dir_name'></td></tr>";
        echo "<tr><td class='even'><b>"._ND_BK_ORDER.'</b></td>';
        echo "<td><input type='text' name='sort' size='4' value='0'></td></tr>";
        echo "<tr><td class='even'><b>"._ND_BK_CLASS.'</b></td><td>';
        echo "<input type='radio' name='where' value='0' checked> "._ND_BK_TOP;
        echo "<input type='radio' name='where' value='".$bd_id."'>"._ND_BK_SUB;
        echo '</td></tr>';
        echo "<tr><td class='even'> </td>";
        echo "<td><input type='submit' class='button' value='submit'></td></tr>";
        echo '</table>';
        echo "<br><a href='bookmark.php?bd=".$bd_id."'>"._ND_BACK.'</a>';
        echo '</center>';
        echo "<input type='hidden' name='mode' value='regbd2'>";
        echo '</form>';
        include XOOPS_ROOT_PATH.'/footer.php';
        break;

    case 'regbd2':
        $dir_name4sql = addslashes($myts->stripSlashesGPC($_POST['dir_name']));
        $pbd_id = (int) $_POST['where'];
        $sort = (int) $_POST['sort'];
        if ($bm->regNewDir($pbd_id, $dir_name4sql, $sort)) {
            $mes = _ND_BK_MKDIROK;
        } else {
            $mes = _ND_DIR_FALSE;
        }
        redirect_header(MOD_URL.'/bookmark.php', 1, $mes);
        break;

    /*
     * bookmark directory edit form
     */
    case 'bdedit':
        $sql = 'SELECT * FROM '.$xoopsDB->prefix('newdb_bookmark_dir')." WHERE bd_id='".$bd_id."'";
        $rs = $xoopsDB->query($sql);
        $row = $xoopsDB->fetchArray($rs);

        include XOOPS_ROOT_PATH.'/header.php';
        include __DIR__.'/style.css';
        echo "<form action='bookmark.php' method='POST'>";
        echo "<center><table class='list_table' style='width:400px;'>";
        echo "<tr><th colspan='2'>"._ND_BK_EDITDIR.'</th></tr>';
        echo "<tr><td class='even' style='width:100px;'><b>"._ND_BK_DIR.'</b></td>';
        echo "<td><input type='text' name='dir_name' value='".$row['directory']."'></td></tr>";
        echo "<tr><td class='even'><b>"._ND_BK_ORDER.'</b></td>';
        echo "<td><input type='text' name='sort' size='4' value='".$row['sort']."'></td></tr>";
        echo "<tr><td class='even'><b>"._ND_BK_MOVE.'</b></td><td>';
        echo "<select name='newbd'>";
        echo "<option value='no'>"._ND_BK_NMOVE.'</option>';
        echo "<option value='0'>"._ND_BK_GOTOP.'</option>';
        echo "<option value='no'>-------------</option>";

        $bd_array = explode(',', $bm->checkChildDir($bd_id, $bd_id.','));
        $sql = 'SELECT * FROM '.$xoopsDB->prefix('newdb_bookmark_dir')." WHERE uid='".$uid."'";
        $rs = $xoopsDB->query($sql);
        while ($row = $xoopsDB->fetchArray($rs)) {
            if (!in_array($row['bd_id'], $bd_array)) {
                echo "<option value='".$row['bd_id']."'>".$row['directory'].'</option>';
            }
        }
        echo '</select></td></tr>';
        echo "<tr><td class='even'><b>"._ND_DELETE.'</b></td>';
        echo "<td><input type='checkbox' name='delete' value='y'> "._ND_BK_DEL_DESC.'</td></tr>';
        echo "<tr><td class='even'> </td>";
        echo "<td><input type='submit' class='button' value='submit'></td></tr>";
        echo '</table>';
        echo "<br><a href='bookmark.php?bd=".$bd_id."'>"._ND_BACK.'</a>';
        echo '</center>';
        echo "<input type='hidden' name='bd' value='".$bd_id."'>";
        echo "<input type='hidden' name='mode' value='bdedit2'>";
        echo '</form>';
        include XOOPS_ROOT_PATH.'/footer.php';
        break;

    case 'bdedit2':
        // delete
        if (!empty($_POST['delete'])) {
            $list = explode(',', $bm->checkChildDir($bd_id, $bd_id.','));
            for ($i = 0, $iMax = count($list); $i < $iMax; ++$i) {
                if ($list[$i] == '') {
                    continue;
                }
                $bm->deleteDir($list[$i]);
            }
            $mes = _ND_BK_RMDIR;

            // update
        } else {
            $dir_name4sql = addslashes($myts->stripSlashesGPC($_POST['dir_name']));
            $sort = (int) $_POST['sort'];
            ($_POST['newbd'] === 'no') ? $pbd = '' : $pbd = ",pbd_id='".(int) $_POST['newbd']."'";
            $set = "directory='".$dir_name4sql."', sort='".$sort."'".$pbd;
            $where = "bd_id='".$bd_id."'";
            if ($bm->updateDir($set, $where)) {
                $mes = _ND_BK_EDITDIROK;
            } else {
                $mes = _ND_BK_EDITDIRNG;
            }
        }
        redirect_header(MOD_URL.'/bookmark.php', 1, $mes);
        break;

    /*
     * bookmark file edit
     */
    case 'bfedit':
        $data = array();
        foreach ($_POST['data'] as $value) {
            $data[] = (int) $value;
        }
        if (count($data) == 0) {
            $mes = _ND_BK_NDATA;
            redirect_header(MOD_URL.'/bookmark.php', 1, $mes);
        }

        //delete
        if ($_POST['bfmode'] === 'delete') {
            $mes = $bm->deleteFile($data).' '._ND_BK_DELETED;
            redirect_header(MOD_URL.'/bookmark.php', 1, $mes);

            // edit
        } else {
            include XOOPS_ROOT_PATH.'/header.php';
            include __DIR__.'/style.css';
            echo "<form action='bookmark.php' method='POST'>";
            echo "<center><table class='list_table' style='width:400px;'>";
            echo "<tr><th colspan='2'>"._ND_BK_DEDIT.'</th></tr>';
            $data_list = '';
            for ($i = 0, $iMax = count($data); $i < $iMax; ++$i) {
                $data_list .= $data[$i].',';
                $sql = 'SELECT * FROM '.$xoopsDB->prefix('newdb_bookmark_file');
                $sql .= " WHERE bf_id='".$data[$i]."'";
                $rs = $xoopsDB->query($sql);
                while ($row = $xoopsDB->fetchArray($rs)) {
                    $sql = 'SELECT * FROM '.$xoopsDB->prefix('newdb_master');
                    $sql .= " WHERE label_id='".$row['label_id']."'";
                    $rs2 = $xoopsDB->query($sql);
                    $row2 = $xoopsDB->fetchArray($rs2);
                    echo "<tr><td class='even' style='width:100px;'><b>";
                    if ($xoopsModuleConfig['dname_flg']) {
                        echo $row2['label'];
                    } else {
                        echo $row2['label_id'];
                    }
                    echo '</b></td>';
                    echo "<td><input type='text' name='note[]' value='".$row['note']."' style='width:98%;'></td></tr>";
                }
            }
            echo "<tr><td class='even'><b>"._ND_BK_DIR.'</b></td>';
            echo "<td><select name='moveto'>";
            echo "<option value='no'>"._ND_BK_NMOVE.'</option>';
            echo "<option value='no'>----------</option>";

            $sql = 'SELECT * FROM '.$xoopsDB->prefix('newdb_bookmark_dir');
            $sql .= " WHERE uid='".$uid."' ORDER BY sort";
            $rs = $xoopsDB->query($sql);
            while ($row = $xoopsDB->fetchArray($rs)) {
                echo "<option value='".$row['bd_id']."'>".$row['directory'].'</option>';
            }
            echo '</td></tr>';
            echo "<tr><td class='even'></td>";
            echo "<td><input type='submit' class='button' value='submit'></td></tr>";
            echo "</table><br><a href='bookmark.php'>"._ND_BACK.'</a></center>';
            echo "<input type='hidden' name='data' value='".$data_list."'>";
            echo "<input type='hidden' name='mode' value='bfedit2'>";
            echo '</form>';
            include XOOPS_ROOT_PATH.'/footer.php';
        }
        break;

    case 'bfedit2':
        $data = array();
        $tmp = explode(',', $_POST['data']);
        foreach ($tmp as $value) {
            if ($value == '') {
                continue;
            }
            $data[] = (int) $value;
        }
        $note = array();
        foreach ($_POST['note'] as $value) {
            $note[] = $myts->stripSlashesGPC($value);
        }
        $bd_id = '';
        if ($_POST['moveto'] !== 'no') {
            $bd_id = (int) $_POST['moveto'];
        }
        if (count($data) == count($note)) {
            $mes = $bm->updateFile($data, $note, $bd_id).' '._ND_BK_DATACHANGED;
        } else {
            $mes = _ND_BK_NDATACHANGED;
        }
        redirect_header(MOD_URL.'/bookmark.php', 1, $mes);
        break;

    /*
     * bookmark top
     */
    default:
        $com = new Component();
        include XOOPS_ROOT_PATH.'/header.php';
        include __DIR__.'/style.css';
        echo "<script language='JavaScript' src='tab.js'></script>\n";
        echo "<form action='bookmark.php' method='POST'>";
        echo "<table><tr><td style='width:20%; padding:0 15px 0 5px;'>";
        echo "<table class='border' style='padding:5px 5px 10px 5px;'><tr><td>";
        echo $bm->getDynamicMenu();
        echo '</td></tr></table>';
        echo "<table class='list_table' style='margin-top:15px;'>";
        echo "<tr><td style='text-align:center; padding:6px;'>"._ND_BK_DIR.'<br>';
        echo "<a href='bookmark.php?mode=regbd&bd=".$bd_id."'>"._ND_MAKE.'</a> / ';
        echo "<a href='bookmark.php?mode=bdedit&bd=".$bd_id."'>"._ND_EDIT.'</a>';
        echo '</td></tr></table>';
        echo "<table class='list_table' style='margin-top:15px;'>";
        echo "<tr><td style='text-align:center; padding:6px;'>"._ND_CONFIG_THUMB.'<br>';
        echo "<a href=\"javascript:seltab_all_open('box')\">"._ND_SHOW.'</a> / ';
        echo "<a href=\"javascript:seltab_all_close('box')\">"._ND_HIDE.'</a>';
        echo '</td></tr></table>';
        echo "<table class='list_table' style='margin-top:15px;'>";
        echo "<tr><td style='text-align:center; padding:6px;'>"._ND_BK_DATA.'<br>';
        echo "<a href=\"javascript:check_all('data', 1)\">"._ND_SELECTALL.'</a> / ';
        echo "<a href=\"javascript:check_all('data', 0)\">"._ND_UNSET.'</a>';
        echo '</td></tr>';
        echo "<tr><td style='text-align:center; padding:6px;'>"._ND_BK_SD.'<br>';
        echo "<select name='bfmode'>";
        echo "<option value='edit'>"._ND_BK_CM.'</option>';
        echo "<option value='delete'>"._ND_DELETE.'</option>';
        echo '</select><br>';
        echo "<input type='hidden' name='mode' value='bfedit'>";
        echo "<input type='submit' value='submit' style='border:1px solid; background:white;'>";
        echo '</td></tr></table>';
        echo '</td><td>';

        // data
        $bf = array();
        $bf = $bm->getBookmark($bd_id);
        echo "<script language='JavaScript' src='border.js'></script>\n";
        echo "<table class='list_table'>";
        echo '<tr><th>'.$bm->current_dir.'</th></tr>';
        echo '</table>';

        for ($i = 0, $j = 1, $iMax = count($bf); $i < $iMax; ++$i) {
            echo "<table class='list_table'>";
            echo "<tr><td class='even' style='width:20px;'>";
            echo "<input type='checkbox' id='data".($i + 1)."' name='data[]' value='".$bf[$i]['bf_id']."'></td>";
            echo "<td style='width:100px;'><a href='detail.php?id=".$bf[$i]['label_id']."'>";
            if ($xoopsModuleConfig['dname_flg']) {
                echo $bf[$i]['label'];
            } else {
                echo $bf[$i]['label_id'];
            }
            echo '</a></td>';
            echo '<td>'.$bf[$i]['note'].'</td></tr>';
            echo '</table>';
            if ($com->setLabelID($bf[$i]['label_id'])) {
                $thumb = $com->getThumbnail(EXTRACT_PATH, XOOPS_URL, 'ALL', '180px|180px|2');
                if (strstr($thumb, '<img')) {
                    echo "<div id='box".$j."' style='display:none;'>";
                    echo $thumb;
                    echo '</div>';
                    ++$j;
                }
            }
        }
        echo '</td></tr></table></form';
        include XOOPS_ROOT_PATH.'/footer.php';
}
