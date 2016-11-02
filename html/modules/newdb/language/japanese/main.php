<?php

define("_ND_UP","昇順");
define("_ND_DOWN","降順");

# general
define("_ND_DIR_FALSE","ディレクトリ作成に失敗しました.");
define("_ND_NACCESS","不正なアクセスです");
define("_ND_NACCESS2","アクセス権限がありません.");
define("_ND_NODATA","データが存在しません.");
define("_ND_BACK","戻る");
define("_ND_DELETE","排除");
define("_ND_MAKE","作成");
define("_ND_EDIT","編集");
define("_ND_SHOW","表示");
define("_ND_HIDE","非表示");
define("_ND_SELECTALL","全て選択");
define("_ND_UNSET","解除");

# class
define("_ND_CLASS_ACOM","オーサーコメント");
define("_ND_CLASS_UCOM","ユーザーコメント");
define("_ND_CLASS_SHOWALL","全て表示");
define("_ND_CLASS_RDATA","関連データ");
define("_ND_CLASS_RURL","関連URL");
define("_ND_CLASS_CATEGORY","カテゴリ");
define("_ND_CLASS_ALL","全");
define("_ND_CLASS_HIT","件");
define("_ND_CLASS_HIT_NOW","件目");
define("_ND_CLASS_REFINE2","さらに絞込む");
define("_ND_CLASS_REFINE","新しく絞込む");

# edata.php
define("_ND_EDATA_MKDIR","新規ディレクトリ作成");
define("_ND_EDATA_UPPERD","上位ディレクトリ");
define("_ND_EDATA_DNAME","新規ディレクトリ名");
define("_ND_EDATA_ADDFILE","ファイルを追加");
define("_ND_EDATA_ADDFILE_DESC","tarをアップロードしても展開されません");
define("_ND_EDATA_TARGETD","対象ディレクトリ");
define("_ND_EDATA_FILE","ファイル");
define("_ND_EDATA_FD","複数のファイル・ディレクトリを追加");
define("_ND_EDATA_NDIRNAME","ディレクトリ名を入力してください");
define("_ND_EDATA_FEXIST","このファイルは既に存在します.上書きは出来ません.");
define("_ND_EDATA_ADDFILENG","ファイル追加に失敗しました.");
define("_ND_EDATA_ADDFILEOK","ファイルを追加しました.");
define("_ND_EDATA_NSUF","この拡張子は許可されていません.");
define("_ND_EDATA_NFILE","ファイルが選択されていません");
define("_ND_EDATA_NODATA","該当データが見つかりません");
define("_ND_EDATA_NSDATA","データが選択されていません");
define("_ND_EDATA_RETRY","このファイルは既にuploadディレクトリに存在します, ファイル名を変更して再試行して下さい.");
define("_ND_EDATA_NODIR","指定されたディレクトリは存在しません.");
define("_ND_EDATA_DATAADD","データを追加しました");
define("_ND_EDATA_IFMES","上位ディレクトリを移動すると, チェックを付けていなくても下位ディレクトリも影響を受けます.");
define("_ND_EDATA_MOVETO","へ移動させる");
define("_ND_EDATA_DIRMOVED","ディレクトリを移動しました.");
define("_ND_EDATA_NDIRMOVED","ディレクトリ移動は行われませんでした.");
define("_ND_EDATA_FMOVED","ファイルを移動しました.");
define("_ND_EDATA_TRASH1","選択されたデータはゴミ箱に移動します");
define("_ND_EDATA_TRASH2","上位ディレクトリを排除すると, チェックを付けていなくても下位ディレクトリ, ファイルも全て排除されます.");
define("_ND_EDATA_DATADELETED","データを排除しました");
define("_ND_EDATA_TRASH3","ここにあるデータを消去すると元に戻せません");
define("_ND_EDATA_DODEL","排除する");
define("_ND_EDATA_RESTORE","へ復元する");
define("_ND_EDATA_DATADIS","データを完全に排除しました");
define("_ND_EDATA_DATARESTORED","データを復元しました");
define("_ND_EDATA_DATARESTOREDNG","データ復元失敗しました");
define("_ND_EDATA_AF","ファイル追加");
define("_ND_EDATA_MD","ディレクトリ移動");
define("_ND_EDATA_MF","ファイル移動");
define("_ND_EDATA_DF","ファイル排除");
define("_ND_EDATA_TR","ごみ箱");

