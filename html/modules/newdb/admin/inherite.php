<?php

$url = XOOPS_URL . '/modules/newdb/admin/index.php';
isset($_POST['action']) ? $action = $_POST['action'] : $action = '';

switch ($action) {
    case 'do_inherite':
        Do_inherite($url);
        break;

    case 'check_inherite':
        Check_inherite($url);
        break;

    default:
        Inherite_top($url);
        break;
}

function Do_inherite($url)
{
    global $xoopsDB;
    $from = (int)$_POST['from'];
    $to   = (int)$_POST['to'];

    $sql = 'SELECT * FROM ' . $xoopsDB->prefix('newdb_master') . " WHERE author='" . $from . "'";
    $rs  = $xoopsDB->query($sql);
    while ($row = $xoopsDB->fetchArray($rs)) {
        $users = $row['users'];
        if (!empty($users)) {
            $users .= ',';
        }
        $users .= $to;

        $sql = 'UPDATE ' . $xoopsDB->prefix('newdb_master');
        $sql .= " SET users='" . $users . "', author='" . $to . "' WHERE label_id='" . $row['label_id'] . "'";
        $rs2 = $xoopsDB->query($sql);
    }

    $sql = 'UPDATE ' . $xoopsDB->prefix('newdb_item');
    $sql .= " SET reg_user='" . $to . "' WHERE reg_user='" . $from . "'";
    $rs = $xoopsDB->query($sql);

    redirect_header($url, 2, _ND_INH_OK);
}

function Check_inherite($url)
{
    global $xoopsDB;

    $from = (int)$_POST['from'];
    $to   = (int)$_POST['to'];

    $sql       = 'SELECT uname FROM ' . $xoopsDB->prefix('users') . " WHERE uid='" . $from . "'";
    $rs        = $xoopsDB->query($sql);
    $row       = $xoopsDB->fetchArray($rs);
    $from_name = $row['uname'];

    $sql     = 'SELECT uname FROM ' . $xoopsDB->prefix('users') . " WHERE uid='" . $to . "'";
    $rs      = $xoopsDB->query($sql);
    $row     = $xoopsDB->fetchArray($rs);
    $to_name = $row['uname'];

    xoops_cp_header();
    include __DIR__ . '/style.css';
    echo '<center>';
    echo "<table class='list_table' style='width:500px; text-align:center;'>";
    echo '<tr><td>';
    echo $from_name . _ND_INH_FROM . $to_name . _ND_INH_TO;
    echo '</td></tr>';
    echo '</table><br>';

    echo "<form action='" . $url . "' method='POST' style='margin:0; padding:0;'>";
    echo "<input type='hidden' value='" . $from . "' name='from'>";
    echo "<input type='hidden' value='" . $to . "' name='to'>";
    echo "<input type='hidden' value='inherite' name='mode'>";
    echo "<input type='hidden' value='do_inherite' name='action'>";
    echo "<input type='submit' value='submit'>";
    echo '</form></center>';
    echo '</center>';
    xoops_cp_footer();
}

function Inherite_top($url)
{
    global $xoopsDB;
    $users = array();
    $sql   = 'SELECT uname, uid FROM ' . $xoopsDB->prefix('users');
    $rs    = $xoopsDB->query($sql);
    while ($row = $xoopsDB->fetchArray($rs)) {
        $users[$row['uid']] = $row['uname'];
    }

    xoops_cp_header();
    include __DIR__ . '/style.css';
    echo '<center>';

    echo "<div class='title'>" . _ND_INHERITE_ADMIN . '</div>';
    echo "<div class='title_desc'></div>";

    echo "<form action='" . $url . "' method='POST' style='margin:0; padding:0;'>";
    echo "<table class='list_table' style='width:500px; text-align:center;'>";
    echo "<tr><th style='width:20%;'>" . _ND_INH_USER . '</th></tr>';
    echo '<tr><td>';

    echo "<select name='from'>";
    foreach ($users as $k => $v) {
        echo "<option value='" . $k . "'>" . $v . '</option>';
    }
    echo '</select>';
    echo _ND_INH_FROM;
    echo "<select name='to'>";
    foreach ($users as $k => $v) {
        echo "<option value='" . $k . "'>" . $v . '</option>';
    }
    echo '</select>';
    echo _ND_INH_TO;
    echo '</td></tr></table><br>';

    echo "<input type='hidden' value='inherite' name='mode'>";
    echo "<input type='hidden' value='check_inherite' name='action'>";
    echo "<input type='submit' value='submit'>";
    echo '</form></center>';

    xoops_cp_footer();
}
