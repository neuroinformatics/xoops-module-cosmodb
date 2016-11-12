<?php

include __DIR__.'/header.php';
include __DIR__.'/class/tarextractor.php';
include __DIR__.'/class/regcopy.php';
include __DIR__.'/class/regdatabase.php';
include __DIR__.'/class/keyword.php';
include __DIR__.'/class/commentpost.php';
include __DIR__.'/include/getreglist.php';
include __DIR__.'/include/checkkeydir.php';

include XOOPS_ROOT_PATH.'/header.php';

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
if (!$perm) {
    redirect_header(MOD_URL, 2, _ND_NACCESS2);
}

$error = '';
$data_name = '';

if (isset($_POST['method'])) {
    $method = $myts->stripSlashesGPC($_POST['method']);
} else {
    $method = '';
}

// check input data
$required = '';
if ($method === 'do_reg') {
    $sql = 'SELECT * FROM '.$xoopsDB->prefix('newdb_component_master');

    // data name
    if ($xoopsModuleConfig['dname_flg']) {
        $rs = $xoopsDB->query($sql." WHERE comp_id='2'");
        $row = $xoopsDB->fetchArray($rs);
        $dname = '';
        if (empty($_POST['name'])) {
            $required .= $row['tag'].' '._ND_REG26.'<br>';
        } else {
            $dname = $_POST['name'];
            if ($row['textmax'] && strlen($dname) > $row['textmax']) {
                $required .= $row['tag'].' '._ND_REG27.'<br>';
            }
        }
    }

    // text check
    $rs = $xoopsDB->query($sql." WHERE type='4'");
    $n = $xoopsDB->getRowsNum($rs);
    for ($i = 0; $i < $n; ++$i) {
        isset($_POST['CT'.$i]) ? $ct[$i] = $_POST['CT'.$i] : $ct[$i] = '';
        $rs = $xoopsDB->query($sql." WHERE comp_id='".$_POST['CT'.$i.'_id']."'");
        $row = $xoopsDB->fetchArray($rs);
        if ($row['nonull'] && $ct[$i] == '') {
            $required .= $row['tag'].' '._ND_REG26.'<br>';
        }
        if ($row['textmax'] && strlen($ct[$i]) > $row['textmax']) {
            $required .= $row['tag'].' '._ND_REG27.'<br>';
        }
    }

    // checkbox check
    $rs = $xoopsDB->query($sql." WHERE type='3'");
    $n = $xoopsDB->getRowsNum($rs);
    for ($i = 0; $i < $n; ++$i) {
        isset($_POST['CC'.$i]) ? $cc[$i] = $_POST['CC'.$i] : $cc[$i] = '';
        $rs = $xoopsDB->query($sql." WHERE comp_id='".$_POST['CC'.$i.'_id']."'");
        $row = $xoopsDB->fetchArray($rs);

        $flg = 0;
        if (is_array($cc[$i])) {
            foreach ($cc[$i] as $key => $value) {
                if (!empty($value)) {
                    $flg = 1;
                }
            }
        }
        if ($row['nonull'] && !$flg) {
            $required .= $row['tag'].' '._ND_REG26.'<br>';
        }
    }

    // radio
    $rs = $xoopsDB->query($sql." WHERE type='2'");
    $n = $xoopsDB->getRowsNum($rs);
    for ($i = 0; $i < $n; ++$i) {
        isset($_POST['CR'.$i]) ? $cr[$i] = $_POST['CR'.$i] : $cr[$i] = '';
        $rs = $xoopsDB->query($sql." WHERE comp_id='".$_POST['CR'.$i.'_id']."'");
        $row = $xoopsDB->fetchArray($rs);

        if ($row['nonull'] && empty($cr[$i])) {
            $required .= $row['tag'].' '._ND_REG26.'<br>';
        }
    }

    // select
    $rs = $xoopsDB->query($sql." WHERE type='5'");
    $n = $xoopsDB->getRowsNum($rs);
    for ($i = 0; $i < $n; ++$i) {
        if (isset($_POST['CS'.$i])) {
            $cs[$i] = $_POST['CS'.$i];
        }
    }

    if ($required) {
        $method = '';
    }
}

