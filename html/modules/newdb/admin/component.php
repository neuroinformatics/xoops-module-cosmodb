<?php
/*
delete query does not be implemented in the case component master is deleted.
*/
$url = XOOPS_URL . '/modules/newdb/admin/index.php';

if (isset($_POST['action'])) {
    $action = $_POST['action'];
} elseif (isset($_GET['action'])) {
    $action = $_GET['action'];
} else {
    $action = '';
}

switch ($action) {
    case 'new_component':
        Add_new_component($url);
        break;

    case 'change':
        Change_component($url);
        break;

    default:
        Component_top($url);
        break;
}

function Change_component($url)
{
    global $xoopsDB;

    if (isset($_GET['id'])) {
        $id = (int)$_GET['id'];
    } elseif (isset($_POST['comp_id'])) {
        $id = (int)$_POST['comp_id'];
    }
    $sql = 'SELECT * FROM ' . $xoopsDB->prefix('newdb_component_master') . " WHERE comp_id='" . $id . "'";
    $rs  = $xoopsDB->query($sql);
    $row = $xoopsDB->fetchArray($rs);

    // edit form
    if (isset($_GET['id'])) {
        if ($row['type'] == '1') {
            showSysForm($url, $id);
        } else {
            showForm($url, 'change', $id);
        }

        // do edit
    } elseif (isset($_POST['comp_id'])) {
        $myts  =  MyTextSanitizer::getInstance();
        $tag   = $myts->stripSlashesGPC($_POST['comp_tag']);
        $onoff = (int)$_POST['comp_onoff'];
        $sort  = (int)$_POST['comp_sort'];

        $nonull       = 0;
        $textmax      = 0;
        $onoff_refine = 0;
        if (isset($_POST['comp_nonull'])) {
            $nonull = (int)$_POST['comp_nonull'];
        }
        if (isset($_POST['comp_textmax'])) {
            $textmax = (int)$_POST['comp_textmax'];
        }
        if (isset($_POST['comp_onoff_refine'])) {
            $onoff_refine = (int)$_POST['comp_onoff_refine'];
        }

        // system
        if ($row['type'] == '1') {
            if (empty($tag)) {
                redirect_header($url . '?mode=component', 1, _ND_COMPONENT_INPUTNG);
            }
            $tag4sql = addslashes($tag);
            $sql     = 'UPDATE ' . $xoopsDB->prefix('newdb_component_master');
            $sql .= " SET tag='" . $tag4sql . "', onoff='" . $onoff . "', sort='" . $sort . "', ";
            $sql .= "textmax='" . $textmax . "', onoff_refine='" . $onoff_refine . "' ";
            $sql .= "WHERE comp_id='" . $id . "'";
            $rs = $xoopsDB->query($sql);

            // custom
        } else {
            if (isset($_POST['comp_delete'])) {
                $sql = 'DELETE FROM ' . $xoopsDB->prefix('newdb_component_master') . " WHERE comp_id='" . $id . "'";
                $rs  = $xoopsDB->query($sql);
                if ($rs) {
                    redirect_header($url . '?mode=component', 1, _ND_COMPONENT_OK);
                } else {
                    redirect_header($url . '?mode=component', 1, _ND_COMPONENT_NG);
                }
            }

            $name    = $myts->stripSlashesGPC($_POST['comp_name']);
            $exp     = $myts->stripSlashesGPC($_POST['comp_desc']);
            $select  = $myts->stripSlashesGPC($_POST['comp_select']);
            $default = $myts->stripSlashesGPC($_POST['comp_default']);
            $type    = (int)$_POST['comp_type'];

            if (!$tag || !$name || !($type == '2' || $type == '3' || $type == '4' || $type = '5') || ($type != '4' && !$select)) {
                redirect_header($url . '?mode=component', 1, _ND_COMPONENT_INPUTNG);
            }

            $tag4sql     = addslashes($tag);
            $name4sql    = addslashes($name);
            $exp4sql     = addslashes($exp);
            $select4sql  = addslashes($select);
            $default4sql = addslashes($default);

            $sql = 'UPDATE ' . $xoopsDB->prefix('newdb_component_master');
            $sql .= " SET tag='" . $tag4sql . "', name='" . $name4sql . "', exp='" . $exp4sql . "', ";
            $sql .= "type='" . $type . "', onoff='" . $onoff . "', sort='" . $sort . "', ";
            $sql .= "nonull='" . $nonull . "', textmax='" . $textmax . "', onoff_refine='" . $onoff_refine . "'";
            if ($type == '2' || $type == '3' || $type = '5') {
                $sql .= ", default_value='" . $default4sql . "', select_value='" . $select4sql . "'";
            }
            $sql .= " WHERE comp_id='" . $id . "'";
            $rs = $xoopsDB->query($sql);
        }

        if ($rs) {
            redirect_header($url . '?mode=component', 1, _ND_COMPONENT_EDITOK);
        } else {
            redirect_header($url . '?mode=component', 1, _ND_COMPONENT_EDITNG);
        }
    }
}

