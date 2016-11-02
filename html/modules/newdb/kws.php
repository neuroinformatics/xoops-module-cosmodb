<?php
    include 'header.php';
    include 'class/keyword.php';
    include 'class/listmanager.php';
    $kw = new Keyword();
    $lm = new ListManager();

    $mode = '';
    if (isset($_POST['user'])) {
        $user = intval($_POST['user']);
        if (isset($_POST['srefine'])) {
            $mode = 'refine';
        } elseif (isset($_POST['ssave'])) {
            $mode = 'save';
        }
    }

    switch ($mode) {
        case 'save':
            $sql = 'SELECT * FROM '.$xoopsDB->prefix('newdb_list_refine');
            $sql .= " WHERE user='".$user."'";
            $rs = $xoopsDB->query($sql);
            $xoopsDB->getRowsNum($rs);
            if ($xoopsDB->getRowsNum($rs) && $uid) {
                $row = $xoopsDB->fetchArray($rs);
                $labels = explode(',', $row['labels']);
                $date = date('m/d H:i', time());

                include 'class/bookmark.php';
                $bm = new Bookmark($uid);
                if ($bd_id = $bm->regNewDir(0, $date, 0)) {
                    for ($i = 0; $i < count($labels); ++$i) {
                        $bm->regNewFile($bd_id, $labels[$i], '');
                    }
                }
                $mes = _ND_KW_OK;
            } else {
                if (!$uid) {
                    $mes = _ND_NACCESS2;
                } else {
                    $mes = _ND_KW_NG;
                }
            }
            redirect_header(MOD_URL.'/bookmark.php', 2, $mes);
            break;

        case 'refine':
            include XOOPS_ROOT_PATH.'/header.php';
            include 'style.css';
            showHistory($user);
            showForm($kw, $lm, $user);
            include XOOPS_ROOT_PATH.'/footer.php';
            break;

        default:
            include XOOPS_ROOT_PATH.'/header.php';
            include 'style.css';
            showForm($kw, $lm, time());
            include XOOPS_ROOT_PATH.'/footer.php';
            break;
    }

    function showHistory($user)
    {
        global $xoopsDB;
        echo "<center><div class='title' style='margin-top:20px'>"._ND_KW_SELECT."</div>\n";
        echo "<div class='title_desc' style='margin-bottom:10px'>"._ND_KW_ALREADY."</div>\n";
        echo "<table style='width:500px'>";

        $sql = 'SELECT * FROM '.$xoopsDB->prefix('newdb_list_refine_option')." WHERE user='".$user."'";
        $rs = $xoopsDB->query($sql);
        while ($row = $xoopsDB->fetchArray($rs)) {
            $andor = explode(':', $row['keywords']);
            $kw = str_replace($andor[0].':', '', $row['keywords']);
            $kw = explode(',', $kw);
            echo '<tr><td>'.strtoupper($andor[0]).'</td><td>';

            for ($i = 0; $i < count($kw); ++$i) {
                $sql = 'SELECT * FROM '.$xoopsDB->prefix('newdb_keyword')." WHERE kw_id='".$kw[$i]."'";
                $rs2 = $xoopsDB->query($sql);
                $row2 = $xoopsDB->fetchArray($rs2);

                $path = $row2['path'].$kw[$i];
                $path = explode('/', $path);

                $path4show = '';
                for ($j = 0; $j < count($path); ++$j) {
                    if ($path[$j]) {
                        $sql = 'SELECT * FROM '.$xoopsDB->prefix('newdb_keyword')." WHERE kw_id='".$path[$j]."'";
                        $rs2 = $xoopsDB->query($sql);
                        $row2 = $xoopsDB->fetchArray($rs2);
                        $path4show .= $row2['keyword'].'/';
                    }
                }
                $path4show = substr($path4show, 0, -1);
                echo '<li>'.$path4show.'</li>';
            }
            echo '</td></tr>';
        }
        echo '</table></center>';
    }

    function showForm($kw, $lm, $user = '')
    {
        global $xoopsDB;
        $rs = $xoopsDB->query('SELECT * FROM '.$xoopsDB->prefix('newdb_list')." WHERE onoff='0'");
        $row = $xoopsDB->fetchArray($rs);
        echo "<script language='JavaScript' src='tab.js'></script>\n";
        echo "<script language='JavaScript' src='key.js'></script>\n";
        echo "<form action='list.php' method='GET'><center>";
        echo "<div class='title' style='margin-top:20px'>"._ND_CONFIG_KEYWORD."</div>\n";
        echo "<div class='title_desc'>"._ND_KW_SELECTSEARCH."</div>\n";
        echo "<table style='width:500px'><tr><td>".$kw->getCateTB().'';
        echo "<table class='list_table' style='width:150px; margin-top:10px; text-align:center'>";
        echo '<tr><th>'._ND_KW_METHOD.'</th></tr><tr><td>';
        echo "<input type='radio' name='andor' value='and' checked>AND&nbsp;&nbsp;";
        echo "<input type='radio' name='andor' value='or'>OR&nbsp;&nbsp;</td></tr></table>";
        echo "<center><input type='submit' value='submit' class='button' style='margin-top:15px;'></center>";
        echo '</td><td> </td><td>'.$kw->getKeyTB().'</td></tr></table>';
        echo "<input type='hidden' name='user' value='".$user."'>";
        echo "<input type='hidden' name='kws' value='y'>";
        echo "<input type='hidden' name='notkws' value='' id='notkws'>";
        echo "<input type='hidden' name='id' value='".$row['list_id']."'>";
        echo '</form></center>';
    }
