# GitHubで公開・作業する方法

## 1. まず決めること

このプロジェクトは、用途によって公開方法が変わります。

| 目的 | 方法 |
| --- | --- |
| 今のプレビューHTMLを共有したい | GitHub Pagesで公開 |
| Laravelアプリとしてログイン・DB・自動監視まで動かしたい | GitHubでコード管理し、別途PHP/MySQL対応サーバーへデプロイ |

GitHub Pagesは静的HTMLの公開には使えますが、Laravel、MySQL、ログイン、Cron、自動監視は動きません。

## 2. GitHubにアップする手順

```bash
cd /path/to/project
git init
git add .
git commit -m "Initial web management sheet prototype"
git branch -M main
git remote add origin https://github.com/YOUR_ACCOUNT/YOUR_REPOSITORY.git
git push -u origin main
```

## 3. GitHub Pagesでプレビューを公開する場合

GitHub Pagesで公開する場合は、`outputs/preview.html` を公開対象にします。

GitHub Pagesだけでできること:

- ページ表示前のロール別簡易パスワード入力
- 管理者 / スタッフ / エンジニアのロール別UI表示と操作制御
- ボタンやフォームの表示・非表示、無効化
- 30分操作がない場合の自動ロック
- ログイン失敗が5回続いた場合の10分一時ロック
- プレビューパスワードのハッシュ照合
- ログイン連打防止、失敗時の遅延、ハニーポット項目による自動投稿対策
- 入力内容をHTMLとして実行しにくくする最低限の表示処理
- 外部参照や埋め込みを抑えるためのHTMLメタ設定
- 検索エンジンに拾われにくくする `noindex` 設定
- プレビュー用途の操作確認

プレビュー用パスワードは、GitHubに置くドキュメントへ平文で記載せず、社内の安全な共有方法で管理します。

GitHub Pagesだけではできないこと:

- 本物のBasic認証
- サーバー側のログイン認証
- 改ざんできない権限管理
- DB保存
- 暗号化された認証情報管理
- 監査ログの真正な記録

重要: GitHub Pages上のJavaScriptによるパスワードや権限制御は、社内確認用の簡易ガードです。HTMLとJavaScriptは閲覧者が確認できるため、実パスワード、サーバー認証情報、顧客の秘密情報は絶対に置かないでください。

外部攻撃への対策として、現在のプレビューにはCSP、referrer制御、noindex、ログイン試行ロック、ログイン連打防止、失敗時の遅延、ハニーポット項目、セッション自動ロック、入力値のエスケープを入れています。ただし、GitHub Pagesではサーバー側でアクセスを拒否できないため、URLを知っている第三者への完全な防御にはなりません。

おすすめは、GitHub Pages用に `docs/` またはルート直下へ静的プレビューを配置する方法です。

例:

```text
index.html
assets/
  carearc-logo-black.png
```

GitHub側の設定:

1. GitHubリポジトリを開く
2. `Settings` を開く
3. `Pages` を開く
4. `Build and deployment` で `Deploy from a branch` を選択
5. Branchを `main`、公開フォルダを `/root` または `/docs` に設定
6. 表示されたURLを社内共有する

## 4. Laravelアプリとして運用する場合

Laravelとして本運用する場合、GitHubはコード管理場所として使います。

必要なもの:

- PHP 8.2以上
- Composer
- MySQLまたはMariaDB
- Webサーバー
- HTTPS
- Cron
- Basic認証

本番サーバーでは以下を実行します。

```bash
composer install --no-dev --optimize-autoloader
cp .env.example .env
php artisan key:generate
php artisan migrate --force
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

## 5. 絶対にGitHubへ入れないもの

- `.env`
- 実パスワード
- Basic認証情報
- サーバーパスワード
- FTP / SSH / DB認証情報
- APIキー
- 秘密鍵
- 顧客から預かった非公開情報

認証情報は、アプリ内の暗号化保存機能で管理します。

## 6. 作業ルール

- 作業前にブランチを作る
- 修正後にコミットする
- 重要な変更はPull Requestで確認する
- 本番反映前にプレビューを確認する
- 認証情報や顧客情報が差分に入っていないか確認する

例:

```bash
git checkout -b feature/update-users-screen
git add .
git commit -m "Update users screen permissions"
git push -u origin feature/update-users-screen
```
