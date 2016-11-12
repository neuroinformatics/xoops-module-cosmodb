<?php

include __DIR__.'/header.php';

if (isset($_GET['type'])) {
    $mode = $_GET['type'];
} else {
    redirect_header(XOOPS_URL, 2, _ND_NACCESS);
}

if (isset($_GET['st'])) {
    $st = (int) $_GET['st'];
} else {
    $st = 0;
}
$lim = 40;

// users
$users = array();
$sql = 'SELECT uid, uname FROM '.$xoopsDB->prefix('users');
$rs = $xoopsDB->query($sql);
while ($row = $xoopsDB->fetchArray($rs)) {
    $users[$row['uid']] = $row['uname'];
}

switch ($mode) {

    case 'reg':
        $news = array();
        $sql = 'SELECT * FROM '.$xoopsDB->prefix('newdb_master');
        $rs = $xoopsDB->query($sql);
        $n = $xoopsDB->getRowsNum($rs);

        $sql = 'SELECT * FROM '.$xoopsDB->prefix('newdb_master').' ORDER BY reg_date DESC LIMIT '.$st.','.$lim.'';
        $rs = $xoopsDB->query($sql);
        while ($row = $xoopsDB->fetchArray($rs)) {
            $date = date('Y-m-d', $row['reg_date']);
            $user = 'Guest';
            if (isset($users[$row['author']])) {
                $user = $users[$row['author']];
            }

            if ($xoopsModuleConfig['dname_flg']) {
                $label = $row['label'];
            } else {
                $label = $row['label_id'];
            }

            $news[] = array(
                'label' => "<a href='".XOOPS_URL.'/modules/newdb/detail.php?id='.$row['label_id']."'>".$label.'</a>',
                'user' => $user,
                'date' => $date,
            );
        }
        include XOOPS_ROOT_PATH.'/header.php';
        showResult($news, $n, $st, $lim, 'reg');
        include XOOPS_ROOT_PATH.'/footer.php';
        break;

    case 'file':
        $news = array();
        $lid = (int) $_GET['lid'];

        $sql = 'SELECT * FROM '.$xoopsDB->prefix('newdb_item')." WHERE label_id='".$lid."' AND type='file'";
        $rs = $xoopsDB->query($sql);
        $n = $xoopsDB->getRowsNum($rs);

        $sql = 'SELECT * FROM '.$xoopsDB->prefix('newdb_item')." WHERE label_id='".$lid."' AND type='file'";
        $sql .= ' ORDER BY reg_date DESC LIMIT '.$st.','.$lim;
        $rs = $xoopsDB->query($sql);
        while ($row = $xoopsDB->fetchArray($rs)) {
            $date = date('Y-m-d', $row['reg_date']);
            $user = '';
            if (isset($users[$row['reg_user']])) {
                $user = $users[$row['reg_user']];
            }

            if (!empty($row['path'])) {
                $l = $row['path'].'/'.$row['name'].'</a>';
            } else {
                $l = $row['name'].'</a>';
            }

            $news[] = array(
                'label' => $l,
                'user' => $user,
                'date' => $date,
            );
        }
        include XOOPS_ROOT_PATH.'/header.php';
        include __DIR__.'/style.css';
        showResult($news, $n, $st, $lim, 'file', $lid);
        include XOOPS_ROOT_PATH.'/footer.php';
        break;
}

function showResult($news, $n, $st, $lim, $type, $lid = '')
{
    require XOOPS_ROOT_PATH.'/class/pagenav.php';
    if ($type === 'file') {
        $title = _ND_NEWS_REGFILE;
        $xp = new XoopsPageNav($n, $lim, $st, 'st', '&type='.$type.'&lid='.$lid);
    } else {
        $title = _ND_NEWS_REGPAST;
        $xp = new XoopsPageNav($n, $lim, $st, 'st', '&type='.$type);
    }
    $link = $xp->renderNav();

    echo "<center><div class='title' style='margin:10px 0 0 10px;'>".$title.'</div></center>';

    echo "<div style='text-align:center; margin-bottom:10px;'>".$link.'</div>';
    echo "<table style='width:100%;'>";
    for ($i = 0, $iMax = count($news); $i < $iMax; ++$i) {
        echo '<tr>';
        echo "<td style='width:100px; border-bottom:1px dashed #ddd;'>".$news[$i]['date'].'</td>';
        echo "<td style='border-bottom:1px dashed #ddd;'>".$news[$i]['label'].' </td>';
        echo "<td style='width:12%; border-bottom:1px dashed #ddd;'>".$news[$i]['user'].'</td>';
        echo '</tr>';
    }
    echo '</table><br>';
    echo "<div style='text-align:center;'>".$xp->renderNav();

    if ($type === 'file') {
        echo "<br><a href='detail.php?id=".$lid."'>"._ND_BACK.'</a></div>';
    } else {
        echo "<br><a href='index.php'>TOPへ</a></div>";
    }
}
