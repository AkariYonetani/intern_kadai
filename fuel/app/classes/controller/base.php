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

        // 独自config（星の段階数・金額の上限・cookieの設定など）を読み込む
        Config::load('wishlist', true);

        if ($this->require_login && ! Session::get('user_id')) {
            Response::redirect('auth/login');
        }
        if ($this->require_login) {
            $user_id = Session::get('user_id');
            $categories = \Model\Category::find_by_user_id($user_id);
            $categories_json = json_encode($categories);
            $this->template->header = View::forge('parts/header');
            $this->template->sidebar = View::forge('parts/sidebar', array(
                'categories_json' => $categories_json,
            ));
            // 前の画面で Session::set_flash() したメッセージ（1回読むと消える）
            $this->template->flash_success = Session::get_flash('success');
            $this->template->flash_error = Session::get_flash('error');
        }
    }
}
