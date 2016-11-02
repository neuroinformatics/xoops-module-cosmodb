<?php
	
	$modversion['name'] = _ND_MOD_NAME;
	$modversion['version'] = "1.0.1";
	$modversion['description'] = ' ';
	$modversion['credits'] = 'Takuto Nishioka';
	$modversion['author'] = 'Takuto Nishioka';
	$modversion['official'] = 0;
	$modversion['image'] = 'images/logo.gif';
	$modversion['dirname'] = 'newdb';

	# install
	$modversion['onInstall'] = 'sql/install.php';
	
	# Database things
	$modversion['sqlfile']['mysql'] = 'sql/mysql.sql';
	$modversion['tables']['0'] = 'newdb_master';
	$modversion['tables']['1'] = 'newdb_component';
	$modversion['tables']['2'] = 'newdb_component_master';
	$modversion['tables']['3'] = 'newdb_item';
	$modversion['tables']['4'] = 'newdb_comment_topic';
	$modversion['tables']['5'] = 'newdb_comment';
	$modversion['tables']['6'] = 'newdb_keyword';
	$modversion['tables']['7'] = 'newdb_list';
	$modversion['tables']['8'] = 'newdb_list_refine';
	$modversion['tables']['9'] = 'newdb_detail';
	$modversion['tables']['10'] = 'newdb_bookmark_dir';
	$modversion['tables']['11'] = 'newdb_bookmark_file';
	$modversion['tables']['12'] = 'newdb_link';
	$modversion['tables']['13'] = 'newdb_fulltext_search';
	$modversion['tables']['14'] = 'newdb_list_refine_option';
	$modversion['tables']['15'] = 'newdb_file_search';

	# Admin things
	$modversion['hasAdmin'] = 1;
	$modversion['adminindex'] = 'admin/index.php';
	$modversion['adminmenu'] = 'admin/menu.php';

	# Search
	$modversion['hasSearch'] = 0;
	
	# Main Menu
	$modversion['hasMain'] = 1;
	
	global $xoopsDB;
	$sql = "SELECT list_id, name FROM ".$xoopsDB->prefix('newdb_list');
	$sql.= " WHERE onoff='0' ORDER BY sort";
	$rs = $xoopsDB->query($sql);
	$i=1;
	while($row = $xoopsDB->fetchArray($rs)){
		$list_id = $row['list_id'];
		$name = $row['name'];
	
		$modversion['sub'][$i]['name'] = $name;
		$modversion['sub'][$i]['url'] = 'list.php?id='.$list_id;
		$i++;
	}
	$modversion['sub'][($i+1)]['name'] = _ND_KW_SEARCH;
	$modversion['sub'][($i+1)]['url'] = 'kws.php';
	$modversion['sub'][($i+2)]['name'] = _ND_BOOKMARK;
	$modversion['sub'][($i+2)]['url'] = 'bookmark.php';
	$modversion['sub'][($i+3)]['name'] = _ND_REGISTER;
	$modversion['sub'][($i+3)]['url'] = 'register.php';
	
	# Blocks
	$modversion['blocks'][1]['file'] = 'search.php';
	$modversion['blocks'][1]['name'] = _ND_BLOCK_SEARCH;
	$modversion['blocks'][1]['show_func'] = "b_newdb_search";

	$modversion['blocks'][2]['file'] = 'news1.php';
	$modversion['blocks'][2]['name'] = _ND_BLOCK_NEWS1;
	$modversion['blocks'][2]['show_func'] = 'b_newdb_news1';
	$modversion['blocks'][2]['template'] = 'newdb_news1.html';

	$modversion['blocks'][3]['file'] = 'news2.php';
	$modversion['blocks'][3]['name'] = _ND_BLOCK_NEWS2;
	$modversion['blocks'][3]['show_func'] = 'b_newdb_news2';
	$modversion['blocks'][3]['template'] = 'newdb_news2.html';

	# Config
	$modversion['config'][1]['name'] = 'reg_perm';
	$modversion['config'][1]['title'] = '_ND_REG_PERM';
	$modversion['config'][1]['description'] = '_ND_REG_PERM_DESC';
	$modversion['config'][1]['formtype'] = 'group_multi';
	$modversion['config'][1]['valuetype'] = 'array';

	$modversion['config'][2]['name'] = 'access_perm';
	$modversion['config'][2]['title'] = '_ND_ACCESS_PERM';
	$modversion['config'][2]['description'] = '_ND_ACCESS_PERM_DESC';
	$modversion['config'][2]['formtype'] = 'group_multi';
	$modversion['config'][2]['valuetype'] = 'array';

	$modversion['config'][3]['name'] = 'use_datafunc';
	$modversion['config'][3]['title'] = '_ND_UPLOAD';
	$modversion['config'][3]['description'] = '_ND_UPLOAD_DESC';
	$modversion['config'][3]['formtype'] = 'yesno';
	$modversion['config'][3]['valuetype'] = 'int';
 	$modversion['config'][3]['default'] = 1;

	$modversion['config'][4]['name'] = 'upload_limit';
	$modversion['config'][4]['title'] = '_ND_UPLIMIT';
	$modversion['config'][4]['description'] = '_ND_UPLIMIT_DESC';
	$modversion['config'][4]['formtype'] = 'text';
	$modversion['config'][4]['valuetype'] = 'int';
	$modversion['config'][4]['default'] = 100000000;

	$modversion['config'][5]['name'] = 'use_suffix';
	$modversion['config'][5]['title'] = '_ND_SUFFIX';
	$modversion['config'][5]['description'] = '_ND_SUFFIX_DESC';
	$modversion['config'][5]['formtype'] = 'yesno';
	$modversion['config'][5]['valuetype'] = 'int';
	$modversion['config'][5]['default'] = 1;

	$modversion['config'][6]['name'] = 'suffix';
	$modversion['config'][6]['title'] = '_ND_SUFFIX1';
	$modversion['config'][6]['description'] = '_ND_SUFFIX1_DESC';
	$modversion['config'][6]['formtype'] = 'text';
	$modversion['config'][6]['valuetype'] = 'text';
	$modversion['config'][6]['default'] = 'txt|zip|bmp|gif|png|jpg|jpeg';

	$modversion['config'][7]['name'] = 'dname_flg';
	$modversion['config'][7]['title'] = '_ND_DNAME_FLG';
	$modversion['config'][7]['description'] = '_ND_DNAME_FLG_DESC';
	$modversion['config'][7]['formtype'] = 'yesno';
	$modversion['config'][7]['valuetype'] = 'int';
	$modversion['config'][7]['default'] = 1;

	$modversion['config'][8]['name'] = 'acom_flg';
	$modversion['config'][8]['title'] = '_ND_COMMENT_FLG';
	$modversion['config'][8]['description'] = '_ND_COMMENT_FLG_DESC';
	$modversion['config'][8]['formtype'] = 'yesno';
	$modversion['config'][8]['valuetype'] = 'int';
	$modversion['config'][8]['default'] = 1;

	$modversion['config'][9]['name'] = 'acom_post';
	$modversion['config'][9]['title'] = '_ND_ACOM_POST';
	$modversion['config'][9]['description'] = '_ND_ACOM_POST_DESC';
	$modversion['config'][9]['formtype'] = 'yesno';
	$modversion['config'][9]['valuetype'] = 'int';
	$modversion['config'][9]['default'] = 1;

	$modversion['config'][10]['name'] = 'guest_post';
	$modversion['config'][10]['title'] = '_ND_GUEST_POST';
	$modversion['config'][10]['description'] = '_ND_GUEST_POST_DESC';
	$modversion['config'][10]['formtype'] = 'yesno';
	$modversion['config'][10]['valuetype'] = 'int';
	$modversion['config'][10]['default'] = 1;
	
	$modversion['config'][11]['name'] = 'mail_flg';
	$modversion['config'][11]['title'] = '_ND_MAIL_FLG';
	$modversion['config'][11]['description'] = '_ND_MAIL_FLG_DESC';
	$modversion['config'][11]['formtype'] = 'yesno';
	$modversion['config'][11]['valuetype'] = 'int';
	$modversion['config'][11]['default'] = 1;
	
?>