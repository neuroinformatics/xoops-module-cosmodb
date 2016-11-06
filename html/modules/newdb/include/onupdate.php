<?php

require_once __DIR__.'/sqlfunc.php';

function cosmodb_module_onupdate($module, $oldversion)
{
    global $msgs;
    $dirname = $module->get('dirname');

    // for Cube Legacy
    if (defined('XOOPS_CUBE_LEGACY')) {
        $root = &XCube_Root::getSingleton();
        $root->mDelegateManager->add('Legacy.Admin.Event.ModuleUpdate.'.ucfirst($dirname).'.Success', 'cosmodb_module_onupdate_append_message');
        $root->mDelegateManager->add('Legacy.Admin.Event.ModuleUpdate.'.ucfirst($dirname).'.Fail', 'cosmodb_module_onupdate_append_error');
    }

    $msgs = array();
    $msgs[] = 'Updating modules...';
    if ($oldversion < 130) {
        // from v05 to v10
        if (cosmodb_sql_has_table($dirname.'_fts')) {
            $sqls = <<<'SQL'
DROP TABLE `{dirname}_fts`;
SQL;
            if (!cosmodb_sql_queries($sqls, $dirname, $msgs)) {
                return false;
            }
        }
        if (cosmodb_sql_has_table($dirname.'_fs')) {
            $sqls = <<<'SQL'
DROP TABLE `{dirname}_fs`;
SQL;
            if (!cosmodb_sql_queries($sqls, $dirname, $msgs)) {
                return false;
            }
        }
        // from v10 to v11
        $field = cosmodb_sql_fetch_column($dirname.'_master', 'label');
        if ($field !== null && $field['Type'] != 'char(255)') {
            $sqls = <<<'SQL'
ALTER TABLE `{dirname}_master`
  MODIFY `label` char(255) NOT NULL;
SQL;
            if (!cosmodb_sql_queries($sqls, $dirname, $msgs)) {
                return false;
            }
        }
        if (!cosmodb_sql_has_table($dirname.'_list_textsearch')) {
            $sqls = <<<'SQL'
CREATE TABLE `{dirname}_list_textsearch` (
  `ref_id` int(20) NOT NULL,
  `user` char(32) NOT NULL,
  `text` char(255) DEFAULT NULL,
  `labels` text,
  PRIMARY KEY (`ref_id`)
);
SQL;
            if (!cosmodb_sql_queries($sqls, $dirname, $msgs)) {
                return false;
            }
        }
        // from v11 to v12
        $field = cosmodb_sql_fetch_column($dirname.'_component_master', 'nonull');
        if ($field === false) {
            $sqls = <<<'SQL'
ALTER TABLE `{dirname}_component_master`
  ADD (
    `nonull` int(2) NOT NULL DEFAULT '0',
    `textmax` int(2) NOT NULL DEFAULT '0',
    `onoff_refine` int(2) NOT NULL DEFAULT '0'
  );
SQL;
            if (!cosmodb_sql_queries($sqls, $dirname, $msgs)) {
                return false;
            }
        }
        $field = cosmodb_sql_fetch_column($dirname.'_component_master', 'tag');
        if ($field !== null && $field['Type'] != 'char(100)') {
            $sqls = <<<'SQL'
ALTER TABLE `{dirname}_component_master`
  MODIFY `tag` char(100) NOT NULL,
  MODIFY `name` char(100) NOT NULL,
  MODIFY `exp` char(120) DEFAULT NULL;
SQL;
            if (!cosmodb_sql_queries($sqls, $dirname, $msgs)) {
                return false;
            }
        }
        // v12 to v13
        $field = cosmodb_sql_fetch_column($dirname.'_list', 'name');
        if ($field !== null && $field['Type'] != 'char(128)') {
            $sqls = <<<'SQL'
ALTER TABLE `{dirname}_list`
  MODIFY `name` char(128) NOT NULL;
SQL;
            if (!cosmodb_sql_queries($sqls, $dirname, $msgs)) {
                return false;
            }
        }
        $field = cosmodb_sql_fetch_column($dirname.'_keyword', 'keyword');
        if ($field !== null && $field['Type'] != 'char(128)') {
            $sqls = <<<'SQL'
ALTER TABLE `{dirname}_keyword`
  MODIFY `keyword` char(128) NOT NULL;
SQL;
            if (!cosmodb_sql_queries($sqls, $dirname, $msgs)) {
                return false;
            }
        }
        // fix varchar problem
        $field = cosmodb_sql_fetch_column($dirname.'_master', 'author');
        if ($field !== null && $field['Type'] != 'vachar(30)') {
            $sqls = <<<'SQL'
ALTER TABLE `{dirname}_master`
  MODIFY `label` char(255) NOT NULL,
  MODIFY `author` char(30) NOT NULL;
ALTER TABLE `{dirname}_component_master`
  MODIFY `type` int(2) NOT NULL,
  MODIFY `default_value` char(200) DEFAULT NULL;
ALTER TABLE `{dirname}_component`
  MODIFY `comp_id` int(10) NOT NULL,
  MODIFY `label_id` int(10) NOT NULL;
ALTER TABLE `{dirname}_item`
  MODIFY `label_id` int(10) NOT NULL,
  MODIFY `type` char(10) NOT NULL,
  MODIFY `name` char(100) NOT NULL,
  MODIFY `reg_user` char(30) NOT NULL;
ALTER TABLE `{dirname}_comment_topic`
  MODIFY `label_id` int(10) NOT NULL,
  MODIFY `com_id` int(10) NOT NULL,
  MODIFY `type` char(10) NOT NULL;
ALTER TABLE `{dirname}_comment`
  MODIFY `subject` char(100) NOT NULL DEFAULT 'no subject',
  MODIFY `reg_user` int(10) NOT NULL;
ALTER TABLE `{dirname}_list`
  MODIFY `thumb_dir` char(30) DEFAULT NULL;
ALTER TABLE `{dirname}_list_refine`
  MODIFY `user` char(32) NOT NULL;
ALTER TABLE `{dirname}_list_textsearch`
  MODIFY `user` char(32) NOT NULL,
  MODIFY `text` char(255) DEFAULT NULL;
ALTER TABLE `{dirname}_fulltext_search`
  MODIFY `user` char(32) NOT NULL,
  MODIFY `label_id` int(10) NOT NULL,
  MODIFY `pcom_id` int(10) NOT NULL,
  MODIFY `subject` char(100) NOT NULL,
  MODIFY `info` char(128) NOT NULL,
  MODIFY `type` char(6) NOT NULL;
ALTER TABLE `{dirname}_file_search`
  MODIFY `user` char(32) NOT NULL,
  MODIFY `label_id` int(10) NOT NULL,
  MODIFY `name` char(30) NOT NULL,
  MODIFY `info` char(128) NOT NULL;
ALTER TABLE `{dirname}_bookmark_dir`
  MODIFY `directory` char(30) NOT NULL,
  MODIFY `uid` int(2) NOT NULL;
ALTER TABLE `{dirname}_bookmark_file`
  MODIFY `label_id` int(10) NOT NULL,
  MODIFY `uid` int(2) NOT NULL;
ALTER TABLE `{dirname}_link`
  MODIFY `label_id` int(10) NOT NULL,
  MODIFY `uid` int(2) NOT NULL;
SQL;
            if (!cosmodb_sql_queries($sqls, $dirname, $msgs)) {
                return false;
            }
        }
    }

    return true;
}

function cosmodb_module_onupdate_append_message(&$module_obj, &$log)
{
    if (is_array(@$GLOBALS['msgs'])) {
        foreach ($GLOBALS['msgs'] as $message) {
            $log->add(strip_tags($message));
        }
    }
}

function cosmodb_module_onupdate_append_error(&$module_obj, &$log)
{
    if (is_array(@$GLOBALS['msgs'])) {
        foreach ($GLOBALS['msgs'] as $message) {
            $log->addError(strip_tags($message));
        }
    }
}

$mydirname = basename(dirname(__DIR__));
if (!function_exists('xoops_module_update_'.$mydirname)) {
    eval('function xoops_module_update_'.$mydirname.'($module, $oldversion){ return cosmodb_module_onupdate($module, $oldversion);}');
}
