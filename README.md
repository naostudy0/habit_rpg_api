# Habit RPG API

AIがあなたの過去の行動から「続けられる習慣」を提案する、RPG風 習慣管理アプリのバックエンドAPI  
（ポートフォリオ）

## 概要

Habit RPG APIは、習慣管理アプリ「Habit RPG App」を支えるバックエンドAPIです。


最大の特徴は、Docker上で動作するLLM（大規模言語モデル）を用いて、過去の予定データを分析し、その人に合った新しい習慣・予定をAIが提案する点です。

本APIは AI駆動開発の学習・実践 を目的として個人で開発したポートフォリオであり、
バックエンド（Laravel / Docker / AI連携）を主軸に設計・実装しています。

- AIによる予定生成ロジックは Laravel側で実装
- Docker環境にAI（Ollama）を組み込んだ構成を採用
- APIファーストな設計でフロントエンド（Flutter）と連携

## コンセプト

- 習慣化が苦手な人でも「ゲーム感覚」で続けられる
- 過去の行動履歴をもとに、無理のない現実的な習慣をAIが提案
- LLMを実サービス想定で扱うため、Docker環境にAIを組み込んだ構成を採用

※ 現時点ではRPG要素に関するAPIは限定的ですが、将来的な経験値・レベルシステムの追加を前提に、拡張可能なデータ設計・サービス分割を意識しています。

## 主な機能

- ユーザー認証（ログイン・ログアウト）
  - Laravel Sanctumによるトークンベース認証

- 予定・習慣管理
  - 作成 / 編集 / 削除
  - 完了状態の切り替え
  - 一覧取得

- AIによる予定・習慣提案
  - 過去の予定データを元にLLMが提案
  - Laravel側でプロンプト設計・生成処理を実装
  - Ollama（Dockerコンテナ）と連携

- ユーザープロフィール管理
  - プロフィール取得
  - プロフィール更新

## システム構成（概要）

```
Flutter（フロントエンド）
        ↓ HTTP API
Laravel（バックエンド / API）
        ↓
LLM（Ollama）
        ↓
MySQL（データベース）
```

- Laravel：認証、予定管理、AIプロンプト生成、レスポンス整形
- MySQL：データ永続化
- Ollama：LLMによる予定提案生成

**注意**: Docker環境の詳細については、プロジェクトルート（`habit_rpg_docker`）のREADMEを参照してください。

## 技術スタック

- フレームワーク: Laravel 12.0
- 言語: PHP 8.2+
- 認証: Laravel Sanctum
- データベース: MySQL 8.0
- AI / LLM: Ollama
- テスト: PHPUnit

### 設計方針

- APIファースト
- AIロジックはLaravel側に集約
- 実務利用を想定した構成
- レイヤードアーキテクチャ（Controller → Service → Repository → Model）
- AIによる判断・生成処理はすべてバックエンドで完結させ、フロントエンドは結果の表示と操作に専念させる構成としています


## 必要な環境

- PHP 8.2以上
- Composer
- MySQL 8.0（Docker環境で提供される場合は、Docker環境のREADMEを参照）

**注意**: Docker環境でのセットアップについては、プロジェクトルート（`habit_rpg_docker`）のREADMEを参照してください。

## セットアップ

### 前提条件

Docker環境が既に起動していることを前提とします。Docker環境のセットアップについては、プロジェクトルート（`habit_rpg_docker`）のREADMEを参照してください。

### 1. 依存関係のインストール

```bash
composer install
```

### 2. 環境設定

`.env.example`を参考に`.env`ファイルを作成し、必要な環境変数を設定してください。

```bash
cp .env.example .env
```

主要な環境変数：

```env
APP_NAME=HabitRPG
APP_ENV=local
APP_KEY=
APP_DEBUG=true
APP_URL=http://localhost

DB_CONNECTION=mysql
DB_HOST=mysql
DB_PORT=3306
DB_DATABASE=your_database_name
DB_USERNAME=your_username
DB_PASSWORD=your_password

OLLAMA_URL=http://ollama:11434/api/generate
OLLAMA_MODEL=llama3
```

