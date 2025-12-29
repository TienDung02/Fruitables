Chọn Ngôn ngữ / Choose Language / 言語を選択 / Выберите язык

- 🇻🇳 [Tiếng Việt](README.md)
- 🇺🇸 [English](README.en.md)
- 🇯🇵 日本語 (現在)
- 🇷🇺 [Русский](README.ru.md)

<p align="center">
  <h1 align="center">Fruitables</h1>
  <p align="center">
    **Laravel 12 + Vue 3 + Inertia.js**をベースに構築されたスターターキットプロジェクトです。バックエンドのLaravelの強力な機能を維持しながら、SPAアーキテクチャで最新のWebアプリケーションを迅速に開発することを目的としています。
    <br />
    <a href="https://fruitable.site/"><strong>ウェブサイトを見る »</strong></a>
  </p>
</p>

---

## 目次
<details>
  <summary>クリックして展開</summary>
  <ol>
    <li><a href="#overview">プロジェクト概要</a></li>
    <li><a href="#features">機能</a></li>
    <li><a href="#advancedfeatures">高度な機能</a></li>
    <li><a href="#technology">使用技術</a></li>
    <li><a href="#database">データベース</a></li>
    <li><a href="#setup">インストールガイド</a></li>
    <li><a href="#docker">Docker</a></li>
    <li><a href="#status">ステータスとロードマップ</a></li>
    <li><a href="#contact">お問い合わせ</a></li>
  </ol>
</details>

---

## プロジェクトの目的 <a id="objectives"></a>

**Fruitables**は、**最新のWeb技術、特にLaravel + Vue (Inertia)エコシステムの学習と実践**を目的とした個人プロジェクトです。

このプロジェクトは、**フルーツを販売するEコマースウェブサイト**の構築に焦点を当てており、ユーザーは以下のことができます:
- 商品の閲覧
- カートへの追加
- 注文とオンライン決済

現在、プロジェクトは**デモ/開発中**の状態で、主に**ユーザー側の機能**に焦点を当てています。

---

## 機能 <a id="features"></a>

### ユーザー機能
- アカウント登録/ログイン
- 商品リスト表示
- 商品詳細表示
- ショッピングカート管理
- 注文
- オンライン決済
- 配送先住所管理
- 商品レビュー
- お気に入り商品の保存(ウィッシュリスト)
- ユーザー通知の受信

## 高度な機能 <a id="advancedfeatures"></a>
- ユーザー認証
- 多言語対応
- SanctumによるAPI保護
- オンライン決済(SePay)
- データベースによるセッション&キュー管理

---

> **管理者**機能は現在開発中で、**まだREADMEの範囲に含まれていません**。

---

## エンティティ関係図(ERD) <a id="erd"></a>

