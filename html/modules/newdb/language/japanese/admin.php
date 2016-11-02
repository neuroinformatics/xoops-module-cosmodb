<?php
# Admin menu
define("_ND_ADMIN_ITEM","管理項目");
define("_ND_ADMIN_ITEM_DESC","説明");

define("_ND_LIST_ADMIN", "一覧表示設定");
define("_ND_DETAIL_ADMIN", "詳細画面設定");
define("_ND_KEYWORD_ADMIN", "キーワード設定");
define("_ND_COMPONENT_ADMIN", "項目設定");
define("_ND_INHERITE_ADMIN", "データ継承");
define("_ND_IMPORT_ADMIN", "CSVインポート");
define("_ND_CONFIG", "一般設定");

define("_ND_LIST_ADMIN_DESC", "一覧表示画面の設定（リスト形式、サムネイル形式）");
define("_ND_DETAIL_ADMIN_DESC", "各登録データ詳細画面の設定");
define("_ND_KEYWORD_ADMIN_DESC", "各データに付加するキーワードの設定");
define("_ND_COMPONENT_ADMIN_DESC", "一覧画面での並び替えに使用する項目の設定");
define("_ND_INHERITE_ADMIN_DESC", "データの継承を実行");
define("_ND_IMPORT_ADMIN_DESC", "CSVをインポートする");
define("_ND_CONFIG_DESC", "モジュール全般の設定");

define("_ND_SORT_KEYWORD", "並べ替え");
define("_ND_SORT_ORDER","並び順");
define("_ND_DB_ERROR", "データベース接続エラー");

# Keyword Admin
define("_ND_CATEGORY", "カテゴリ");
define("_ND_CATEGORY_DESC", "カテゴリ名をクリックするとキーワードが表示されます");
define("_ND_ADD_NEYCATEGORY", "新規カテゴリ");
define("_ND_CATEGORY_NAME", "カテゴリ名");
define("_ND_CATEGORY_ADDED", "カテゴリが追加されました。");
define("_ND_CATEGORY_NONADDED", "カテゴリの追加に失敗しました。");

define("_ND_KEYWORD", "キーワード");
define("_ND_KEYWORD_DESC", "キーワード追加でサブキーワードを作成出来ます");
define("_ND_ADD_NEYKEYWORD", "キーワード追加");
define("_ND_KEYWORD_NAME", "キーワード名");
define("_ND_KEYWORD_ADDED", "キーワードが追加されました。");
define("_ND_KEYWORD_NONADDED", "キーワードの追加に失敗しました。");
define("_ND_CHANGE_KEYWORD", "キーワード変更");
define("_ND_KEYWORD_CHANGED", "キーワードが変更されました。");
define("_ND_DELETE_KEYWORD", "キーワード排除");

# component.php
define("_ND_ADD_NEWCOMPONENT", "項目追加");
define("_ND_COMPONENT_DESC", "変更・排除する場合は各項目名をクリック");
define("_ND_COMPONENT_NAME", "項目名");
define("_ND_COMPONENT_ONOFF", "切り替え");
define("_ND_COMPONENT_VALUE","選択項目");
define("_ND_COMPONENT_INPUTNG","必要事項をきちんと入力してください.");
define("_ND_COMPONENT_OK","項目を排除しました.");
define("_ND_COMPONENT_NG","項目排除失敗しました.");
define("_ND_COMPONENT_EDITOK","項目を編集しました.");
define("_ND_COMPONENT_EDITNG","項目編集失敗しました.");
define("_ND_COMPONENT_ADDOK","項目を追加しました.");
define("_ND_COMPONENT_ADDNG","項目追加失敗しました.");
define("_ND_COMPONENT_SEDIT","システム項目編集");
define("_ND_COMPONENT_SORT","ソートに使用するか");
define("_ND_COMPONENT_YES","使用する");
define("_ND_COMPONENT_NO","使用しない");
define("_ND_COMPONENT_ORDER","並び順");
define("_ND_COMPONENT_EDIT","項目編集");
define("_ND_COMPONENT_TEMPNAME","テンプレート名");
define("_ND_COMPONENT_ITEM_DESC","項目の説明");
define("_ND_COMPONENT_TYPE","タイプ");
define("_ND_COMPONENT_DEL","排除");
define("_ND_COMPONENT_DEL_DESC","この項目を排除する場合はチェック");
define("_ND_COMPONENT_SELECT_ITEM","選択項目");
define("_ND_COMPONENT_SELECT_ITEM_DESC","タイプ:text 以外を選択した場合は, 選択項目を[,]で区切って入力.");
define("_ND_COMPONENT_DEFAULT","初期値");
define("_ND_COMPONENT_DEFAULT_DESC","タイプ:radio を選択した場合は上記選択項目より１つ選択.");
define("_ND_COMPONENT_SRT","ソート");
define("_ND_COMPONENT_REGITEM","登録されている項目");

# inherite.php
define("_ND_INH_OK","データを継承しました.");
define("_ND_INH_USER","対象ユーザーを選択");
define("_ND_INH_FROM"," のデータを全て ");
define("_ND_INH_TO"," に継承する. ");

# detail.php
define("_ND_DETAIL_OK","変更しました.");
define("_ND_DETAIL_NG","変更失敗しました.");
define("_ND_DETAIL_TEMP","表示テンプレート");

# list.php
define("_ND_LIST_LIST","リスト形式");
define("_ND_LIST_THUMB","サムネイル形式");
define("_ND_LIST_CONFIG","設定");
define("_ND_LIST_SHOWNAME","表示名");
define("_ND_LIST_SNAME_DESC","この表示形式の名称.");
define("_ND_LIST_H","見出し");
define("_ND_LIST_H_DESC","リスト表示のトップに表示される見出し.");
define("_ND_LIST_CHANGE","切り替え");
define("_ND_LIST_DEL","この表示形式を排除する場合はチェック");
define("_ND_LIST_DIR","対象ディレクトリ");
define("_ND_LIST_DIR_DESC","ここで指定したディレクトリ内にある画像が全て表示される.");
define("_ND_LIST_SIZE","画像表示サイズ");
define("_ND_LIST_SIZE_DESC","複数入力する場合は[;]で区切る.[0]を指定した場合はデフォルトサイズ.");
define("_ND_LIST_SIZE_DESC2","[表示名, widht, height, 一列に表示する数]");
define("_ND_LIST_USE_ITEM","使用中の項目");
define("_ND_LIST_USE_ITEM_DESC","現在使用中の表示設定");
define("_ND_LIST_NUSE_ITEM","未使用の項目");
define("_ND_LIST_NUSE_ITEM_DESC","変更・排除する場合は各項目名をクリック");

?>