switch ($method) {

    case 'do_reg':

        if ($xoopsModuleConfig['dname_flg']) {
            if (empty($_POST['name'])) {
                redirect_header(MOD_URL.'/register.php', 2, _ND_REG1);
            } else {
                $label_name = $myts->stripSlashesGPC($_POST['name']);
            }
        } else {
            $label_name = ' --- ';
        }

        // Register label
        $rdb = new RegDatabase();
        if ($rdb->setRegLabel($label_name, EXTRACT_PATH, $uid)) {
            if (!$rdb->regNewLabel()) {
                $error = $rdb->error();
            }
        } else {
            $error = $rdb->error();
        }
        if ($error) {
            redirect_header(MOD_URL.'/register.php', 2, $error);
        }
        $label_id = $rdb->labelid;

        // Data upload
        if (isset($_POST['uploaded_data'])) {
            $data_name = $myts->stripSlashesGPC($_POST['uploaded_data']);
        } elseif (isset($_POST['use_upload'])) {
            if (is_uploaded_file($_FILES['userfile']['tmp_name'])) {
                $fname = $_FILES['userfile']['name'];
                $tmp_name = $_FILES['userfile']['tmp_name'];

                if (filesize($tmp_name) > MAX_UPLOAD_SIZE) {
                    $error = _ND_REG2;
                } elseif (!preg_match("/.*(\.tar)$/i", $fname)) {
                    $error = _ND_REG4;
                }
            }

            if ($error) {
                redirect_header(MOD_URL.'/register.php', 2, $error);
            }
            $data_name = $fname;
            move_uploaded_file($tmp_name, UPLOAD_PATH.'/'.$data_name);
        }

        if (isset($_POST['use_upload']) || isset($_POST['uploaded_data'])) {
            if ($xoopsModuleConfig['use_suffix']) {
                $suf_array = explode('|', $xoopsModuleConfig['suffix']);
                $suf = '';
                for ($i = 0, $iMax = count($suf_array); $i < $iMax; ++$i) {
                    if (empty($suf_array[$i])) {
                        continue;
                    } else {
                        $suf .= '|';
                    }
                    $suf .= strtolower($suf_array[$i]).'|'.strtoupper($suf_array[$i]);
                }
            } else {
                $suf = '';
            }
            // extract
            if (preg_match("/.*(\.tar)$/i", $data_name)) {
                $archive = UPLOAD_PATH.'/'.$data_name;
                $tar = new TarExtractor();
                $tar->file_limit = MAX_UPLOAD_SIZE;
                if ($tar->setArchive($archive, EXTRACT_PATH)) {
                    if ($tar->doRegExtract($label_id, $suf)) {
                        unlink($archive);
                    } else {
                        $error = $tar->error();
                    }
                } else {
                    $error = $tar->error();
                }

                // copy
            } elseif (is_dir(UPLOAD_PATH.'/'.$xoopsUser->uname().'/'.$data_name)) {
                $from_path = UPLOAD_PATH.'/'.$xoopsUser->uname().'/'.$data_name;
                $fcopy = new RegCopy();
                if ($fcopy->setPath($from_path, EXTRACT_PATH)) {
                    if ($fcopy->doRegCopy($suf, $label_id)) {
                        $fcopy->delDirectory($from_path);
                    } else {
                        $error = $fcopy->error();
                    }
                } else {
                    $error = $fcopy->error();
                }
            }
        }
        if ($error) {
            redirect_header(MOD_URL.'/register.php', 2, $error);
        }
        checkKeyDir($label_id);

        // extension program for advanced user
        //include __DIR__ . '/extension/exregister.php';

        // Register files
        if (!$rdb->regNewItems()) {
            $error = $rdb->error();
            redirect_header(MOD_URL.'/register.php', 2, $error);
        }

        // Register custom field (newdb_component)
        // custom text
        $sql = 'SELECT * FROM '.$xoopsDB->prefix('newdb_component_master');

        // select
        $rs = $xoopsDB->query($sql." WHERE type='5'");
        $n = $xoopsDB->getRowsNum($rs);
        for ($i = 0; $i < $n; ++$i) {
            $cs = 'CS'.$i;
            if (isset($_POST[$cs])) {
                $comp_id = (int) $_POST[$cs.'_id'];
                $cs_value = $myts->stripSlashesGPC($_POST[$cs]);
                $cs_value = addslashes($cs_value);

                $sql2 = 'INSERT INTO '.$xoopsDB->prefix('newdb_component');
                $sql2 .= " VALUES('".$comp_id."', '".$label_id."', '".$cs_value."')";
                $rs = $xoopsDB->query($sql2);
            }
        }

        // text
        $rs = $xoopsDB->query($sql." WHERE type='4'");
        $n = $xoopsDB->getRowsNum($rs);
        for ($i = 0; $i < $n; ++$i) {
            $ct = 'CT'.$i;
            if (isset($_POST[$ct]) && isset($_POST[$ct.'_id'])) {
                $comp_id = (int) $_POST[$ct.'_id'];
                $ct_value = $myts->stripSlashesGPC($_POST[$ct]);
                $ct_value = addslashes($ct_value);
                $sql2 = 'INSERT INTO '.$xoopsDB->prefix('newdb_component');
                $sql2 .= " VALUES('".$comp_id."', '".$label_id."', '".$ct_value."')";
                $rs = $xoopsDB->query($sql2);
            }
        }

        // custom checkbox
        $rs = $xoopsDB->query($sql." WHERE type='3'");
        $n = $xoopsDB->getRowsNum($rs);
        for ($i = 0; $i < $n; ++$i) {
            $cc = 'CC'.$i;
            if (isset($_POST[$cc])) {
                $comp_id = (int) $_POST[$cc.'_id'];

                foreach ($_POST[$cc] as $key => $value) {
                    $cc_value = $myts->stripSlashesGPC($value);
                    $cc_value = addslashes($cc_value);

                    $sql2 = 'INSERT INTO '.$xoopsDB->prefix('newdb_component');
                    $sql2 .= " VALUES('".$comp_id."', '".$label_id."', '".$cc_value."')";
                    $rs = $xoopsDB->query($sql2);
                }
            }
        }

        // custom radio
        $rs = $xoopsDB->query($sql." WHERE type='2'");
        $n = $xoopsDB->getRowsNum($rs);
        for ($i = 0; $i < $n; ++$i) {
            $cr = 'CR'.$i;
            if (isset($_POST[$cr])) {
                $comp_id = (int) $_POST[$cr.'_id'];
                $cr_value = $myts->stripSlashesGPC($_POST[$cr]);
                $cr_value = addslashes($cr_value);

                $sql2 = 'INSERT INTO '.$xoopsDB->prefix('newdb_component');
                $sql2 .= " VALUES('".$comp_id."', '".$label_id."', '".$cr_value."')";
                $rs = $xoopsDB->query($sql2);
            }
        }

        // Register keyword
        $kw = '';
        if (!empty($_POST['kw'])) {
            foreach ($_POST['kw'] as $key => $value) {
                $kw .= '['.$value.'],';
            }
        }
        $sql = 'UPDATE '.$xoopsDB->prefix('newdb_master');
        $sql .= " SET keyword='".$kw."' WHERE label_id='".$label_id."'";
        $rs = $xoopsDB->query($sql);

        // Register comment
        if (!$xoopsModuleConfig['acom_flg']) {
            $com = '';
        } else {
            $com = $_POST['comment'];
        }
        $cp = new CommentPost();
        $cp->setMethod('new');
        $cp->setSubject(_ND_REG5);
        $cp->setMessage($com);
        $cp->setUid($uid);
        $cp->setLid($label_id);
        $cp->setType('auth');
        $cp->register();

        // Register thumbnail
        $mes = '';
        if (!empty($_POST['dir'])) {
            $dir = EXTRACT_PATH.'/'.$label_id.'/thumbnail/'.$myts->stripSlashesGPC($_POST['dir']);
            if (!is_dir($dir) && !mkdir($dir, 0777)) {
                $mes = 'Not registered thumbnail image';
            }
        } else {
            $mes = 'Not registered thumbnail image';
        }
        if (!empty($mes)) {
            redirect_header(MOD_URL.'/register.php', 2, $mes);
        }

        if (is_uploaded_file($_FILES['thumbfile']['tmp_name'])) {
            $fname = $_FILES['thumbfile']['name'];
            $tmp_name = $_FILES['thumbfile']['tmp_name'];

            if (preg_match("/.*(\.gif)$/i", $fname) || preg_match("/.*(\.bmp)$/i", $fname) || preg_match("/.*(\.jpg)$/i", $fname)
                || preg_match("/.*(\.jpeg)$/i", $fname)
                || preg_match("/.*(\.png)$/i", $fname)
            ) {
                if (move_uploaded_file($tmp_name, $dir.'/'.$fname)) {
                    $mes = _ND_CONFIG_IMGUPOK;
                } else {
                    $mes = _ND_CONFIG_IMGUPNG;
                }
            } else {
                $mes = _ND_CONFIG_UPSUF;
            }
        } else {
            $mes = _ND_CONFIG_NFILESELECT;
        }

        // caption
        $mes = '';
        if (!empty($_POST['caption'])) {
            $caption_path = EXTRACT_PATH.'/'.$label_id.'/caption/'.$myts->stripSlashesGPC($_POST['dir']);
            if (!is_dir($caption_path) && !mkdir($caption_path, 0777)) {
                $mes = _ND_DIR_FALSE;
            }

            // image file -> caption file: ex. file.jpg -> file.txt

            $dot_pos = strrpos($fname, '.');
            $caption_file = substr($fname, 0, $dot_pos).'.txt';
            $caption_path = $caption_path."/$caption_file";

            $fp = fopen($caption_path, 'w');
            fwrite($fp, $_POST['caption']);
            fclose($fp);
        }

        // Increment post counter 2006/05/12
        $user = new XoopsUser($uid);
        $user->incrementPost();

        redirect_header(MOD_URL.'/detail.php?id='.$label_id, 2, _ND_REG7);
        break;

    /*
     * register top
     */
    default:
        $const_mk = "<span style='color:red;'>* </span>";
        include XOOPS_ROOT_PATH.'/header.php';
        include __DIR__.'/style.css';
        echo "<script language='JavaScript' src='tab.js'></script>\n";

        if ($required) {
            echo "<div style='color:red; margin:20px;'>".$required.'</div>';
        }

        echo "<center>\n";
        echo "<form enctype='multipart/form-data' action='register.php' method='POST'>\n";

        // datasheet
        echo "<div class='title' style='margin-top:0;'>"._ND_REG8."</div>\n";
        echo "<div class='title_desc'>"._ND_REG9."</div>\n";

        echo "<table class='list_table' style='width:550px;'>\n";
        echo "<tr style='text-align:center;'><th style='width:140px;'>"._ND_REG28.'</th><th>'._ND_REG29."</th></tr>\n";

        $sql = 'SELECT * FROM '.$xoopsDB->prefix('newdb_component_master')." WHERE name='Data Name'";
        $rs = $xoopsDB->query($sql);
        $row = $xoopsDB->fetchArray($rs);

        if ($xoopsModuleConfig['dname_flg']) {
            if (!isset($dname)) {
                $dname = '';
            }
            echo "<tr><td style='width:120px;' class='even'>".$const_mk.'<b>'.$row['tag'].'</b><br>';
            echo '</td>';
            echo "<td><input type='text' name='name' style='width:180px;' value='".$dname."'>";

            if ($row['textmax']) {
                echo '&nbsp;&nbsp;&nbsp;'.$row['textmax'].' '._ND_REG30;
            }
            echo "</td></tr>\n";
        }
        if ($xoopsModuleConfig['acom_flg']) {
            echo "<tr><td class='even'><b>"._ND_REG12.'</b></td>';
            echo "<td><textarea name='comment' style='width:98%; height:120px;'></textarea></td></tr>\n";
        }

        // type 5:select, 4:text, 3:checkbox, 2:radio

        $sql = 'SELECT * FROM '.$xoopsDB->prefix('newdb_component_master');
        $sql .= " WHERE type <> '1' ORDER BY sort";
        $rs = $xoopsDB->query($sql);

        // ID number for custom field (custom radio, custom check...)
        $custom_id = array(
            'CR' => 0,
            'CC' => 0,
            'CT' => 0,
            'CS' => 0,
        );

        while ($row = $xoopsDB->fetchArray($rs)) {
            $comp_id = $row['comp_id'];
            $textmax = $row['textmax'];
            $nonull = $row['nonull'];

            echo "<tr><td style='width:120px;' class='even'>";

            if ($nonull && ($row['type'] >= '2' && $row['type'] <= '4')) {
                echo $const_mk;
            }

            echo '<b>'.htmlspecialchars($row['tag']).'</b><br>';
            echo htmlspecialchars($row['exp'])."</td><td>\n";

            // CR(radio)
            if ($row['type'] == '2') {
                $svalue = explode(',', $row['select_value']);
                for ($j = 0, $iMax = count($svalue); $j < $iMax; ++$j) {
                    $check = '';
                    if ($required) {
                        if ($svalue[$j] == $cr[$custom_id['CR']]) {
                            $check = ' checked';
                        }
                    } else {
                        if ($svalue[$j] == $row['default_value']) {
                            $check = ' checked';
                        }
                    }
                    echo "<input type='radio' name='CR".$custom_id['CR']."' value='".$svalue[$j]."' ".$check.'>';
                    $tmp = str_replace('{', '<img src="images/admin/', $svalue[$j]);
                    $tmp = str_replace('}', '">', $tmp);
                    echo $tmp."&nbsp;&nbsp;\n";
                }
                echo "<input type='hidden' name='CR".$custom_id['CR']."_id' value='".$comp_id."'>";
                ++$custom_id['CR'];

                // CC(check)
            } elseif ($row['type'] == '3') {
                $svalue = explode(',', $row['select_value']);
                for ($j = 0, $iMax = count($svalue); $j < $iMax; ++$j) {
                    $checked = '';
                    if (!empty($cc[$custom_id['CC']])) {
                        if (in_array($svalue[$j], $cc[$custom_id['CC']])) {
                            $checked = ' checked';
                        }
                    }
                    echo "<input type='checkbox' name='CC".$custom_id['CC']."[]' value='".$svalue[$j]."' ".$checked.'>';
                    $tmp = str_replace('{', '<img src="images/admin/', $svalue[$j]);
                    $tmp = str_replace('}', '">', $tmp);
                    echo $tmp."&nbsp;&nbsp;\n";
                }
                echo "<input type='hidden' name='CC".$custom_id['CC']."_id' value='".$comp_id."'>";
                ++$custom_id['CC'];

                // CT(text)
            } elseif ($row['type'] == '4') {
                if (!isset($ct[$custom_id['CT']])) {
                    $ct[$custom_id['CT']] = '';
                }
                if ($textmax) {
                    echo "<input type='text' name='CT".$custom_id['CT']."' style='width:180px' value='".$ct[$custom_id['CT']]."'>\n";
                    echo '&nbsp;&nbsp;&nbsp;'.$textmax.' '._ND_REG30;
                } else {
                    echo "<textarea name='CT".$custom_id['CT']."' style='width:98%; height:60px;'>".$ct[$custom_id['CT']]."</textarea>\n";
                }
                echo "<input type='hidden' name='CT".$custom_id['CT']."_id' value='".$comp_id."'>";
                ++$custom_id['CT'];

                // CS(select)
            } elseif ($row['type'] == '5') {
                $svalue = explode(',', $row['select_value']);
                echo "<select name='CS".$custom_id['CS']."'>";
                for ($j = 0, $iMax = count($svalue); $j < $iMax; ++$j) {
                    $check = '';
                    if ($required) {
                        if ($svalue[$j] == $cs[$custom_id['CS']]) {
                            $check = ' selected';
                        }
                    } else {
                        if ($svalue[$j] == $row['default_value']) {
                            $check = ' selected';
                        }
                    }
                    echo "<option value='".$svalue[$j]."' ".$check.'>'.$svalue[$j]."</option>\n";
                }
                echo '</select>';
                echo "<input type='hidden' name='CS".$custom_id['CS']."_id' value='".$comp_id."'>";
                ++$custom_id['CS'];
            }

            echo "</td></tr>\n";
        }

        // options
        echo "<tr><td class='even'><b>"._ND_REG13.'</b></td>';
        echo "<td><a href=\"javascript:seltab('box', 'head', 10, 1)\">"._ND_REG14.'</a>';
        if ($xoopsModuleConfig['use_datafunc']) {
            echo " | <a href=\"javascript:seltab('box', 'head', 10, 2)\">"._ND_REG15.'</a>';
        }
        echo " | <a href=\"javascript:seltab('box', 'head', 10, 3)\">"._ND_REG15_.'</a>';
        echo '</td></tr></table>';

        // keyword
        $kw = new Keyword();
        echo "<div id='box1' style='display:none;'>\n";
        echo "<div class='title'>"._ND_REG16."</div>\n";
        echo "<div class='title_desc'>"._ND_REG17."</div>\n";

        echo "<table style='width:520px;'>";
        echo '<tr><td>'.$kw->getCateTB().'</td><td></td><td>'.$kw->getKeyTB().'</td></tr>';
        echo '</table>';
        echo '</div>';

        if ($xoopsModuleConfig['use_datafunc']) {
            // data file
            $uplimit = floor(MAX_UPLOAD_SIZE / 1000 / 1000).' MB';
            $list = array();
            getRegList(UPLOAD_PATH.'/'.$xoopsUser->uname(), $list);
            uasort($list, 'strcmp');

            echo "<div id='box2' style='display:none;'>";
            echo "<div class='title'>"._ND_REG18."</div>\n";
            echo "<div class='title_desc'>"._ND_REG19.' (limit '.$uplimit.")</div>\n";

            echo "<table class='list_table' style='width:300px;'>";
            echo "<tr><th colspan='2'>"._ND_REG20.'</th></tr>';
            echo "<tr><td class='even' style='width:30px;'>";
            echo "<input type='checkbox' name='use_upload' value='y'>";
            echo "<td style='text-align:center;'>";
            echo "<input type='file' name='userfile'>";
            echo '</td></tr></table><br><br>';

            if (count($list)) {
                echo "<table class='list_table' style='width:300px;'>";
                echo "<tr><th colspan='3'>"._ND_REG21.'</th></tr>';
                echo '</table>';

                foreach ($list as $name => $size) {
                    echo "<table class='list_table' style='width:300px;'><tr>";
                    echo "<td class='even' style='width:30px;'>";
                    echo "<input type='radio' name='uploaded_data' value='".$name."'></td>";
                    echo "<td>$name</td>";
                    echo "<td class='even' style='width:100px;'>$size kb</td>";
                    echo '</tr></table>';
                }
            }
            echo '</div>';
        }

        // thumbnail image
        echo "<div id='box3' style='display:none;'>\n";
        echo "<div class='title'>"._ND_REG24."</div>\n";
        echo "<div class='title_desc'>"._ND_REG25."</div>\n";
        echo "<table class='list_table' style='width:500px;'>\n";
        echo "<tr><td style='width:120px;' class='even'><b>"._ND_REG_THUMB1
             ."</b></td><td><input type='text' name='dir' size='8' value='img'></td></tr>";
        echo "<tr><td class='even'><b>"._ND_REG_THUMB2."</b></td><td><input type='file' name='thumbfile'></td></tr>";
        echo "<tr><td class='even'><b>"._ND_REG_THUMB3
             ."</b></td><td><textarea name='caption' cols='40' style='width:98%;height:120px;'></textarea></td></tr>";
        echo '</table>';
        echo '</div>';

        // submit
        echo "<div class='title'>"._ND_REG22."</div>\n";
        echo "<div class='title_desc'>"._ND_REG23."</div>\n";
        echo "<input type='submit' class='button' value='submit'>&nbsp;&nbsp;";
        echo "<input type='reset' class='button' value='reset'>";
        echo "<input type='hidden' name='method' value='do_reg'>";
        echo '</form></center>';

        include XOOPS_ROOT_PATH.'/footer.php';
        break;
}
