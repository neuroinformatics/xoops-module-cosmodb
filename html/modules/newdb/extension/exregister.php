<?php

    // this script is executed when new data is registered.
    // 1: The uploaded data is moved into the extract directory. (register.php)
    // 2: This script is executed. (exregister.php)
    // 3: Register data information in the database. (register.php)
    // 4: Finish.

    // you can use following variables.
    // MOD_PATH -> XOOPS_ROOT_PATH.'/modules/newdb'
    // MOD_URL -> XOOPS_URL.'/modules/newdb'
    // EXTRACT_PATH' -> MOD_PATH.'/extract'
    // UPLOAD_PATH -> MOD_PATH.'/upload'
    //	$uid -> xoops user ID;
    // $label_id -> data ID
    // $xoopsDB->...

    // image file convert program for kanzaki lab.
    //include 'convert/convert.php';
    //Convert(EXTRACT_PATH, MOD_PATH, $label_id);
;
