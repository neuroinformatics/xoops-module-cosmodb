<?php
include __DIR__ . '/header.php';
include XOOPS_ROOT_PATH . '/header.php';
include __DIR__ . '/style.css';
$lim    = 10;
$kw     = '';
$andor  = 0;
$method = '';
if (isset($_GET['kw']) && !isset($_GET['st'])) {
    $method = 'search';
    $andor  = (int)$_GET['andor'];
    $kw     = $myts->stripSlashesGPC($_GET['kw']);
    $kw     = str_replace('　', ' ', $kw);
} elseif (isset($_GET['st'])) {
    $method = 'show';
    $andor  = (int)$_GET['andor'];
    $st     = (int)$_GET['st'];
    $time   = (int)$_GET['user'];
    $kw     = $myts->stripSlashesGPC($_GET['kw']);
    $kw     = str_replace('　', ' ', $kw);
}

echo "<form action='fts.php' method='GET' style='text-align:center;'>";
echo "<input type='text' name='kw' style='width:40%' value='" . $kw . "'> ";
echo "<select name='andor'>";
if (!$andor) {
    echo "<option value='0' selected> AND</option>";
    echo "<option value='1'> OR</option>";
} else {
    echo "<option value='0;'> AND</option>";
    echo "<option value='1' selected> OR</option>";
}
echo '</select> ';
echo "<input type='submit' class='button' value='search'>";
echo '</form>';

switch ($method) {
    case 'show':
        showResult($time, $st, $lim, $kw, $andor, $xoopsModuleConfig['dname_flg']);
        break;

    case 'search':
        ($andor == 0) ? $ao = 'AND' : $ao = 'OR';
        $kw_array = explode(' ', $kw);
        $list     = array();

        $sql = '';
        foreach ($kw_array as $v) {
            if (empty($v)) {
                continue;
            }
            if ($sql) {
                $sql .= $ao;
            }
            $sql .= " message like '%" . addslashes($v) . "%' ";
        }
        $sql = 'SELECT * FROM ' . $xoopsDB->prefix('newdb_comment') . ' WHERE ' . $sql;
        $rs  = $xoopsDB->query($sql);
        if ($xoopsDB->getRowsNum($rs)) {
            while ($row = $xoopsDB->fetchArray($rs)) {
                if (!in_array($row['com_id'], $list)) {
                    $list[] = $row['com_id'];
                }
            }
        }

        if (count($list)) {
            showResult(setResult($list, $kw_array), 0, $lim, $kw, $andor, $xoopsModuleConfig['dname_flg']);
        } else {
            echo "<div style='text-align:right; background:#e7efff; padding:3px; border-top:1px solid #3165ce; margin-bottom:10px;'>";
            echo '<b>0</b> ' . _ND_FS_HIT . '</div>';
        }
        break;
}

