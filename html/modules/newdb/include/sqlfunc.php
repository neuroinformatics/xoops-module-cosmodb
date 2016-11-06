<?php

require_once XOOPS_ROOT_PATH.'/class/database/sqlutility.php';

function cosmodb_sql_prefix_query($query, $prefix)
{
    $pattern = "/^(INSERT INTO|CREATE TABLE|ALTER TABLE|UPDATE)(\s)+([`]?)([^`\s]+)\\3(\s)+/siU";
    $pattern2 = "/^(DROP TABLE)(\s)+([`]?)([^`\s]+)\\3(\s)?$/siU";
    if (preg_match($pattern, $query, $matches)) {
        $replace = '\\1 '.$prefix.'_\\4\\5';
        $matches[0] = preg_replace($pattern, $replace, $query);

        return $matches;
    } elseif (preg_match($pattern2, $query, $matches)) {
        $replace = '\\1 '.$prefix.'_\\4\\5';
        $matches[0] = preg_replace($pattern2, $replace, $query);

        return $matches;
    }

    return false;
}

function cosmodb_sql_queries($sqls, $dirname, &$ret)
{
    global $xoopsDB;
    $pieces = array();
    $sqls = str_replace('{dirname}', $dirname, $sqls);
    SqlUtility::splitMySqlFile($pieces, $sqls);
    $modified_actions = array(
        'CREATE ' => 'created.',
        'DROP ' => 'dropped.',
        'ALTER ' => 'altered.',
    );
    $modified_tables = array();
    $errs = array();
    $msgs = array();
    $error = false;
    foreach ($pieces as $piece) {
        // [0] contains the prefixed query
        // [4] contains unprefixed table name
        $prefixed_query = cosmodb_sql_prefix_query($piece, $xoopsDB->prefix());
        if (!$prefixed_query) {
            $errs[] = $piece.' is not a valid SQL!';
            $error = true;
            break;
        }
        if (!$xoopsDB->query($prefixed_query[0])) {
            $errs[] = $xoopsDB->error().' of SQL '.$prefixed_query[0];
            $error = true;
            break;
        }
        foreach ($modified_actions as $act => $msg) {
            if (!isset($modified_tables[$act])) {
                $modified_tables[$act] = array();
            }
            if (strncmp($act, strtoupper($prefixed_query[0]), strlen($act)) == 0 && !in_array($prefixed_query[4], $modified_tables[$act])) {
                $msgs[] = 'Table '.$xoopsDB->prefix($prefixed_query[4]).' '.$msg;
                $modified_tables[$act][] = $prefixed_query[4];
            }
        }
    }
    if ($error) {
        // if there was an error, delete the tables created so far,
        // so the next installation will not fail
        foreach ($created_tables as $ct) {
            $xoopsDB->query('DROP TABLE '.$xoopsDB->prefix($ct));
        }
        // set error messages
        $ret = array_merge($ret, $errs);
    } else {
        $ret = array_merge($ret, $msgs);
    }
    if (!defined('XOOPS_CUBE_LEGACY')) {
        echo implode('<br />', $ret);
    }

    return !$error;
}

function cosmodb_sql_has_table($table)
{
    global $xoopsDB;
    $sql = sprintf('SHOW TABLES LIKE %s', $xoopsDB->quoteString($xoopsDB->prefix($table)));
    $result = $xoopsDB->query($sql);
    if (!$result) {
        return false;
    }
    $num = $xoopsDB->getRowsNum($result);
    $xoopsDB->freeRecordSet($result);

    return  $num != 0;
}

function cosmodb_sql_has_index($table, $name)
{
    global $xoopsDB;
    $sql = sprintf('SHOW INDEX FROM `%s` WHERE `Key_name`=%s', $xoopsDB->prefix($table), $xoopsDB->quoteString($name));
    $result = $xoopsDB->query($sql);
    if (!$result) {
        return false;
    }
    $num = $xoopsDB->getRowsNum($result);
    $xoopsDB->freeRecordSet($result);

    return  $num != 0;
}

function cosmodb_sql_fetch_column($table, $name)
{
    global $xoopsDB;
    $sql = sprintf('SHOW COLUMNS FROM `%s` WHERE `Field`=%s', $xoopsDB->prefix($table), $xoopsDB->quoteString($name));
    $result = $xoopsDB->query($sql);
    if (!$result) {
        return false;
    }
    $ret = $xoopsDB->fetchArray($result);
    $xoopsDB->freeRecordSet($result);

    return $ret;
}
