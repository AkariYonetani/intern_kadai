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

    // 最後に開いたカテゴリを記憶する cookie
    'last_category_cookie' => array(
        'name'       => 'last_category_id',
        'expiration' => 60 * 60 * 24 * 30, // 有効期限（秒）：30日
    ),
);
