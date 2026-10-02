<?php

/**
 * 全コントローラー共通の処理
 */
abstract class Controller_Base extends Controller_Template
{
    /**
     * ログインが必要なコントローラーかどうか
     */
    protected $require_login = true;

    public function before()
    {
        parent::before();

        if ($this->require_login && ! Session::get('user_id')) {
            Response::redirect('auth/login');
        }
        if (Session::get('user_id')) {
            $this->template->header = View::forge('parts/header');
        }
    }
}