# fs.php
define("_ND_FS_HIT","件");
define("_ND_FS_HIT2","件中");
define("_ND_FS_HIT3","件目");

# kws.php
define("_ND_KW_OK","登録しました.");
define("_ND_KW_NG","登録失敗しました。");
define("_ND_KW_SELECT","選択したキーワード");
define("_ND_KW_ALREADY","すでに検索にかけたキーワード");
define("_ND_KW_SELECTSEARCH","検索するキーワードを選択");
define("_ND_KW_METHOD","検索方法");

# link.php
define("_ND_LINK_DELOK","リンクを排除しました.");
define("_ND_LINK_DELNG","リンク排除失敗しました");
define("_ND_LINK_EDITOK","リンクを編集しました.");
define("_ND_LINK_EDITNG","リンク編集失敗しました.");
define("_ND_LINK_ADDOK","リンクを追加しました.");
define("_ND_LINK_RLINKOK","相互リンクしました.");
define("_ND_LINK_RLINKNG","相互リンク失敗しました.");
define("_ND_LINK_ADDNG","リンク追加失敗しました.");
define("_ND_LINK_SELECT_TARGET","対象を選択してください.");
define("_ND_LINK_ADDLINK","リンク追加");
define("_ND_LINK_TARGET","対象");
define("_ND_LINK_METHOD1","DB内にある他のデータへリンク");
define("_ND_LINK_METHOD2","DB外のURLへリンク");
define("_ND_LINK_TARGETID","対象データID");
define("_ND_LINK_RLNK","相互リンク");
define("_ND_LINK_RLINK_DESC","相互リンクにする場合はチェック");
define("_ND_LINK_NAME","リンク名");
define("_ND_LINK_EDIT","リンク編集");
define("_ND_LINK_DEL_DESC","排除する場合はチェック");

# bookmark.php
define("_ND_BK_ADD","Bookmark登録");
define("_ND_BK_DIR","ディレクトリ");
define("_ND_BK_MKDIR","ディレクトリ作成");
define("_ND_BK_ORDER","並び順");
define("_ND_BK_CLASS","階層");
define("_ND_BK_TOP","トップディレクトリ");
define("_ND_BK_SUB","サブディレクトリ");
define("_ND_BK_MKDIROK","ディレクトリを作成しました.");
define("_ND_BK_EDITDIR","ディレクトリ編集");
define("_ND_BK_MOVE","移動");
define("_ND_BK_NMOVE","移動させない");
define("_ND_BK_GOTOP","トップへ移動");
define("_ND_BK_DEL_DESC","このディレクトリを排除（データも全て排除されます）");
define("_ND_BK_RMDIR","ディレクトリを排除しました");
define("_ND_BK_EDITDIROK","ディレクトリを変更しました.");
define("_ND_BK_EDITDIRNG","ディレクトリ変更失敗しました.");
define("_ND_BK_NDATA","データが選択されていません");
define("_ND_BK_DELETED","個のデータを排除しました");
define("_ND_BK_DEDIT","データ編集");
define("_ND_BK_DATACHANGED","個のデータを更新しました.");
define("_ND_BK_NDATACHANGED","データ更新に失敗しました.");
define("_ND_BK_CM","変更・移動");
define("_ND_BK_SD","選択データ");
define("_ND_BK_DATA","データ");

# config.php
define("_ND_CONFIG_INFOCHANGE","データ情報を変更しました.");
define("_ND_CONFIG_KEYOK","キーワードを変更しました.");
define("_ND_CONFIG_KEYNG","キーワード変更失敗しました.");
define("_ND_CONFIG_DEL","データを排除しました.");
define("_ND_CONFIG_CONFIRM","[注意] 一度排除すると二度と元には戻せません. 現在登録されてあるファイルも全て排除されます.");
define("_ND_CONFIG_CONFIRM2","本当にこのデータを排除しますか？");
define("_ND_CONFIG_NEWUP","新規アップロード");
define("_ND_CONFIG_NEWUP_DESC","アップロードするディレクトリ名を入力してファイル (gif, bmp, jpg, jpeg, png) を選択");
define("_ND_CONFIG_FILE_DEL","ファイル排除");
define("_ND_CONFIG_NOMES","排除確認メッセージは表示されません");
define("_ND_CONFIG_THUMBDEL","サムネイル画像を排除しました.");
define("_ND_CONFIG_INPUTDIR","ディレクトリを入力してください.");
define("_ND_CONFIG_IMGUPOK","画像をアップロードしました.");
define("_ND_CONFIG_IMGUPNG","画像アップロード失敗しました.");
define("_ND_CONFIG_UPSUF","アップロード出来るのは「gif,bmp,jpg,jpeg,png」のみです.");
define("_ND_CONFIG_NFILESELECT","ファイルが選択されていません.");
define("_ND_CONFIG_INFO","データ情報");
define("_ND_CONFIG_KEYWORD","キーワード");
define("_ND_CONFIG_THUMB","サムネイル");
define("_ND_CONFIG_DELETE","データ消去");

