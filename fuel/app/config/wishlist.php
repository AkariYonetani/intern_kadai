<?php

/**
 * 欲しいものリストの独自設定
 *
 * 読み込み：Config::load('wishlist', true)
 * 取得　　：Config::get('wishlist.desire_level.max')
 */
return array(
    // 欲しい度合い（星）
    'desire_level' => array(
        'max'     => 5, // 星の段階数
        'default' => 3, // 登録画面での初期値
    ),

    // 文字数の上限は、DB設計の列の長さ（varchar の文字数）に合わせる
    // サーバーのバリデーションと、画面の入力欄の maxlength の両方がこの値を使う

    // 欲しいもの
    'item' => array(
        'price_max'  => 99999999, // 金額の上限（円）
        'name_max'   => 100,      // アイテム名（items.name varchar(100)）
        'reason_max' => 500,      // 理由（items.reason varchar(500)）
        'url_max'    => 2048,     // URL（items.url varchar(2048)）
    ),

    // カテゴリ
    'category' => array(
        'name_max' => 50, // カテゴリ名（categories.name varchar(50)）
    ),

    // ユーザー
    'user' => array(
        'name_max'     => 50,  // 名前（users.name varchar(50)）
        'email_max'    => 255, // メールアドレス（users.email varchar(255)）
        'password_min' => 8,   // パスワードの最低文字数
        'password_max' => 72,  // password_hash（bcrypt）が扱えるのは72バイトまでのため
    ),

    // 欲しいものの画像
    'image' => array(
        'max_size'   => 2 * 1024 * 1024, // サイズの上限（バイト）：2MB
        // ファイル名の拡張子（利用者が付けたもの）として許可するもの
        'extensions' => array('jpg', 'jpeg', 'png'),
        // ファイルの中身から判定した形式（MIMEタイプ） => 保存するときの拡張子
        'types'      => array(
            'image/jpeg' => 'jpg',
            'image/png'  => 'png',
        ),
        // 保存先。public/ の外なので、URLで直接は開けない（表示は api/item/image/{ID} を通す）
        'dir'        => APPPATH . 'uploads' . DS . 'items' . DS,
    ),

    // 最後に開いたカテゴリを記憶する cookie
    'last_category_cookie' => array(
        'name'       => 'last_category_id',
        'expiration' => 60 * 60 * 24 * 30, // 有効期限（秒）：30日
    ),
);
