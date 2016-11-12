<?php

$url = XOOPS_URL.'/modules/newdb/admin/index.php';
if (isset($_POST['action'])) {
    $action = $_POST['action'];
} elseif (isset($_GET['action'])) {
    $action = $_GET['action'];
} else {
    $action = '';
}

switch ($action) {

    case 'change':
        Change_detail($url);
        break;

    default:
        Detail_top($url);
        break;
}

function Change_detail($url)
{
    global $xoopsDB;
    $myts = MyTextSanitizer::getInstance();

    $template = $myts->stripSlashesGPC($_POST['template']);
    $template4sql = addslashes($template);

    $sql = 'UPDATE '.$xoopsDB->prefix('newdb_detail')." SET template='".$template4sql."'";
    $rs = $xoopsDB->query($sql);
    if ($rs) {
        redirect_header($url.'?mode=detail', 2, _ND_DETAIL_OK);
    } else {
        redirect_header($url.'?mode=detail', 2, _ND_DETAIL_NG);
    }
}

function Detail_top($url)
{
    global $xoopsDB;
    $sql = 'SELECT * FROM '.$xoopsDB->prefix('newdb_detail');
    $rs = $xoopsDB->query($sql);
    $row = $xoopsDB->fetchArray($rs);

    xoops_cp_header();
    include __DIR__.'/style.css';
    echo "<center>\n";
    echo "<div class='title'>"._ND_DETAIL_ADMIN.'</div>';
    echo "<div class='title_desc'>"._ND_DETAIL_ADMIN_DESC.'</div>';

    echo "<form action='".$url."' method='POST' style='margin:0; padding:0;'>";
    echo "<table class='list_table' style='width:550px;'>";
    echo '<tr><th>'._ND_DETAIL_TEMP.'</th></tr>';
    echo '<tr><td><center>';
    echo "<textarea style='width:100%; height:400px;' name='template;'>".$row['template'].'</textarea>';
    echo '</center></td></tr>';
    echo '</table>';

    echo "<div class='title'>"._ND_DETAIL_TEMPSHOW.'</div>';
    echo "<div class='title_desc'>"._ND_DETAIL_TEMPSHOW_DESC.'</div>';
    echo "<table class='list_table' style='width:550px;'>";
    echo "<tr><th style='width:150px;'>"._ND_DETAIL_HOWTO.'</th><th>'._ND_DETAIL_HOWTO_DESC.'</th></tr>';
    echo "<tr><td class='even'>{Acomment}</td><td>"._ND_DETAIL_ACOM.'</td></tr>';
    echo "<tr><td class='even'>{Ucomment}</td><td>"._ND_DETAIL_UCOM.'</td></tr>';
    echo "<tr><td class='even'>{Keyword}</td><td>"._ND_DETAIL_KEY.'</td></tr>';
    echo "<tr><td class='even'>{Dtree}</td><td>"._ND_DETAIL_FILE.'</td></tr>';
    echo "<tr><td class='even'>{AddBookmark}</td><td>"._ND_DETAIL_BOOK.'</td></tr>';
    echo "<tr><td class='even'>{AddLink}</td><td>"._ND_DETAIL_LINK.'</td></tr>';
    echo "<tr><td class='even'>{Config}</td><td>"._ND_DETAIL_CONFIG.'</td></tr>';
    echo "<tr><td class='even'>{FileManager}</td><td>"._ND_DETAIL_MANAGER.'</td></tr>';
    echo "<tr><td class='even'>{Image \$1 \$2|\$3|\$4}</td><td>"._ND_DETAIL_THUMB.'</td></tr>';
    echo "<tr><td class='even'>{tab\$1}</td><td>"._ND_DETAIL_TAB.'</td></tr>';
    echo "<tr><td class='even'>{href_tab\$1}</td><td>"._ND_DETAIL_TAB_DESC.'</td></tr>';
    echo "<tr><td class='even'>{Ref \$1}</td><td>"._ND_DETAIL_REF.'</td></tr>';

    $sql = 'SELECT * FROM '.$xoopsDB->prefix('newdb_component_master').' ORDER BY sort';
    $rs = $xoopsDB->query($sql);
    while ($row = $xoopsDB->fetchArray($rs)) {
        echo "<tr><td class='even'>{".$row['name'].'}</td><td>'.$row['tag'].'</td></tr>';
    }
    echo '</table>';

    echo "<table style='margin: 20px 0 20px 0; border:0;'>";
    echo '<tr><td>';
    echo "<input type='submit' value='submit'>";
    echo "<input type='hidden' value='detail' name='mode'>";
    echo "<input type='hidden' value='change' name='action'>";
    echo '</td></tr></table>';
    echo '</form>';
    echo '</center>';
    xoops_cp_footer();
}