![Entity Relationship Diagram](https://github.com/TienDung02/Fruitables/blob/main/ERD.png)

---

## 使用技術 <a id="technology"></a>

### バックエンド
- **Laravel 12**
- **PHP 8.2**
- Laravel Sanctum(認証)
- キュー、セッション、キャッシュ: **データベース**
- オンライン決済: **SePay**

### フロントエンド
- **Vue 3**
- **Inertia.js**
- **Vite**
- Tailwind CSS
- Bootstrap 5
- Pinia(状態管理)
- Vue I18n
- Axios

### データベース
- SQLite(ローカル環境のデフォルト)
- MySQL / MariaDBに切り替え可能

---

## Laravelプロジェクトのインストールガイド <a id="setup"></a>

### システム要件

開始前に、以下がインストールされていることを確認してください:

- **PHP** >= 8.2
- **Composer**
- **MySQL / MariaDB**
- **Node.js & npm**
- **MySQL または MariaDB**
- **Git**

---

### ステップ1: プロジェクトのクローン
```bash
https://github.com/TienDung02/Fruitables.git
```
```bash
cd Fruitables
```

### ステップ2: バックエンドのインストール
```bash
composer install
```

### ステップ3: 環境設定
```bash
cp .env.example .env
```

```bash
php artisan key:generate
```

.envを編集:
```bash
# データベース設定
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=your_database
DB_USERNAME=your_username
DB_PASSWORD=your_password
```

- 使用しない場合はスキップ可能

```bash
# Sepayにリンクされた銀行口座設定(オプション)
BANK_ACCOUNT_NUMBER=
BANK_ACCOUNT_NAME=
BANK_CODE=
BANK_NAME=
```

```bash
# SEPAY設定(オプション)
SEPAY_API_TOKEN=
SEPAY_SECRET_KEY=
SEPAY_ACCOUNT_NUMBER=
SEPAY_BANK_CODE=
SEPAY_ACCOUNT_NAME=
```

```bash
# Resendでのメール設定(オプション)
MAIL_MAILER=resend 
RESEND_KEY=
MAIL_FROM_ADDRESS="no-reply@fruitable.site"
MAIL_FROM_NAME="${APP_NAME}"
```

### ステップ4: マイグレーションとシード
必要なテーブルを作成
```bash
php artisan migrate
```

新しく作成したテーブルのサンプルデータを生成
```bash
php artisan db:seed
```

### ステップ5: フロントエンドのインストール
```bash
npm install
```

### ステップ6: フロントエンドのビルド
本番環境/ローカルデモ用に実行
```bash
npm run build
```
- JS/CSSをpublic/buildにビルド
- 開発サーバーを実行する必要なし → 低RAMマシンに適している

開発用に実行
```bash
npm run dev
```
```bash
php artisan serve
```
- ホットリロード付きのVite開発サーバーを起動
- フロントエンドコードを継続的に変更する場合のみ使用

### ステップ7: アプリケーションの実行
```bash
php artisan serve --host=localhost --port=8000
```

アクセス: http://localhost:8000

## Docker <a id="docker"></a>

### 要件
- Docker >= 24
- Docker Compose(マルチコンテナの場合はオプション)
- ホストマシンでポート8080が利用可能

### Docker構造
プロジェクトはマルチステージDockerビルドを使用:
1. ステージ1 – バックエンドビルド: Composerのインストール、PHP依存関係のインストール、すべてのソースのコピー
2. ステージ2 – フロントエンドビルド: Node 20 + npm build assets(Vite + Tailwind)を使用
3. ステージ3 – ランタイム: PHP-FPM + Nginx + supervisordを同時実行、Laravel + フロントエンドを提供

### インストールと実行
1. イメージをビルド:
```bash
docker build -t fruitables:latest .
```

2. 古いコンテナ/イメージを削除(必要な場合):
```bash
docker container prune
```
```bash
docker image prune -a
```

3. コンテナを実行:
```bash
docker run -it -p 8080:80 --name fruitables fruitables:latest
```

4. 初回Laravel設定のためにコンテナに入る:
```bash
docker exec -it fruitables sh
```

5. .envファイルを作成し、アプリケーションキーを生成:
```bash
cp .env.example .env
```
```bash
php artisan key:generate
```

6. 権限を設定:
```bash
chown -R www-data:www-data storage bootstrap/cache
```
```bash
chmod -R 775 storage bootstrap/cache
```

7. キャッシュをクリア(必要な場合):
```bash
php artisan config:clear
php artisan cache:clear
php artisan view:clear
```

8. ブラウザを開く: http://localhost:8080 にアクセス

### 注意事項
- 500 Server Errorが発生した場合、.envファイル、データベース設定、storage/bootstrap/cacheの権限を確認してください。
- フロントエンドは既に/public/buildにビルドされています。ランタイムコンテナでnpm run devを実行する必要はありません。

### 重要なディレクトリ
- app/: Laravelコード
- resources/: フロントエンドリソース(CSS、JS、Vue)
- public/build: フロントエンドビルド出力
- storage/、bootstrap/cache/: キャッシュとログ
- docker/: Nginx、supervisord、php.ini設定

## ステータスとロードマップ <a id="status"></a>

現在のステータス:
- **DEMO / 開発中**

今後の計画:
- セキュリティと認可の最適化
- 管理者管理システムの完成
- パフォーマンスとUXの改善

## お問い合わせ <a id="contact"></a>

- プロジェクト名: **Fruitables**
- 目的: 学習/個人プロジェクト
- メール: nongtiendung2309@gmail.com
