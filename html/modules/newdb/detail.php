<?php

include __DIR__.'/header.php';
include __DIR__.'/class/component.php';
include __DIR__.'/include/checkkeydir.php';

$label_id = (int) $_GET['id'];
if (!$label_id) {
    redirect_header(MOD_URL, 2, _ND_NACCESS);
}

// extension program for advanced user
//include __DIR__ . '/extension/exdetail.php';

$com_limit = 10;
if (isset($_GET['com'])) {
    $com_limit = 0;
}

$com = new Component();
if ($com->setLabelID($label_id)) {
    checkKeyDir($label_id);

    $sql = 'SELECT template FROM '.$xoopsDB->prefix('newdb_detail');
    $rs = $xoopsDB->query($sql);
    $row = $xoopsDB->fetchArray($rs);
    $template = "<script language='JavaScript' src='tab.js'></script>\n";
    $template .= "<script language='JavaScript' src='border.js'></script>\n";
    $template .= "<script language='JavaScript' src='clipboard.js'></script>\n";
    $template .= "<div id='copy'></div>".$row['template'];

    $sql = 'SELECT reg_date,author,views FROM '.$xoopsDB->prefix('newdb_master')." WHERE label_id='".$label_id."'";
    $rs = $xoopsDB->query($sql);
    $row = $xoopsDB->fetchArray($rs);
    $date = date('Y-m-d', $row['reg_date']);
    $views = $row['views'] + 1;

    if ($uid != $row['author']) {
        $sql = 'UPDATE '.$xoopsDB->prefix('newdb_master');
        $sql .= " SET views='".$views."' WHERE label_id='".$label_id."'";
        $rs2 = $xoopsDB->queryF($sql);
    }

    $sql = 'SELECT uname FROM '.$xoopsDB->prefix('users')." WHERE uid='".$row['author']."'";
    $rs = $xoopsDB->query($sql);
    $row = $xoopsDB->fetchArray($rs);
    $author = $row['uname'];

    //## Basic Information

    if (strstr($template, '{ID}')) {
        $template = str_replace('{ID}', $label_id, $template);
    }
    if (strstr($template, '{Data Name}')) {
        $template = str_replace('{Data Name}', $com->label, $template);
    }
    if (strstr($template, '{Author}')) {
        $template = str_replace('{Author}', $author, $template);
    }
    if (strstr($template, '{Creation Date}')) {
        $template = str_replace('{Creation Date}', $date, $template);
    }
    if (strstr($template, '{Views}')) {
        $template = str_replace('{Views}', $views, $template);
    }
    if (strstr($template, '{Keyword}')) {
        $template = str_replace('{Keyword}', $com->getKeywordList(), $template);
    }

    //## Data file tree menu

    if (strstr($template, '{Dtree}')) {
        $perm = 0;
        if ($uid) {
            foreach ($xoopsModuleConfig['access_perm'] as $group_id) {
                $sql = 'SELECT uid FROM '.$xoopsDB->prefix('groups_users_link');
                $sql .= " WHERE groupid='".$group_id."'";
                $rs = $xoopsDB->query($sql);
                while ($row = $xoopsDB->fetchArray($rs)) {
                    if ($row['uid'] == $uid) {
                        $perm = 1;
                    }
                }
            }
        }
        if ($perm && $xoopsModuleConfig['use_datafunc']) {
            $tree = $com->getDynamicMenu();
            $template = str_replace('{Dtree}', $tree, $template);
        } else {
            $template = str_replace('{Dtree}', _ND_NACCESS2, $template);
        }
    }

    //## Comment things

    if (strstr($template, '{Acomment}')) {
        if ($xoopsModuleConfig['acom_post']) {
            $acom = $com->getAuThread($uid, $isadmin, 1);
        } else {
            $acom = $com->getAuThread($uid, $isadmin);
        }
        $template = str_replace('{Acomment}', $acom, $template);
    }

    if (strstr($template, '{Ucomment}')) {
        if ($xoopsModuleConfig['guest_post']) {
            $ucom = $com->getThread($com_limit, 1);
        } elseif ($uid && !$xoopsModuleConfig['guest_post']) {
            $ucom = $com->getThread($com_limit, 1);
        } else {
            $ucom = $com->getThread($com_limit);
        }
        $template = str_replace('{Ucomment}', $ucom, $template);
    }

    //## Link

    if (strstr($template, '{Link}')) {
        $link = $com->getLink($uid, $isadmin, $xoopsModuleConfig['dname_flg']);
        $template = str_replace('{Link}', $link, $template);
    }

    //## Thumbnail

    if (strstr($template, '{Image ')) {
        $st = 0;
        $end = 0;
        for (; ;) {
            $st = strpos($template, '{Image ', $end);
            if ($st > $end) {
                $end = strpos($template, '}', $st);
                $image = substr($template, $st, $end - $st + 1);
                $tmp = str_replace('{Image ', '', $image);
                $tmp = str_replace('}', '', $tmp);
                $option = explode(' ', $tmp);

                $thumb = $com->getThumbnail(EXTRACT_PATH, XOOPS_URL, $option[0], $option[1]);
                $template = str_replace($image, $thumb, $template);
            } else {
                break;
            }
        }
    }

    //## Clipboard function

    if (strstr($template, '{Ref ')) {
        $st = 0;
        $end = 0;
        for (; ;) {
            $st = strpos($template, '{Ref ', $end);
            if ($st > $end) {
                $end = strpos($template, '}', $st);
                $ref = substr($template, $st, $end - $st + 1);

                $perm = 0;
                if ($uid) {
                    foreach ($xoopsModuleConfig['access_perm'] as $group_id) {
                        $sql = 'SELECT uid FROM '.$xoopsDB->prefix('groups_users_link');
                        $sql .= " WHERE groupid='".$group_id."'";
                        $rs = $xoopsDB->query($sql);
                        while ($row = $xoopsDB->fetchArray($rs)) {
                            if ($row['uid'] == $uid) {
                                $perm = 1;
                            }
                        }
                    }
                }

                if (!$perm || !$xoopsModuleConfig['use_datafunc']) {
                    $path = _ND_NACCESS2;
                } else {
                    $suffix = str_replace('{Ref ', '', $ref);
                    $suffix = str_replace('}', '', $suffix);

                    $path = '';
                    $sql = 'SELECT * FROM '.$xoopsDB->prefix('newdb_master')." WHERE label_id='".$label_id."'";
                    $rs = $xoopsDB->query($sql);
                    $row = $xoopsDB->fetchArray($rs);
                    $label = $row['label'];

                    $sql = 'SELECT * FROM '.$xoopsDB->prefix('newdb_item');
                    $sql .= " WHERE label_id='".$label_id."' AND name like '%.".$suffix."' AND type='file'";
                    $rs = $xoopsDB->query($sql);
                    if ($xoopsDB->getRowsNum($rs)) {
                        while ($row = $xoopsDB->fetchArray($rs)) {
                            if (!empty($row['path'])) {
                                $p = $row['path'].'/';
                            } else {
                                $p = '';
                            }
                            $tmp = 'extract/'.$label_id.'/data/'.$p.$row['name'];
                            $path .= "<a style='cursor: pointer;' onClick=\"javascript:setClipboard('".$tmp."')\">".$row['name'].'</a><br>';
                        }
                    }
                }
                $template = str_replace($ref, $path, $template);
            } else {
                break;
            }
        }
    }

    //## Add Bookmark link

    if (strstr($template, '{AddBookmark}')) {
        if ($uid) {
            $bookmark = "<a href='bookmark.php?lid=".$label_id."&mode=regbf'>";
            $bookmark .= "<img src='images/book.png' alt='add bookmark'></a>";
        } else {
            $bookmark = '';
        }
        $template = str_replace('{AddBookmark}', $bookmark, $template);
    }

    //## Add Link link

    if (strstr($template, '{AddLink}')) {
        if ($uid) {
            $link = "<a href='link.php?lid=".$label_id."'>";
            $link .= "<img src='images/link.png' alt='add link'></a>";
        } else {
            $link = '';
        }
        $template = str_replace('{AddLink}', $link, $template);
    }

    //## Config link

    if (strstr($template, '{Config}')) {
        $sql = 'SELECT * FROM '.$xoopsDB->prefix('newdb_master');
        $sql .= " WHERE label_id='".$label_id."'";
        $rs = $xoopsDB->query($sql);
        $row = $xoopsDB->fetchArray($rs);
        if ($row['author'] == $uid || $isadmin) {
            $edit = "<a href='config.php?lid=".$label_id."'>";
            $edit .= "<img src='images/config.png' alt='config'></a>";
        } else {
            $edit = '';
        }
        $template = str_replace('{Config}', $edit, $template);
    }

    //## FileManager link

    if (strstr($template, '{FileManager}')) {
        $perm = 0;
        if ($uid) {
            foreach ($xoopsModuleConfig['reg_perm'] as $group_id) {
                $sql = 'SELECT uid FROM '.$xoopsDB->prefix('groups_users_link');
                $sql .= " WHERE groupid='".$group_id."'";
                $rs = $xoopsDB->query($sql);
                while ($row = $xoopsDB->fetchArray($rs)) {
                    if ($row['uid'] == $uid) {
                        $perm = 1;
                    }
                }
            }
        }
        if ($perm && $xoopsModuleConfig['use_datafunc']) {
            $dedit = "<a href='edata.php?lid=".$label_id."'>";
            $dedit .= "<img src='images/file.png' alt='file manager'></a>";
        } else {
            $dedit = '';
        }

        $template = str_replace('{FileManager}', $dedit, $template);
    }

    //## NEWS

    if (strstr($template, '{News}')) {
        $news = '';

        $perm = 0;
        if ($uid) {
            foreach ($xoopsModuleConfig['access_perm'] as $group_id) {
                $sql = 'SELECT uid FROM '.$xoopsDB->prefix('groups_users_link');
                $sql .= " WHERE groupid='".$group_id."'";
                $rs = $xoopsDB->query($sql);
                while ($row = $xoopsDB->fetchArray($rs)) {
                    if ($row['uid'] == $uid) {
                        $perm = 1;
                    }
                }
            }
        }
        if ($perm && $xoopsModuleConfig['use_datafunc']) {
            $sql = 'SELECT * FROM '.$xoopsDB->prefix('newdb_item');
            $sql .= " WHERE label_id='".$label_id."' AND type='file' ORDER BY reg_date DESC LIMIT 0,6";
            $rs = $xoopsDB->query($sql);
            while ($row = $xoopsDB->fetchArray($rs)) {
                $sql = 'SELECT uname FROM '.$xoopsDB->prefix('users')." WHERE uid='".$row['reg_user']."'";
                $rs2 = $xoopsDB->query($sql);
                $row2 = $xoopsDB->fetchArray($rs2);
                $date = date('m-d H:i', $row['reg_date']);
                $uname = $row2['uname'];
                $path = $row['path'];
                if (!empty($path)) {
                    $path .= '/';
                }

                $news .= "<tr style='text-align:left;'>";
                $news .= "<td style='width:100px; border-bottom:1px dashed #ddd;'>".$date.'</td>';
                $news .= "<td style='border-bottom:1px dashed #ddd;'>".$path.$row['name'].' </td>';
                $news .= "<td style='width:12%; border-bottom:1px dashed #ddd;'>".$uname.'</td>';
                $news .= '</tr>';
            }
            if (!empty($news)) {
                $news = '<table>'.$news.'</table>';
                $news .= "<div style='text-align:right;'><a href='news.php?type=file&lid=".$label_id."'>"._ND_CLASS_SHOWALL.'</a></div>';
            }
        } else {
            $news .= _ND_NACCESS2;
        }
        $template = str_replace('{News}', $news, $template);
    }

    //## Custom component value

    $sql = 'SELECT * FROM '.$xoopsDB->prefix('newdb_component_master');
    $rs = $xoopsDB->query($sql);
    while ($row = $xoopsDB->fetchArray($rs)) {
        if (strstr($template, '{'.$row['name'].'}')) {
            $sql = 'SELECT * FROM '.$xoopsDB->prefix('newdb_component');
            $sql .= " WHERE label_id='".$label_id."' AND comp_id='".$row['comp_id']."'";
            $rs2 = $xoopsDB->query($sql);
            $value = '';
            while ($row2 = $xoopsDB->fetchArray($rs2)) {
                if ($value) {
                    $value .= ', ';
                }

                if ($row['type'] == '2' || $row['type'] == '3') {
                    $row2['value'] = str_replace('{', '<img src="images/admin/', $row2['value']);
                    $row2['value'] = str_replace('}', '">', $row2['value']);
                } elseif ($row['type'] == '4') {
                    if ($row['textmax'] == '0') {
                        $row2['value'] = str_replace("\r\n", "\r", $row2['value']);
                        $row2['value'] = str_replace("\r", "\n", $row2['value']);
                        $row2['value'] = str_replace("\n", '<br>', $row2['value']);
                    }
                }
                $value .= $row2['value'];
            }
            $template = str_replace('{'.$row['name'].'}', $value, $template);
        }
    }

    //## Tab pages

    if (strstr($template, '{tab')) {
        for ($i = 1; ; ++$i) {
            if (!strstr($template, '{tab'.$i.'}')) {
                break;
            }
            if ($i == 1) {
                $div = "<div id='detailbox".$i."'>";
            } else {
                $div = "<div id='detailbox".$i."' style='display:none'>";
            }
            $template = str_replace('{tab'.$i.'}', $div, $template);
        }
    }
    if (strstr($template, '{/tab}')) {
        $template = str_replace('{/tab}', '</div>', $template);
    }

    if (strstr($template, '{href_tab')) {
        for ($i = 1; ; ++$i) {
            if (!strstr($template, '{href_tab'.$i.'}')) {
                break;
            }
            $div = "<a href=\"javascript:seltab('detailbox', 'head', 10, ".$i.')">';
            $template = str_replace('{href_tab'.$i.'}', $div, $template);
        }
    }
    if (strstr($template, '{/href_tab}')) {
        $template = str_replace('{/href_tab}', '</a>', $template);
    }

    //## Show detail page
    include XOOPS_ROOT_PATH.'/header.php';

    $xoopsTpl->assign('xoops_pagetitle', $com->label);

    include __DIR__.'/style.css';
    echo $template;
    include XOOPS_ROOT_PATH.'/footer.php';
} else {
    redirect_header(MOD_URL, 2, $com->error());
}