function Add_new_component($url)
{
    if (isset($_POST['comp_name'])) {
        global $xoopsDB;
        $myts =  MyTextSanitizer::getInstance();

        $tag          = $myts->stripSlashesGPC($_POST['comp_tag']);
        $name         = $myts->stripSlashesGPC($_POST['comp_name']);
        $exp          = $myts->stripSlashesGPC($_POST['comp_desc']);
        $type         = (int)$_POST['comp_type'];
        $select       = $myts->stripSlashesGPC($_POST['comp_select']);
        $default      = $myts->stripSlashesGPC($_POST['comp_default']);
        $onoff        = (int)$_POST['comp_onoff'];
        $onoff_refine = (int)$_POST['comp_onoff_refine'];
        $sort         = (int)$_POST['comp_sort'];

        $nonull  = 0;
        $textmax = 0;
        if (isset($_POST['comp_nonull'])) {
            $nonull = (int)$_POST['comp_nonull'];
        }
        if (isset($_POST['comp_textmax'])) {
            $textmax = (int)$_POST['comp_textmax'];
        }

        if (!$tag || !$name || !($type == '2' || $type == '3' || $type == '4' || $type == '5') || ($type != '4' && !$select)) {
            redirect_header($url . '?mode=component', 1, _ND_COMPONENT_INPUTNG);
        }

        $tag4sql     = addslashes($tag);
        $name4sql    = addslashes($name);
        $exp4sql     = addslashes($exp);
        $select4sql  = addslashes($select);
        $default4sql = addslashes($default);
        $sql         = 'INSERT INTO ' . $xoopsDB->prefix('newdb_component_master');

        if ($type == '4') {
            $select4sql  = '';
            $default4sql = '';
        }

        $sql .= " VALUES('','" . $tag4sql . "','" . $name4sql . "','" . $exp4sql . "','" . $type . "',";
        $sql .= "'" . $default4sql . "','" . $select4sql . "','" . $onoff . "','" . $sort . "','" . $nonull . "',";
        $sql .= "'" . $textmax . "','" . $onoff_refine . "')";
        $rs = $xoopsDB->query($sql);

        if ($rs) {
            redirect_header($url . '?mode=component', 1, _ND_COMPONENT_ADDOK);
        } else {
            redirect_header($url . '?mode=component', 1, _ND_COMPONENT_ADDNG);
        }
    } else {
        showForm($url, 'new_component', -1);
    }
}

/**
 * showSysForm (type 1)
 * showForm (type 2,3,4).
 *
 * show form for new register and edit.
 * @param $url
 * @param $id
 */
