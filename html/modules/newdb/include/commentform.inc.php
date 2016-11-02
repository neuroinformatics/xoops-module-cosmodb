<?php
        if (!defined('XOOPS_ROOT_PATH')) {
            exit();
        }
        include_once XOOPS_ROOT_PATH.'/include/xoopscodes.php';
        if ($method == 'new' && $xoopsModuleConfig['mail_flg']) {
            echo "<script language='JavaScript' src='tab.js'></script>\n";
        }

        echo "<form id='com' action='comment.php' method='POST'>";
        echo "<table class='list_table'>";
        echo "<tr><th colspan='2'>"._ND_COMVIEW_FORM.'</th></tr>';

        if ($type != 'auth') {
            echo "<tr><td class='even'><b>"._ND_COMVIEW_TITLE.'</b></td>';
            echo "<td><input id='subject' name='subject' size='60' maxlength='100' value='$subject'></td></tr>";
        }

        echo "<tr><td class='even'><b>"._ND_COMVIEW_MES.'</b></td><td>';
        xoopsCodeTarea('message');
        xoopsSmilies('message');
        echo '</td></tr>';

        if ($method == 'new' && $xoopsModuleConfig['mail_flg']) {
            $n = 0;
            echo "<tr><td class='even'><b>"._ND_COMVIEW_MAIL.'</b><br>';
            echo "<a onClick=\"javascript:check_all('mail', 1)\">"._ND_COMVIEW_ALL.'</a> / ';
            echo "<a onClick=\"javascript:check_all('mail', 0)\">"._ND_COMVIEW_NON.'</a>';
            echo '</td><td><table><tr>';
            $rs = $xoopsDB->query('SELECT * FROM '.$xoopsDB->prefix('users'));
            while ($row = $xoopsDB->fetchArray($rs)) {
                if (!($n % 4)) {
                    echo '</tr><tr>';
                }
                echo "<td style='width:25%; border:0'>";
                echo "<input type='checkbox' name='mail[]' id='mail".($n + 1)."' value='".$row['email']."'>";
                echo $row['uname'].'</td>';
                ++$n;
            }
            echo '</tr></table></tr>';
        }

        echo "<tr><td class='even'> </td>";
        echo "<td><input type='submit' class='button' id='contents_preview' name='preview' value='preview'>";
        echo "<input type='submit' class='button' id='contents_submit' name='submit' value='submit'></td>";
        echo "</tr></table><input type='hidden' id='method' name='method' value='$method'>";

        if ($lid > 0) {
            echo "<input type='hidden' id='id' name='lid' value='".$lid."'>";
        } elseif ($cid > 0) {
            echo "<input type='hidden' id='id' name='cid' value='".$cid."'>";
        }
        if ($type == 'auth') {
            echo "<input type='hidden' id='type' name='type' value='auth'>";
        } else {
            echo "<input type='hidden' id='type' name='type' value='user'>";
        }
        echo '</form>';
