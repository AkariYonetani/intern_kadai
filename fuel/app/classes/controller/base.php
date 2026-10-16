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

    /**
     * 応答を返す直前に、セキュリティのためのヘッダーを付ける
     *
     * @param mixed $response アクションの戻り値
     * @return Response
     */
    public function after($response)
    {
        // 親（Controller_Template）が template を Response にしてくれるので、それにヘッダーを足す
        $response = parent::after($response);

        // クリックジャッキング対策：ほかのサイトの iframe の中に、このアプリの画面を表示させない（ログイン画面を含む全画面）
        $response->set_header('X-Frame-Options', 'DENY');

        // 画面をブラウザに保存させない（ログイン画面を含む全画面）
        // ログイン後の画面：ログアウト後に「戻る」で、前の人の画面が表示されないようにする
        // ログイン画面：「戻る」で、前の人が入力したメールアドレスとパスワードが残った画面が表示されないようにする
        $response->set_header('Cache-Control', 'no-store, no-cache, must-revalidate');
        // 古いブラウザ向けの、同じ意味の指示
        $response->set_header('Pragma', 'no-cache');
        $response->set_header('Expires', '0');

        return $response;
    }
}
