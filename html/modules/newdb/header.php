<?php

include __DIR__.'/../../mainfile.php';

define('MOD_URL', XOOPS_URL.'/modules/newdb');
define('MOD_PATH', XOOPS_ROOT_PATH.'/modules/newdb');
define('EXTRACT_PATH', MOD_PATH.'/extract');
define('UPLOAD_PATH', MOD_PATH.'/upload');
define('MAX_UPLOAD_SIZE', $xoopsModuleConfig['upload_limit']);

if (!is_dir(EXTRACT_PATH) && !mkdir(EXTRACT_PATH, 0777)) {
    redirect_header(XOOPS_URL, 2, 'extract '._ND_DIR_FALSE);
}
if (!is_dir(UPLOAD_PATH) && !mkdir(UPLOAD_PATH, 0777)) {
    redirect_header(XOOPS_URL, 2, 'upload '._ND_DIR_FALSE);
}

if (is_object($xoopsUser)) {
    $uid = $xoopsUser->getVar('uid');
    if ($xoopsUser->isAdmin($xoopsModule->mid())) {
        $isadmin = 1;
    } else {
        $isadmin = 0;
    }
} else {
    $uid = 0;
    $isadmin = 0;
}

$myts = MyTextSanitizer::getInstance();
