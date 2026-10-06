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
        if ($this->require_login) {
            $user_id = Session::get('user_id');
            $categories = \Model\Category::find_by_user_id($user_id);
            $categories_json = json_encode($categories);
            $this->template->header = View::forge('parts/header');
            $this->template->sidebar = View::forge('parts/sidebar', array(
                'categories'      => $categories,
                'categories_json' => $categories_json,
            ));
        }
    }
}
