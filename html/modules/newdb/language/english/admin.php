<?php
// Admin menu
define('_ND_ADMIN_ITEM', 'Admin menu');
define('_ND_ADMIN_ITEM_DESC', 'Description');

define('_ND_LIST_ADMIN', 'Setting List page');
define('_ND_DETAIL_ADMIN', 'Setting Detail page');
define('_ND_KEYWORD_ADMIN', 'Setting Keywords');
define('_ND_COMPONENT_ADMIN', 'Setting Items');
define('_ND_INHERITE_ADMIN', 'Data Inheritance');
define('_ND_IMPORT_ADMIN', 'Import CSV');
define('_ND_CONFIG', 'Preferences');

define('_ND_LIST_ADMIN_DESC', 'setting display a list page ( list view, thumbnail view )');
define('_ND_DETAIL_ADMIN_DESC', 'setting data detail page');
define('_ND_KEYWORD_ADMIN_DESC', 'setting keywords which are used for refining data');
define('_ND_COMPONENT_ADMIN_DESC', 'setting items which are used for sorting, refining data');
define('_ND_INHERITE_ADMIN_DESC', "inherite one's data to other user");
define('_ND_IMPORT_ADMIN_DESC', 'Import CSV');
define('_ND_CONFIG_DESC', 'module preferences');

define('_ND_SORT_KEYWORD', 'Sort keyword');
define('_ND_SORT_ORDER', 'order');
define('_ND_DB_ERROR', "database connection didn't be established");

// Keyword Admin
define('_ND_CATEGORY', 'Categories');
define('_ND_CATEGORY_DESC', 'Click category to display keywords');
define('_ND_ADD_NEYCATEGORY', 'Create new category');
define('_ND_CATEGORY_NAME', 'Category name');
define('_ND_CATEGORY_ADDED', 'New category was made.');
define('_ND_CATEGORY_NONADDED', "Error: New category didn't be made.");

define('_ND_KEYWORD', 'Keywords');
define('_ND_KEYWORD_DESC', 'You can create sub keyword by selecting "Add keyword"');
define('_ND_ADD_NEYKEYWORD', 'Add keyword');
define('_ND_KEYWORD_NAME', 'Keyword name');
define('_ND_KEYWORD_ADDED', 'New keyword was made.');
define('_ND_KEYWORD_NONADDED', "Error: New keyword didn't be made.");
define('_ND_CHANGE_KEYWORD', 'Change keyword');
define('_ND_KEYWORD_CHANGED', 'Keyword was changed.');
define('_ND_DELETE_KEYWORD', 'Delete keyword');

// component.php
define('_ND_ADD_NEWCOMPONENT', 'Add Item');
define('_ND_COMPONENT_DESC', 'Check if you want to change or delete item.');
define('_ND_COMPONENT_NAME', 'Name');
define('_ND_COMPONENT_ONOFF', 'Use/unUse');
define('_ND_COMPONENT_VALUE', 'Selecting Items');
define('_ND_COMPONENT_INPUTNG', 'You must input required form.');
define('_ND_COMPONENT_OK', 'Item was deleted.');
define('_ND_COMPONENT_NG', "Error: Item didn't be deleted.");
define('_ND_COMPONENT_EDITOK', 'Item was edited.');
define('_ND_COMPONENT_EDITNG', "Error: Item didn't be edited.");
define('_ND_COMPONENT_ADDOK', 'Item was added.');
define('_ND_COMPONENT_ADDNG', "Error: Item didn't be added.");
define('_ND_COMPONENT_SEDIT', 'Editing system items');
define('_ND_COMPONENT_SORT', 'Use as sorting item?');
define('_ND_COMPONENT_YES', 'yes');
define('_ND_COMPONENT_NO', 'no');
define('_ND_COMPONENT_ORDER', 'Order');
define('_ND_COMPONENT_EDIT', 'Edit item');
define('_ND_COMPONENT_TEMPNAME', 'Template name');
define('_ND_COMPONENT_ITEM_DESC', "Item's description");
define('_ND_COMPONENT_TYPE', 'Type');
define('_ND_COMPONENT_DEL', 'Delete');
define('_ND_COMPONENT_DEL_DESC', 'Check if you want to delete this.');
define('_ND_COMPONENT_SELECT_ITEM', 'Selecting Items');
define('_ND_COMPONENT_SELECT_ITEM_DESC', "When you don't select type \"text\", input selecting items.  Separate them with (,).<br><br>You can use Images that were uploaded in the images/admin directory by using {}. ");
define('_ND_COMPONENT_DEFAULT', 'Default');
define('_ND_COMPONENT_DEFAULT_DESC', 'When you select type "radio", select default item ');
define('_ND_COMPONENT_SRT', 'Sort');
define('_ND_COMPONENT_REGITEM', 'Registered Items');

