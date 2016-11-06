<?php

include __DIR__ . '/header.php';
include __DIR__ . '/class/commentpost.php';

if (!$uid && !$xoopsModuleConfig['guest_post']) {
    redirect_header(MOD_URL, 2, _ND_NACCESS);
}

$method  = '';
$subject = '';
$message = '';
$type    = '';
$lid     = -1;
$cid     = -1;
$preview = 0;

if (isset($_GET['method'])) {
    $method = $myts->stripSlashesGPC($_GET['method']);
    if (isset($_GET['type'])) {
        $type = $myts->stripSlashesGPC($_GET['type']);
    }
    if (isset($_GET['lid'])) {
        $lid = (int)$_GET['lid'];
    } elseif (isset($_GET['cid'])) {
        $cid = (int)$_GET['cid'];
    }
} elseif (isset($_POST['method'])) {
    $method = $myts->stripSlashesGPC($_POST['method']);
    if (isset($_POST['type'])) {
        $type = $myts->stripSlashesGPC($_POST['type']);
    }
    if (isset($_POST['lid'])) {
        $lid = (int)$_POST['lid'];
    } elseif (isset($_POST['cid'])) {
        $cid = (int)$_POST['cid'];
    }
}

// check user
if ($cid > 0 && !$isadmin && $method !== 'new') {
    $sql = 'SELECT * FROM ' . $xoopsDB->prefix('newdb_comment');
    $sql .= " WHERE com_id='" . $cid . "'";
    $rs = $xoopsDB->query($sql);
    if ($xoopsDB->getRowsNum($rs) > 0) {
        $row = $xoopsDB->fetchArray($rs);
        if ($uid != $row['reg_user']) {
            redirect_header(MOD_URL, 2, _ND_NACCESS);
        }
    } else {
        redirect_header(MOD_URL, 2, _ND_NACCESS);
    }
}

// check author comment post permission
if ($cid > 0 && $method === 'new') {
    $sql = 'SELECT * FROM ' . $xoopsDB->prefix('newdb_comment_topic');
    $sql .= " WHERE com_id='" . $cid . "'";
    $rs = $xoopsDB->query($sql);
    if ($xoopsDB->getRowsNum($rs) > 0) {
        $row = $xoopsDB->fetchArray($rs);
        if ($row['type'] === 'auth') {
            if (!$uid || !$xoopsModuleConfig['acom_post']) {
                redirect_header(MOD_URL, 2, _ND_NACCESS);
            }
        }
    }
}

// submit (new, edit, reply)
if (isset($_POST['submit']) || $method === 'delete2') {
    if ($cid > 0) {
        $sql = 'SELECT pcom_id FROM ' . $xoopsDB->prefix('newdb_comment') . " WHERE com_id='" . $cid . "'";
        $rs  = $xoopsDB->query($sql);
        $row = $xoopsDB->fetchArray($rs);

        if ($row['pcom_id'] == 0) {
            $sql = 'SELECT label_id FROM ' . $xoopsDB->prefix('newdb_comment_topic') . " WHERE com_id='" . $cid . "'";
        } else {
            $sql = 'SELECT label_id FROM ' . $xoopsDB->prefix('newdb_comment_topic') . " WHERE com_id='" . $row['pcom_id'] . "'";
        }
        $rs  = $xoopsDB->query($sql);
        $row = $xoopsDB->fetchArray($rs);
        $id  = $row['label_id'];
    } else {
        $id = $lid;
    }
}

if (isset($_POST['submit'])) {
    $cp = new CommentPost();
    $cp->setMethod($_POST['method']);
    if ($type === 'auth') {
        $cp->setSubject(_ND_CLASS_ACOM);
    } else {
        $cp->setSubject($_POST['subject']);
    }
    $cp->setMessage($_POST['message']);
    $cp->setUid($uid);
    if ($lid > 0) {
        $cp->setLid($lid);
    } elseif ($cid > 0) {
        $cp->setCid($cid);
    }
    ($type === 'auth') ? $cp->setType('auth') : $cp->setType('user');

    if ($cp->register()) {
        $ms = _ND_COMMENT_POST;

        if (isset($_POST['mail'])) {
            $mail = array();
            foreach ($_POST['mail'] as $v) {
                if (!empty($v)) {
                    $mail[] = $v;
                }
            }
            if (count($mail)) {
                $sitename = '';
                $sql      = 'SELECT conf_value FROM ' . $xoopsDB->prefix('config') . " WHERE conf_name='sitename'";
                $rs       = $xoopsDB->query($sql);
                $row      = $xoopsDB->fetchArray($rs);
                $sitename = $row['conf_value'];

                $rs  = $xoopsDB->query('SELECT uname FROM ' . $xoopsDB->prefix('users') . " WHERE uid='" . $uid . "'");
                $row = $xoopsDB->fetchArray($rs);

                $msgs_comment = $row['uname'] . " wrote:\n\n";
                $msgs_comment .= $cp->message . "\n\n";
                $msgs_comment .= date('Y-m-d H:i') . "\n";
                $msgs_comment .= "-----------\n";
                $msgs_comment .= XOOPS_URL;

                for ($i = 0, $iMax = count($mail); $i < $iMax; ++$i) {
                    $xoopsMailer = &getMailer();
                    $xoopsMailer->useMail();
                    $xoopsMailer->setFromName($sitename);
                    $xoopsMailer->setToEmails($mail[$i]);
                    $xoopsMailer->setSubject($cp->subject);
                    $xoopsMailer->setBody($msgs_comment);
                    $xoopsMailer->send();
                }
            }
        }

        // Increment post counter 2006/05/12
        $user = new XoopsUser($uid);
        $user->incrementPost();
    } else {
        $ms = _ND_COMMENT_NPOST . '<br>' . $cp->error();
    }

    // submit(delete)
} elseif ($method === 'delete2') {
    $cp = new CommentPost();
    $cp->setMethod('delete');
    $cp->setUid($uid);
    $cp->setCid($cid);
    ($type === 'auth') ? $cp->setType('auth') : $cp->setType('user');

    if ($cp->register()) {
        $ms = _ND_COMMENT_DEL;
    } else {
        $ms = _ND_COMMENT_NDEL;
    }
}

