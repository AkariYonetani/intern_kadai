<?php

/**
 * ログイン・ユーザー登録・ログアウトを扱うコントローラー
 */
class Controller_Auth extends Controller_Base
{
    protected $require_login = false;
    public $template = 'template_guest';
    public function before()
    {
        parent::before();

        // ログイン済みならメイン画面へ（ログアウトは除く）
        if (Session::get('user_id') && $this->request->action !== 'logout') {
            Response::redirect('main/index');
        }
    }

    // ログイン
    public function action_login()
    {
        $input  = array('email' => '');
        $errors = array();

        if (Input::method() === 'POST') {
            $input = array(
                'email' => Input::post('email', ''),
            );

            if (! Security::check_token()) {
                $errors['form'] = '画面の有効期限が切れました。もう一度送信してください。';
            } else {
                $val = Validation::forge();
                $val->add_field('email', 'メールアドレス', 'required|trim|valid_email');
                $val->add_field('password', 'パスワード', 'required');

                $val->set_message('required', ':labelを入力してください');
                $val->set_message('valid_email', ':labelの形式が正しくありません');


                if ($val->run()) {
                    $email    = $val->validated('email');
                    $password = $val->validated('password');

                    $user = Model\User::find_by_email($email);
                    if ($user === null || !password_verify($password, $user['password'])) {
                        $errors['form'] = 'メールアドレスまたはパスワードが違います';
                    } else {
                        Session::rotate();
                        Session::set('user_id', $user['id']);

                        // 最後に開いたカテゴリへ。本人のものかは main/index 側で確かめる
                        $last_id = (int) Cookie::get(Config::get('wishlist.last_category_cookie.name'));
                        if ($last_id > 0) {
                            Response::redirect('main/index/' . $last_id);
                        }
                        Response::redirect('main/index');
                    }
                } else {
                    foreach ($val->error() as $field => $error) {
                        $errors[$field] = $error->get_message();
                    }
                }
            }
        }
        $this->template->title = 'ログイン';
        $this->template->content = View::forge('auth/login', array(
            'input'  => $input,
            'errors' => $errors,
            'limits' => Config::get('wishlist.user'), // 入力欄の maxlength に使う
        ));
    }

    // ユーザー登録
    public function action_register()
    {
        $input  = array('name' => '', 'email' => '');
        $errors = array();

        if (Input::method() === 'POST') {
            $input = array(
                'name'  => Input::post('name', ''),
                'email' => Input::post('email', ''),
            );

            if (! Security::check_token()) {
                $errors['form'] = '画面の有効期限が切れました。もう一度送信してください。';
            } else {
                $val = Validation::forge();
                // 文字数の上限は独自config（wishlist.user）から取る。画面の maxlength と同じ値になる
                $limits = Config::get('wishlist.user');
                $val->add_field('name', '名前', 'required|trim|max_length[' . $limits['name_max'] . ']');
                $val->add_field('email', 'メールアドレス', 'required|trim|valid_email|max_length[' . $limits['email_max'] . ']');
                $val->add_field('password', 'パスワード', 'required|min_length[' . $limits['password_min'] . ']|max_length[' . $limits['password_max'] . ']');

                $val->set_message('required', ':labelを入力してください');
                $val->set_message('max_length', ':labelは:param:1文字以内で入力してください');
                $val->set_message('min_length', ':labelは:param:1文字以上で入力してください');
                $val->set_message('valid_email', ':labelの形式が正しくありません');

                if ($val->run()) {
                    $name     = $val->validated('name');
                    $email    = $val->validated('email');
                    $password = $val->validated('password');

                    if (Model\User::email_exists($email)) {
                        $errors['email'] = 'このメールアドレスはすでに登録されています';
                    } else {
                        $user_id = Model\User::create($name, $email, $password);

                        Session::rotate();
                        Session::set('user_id', $user_id);

                        Response::redirect('main/index');
                    }
                } else {
                    foreach ($val->error() as $field => $error) {
                        $errors[$field] = $error->get_message();
                    }
                }
            }
        }

        $this->template->title   = 'ユーザー登録';
        $this->template->content = View::forge('auth/register', array(
            'input'  => $input,
            'errors' => $errors,
            'limits' => Config::get('wishlist.user'), // 入力欄の maxlength と、パスワードの条件の文に使う
        ));
    }

    // ログアウト
    public function post_logout()
    {
        if (! Security::check_token()) {
            Response::redirect('main/index');
        }

        Session::delete('user_id');
        Response::redirect('auth/login');
    }
}
