<?php

function b_newdb_news1()
{
    global $xoopsDB;
    $new_reg_lim       = 10;
    $block['more']     = _ND_BLOCK_MORE;
    $block['news_url'] = XOOPS_URL . '/modules/newdb/news.php?type=reg';

    // config value
    $sql = 'select mid from ' . $xoopsDB->prefix('modules');
    $sql .= " where dirname='newdb'";
    $res = $xoopsDB->query($sql);
    $row = $xoopsDB->fetchArray($res);
    $mid = $row['mid'];

    $sql = 'select conf_value from ' . $xoopsDB->prefix('config');
    $sql .= " where conf_modid='" . $mid . "' and conf_name='dname_flg'";
    $res       = $xoopsDB->query($sql);
    $row       = $xoopsDB->fetchArray($res);
    $dname_flg = $row['conf_value'];

    // users
    $users = array();
    $sql   = 'SELECT uid, uname FROM ' . $xoopsDB->prefix('users');
    $rs    = $xoopsDB->query($sql);
    while ($row = $xoopsDB->fetchArray($rs)) {
        $users[$row['uid']] = $row['uname'];
    }

    // new register
    $sql = 'SELECT * FROM ' . $xoopsDB->prefix('newdb_master');
    $sql .= ' ORDER BY reg_date DESC LIMIT 0,' . $new_reg_lim;
    $rs = $xoopsDB->query($sql);
    while ($row = $xoopsDB->fetchArray($rs)) {
        $date = date('m-d H:i', $row['reg_date']);
        if (isset($users[$row['author']])) {
            $user = $users[$row['author']];
        } else {
            $user = 'Guest';
        }

        if ($dname_flg) {
            $label = $row['label'];
        } else {
            $label = $row['label_id'];
        }

        $block['new_reg'][] = array(
            'label' => "<a href='" . XOOPS_URL . '/modules/newdb/detail.php?id=' . $row['label_id'] . "'>" . $label . '</a>',
            'user'  => $user,
            'date'  => $date,
        );
    }

    return $block;
}