function showSysForm($url, $id)
{
    global $xoopsDB;
    $sql = 'SELECT * FROM ' . $xoopsDB->prefix('newdb_component_master') . " WHERE comp_id='" . $id . "'";
    $rs  = $xoopsDB->query($sql);
    $row = $xoopsDB->fetchArray($rs);
    $n   = $row['tag'];
    $o   = $row['onoff'];
    $so  = $row['sort'];

    xoops_cp_header();
    include __DIR__ . '/style.css';
    echo '<center>';
    echo "<form action='" . $url . "' method='POST' style='margin:0; padding:0;'>";
    echo "<table class='list_table' style='width:500px;'>";
    echo "<tr><th colspan='2'>" . _ND_COMPONENT_SEDIT . '</th></tr>';

    echo "<tr><td class='list_odd' style='width:150px;'><b>" . _ND_COMPONENT_NAME . '</b></td>';
    echo "<td><input type='text' name='comp_tag' size='15' value='" . $n . "'></td></tr>";

    echo "<tr><td class='list_odd'><b>" . _ND_COMPONENT_SORT . '</b></td><td>';
    $onoff = array(
        _ND_COMPONENT_YES,
        _ND_COMPONENT_NO
    );
    for ($i = 0; $i < 2; ++$i) {
        echo "<input type='radio' name='comp_onoff' value='" . $i . "'";
        if ($o == $i) {
            echo ' checked';
        }
        echo '>' . $onoff[$i] . ' ';
    }
    echo '</td></tr>';

    // author, date only
    if ($id == '3' || $id == '4') {
        echo "<tr><td class='list_odd'><b>" . _ND_COMPONENT_REFINE . '</b></td><td>';
        $onoff = array(
            _ND_COMPONENT_YES,
            _ND_COMPONENT_NO
        );
        for ($i = 0; $i < 2; ++$i) {
            echo "<input type='radio' name='comp_onoff_refine' value='" . $i . "'";
            if ($o == $i) {
                echo ' checked';
            }
            echo '>' . $onoff[$i] . ' ';
        }
        echo '</td></tr>';
    }

    echo "<tr><td class='list_odd'><b>" . _ND_COMPONENT_ORDER . '</b></td>';
    echo "<td><input type='text' name='comp_sort' size='3' value='" . $so . "'></td></tr>";

    // DataName only
    if ($id == '2') {
        echo "<tr><td class='list_odd'><b>" . _ND_COMPONENT_STRMAX . '</b></td>';
        echo "<td><input type='input' name='comp_textmax' size='3' value='" . $row['textmax'] . "'> (0 = " . _ND_COMPONENT_UNLIMITED . ')</td></tr>';
    }

    echo '</table>';

    echo "<br><br><input type='submit' value='submit'>";
    echo "<input type='hidden' value='component' name='mode'>";
    echo "<input type='hidden' value='change' name='action'>";
    echo "<input type='hidden' value='" . $id . "' name='comp_id'>";
    echo '</form>';
    echo '</center>';
    xoops_cp_footer();
}

