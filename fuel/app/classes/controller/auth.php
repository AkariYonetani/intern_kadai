<?php

/**
 * ログイン・ユーザー登録・ログアウトを扱うコントローラー
 */
class Controller_Auth extends Controller
{
    public function action_login()
    {
        return Response::forge(View::forge('auth/login'));
    }
}