**注意**: Docker環境を使用する場合、`DB_HOST`は`mysql`（コンテナ名）に設定してください。

### 3. アプリケーションキーの生成

```bash
php artisan key:generate
```

### 4. データベースマイグレーション

```bash
php artisan migrate
```

### 5. シーダーの実行（オプション）

```bash
php artisan db:seed
```

## 開発

### 開発サーバーの起動

Docker環境を使用する場合、Apacheが自動的にLaravelアプリケーションを提供します（ポート80）。

ローカルで開発する場合：

```bash
# 開発用コマンド（サーバー、キュー、ログを同時起動）
composer run dev

# または個別に起動
php artisan serve
php artisan queue:listen
php artisan pail
```

Dockerコンテナ内でコマンドを実行する場合：

```bash
docker exec -it habit_rpg_container php artisan queue:listen
docker exec -it habit_rpg_container php artisan pail
```

### コードフォーマット

```bash
./vendor/bin/pint
```

## テスト

```bash
# 全テスト実行
composer test
# または
php artisan test

# ユニットテストのみ
php artisan test --testsuite=Unit

# フィーチャーテストのみ
php artisan test --testsuite=Feature

# 個別実行
php artisan test tests/Unit/Repositories/TaskRepositoryTest.php
```

Dockerコンテナ内でテストを実行する場合：

```bash
docker exec -it habit_rpg_container php artisan test
```

## APIエンドポイント

### 認証

- `POST /api/auth/login` - ログイン

### ユーザー（認証必須）

- `GET /api/user` - ユーザー情報取得
- `PUT /api/user` - ユーザー情報更新

### 予定（認証必須）

- `GET /api/tasks` - 予定一覧取得
- `POST /api/tasks` - 予定作成
- `PUT /api/tasks/{uuid}` - 予定更新
- `DELETE /api/tasks/{uuid}` - 予定削除
- `PATCH /api/tasks/{uuid}/complete` - 完了状態の切り替え

### 提案（認証必須）

- `GET /api/task-suggestions` - AI提案一覧取得
- `DELETE /api/task-suggestions/{uuid}` - 提案削除

詳細なAPI仕様については、フロントエンドリポジトリの[API仕様書](https://github.com/naostudy0/habit_rpg_app/blob/main/docs/api.md)を参照してください。

## プロジェクト構造

```
app/
├── Console/          # Artisanコマンド
├── Http/
│   ├── Controllers/ # コントローラー
│   └── Requests/    # フォームリクエスト
├── Models/          # Eloquentモデル
├── Repositories/    # リポジトリ層
├── Services/        # ビジネスロジック
└── Utils/           # ユーティリティ

config/              # 設定ファイル
database/
├── factories/       # ファクトリー
├── migrations/      # マイグレーション
└── seeders/         # シーダー

routes/
├── api.php          # APIルート
├── web.php          # Webルート
└── console.php      # コンソールルート

tests/
├── Feature/         # フィーチャーテスト
└── Unit/            # ユニットテスト
```

## このプロジェクトについて

本プロジェクトは、ポートフォリオとして個人で開発したアプリケーションです。
実務を想定した設計・技術選定を行っていますが、商用サービスとしての提供は行っていません。

- AI駆動開発の学習・実践が主目的です
- バックエンド（Laravel / Docker / AI連携）に注力しています
- APIファーストな設計で拡張性を重視しています

また、習慣管理 × RPG × AI を段階的に発展させていくことを前提に、
拡張しやすいバックエンド設計を意識しています。

## 今後追加予定の機能

- AIを活用した経験値・レベルシステム
  - 予定・習慣の内容や継続状況に応じて経験値を付与
  - ユーザーの行動傾向を踏まえた成長バランスをAIが調整

## ライセンス

本リポジトリは個人のポートフォリオ目的で公開しています。
