<?php

use Fuel\Core\Controller_Template;

/**
 * ログイン・ユーザー登録・ログアウトを扱うコントローラー
 */
class Controller_Auth extends Controller_Template
{
    public function action_login()
    {
        $this->template->title = 'ログイン';
        $this->template->content = View::forge('auth/login');
    }

    public function action_register()
    {
        $this->template->title = 'ユーザー登録';
        $this->template->content = View::forge('auth/register');
    }
}