# list.php
define("_ND_LIST_KEYREFINE","キーワードで絞込む");
define("_ND_LIST_SAVELIST","このリストを保存");
define("_ND_LIST_REFINE","絞込む");
define("_ND_LIST_TSEARCH","テキスト項目の検索");

# comment.php
define("_ND_COMMENT_POST","コメントを投稿しました.");
define("_ND_COMMENT_NPOST","コメント投稿失敗しました.");
define("_ND_COMMENT_DEL","コメントを排除しました.");
define("_ND_COMMENT_NDEL","コメント排除失敗しました.");
define("_ND_COMMENT_CONFIRM","このコメントを排除しますか？ ");
define("_ND_COMMENT_NEXIST","指定されたコメントは存在しません.");

# commentform.inc.php
define("_ND_COMVIEW_FORM","新規投稿フォーム");
define("_ND_COMVIEW_TITLE","タイトル");
define("_ND_COMVIEW_MES","メッセージ");
define("_ND_COMVIEW_MAIL","メール通知");
define("_ND_COMVIEW_ALL","全て");
define("_ND_COMVIEW_NON","解除");

#commentview.php
define("_ND_COMMENTV_BACK","へ戻る");
define("_ND_COMMENTV_OLD","古い順");
define("_ND_COMMENTV_NEW","新しい順");
define("_ND_COMMENTV_SRED","スレッド表示");
define("_ND_COMMENTV_FLAT","フラット表示");
define("_ND_COMMENTV_COM","コメント");
define("_ND_COMMENTV_NEXT","次のコメント");
define("_ND_COMMENTV_BEFORE","前のコメント");
define("_ND_COMMENTV_SRED2","スレッド");
define("_ND_COMMENTV_POSTER","投稿者");
define("_ND_COMMENTV_DATE","投稿日時");


# register.php
define("_ND_REG1","データ名を入力してください.");
define("_ND_REG2","このファイルはアップロード制限サイズを超えています.");
define("_ND_REG3","このデータは既に登録されています.");
define("_ND_REG4","アップロード出来るのはtarアーカイブのみです.");
define("_ND_REG5","オーサーコメント");
define("_ND_REG6","が登録されました.");
define("_ND_REG7","データ登録完了");
define("_ND_REG8","データシート");
define("_ND_REG9","* は必須項目です.");
define("_ND_REG10","データシート");
define("_ND_REG11","登録データ名");
define("_ND_REG12","コメント");
define("_ND_REG13","追加オプション");
define("_ND_REG14","キーワード");
define("_ND_REG15","データファイル");
define("_ND_REG15_","サムネイル");
define("_ND_REG16","キーワード選択");
define("_ND_REG17","データに対するキーワード");
define("_ND_REG18","データファイル選択");
define("_ND_REG19","アップロード制限：tar形式");
define("_ND_REG20","tar アップロード");
define("_ND_REG21","アップロード済ディレクトリから選択");
define("_ND_REG22","送信");
define("_ND_REG23","選択したデータファイルのサイズによっては登録にかなり時間がかかります.");

define("_ND_REG_THUMB1","サムネイルディレクトリ");
define("_ND_REG_THUMB2","サムネイルファイル");
define("_ND_REG_THUMB3","キャプション");

define("_ND_REG24","サムネイル選択");
define("_ND_REG25","ここではサムネイルをひとつだけ追加できます.");
define("_ND_REG26", "が未入力です.");
define("_ND_REG27", "が制限文字数を超えています.");
define("_ND_REG28", "項目");
define("_ND_REG29", "記入欄");
define("_ND_REG30", "文字まで");

# news.php
define("_ND_NEWS_REGFILE","登録されたファイル");
define("_ND_NEWS_REGPAST","過去の登録");

?>