# 管理者ダッシュボード - 統計情報追加

## 概要

管理者ダッシュボード（`http://localhost/admin/dashboard`）に以下の統計情報を追加しました：

## 追加された統計情報

### 1. チャットルームの数
- **表示内容**: 作成済みのチャットルーム（会話）の総数
- **データソース**: `conversations` テーブル
- **クエリ**: `Conversation::count()`
- **アイコン**: fas fa-comments
- **色**: 緑色（text-success）

### 2. ユーザー数
- **表示内容**: 登録済みユーザーの総数（既存機能）
- **データソース**: `users` テーブル
- **クエリ**: `User::count()`
- **アイコン**: fas fa-users
- **色**: 青色（text-primary）

### 3. 本日のアクティブユーザー数
- **表示内容**: 本日メッセージを送信したユーザーの数
- **データソース**: `messages` テーブル
- **クエリ**: `Message::whereDate('sent_at', date('Y-m-d'))->distinct('sender_id')->count('sender_id')`
- **アイコン**: fas fa-user-clock
- **色**: オレンジ色（text-warning）

### 4. 本日送信されたメッセージ数
- **表示内容**: 本日送信されたメッセージの総数
- **データソース**: `messages` テーブル
- **クエリ**: `Message::whereDate('sent_at', date('Y-m-d'))->count()`
- **アイコン**: fas fa-envelope
- **色**: シアン色（text-info）

## 変更されたファイル

### 1. `backend/app/Http/Controllers/Admin/AdminDashboardController.php`
- 新しい統計情報を取得するロジックを追加
- 必要なモデルのインポートを追加（Conversation、Message）
- dashboard()メソッドで新しい変数をビューに渡すように修正

### 2. `backend/resources/views/admin/dashboard.blade.php`
- 統計カードのレイアウトを更新
- 4つの新しい統計カードを追加
- 管理者関連の統計を別セクションに分離
- レスポンシブデザインを維持

## 使用方法

1. 管理者アカウントでログイン
2. `http://localhost/admin/dashboard` にアクセス
3. ダッシュボード上部に新しい統計情報が表示されます

## 技術的詳細

### データベーステーブル構造
- `conversations`: チャットルーム情報
- `messages`: メッセージ情報（sent_at、sender_idを使用）
- `users`: ユーザー情報
- `admins`: 管理者情報

### パフォーマンス考慮事項
- 全ての統計クエリは効率的なカウント操作を使用
- 本日のデータフィルタリングにはdate()関数を使用
- distinct()を使用してアクティブユーザーの重複を除去

## セットアップ要件

実行前に以下を確認してください：
1. データベースマイグレーションが完了していること
2. Conversation、Message、Participantモデルが正しく設定されていること
3. 管理者認証が正常に動作していること

## 表示例

```
[ユーザーアイコン]          [チャットアイコン]        [時計アイコン]           [メールアイコン]
総ユーザー数               チャットルーム数          本日のアクティブユーザー数   本日送信されたメッセージ数
1,234                     56                      89                      456
登録済みユーザー           作成済みルーム            本日メッセージ送信         本日の投稿
```

## 今後の拡張可能性

- 週次/月次の統計情報
- グラフ表示機能
- エクスポート機能
- リアルタイム更新
- より詳細な分析機能