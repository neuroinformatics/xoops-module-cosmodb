<?php

require_once __DIR__.'/sqlfunc.php';

function cosmodb_module_oninstall($module)
{
    global $msgs;
    $dirname = $module->get('dirname');

    // for Cube Legacy
    if (defined('XOOPS_CUBE_LEGACY')) {
        $root = &XCube_Root::getSingleton();
        $root->mDelegateManager->add('Legacy.Admin.Event.ModuleInstall.'.ucfirst($dirname).'.Success', 'cosmodb_module_oninstall_append_message');
        $root->mDelegateManager->add('Legacy.Admin.Event.ModuleInstall.'.ucfirst($dirname).'.Fail', 'cosmodb_module_oninstall_append_error');
    }

    $msgs = array();
    $msgs[] = 'Installing module...';

    $extractPath = $dirname.'/extract';
    $uploadPath = $dirname.'/upload';
    if (!is_dir($extractPath) && !mkdir($extractPath, 0777)) {
        $msgs[] = 'mkdir [extract] false';
    }
    if (!is_dir($uploadPath) && !mkdir($uploadPath, 0777)) {
        $msgs[] = 'mkdir [upload] false';
    }

    $sqls = <<<'SQL'
INSERT INTO `{dirname}_component_master` VALUES ('1', 'ID', 'ID', '', '1', '', '', '0', '0','0','0','0');
INSERT INTO `{dirname}_component_master` VALUES ('2', '{_ND_INS_DATAN}', 'Data Name', '', '1', '', '', '0', '0','0','0','0');
INSERT INTO `{dirname}_component_master` VALUES ('3', '{_ND_INS_AUTHOR}', 'Author', '', '1', '', '', '0', '0','0','0','0');
INSERT INTO `{dirname}_component_master` VALUES ('4', '{_ND_INS_DATE}', 'Creation Date', '', '1', '', '', '0', '0','0','0','0');
INSERT INTO `{dirname}_component_master` VALUES ('5', '{_ND_INS_VIEWS}', 'Views', '', '1', '', '', '0', '0','0','0','0');
INSERT INTO `{dirname}_component_master` VALUES ('6', '{_ND_INS_RANK}', 'Rank', '', '2', '--', 'A,B,C,D,--', '0', '0','0','0','0');
INSERT INTO `{dirname}_list` VALUES('1', '{_ND_INS_LIST}', '1', '<th>{ID}</th>\n<th style="width:20%">{Data Name}</th>\n<th style="width:30%">{_ND_INS_DIR}</th>\n<th>{Author}</th>\n<th>{Creation Date}</th>\n<th>{Rank}</th>\n<th>{Views}</th>', '', '', '<th>{ID}</th>\n<td>{Data Name}</td>\n<td>{Dirs}</td>\n<td>{Author}</td>\n<td style="text-align:center">{Creation Date}</td>\n<td style="text-align:center">{Rank}</td>\n<td style="text-align:center">{Views}</td>', '0', '0');
INSERT INTO `{dirname}_list` VALUES('2', '{_ND_INS_THUMB}', '2', '', 'img', 'S,80,80,5;M,120,120,4;L,160,160,3;Default,0,0,1', '<table class="list_table">\n<tr>\n  <th>{Data Name}</th>\n</tr>\n<tr>\n  <td style="text-align:center">{Image}</td>\n</tr>\n<tr>\n  <td style="text-align:center">\n    {Author}<br>\n    {Creation Date}\n  </td>\n</tr>\n</table>', '0', '0');
INSERT INTO `{dirname}_detail` VALUES('1','<table class="label_header">\n<tr>\n <td style="vertical-align:bottom">\n {Data Name}\n </td>\n <td style="text-align:right">\n {AddBookmark} \n {AddLink} \n {Config} \n {FileManager}\n </td>\n</tr>\n</table>\n\n<div class="h_under" style="text-align:center">\n{href_tab1}Information{/href_tab} | \n{href_tab2}Comment{/href_tab}\n</div>\n\n{tab1}\n{Image img 160px|160px|3}\n<table>\n <tr>\n  <td style="width:5%"> </td>\n  <td style="width:42%">\n   <div class="h">Infomation</div><br>\n    <table>\n     <tr><td>ID</td><td>{ID}</td></tr>\n     <tr><td>{_ND_INS_AUTHOR}</td><td>{Author}</td></tr>\n     <tr><td>{_ND_INS_DATE}</td><td>{Creation Date}</td></tr>\n     <tr><td>{_ND_INS_RANK}</td><td>{Rank}</td></tr>\n     <tr><td>{_ND_INS_VIEWS}</td><td>{Views}</td></tr>\n   </table>\n  </td>\n  <td style="width:5%"> </td>\n  <td style="width:42%;" rowspan="2">\n   <div class="h">Keyword</div><br>{Keyword}\n  </td>\n  <td style="width:5%"> </td>\n </tr>\n <tr>\n  <td style="width:5%"> </td>\n  <td style="width:42%">\n   <div class="h">Data</div><br>{Dtree}\n  </td>\n  <td style="width:5%"> </td>\n  <td style="width:5%"> </td>\n </tr>\n</table>\n\n<center>\n<table style="width:90%">\n <tr>\n  <td>\n   <div class="h">News</div><br>{News}\n   <div class="h">Links</div><br>{Link}\n  </td>\n </tr>\n</table>\n</center>\n\n{/tab}\n\n{tab2}\n{Acomment}<br><br>\n{Ucomment}\n{/tab}');
SQL;
    $defines = array('DATAN', 'AUTHOR', 'DATE', 'VIEWS', 'RANK', 'LIST', 'THUMB', 'DIR');
    foreach ($defines as $def) {
        $key = '_ND_INS_'.$def;
        $sqls = str_replace('{'.$key.'}', constant($key), $sqls);
    }

    $msgs[] = 'insert initial values';
    if (!cosmodb_sql_queries($sqls, $dirname, $msgs)) {
        return false;
    }

    return true;
}

function cosmodb_module_oninstall_append_message(&$module_obj, &$log)
{
    if (is_array(@$GLOBALS['msgs'])) {
        foreach ($GLOBALS['msgs'] as $message) {
            $log->add(strip_tags($message));
        }
    }
}

function cosmodb_module_oninstall_append_error(&$module_obj, &$log)
{
    if (is_array(@$GLOBALS['msgs'])) {
        foreach ($GLOBALS['msgs'] as $message) {
            $log->addError(strip_tags($message));
        }
    }
}

$mydirname = basename(dirname(__DIR__));
if (!function_exists('xoops_module_install_'.$mydirname)) {
    eval('function xoops_module_install_'.$mydirname.'($module){ return cosmodb_module_oninstall($module);}');
}
