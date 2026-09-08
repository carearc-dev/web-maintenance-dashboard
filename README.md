# 社内Webサイト保守管理システム

Laravel / MySQL / Blade / Tailwind CSSで構築する、社内Webサイト保守管理ダッシュボードです。

## 現在の状態

- `docs/architecture.md` に全体設計を作成済み
- Phase 1向けのLaravelソース骨格、DBマイグレーション、Seeder、Policy、Serviceを配置
- HTTP、SSL、セキュリティヘッダー、WordPress状態を自動取得するサービス骨格を配置
- `php artisan sites:check-health` で全サイトまたは指定サイトの自動確認を実行可能
- 実行にはComposerとNode.jsが必要です

## ローカル環境構築

```bash
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate --seed
php artisan serve
```

フロントエンドビルドを有効化する場合:

```bash
npm install
npm run dev
```

## DB作成

MySQLまたはMariaDBで以下のDBを作成してください。

```sql
CREATE DATABASE maintenance_dashboard CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

`.env` のDB接続情報を環境に合わせて変更します。`.env` はGitへコミットしないでください。

## 本番環境構築

```bash
composer install --no-dev --optimize-autoloader
cp .env.example .env
php artisan key:generate
php artisan migrate --force
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

本番では必ず以下を設定してください。

```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://your-domain.example
SESSION_SECURE_COOKIE=true
```

Webサーバー側でBasic認証を設定し、アプリ側のログインとRBACを併用します。

## Cron設定

Laravel Schedulerを1分ごとに実行します。

```cron
* * * * * cd /path/to/app && php artisan schedule:run >> /dev/null 2>&1
```

監視頻度は `.env` の `MONITORING_INTERVAL_MINUTES` で管理します。

自動確認を手動実行する場合:

```bash
php artisan sites:check-health
php artisan sites:check-health 1
```

取得できる内容:

- HTTPステータス、応答速度
- SSL有効期限
- セキュリティヘッダー
- WordPress REST APIの公開状態
- 専用プラグイン導入済みサイトのWordPress/PHP/テーマ/プラグイン更新状況

WordPressの詳細状態を自動取得するには、対象サイト側に `wp-json/carearc-maintenance/v1/status` を返す専用プラグインを導入します。未導入サイトは公開REST APIで判定できる範囲のみ取得し、更新有無や契約範囲は手動確認扱いになります。

## デプロイ方法

1. Gitから本番サーバーへ取得
2. `.env` を本番値で作成
3. `composer install --no-dev --optimize-autoloader`
4. `php artisan migrate --force`
5. `php artisan config:cache route:cache view:cache`
6. WebサーバーのBasic認証、HTTPS、Cronを設定

## セキュリティ注意

- `.env`、APIキー、Basic認証パスワード、DBパスワード、SSH鍵、実在サイトの認証情報はコミット禁止
- 認証情報はLaravel暗号化機能で保存
- 認証情報の表示・コピーは監査ログへ記録
- 外部エンジニアのアクセス制限はBackend Policyで必ず判定

## テスト

```bash
php artisan test
```

この作業環境にはComposer、Node.js、Dockerがないため、現時点では依存関係インストールとテスト実行は未実施です。
