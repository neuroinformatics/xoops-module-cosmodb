<?php

    function b_newdb_news2()
    {
        global $xoopsDB;
        $acom_lim = 10;
        $ucom_lim = 15;
        $block['acom'] = _ND_BLOCK_ACOM;
        $block['ucom'] = _ND_BLOCK_UCOM;

        // users
        $users = array();
        $sql = 'SELECT uid, uname FROM '.$xoopsDB->prefix('users');
        $rs = $xoopsDB->query($sql);
        while ($row = $xoopsDB->fetchArray($rs)) {
            $users[$row['uid']] = $row['uname'];
        }

        // new comment
        $i = 0;
        $j = 0;
        $com = array();
        $ucom = array();
        $acom = array();

        $sql = 'SELECT * FROM '.$xoopsDB->prefix('newdb_comment').' ORDER BY reg_date DESC';
        $rs = $xoopsDB->query($sql);
        while ($row = $xoopsDB->fetchArray($rs)) {
            if ($i >= $acom_lim && $j >= $ucom_lim) {
                break;
            }

            $pcom = $row['pcom_id'];
            if ($row['pcom_id']) {
                $sql = 'SELECT * FROM '.$xoopsDB->prefix('newdb_comment')." WHERE com_id='".$pcom."'";
                $rs2 = $xoopsDB->query($sql);
                $row2 = $xoopsDB->fetchArray($rs2);
                $pcom = $row2['com_id'];
            } else {
                $pcom = $row['com_id'];
            }

            $sql = 'SELECT * FROM '.$xoopsDB->prefix('newdb_comment_topic')." WHERE com_id='".$pcom."'";
            $rs2 = $xoopsDB->query($sql);
            $row2 = $xoopsDB->fetchArray($rs2);

            if ($row2['type'] == 'auth' && $i < $acom_lim) {
                if (!in_array($pcom, $acom)) {
                    $acom[] = $pcom;
                    $com[] = array('t' => 'auth', 'com_id' => $pcom, 'label_id' => $row2['label_id']);
                    ++$i;
                }
            } elseif ($row2['type'] == 'user' && $j < $ucom_lim) {
                if (!in_array($pcom, $ucom)) {
                    $ucom[] = $pcom;
                    $com[] = array('t' => 'user', 'com_id' => $pcom, 'label_id' => $row2['label_id']);
                    ++$j;
                }
            }
        }

        for ($i = 0; $i < count($com); ++$i) {
            $sql = 'SELECT * FROM '.$xoopsDB->prefix('newdb_comment');
            $sql .= " WHERE com_id='".$com[$i]['com_id']."' || pcom_id='".$com[$i]['com_id']."'";
            $sql .= ' ORDER BY reg_date DESC LIMIT 0,1';
            $rs = $xoopsDB->query($sql);
            $row = $xoopsDB->fetchArray($rs);

            $date = date('m-d H:i', $row['reg_date']);
            if (isset($users[$row['reg_user']])) {
                $user = $users[$row['reg_user']];
            } else {
                $user = 'Guest';
            }
            $subject = htmlspecialchars($row['subject']);
            $subject = "<a href='".XOOPS_URL.'/modules/newdb/commentview.php?cid='.$com[$i]['com_id']."'>".$subject.'</a>';

            if ($com[$i]['t'] == 'auth') {
                $block['acomment'][] = array('subject' => $subject, 'user' => $user, 'date' => $date);
            } else {
                $block['ucomment'][] = array('subject' => $subject, 'user' => $user, 'date' => $date);
            }
        }

        return $block;
    }