if (isset($_POST['submit']) || $method === 'delete2') {
    redirect_header(XOOPS_URL . '/modules/newdb/detail.php?id=' . $id, 2, $ms);
}

// preview
if (isset($_POST['preview']) || $_GET['method'] === 'edit') {
    if (isset($_POST['preview'])) {
        if (isset($_POST['subject'])) {
            $subject = $_POST['subject'];
        } else {
            $subject = '';
        }
        $message = $_POST['message'];
    } else {
        $sql = 'SELECT * FROM ' . $xoopsDB->prefix('newdb_comment') . " WHERE com_id='" . $cid . "'";
        $rs  = $xoopsDB->query($sql);
        if ($rs) {
            $row     = $xoopsDB->fetchArray($rs);
            $subject = $row['subject'];
            $message = $row['message'];
        }
    }

    $subject4show = $myts->makeTboxData4Preview($subject);
    $message4show = $myts->makeTareaData4Preview($message, 0);

    include XOOPS_ROOT_PATH . '/header.php';
    include __DIR__ . '/style.css';
    echo "<table class='list_table'>";
    if ($type !== 'auth') {
        echo "<tr><td class='even'>" . $subject4show . '</td></tr>';
    }
    echo '<tr><td><br>' . $message4show . '<br></td></tr>';
    echo '</table><br><br>';

    $subject = $myts->makeTboxData4PreviewInForm($subject);
    $message = $myts->makeTareaData4PreviewInForm($message);
    $preview = 1;
}

switch ($method) {

    case 'new':
        if (!$preview) {
            if ($cid != -1) {
                $sql     = 'SELECT subject FROM ' . $xoopsDB->prefix('newdb_comment') . " WHERE com_id='" . $cid . "'";
                $rs      = $xoopsDB->query($sql);
                $row     = $xoopsDB->fetchArray($rs);
                $subject = 'Re:' . $myts->makeTboxData4PreviewInForm($row['subject']);
            }
            include XOOPS_ROOT_PATH . '/header.php';
            include __DIR__ . '/style.css';
        }
        include __DIR__ . '/include/commentform.inc.php';
        include XOOPS_ROOT_PATH . '/footer.php';
        break;

    case 'edit':
        if (!$preview) {
            include XOOPS_ROOT_PATH . '/header.php';
            include __DIR__ . '/style.css';
        }
        include __DIR__ . '/include/commentform.inc.php';
        include XOOPS_ROOT_PATH . '/footer.php';
        break;

    case 'delete':
        include XOOPS_ROOT_PATH . '/header.php';
        include __DIR__ . '/style.css';
        $sql = 'SELECT * FROM ' . $xoopsDB->prefix('newdb_comment') . " WHERE com_id='" . $cid . "'";
        $rs  = $xoopsDB->query($sql);
        if ($rs) {
            $row          = $xoopsDB->fetchArray($rs);
            $subject4show = $myts->makeTboxData4Preview($row['subject']);
            $message4show = $myts->makeTareaData4Preview($row['message'], 0);

            echo "<table class='list_table'>";
            echo "<tr><td class='even'>" . $subject4show . '</td></tr>';
            echo '<tr><td><br>' . $message4show . '<br></td></tr>';
            echo '</table><br><br>';
            echo _ND_COMMENT_CONFIRM;

            if ($type === 'auth') {
                echo "<a href='comment.php?method=delete2&cid=" . $cid . "&type=auth'>YES</a>";
            } else {
                echo "<a href='comment.php?method=delete2&cid=" . $cid . "'>YES</a>";
            }
            echo " / <a href='javascript:history.back()'>NO</a>";
        } else {
            redirect_header(XOOPS_URL . '/modules/newdb/index.php', 1, _ND_COMMENT_NEXIST);
        }

        include XOOPS_ROOT_PATH . '/footer.php';
        break;

    default:
        break;
}
