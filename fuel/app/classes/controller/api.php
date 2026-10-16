<?php

/**
 * Ajax用コントローラーの共通処理
 */
abstract class Controller_Api extends Controller_Rest
{
    /**
     * 返す形式をJSONに固定する
     */
    protected $format = 'json';

    public function before()
    {
        parent::before();

        if (!Session::get('user_id')) {
            $response = Response::forge(json_encode(array('error' => 'ログインしてください')), 401);
            $response->set_header('Content-Type', 'application/json');
            $response->send(true);
            exit;
        }

        // 独自config（画像の保存先など）を読み込む。このクラスは Controller_Base を継承していないので、ここでも読む
        Config::load('wishlist', true);
    }
}
