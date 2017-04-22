# PHPSimpleIRCBot
PHPで動くシンプルなIRC用bot

## 動かし方
### config.php
`config.php`に必要な事項を設定する（基本的にはserversの中だけ弄れば大丈夫）
serversの中身はキーは設定名、設定の中身は

* host: 接続したいサーバーのホスト名
* port: 接続したいポート
* nick, loginname, realname: それぞれの名前、普通に表示されるのはnick
* encoding: エンコーディング
* channels: 接続したいチャンネルのリスト
* modules: 読み込みたいモジュールとその設定、キーをモジュール名にする

### 実行
`php bot.php <設定名>`で実行できる。
`nohup php bot.php <設定名> &`とかしておくといいかもしれない。

## モジュールについて
このbotはモジュールを追加することで機能を拡張できる。
モジュールを追加する場合、Modules\\Moduleを継承する。
モジュール名が`hoge`の場合、`modules/hoge/hoge.php`に記述し、
`namespace Modules\Hoge;`としたうえで`Hoge`クラスに記述する。
`config.php`で設定したオプションは`$this->options`で取得できる。
詳しいことはrepeatモジュールを参考にする。