function showForm($url, $act, $id)
{
    $tag = '';
    $n   = '';
    $e   = '';
    $t   = '2';
    $s   = '';
    $d   = '';
    $o   = '1';
    $so  = '0';

    $nonull  = '';
    $textmax = 0;
    $orefine = '1';

    $title = _ND_ADD_NEWCOMPONENT;
    if ($act === 'change') {
        global $xoopsDB;
        $sql   = 'SELECT * FROM ' . $xoopsDB->prefix('newdb_component_master') . " WHERE comp_id='" . $id . "'";
        $rs    = $xoopsDB->query($sql);
        $row   = $xoopsDB->fetchArray($rs);
        $tag   = $row['tag'];
        $n     = $row['name'];
        $e     = $row['exp'];
        $t     = $row['type'];
        $s     = $row['select_value'];
        $d     = $row['default_value'];
        $o     = $row['onoff'];
        $so    = $row['sort'];
        $title = _ND_COMPONENT_EDIT;

        if ($row['nonull']) {
            $nonull = 'checked';
        }
        $textmax = $row['textmax'];
        $orefine = $row['onoff_refine'];
    }

    xoops_cp_header();
    include __DIR__ . '/style.css';
    echo '<center>';
    echo "<form action='" . $url . "' method='POST' style='margin:0; padding:0;'>";
    echo "<table class='list_table' style='width:500px;'>";
    echo "<tr><th colspan='2'>" . $title . '</th></tr>';

    echo "<tr><td class='list_odd' style='width:150px;'><b>" . _ND_COMPONENT_NAME . '</b></td>';
    echo "<td><input type='text' name='comp_tag' size='15' value='" . $tag . "'></td></tr>";

    echo "<tr><td class='list_odd' style='width:150px;'><b>" . _ND_COMPONENT_TEMPNAME . '</b></td>';
    echo "<td><input type='text' name='comp_name' size='15' value='" . $n . "'></td></tr>";

    echo "<tr><td class='list_odd' style='width:150px;'><b>" . _ND_COMPONENT_ITEM_DESC . '</b></td>';
    echo "<td><input type='text' name='comp_desc' style='width:95%;' value='" . $e . "'></td></tr>";

    echo "<tr><td class='list_odd'><b>" . _ND_COMPONENT_REQUIRE . '</b><br>' . _ND_COMPONENT_REQUIRE_DESC . '</td>';
    echo "<td><input type='checkbox' name='comp_nonull' value='1' " . $nonull . '></td></tr>';

    echo "<tr><td class='list_odd'><b>" . _ND_COMPONENT_TYPE . '</b></td><td>';
    $type = array(
        'radio',
        'checkbox',
        'text',
        'select'
    );
    for ($i = 0; $i < 4; ++$i) {
        echo "<input type='radio' name='comp_type' value='" . ($i + 2) . "'";
        if ($t == ($i + 2)) {
            echo ' checked';
        }
        echo '>' . $type[$i] . ' ';
    }
    echo '</td></tr>';

    echo "<tr><td class='list_odd'><b>" . _ND_COMPONENT_SELECT_ITEM . '</b><br>' . _ND_COMPONENT_SELECT_ITEM_DESC . '</td>';
    echo "<td><textarea name='comp_select' style='width:95%; height:160px;'>" . $s . '</textarea></td></tr>';

    echo "<tr><td class='list_odd'><b>" . _ND_COMPONENT_DEFAULT . '</b><br>' . _ND_COMPONENT_DEFAULT_DESC . '</td>';
    echo "<td><input type='text' name='comp_default' value='" . $d . "'></td></tr>";

    echo "<tr><td class='list_odd'><b>" . _ND_COMPONENT_STRMAX . '</b><br>' . _ND_COMPONENT_TEXTONLY . '</td>';
    echo "<td><input type='input' name='comp_textmax' size='3' value='" . $textmax . "'> (" . _ND_COMPONENT_STRMAX_DESC . ') </td></tr>';

    echo "<tr><td class='list_odd'><b>" . _ND_COMPONENT_SORT . '</b></td><td>';
    $onoff = array(
        _ND_COMPONENT_YES,
        _ND_COMPONENT_NO
    );
    for ($i = 0; $i < 2; ++$i) {
        echo "<input type='radio' name='comp_onoff' value='" . $i . "'";
        if ($o == $i) {
            echo ' checked';
        }
        echo '>' . $onoff[$i] . ' ';
    }
    echo '</td></tr>';

    echo "<tr><td class='list_odd'><b>" . _ND_COMPONENT_REFINE . '</b><br>' . _ND_COMPONENT_REFINE_DESC . '</td><td>';
    $onoff = array(
        _ND_COMPONENT_YES,
        _ND_COMPONENT_NO
    );
    for ($i = 0; $i < 2; ++$i) {
        echo "<input type='radio' name='comp_onoff_refine' value='" . $i . "'";
        if ($o == $i) {
            echo ' checked';
        }
        echo '>' . $onoff[$i] . ' ';
    }
    echo '</td></tr>';

    echo "<tr><td class='list_odd'><b>" . _ND_COMPONENT_ORDER . '</b><br>' . _ND_COMPONENT_SORT1 . '</td>';
    echo "<td><input type='text' name='comp_sort' size='3' value='" . $so . "'></td></tr>";

    if ($act === 'change') {
        echo "<tr><td class='list_odd'><b>" . _ND_COMPONENT_DEL . '</b><br>' . _ND_COMPONENT_DEL_DESC . '</td>';
        echo "<td><input type='checkbox' name='comp_delete'></td></tr>";
    }

    echo '</table>';

    echo "<br><br><input type='submit' value='submit'>";
    echo "<input type='hidden' value='component' name='mode'>";
    echo "<input type='hidden' value='" . $act . "' name='action'>";

    if ($act === 'change') {
        echo "<input type='hidden' value='" . $id . "' name='comp_id'>";
    }
    echo '</form>';
    echo '</center>';
    xoops_cp_footer();
}