function showResult($time, $st, $lim, $kw, $andor, $dname_flg)
{
    global $xoopsDB;
    $myts =  MyTextSanitizer::getInstance();

    $sql = 'SELECT * FROM ' . $xoopsDB->prefix('newdb_fulltext_search') . " WHERE user='" . $time . "'";
    $rs  = $xoopsDB->query($sql);
    $n   = $xoopsDB->getRowsNum($rs);

    $sql = 'SELECT * FROM ' . $xoopsDB->prefix('newdb_fulltext_search') . " WHERE user='" . $time . "' LIMIT " . $st . ',' . $lim;
    $rs  = $xoopsDB->query($sql);
    $n2  = $xoopsDB->getRowsNum($rs);
    $s   = $st;
    if (!$st) {
        $s = 1;
    }

    require XOOPS_ROOT_PATH . '/class/pagenav.php';
    $xp = new XoopsPageNav($n, $lim, $st, 'st', 'user=' . $time . '&kw=' . $kw . '&andor=' . $andor);
    echo "<div style='text-align:right; background:#e7efff; padding:3px; border-top:1px solid #3165ce; margin-bottom:10px;'>";
    echo '<b>' . $n . '</b> ' . _ND_FS_HIT2 . ' <b>' . $s . '</b> - <b>' . ($st + $n2) . '</b> ' . _ND_FS_HIT3 . '</div>';

    while ($row = $xoopsDB->fetchArray($rs)) {
        $sql  = 'SELECT * FROM ' . $xoopsDB->prefix('newdb_master') . " WHERE label_id='" . $row['label_id'] . "'";
        $rs2  = $xoopsDB->query($sql);
        $row2 = $xoopsDB->fetchArray($rs2);
        if ($dname_flg) {
            $label = $row2['label'];
        } else {
            $label = $row['label_id'];
        }

        $sub = $myts->makeTareaData4Show($row['subject'], 0);
        $mes = $myts->makeTareaData4Show($row['message'], 0);
        $mes = str_replace('<br />', ' ', $mes);
        $mes = str_replace('<br>', ' ', $mes);

        echo "<div style='margin:0 20px 30px 20px;'>";
        echo "<div class='stitle'><a href='commentview.php?cid=" . $row['pcom_id'] . "'>" . $sub . '</a></div>';
        echo "<div style='padding: 5px 40px 5px 0;'>" . $mes . '</div>';
        echo '<div>';
        echo "<span class='sinfo'><a href='detail.php?id=" . $row['label_id'] . "'>" . $label . '</a> - ';
        echo $row['info'] . '</span>';
        echo '</div>';
        echo '</div>';
    }

    echo "<div style='text-align:center;'>" . $xp->renderNav() . '</div>';
}

function setResult($list, $kw)
{
    global $xoopsDB;

    $time = time();
    $rs   = $xoopsDB->queryF('DELETE FROM ' . $xoopsDB->prefix('newdb_fulltext_search') . " WHERE user < '" . ($time - 21600) . "'");

    foreach ($list as $v) {
        $sql = 'SELECT * FROM ' . $xoopsDB->prefix('newdb_comment') . " WHERE com_id='" . $v . "'";
        $rs  = $xoopsDB->query($sql);
        $row = $xoopsDB->fetchArray($rs);

        // get label name
        $pcom = $row['pcom_id'];
        if ($row['pcom_id']) {
            $sql  = 'SELECT * FROM ' . $xoopsDB->prefix('newdb_comment') . " WHERE com_id='" . $pcom . "'";
            $rs2  = $xoopsDB->query($sql);
            $row2 = $xoopsDB->fetchArray($rs2);
            $pcom = $row2['com_id'];
        } else {
            $pcom = $row['com_id'];
        }
        $sql      = 'SELECT * FROM ' . $xoopsDB->prefix('newdb_comment_topic') . " WHERE com_id='" . $pcom . "'";
        $rs2      = $xoopsDB->query($sql);
        $row2     = $xoopsDB->fetchArray($rs2);
        $label_id = $row2['label_id'];
        $type     = $row2['type'];

        $subject = $row['subject'];
        $message = $row['message'];

        for ($i = 0, $iMax = count($kw); $i < $iMax; ++$i) {
            if ($rcd = mberegi($kw[$i], $message)) {
                $message = mberegi_replace($kw[$i], '[b]' . $kw[$i] . '[/b]', $message);
            }
        }
        if (strlen($message) > 200) {
            $message = mbsubstr($message, 0, 200);
        }

        $rs2  = $xoopsDB->query('SELECT * FROM ' . $xoopsDB->prefix('users') . " WHERE uid='" . $row['reg_user'] . "'");
        $row2 = $xoopsDB->fetchArray($rs2);
        $info = date('Y-m-d', $row['reg_date']) . ' ' . $row2['uname'];

        $subject = addslashes($subject);
        $message = addslashes($message);

        $sql = 'INSERT INTO ' . $xoopsDB->prefix('newdb_fulltext_search');
        $sql .= " VALUES('" . $time . "','" . $label_id . "','" . $pcom . "','" . $subject . "','" . $message . "','" . $info . "','" . $type . "')";
        $rs2 = $xoopsDB->queryF($sql);
    }

    return $time;
}

include XOOPS_ROOT_PATH . '/footer.php';
