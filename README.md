# 欲しいものリスト

欲しいものを、カテゴリに分けて一か所で管理できるWebアプリ。
アイテム名・欲しい度合い（星）・金額・理由・販売ページのURL・画像をまとめて記録し、欲しい度合いや金額で並べ替えて見比べられる。

## 主な機能

- ユーザー登録・ログイン・ログアウト
- カテゴリの作成・名前変更・削除（作成と名前変更は画面を読み込み直さずに反映）
  - 登録時に「未分類」カテゴリを自動で作成。カテゴリを削除するときは、中の欲しいものを未分類へ移すか、すべて削除するかを選べる
- 欲しいものの登録・詳細表示・編集・削除（削除は画面を読み込み直さずに反映）
- 画像の添付・差し替え（jpg / png、2MBまで）
- 並び替え（新しい順・欲しい度合いが高い順・金額が安い順・金額が高い順）
- 最後に開いたカテゴリの記憶（次回ログイン時にそのカテゴリを表示）

## 使用技術

| 分類 | 技術 |
|---|---|
| サーバー | PHP 7.3 / FuelPHP 1.8 |
| データベース | MySQL 8.0（InnoDB、utf8mb4） |
| フロントエンド | knockout.js 3.5.3 |
| 開発環境 | Docker（Apache + PHP、MySQL） |

## 起動方法
1. **リポジトリをクローン**
   ```bash
   git clone <リポジトリURL>
   ```

2. **dockerディレクトリに移動**
   ```bash
   cd docker
   ```

3. **Dockerイメージのビルド**
   ```bash
   docker-compose build
   ```

4. **コンテナの起動**
   ```bash
   docker-compose up -d
   ```

5. **テーブルの作成（マイグレーション）**

   `fuel/app/migrations/` のマイグレーションを実行し、users・categories・items の3つのテーブルを作る。
   ```bash
   docker exec -w /var/www/html/my_fuel_project fuelphp-app php oil refine migrate
   ```

## ディレクトリ構成（主なもの）

```
fuel/app/
├── classes/
│   ├── controller/    コントローラー（Controller_Base が全画面共通のログイン確認など）
│   ├── model/         モデル（Model\User・Model\Category・Model\Item）
│   └── itemimage.php  画像の確認・保存・削除
├── config/
│   └── wishlist.php   このアプリ独自の設定（星の段階数、文字数の上限、画像の上限など）
├── migrations/        テーブルを作るマイグレーション
├── views/             画面のテンプレート
└── uploads/items/     アップロードされた画像（Git には含めない）
public/assets/
├── css/style.css
└── js/                knockout.js と画面ごとのJS（sidebar.js・item_list.js・item_form.js）
```

## ライセンス

他者が作成したアイコンを使用しています。出どころとライセンスは [THIRD_PARTY_LICENSES.md](THIRD_PARTY_LICENSES.md) を参照してください。