function Component_top($url)
{
    global $xoopsDB;

    xoops_cp_header();
    include __DIR__ . '/style.css';
    echo '<center>';

    echo "<div class='title'>" . _ND_COMPONENT_REGITEM . '</div>';
    echo "<div class='title_desc'>" . _ND_COMPONENT_DESC . '</div>';

    echo "<form action='" . $url . "' method='POST' style='margin:0; padding:0;'>";
    echo "<table class='list_table' style='width:90%;'>";
    echo "<tr><th style='width:10px;'> </th>";
    echo "<th style='width:20%;'>" . _ND_COMPONENT_NAME . '</th>';
    echo "<th style='width:20%;'>" . _ND_COMPONENT_TEMPNAME . '</th>';
    //echo "<th>"._ND_COMPONENT_VALUE."</th>";
    echo "<th style='width:50px;'>" . _ND_COMPONENT_TYPE . '</th>';
    echo "<th style='width:50px;'>" . _ND_COMPONENT_SRT . '</th>';
    echo "<th style='width:50px;'>" . _ND_COMPONENT_REFI . '</th>';
    echo "<th style='width:30px;'>" . _ND_COMPONENT_ORDER . '</th>';
    echo "<th style='width:30px;'>" . _ND_COMPONENT_STR . '</th>';
    echo '</tr>';

    $sql = 'SELECT * FROM ' . $xoopsDB->prefix('newdb_component_master');
    $sql .= ' ORDER BY sort, onoff, onoff_refine';
    $rs = $xoopsDB->query($sql);

    while ($row = $xoopsDB->fetchArray($rs)) {
        ($row['nonull'] == 1) ? $nonull = '<span style="color:red;">*</span>' : $nonull = '';
        ($row['textmax'] > 0) ? $textmax = $row['textmax'] : $textmax = '-';

        $tag = "<a href='" . $url . '?mode=component&action=change&id=' . $row['comp_id'] . "'>" . $row['tag'] . '</a>';
        //$value = str_replace($row['default_value'], "<span style='color:red;'>".$row['default_value'].'</span>', $row['select_value']);
        echo '<tr>';
        if ($row['name'] === 'Data Name') {
            echo "<td><span style='color:red;'>*</span></td>";
        } else {
            echo '<td>' . $nonull . '</td>';
        }
        echo '<td>' . $tag . '</td>';
        echo '<td>' . $row['name'] . '</td>';
        //echo "<td>".$value."</td>";
        echo "<td style='text-align:center;'>";
        if ($row['type'] == 1) {
            echo 'system';
        } elseif ($row['type'] == 2) {
            echo 'radio';
        } elseif ($row['type'] == 3) {
            echo 'checkbox';
        } elseif ($row['type'] == 4) {
            echo 'text';
        } elseif ($row['type'] == 5) {
            echo 'select';
        }
        echo "</td><td style='text-align:center;'>";
        if (!$row['onoff']) {
            echo 'YES</td>';
        } else {
            echo 'NO</td>';
        }
        echo "</td><td style='text-align:center;'>";
        if ($row['type'] == 1 && $row['name'] === 'Data Name' || $row['name'] === 'ID' || $row['name'] === 'Views') {
            echo '-';
        } elseif ($row['type'] == 4) {
            echo '-';
        } else {
            if (!$row['onoff_refine']) {
                echo 'YES</td>';
            } else {
                echo 'NO</td>';
            }
        }

        echo "</td><td style='text-align:center;'>" . $row['sort'] . '</td>';
        echo "</td><td style='text-align:center;'>" . $textmax . '</td>';
        echo '</tr>';
    }
    echo '</table>';

    echo "<table style='margin: 20px 0 20px 0; border:0;'>";
    echo '<tr><td>';
    echo "<input type='submit' value='" . _ND_ADD_NEWCOMPONENT . "'>";
    echo "<input type='hidden' value='component' name='mode'>";
    echo "<input type='hidden' value='new_component' name='action'>";
    echo '</td></tr></table>';
    echo '</form>';
    echo '</center>';
    xoops_cp_footer();
}
