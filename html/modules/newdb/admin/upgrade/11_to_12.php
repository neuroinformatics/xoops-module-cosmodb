<?php
    include '../../../../mainfile.php';
    $mes = '';

    $sql = 'alter table '.$xoopsDB->prefix('newdb_component_master').' add (';
    $sql .= "	nonull int(2) NOT NULL default '0',";
    $sql .= "	textmax int(2) NOT NULL default '0',";
    $sql .= "	onoff_refine int(2) NOT NULL default '0'";
    $sql .= ')';
    $rs = $xoopsDB->queryF($sql);
    if ($rs) {
        $mes .= "<br>success: alter table 'newdb_component_master'";
    } else {
        $mes .= "<br>error: alter table 'newdb_component_master' was failed.";
    }

    redirect_header(XOOPS_URL, 4, $mes);
