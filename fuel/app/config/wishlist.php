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

    // 欲しいもの
    'item' => array(
        'price_max' => 99999999, // 金額の上限（円）
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
