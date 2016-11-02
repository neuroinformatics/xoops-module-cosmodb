
【CosmoDB 関連URL】

CosmoDB プロジェクトサイト (マニュアルがあります)
http://www.web-learner.com/

サポートフォーラム
https://sourceforge.jp/forum/forum.php?forum_id=8326

バグ報告
https://sourceforge.jp/forum/forum.php?forum_id=8325

開発用フォーラム
https://sourceforge.jp/forum/forum.php?forum_id=8327


【アップデート情報】

【v 1.1.1 -> v1.2】

◆ バージョンアップ手順

    1. ファイルを上書き
    2. 管理画面からモジュールのアップデート
    3. http://XOOPS_URL/modules/newdb/admin/upgrade/11_to_12.php を実行する
      (alter table xoops_newdb_component_master ... ; が実行されます)

◆ 変更点

○ 管理画面：項目管理
　・必須項目、文字数制限機能の実装
　・Select、Textboxタイプを追加
　・radio、checkbox に画像選択項目を指定できるように修正
　・各項目毎に絞込みに 使用する/しない を選択できるように修正

○ 詳細画面設定に実装
　・使用できるテンプレート一覧を表示

○ 新規登録画面、データコンフィグ画面
　・項目設定で設定した並び順を反映するように修正

○ その他
　・ディレクトリ一覧テンプレートで文字数を指定できるように修正
　　{Dirs} => {Dirs 4} (どちらの形式も使用可)
　・検索ブロックにデータ名検索を追加

【ver 1.1 -> 1.1.1】
アンインストール時にテーブルが排除されないバグを修正。

【ver 1.0.x -> 1.1】
管理画面からモジュールのアップデートを行った後、
http://XOOPS_URL/modules/newdb/admin/upgrade/10_to_11.php を実行する必要があります。

text項目検索機能を追加


【ver0.5 -> ver 1.0.x】
管理画面からモジュールのアップデートを行った後、
http://XOOPS_URL/modules/newdb/admin/upgrade/05_to_10.php を実行する必要があります。


【ベータ版】
アンインストール後、インストールを行う必要があります。