define('_ND_COMPONENT_REQUIRE', 'Required');
define('_ND_COMPONENT_REQUIRE_DESC', 'Check if you want to make this required item');
define('_ND_COMPONENT_REFI', 'Refine');
define('_ND_COMPONENT_REFINE', 'Use as refining item?');
define('_ND_COMPONENT_REFINE_DESC', "When you don't select type \"text\"");
define('_ND_COMPONENT_STR', 'Str limit');
define('_ND_COMPONENT_STRMAX', 'Str limit');
define('_ND_COMPONENT_STRMAX_DESC', '0 = unlimited');
define('_ND_COMPONENT_UNLIMITED', 'unlimited');
define('_ND_COMPONENT_TEXTONLY', 'Whe you select type "text"');
define('_ND_COMPONENT_SORT1', 'for Register page, Sort box');

// inherite.php
define('_ND_INH_OK', 'Data were inherited.');
define('_ND_INH_USER', 'Select "from" and "to" user');
define('_ND_INH_FROM', ' -> ');
define('_ND_INH_TO', '');

// detail.php
define('_ND_DETAIL_OK', 'Template was edited.');
define('_ND_DETAIL_NG', "Error: Template didn't be edited.");
define('_ND_DETAIL_TEMP', 'Template');
define('_ND_DETAIL_TEMPSHOW', 'TEMPLATES');
define('_ND_DETAIL_TEMPSHOW_DESC', 'You can use these for setting');
define('_ND_DETAIL_HOWTO', 'How to use');
define('_ND_DETAIL_HOWTO_DESC', 'Description');
define('_ND_DETAIL_ACOM', 'Author comment component');
define('_ND_DETAIL_UCOM', 'User comment component');
define('_ND_DETAIL_KEY', 'Keyword list');
define('_ND_DETAIL_FILE', 'registered data file list');
define('_ND_DETAIL_BOOK', 'The link to the register bookmark page');
define('_ND_DETAIL_LINK', 'The link to the register link page');
define('_ND_DETAIL_CONFIG', 'The link to the data configuration page');
define('_ND_DETAIL_MANAGER', 'The link to the file management page');
define('_ND_DETAIL_THUMB', 'Show thumbnails<br>{Image target_dir width|height|row}');
define('_ND_DETAIL_TAB', 'Separate pages<br>ex. {tab1}contents for page1{/tab}');
define('_ND_DETAIL_TAB_DESC', 'link for {tab} templates<br>ex. {href_tab1}show page1{/href_tab}');
define('_ND_DETAIL_REF', 'file path copy function (for study use) {Ref suffix}');

// list.php
define('_ND_LIST_LIST', 'List view');
define('_ND_LIST_THUMB', 'Thumbnail view');
define('_ND_LIST_CONFIG', ' Setting');
define('_ND_LIST_SHOWNAME', 'Name');
define('_ND_LIST_SNAME_DESC', "This view's name.");
define('_ND_LIST_H', 'Caption');
define('_ND_LIST_H_DESC', 'This is displaied at the top of the list view.');
define('_ND_LIST_CHANGE', 'Use/unUse');
define('_ND_LIST_DEL', 'Check if you want to delete this view.');
define('_ND_LIST_DIR', 'Target Directory');
define('_ND_LIST_DIR_DESC', 'Images which exist in this directory are displaied.');
define('_ND_LIST_SIZE', 'Image size');
define('_ND_LIST_SIZE_DESC', 'When you input some size, separate them with (;).');
define('_ND_LIST_SIZE_DESC2', '[Size name, widht, height, how many images in line]');
define('_ND_LIST_USE_ITEM', 'Views which are used now');
define('_ND_LIST_USE_ITEM_DESC', 'Views which are used now');
define('_ND_LIST_NUSE_ITEM', "Views which aren't used");
define('_ND_LIST_NUSE_ITEM_DESC', 'Click item name if you want to edit or delete.');
