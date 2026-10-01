<?php

use Fuel\Core\Controller_Template;

/**
 * ログイン・ユーザー登録・ログアウトを扱うコントローラー
 */
class Controller_Auth extends Controller_Template
{
    protected $require_login = false;
    public function action_login()
    {
        $this->template->title = 'ログイン';
        $this->template->content = View::forge('auth/login');
    }

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
                $val->add_field('name', 'ユーザー名', 'required|trim|max_length[50]');
                $val->add_field('email', 'メールアドレス', 'required|trim|valid_email|max_length[255]');
                $val->add_field('password', 'パスワード', 'required|min_length[8]|max_length[72]');

                $val->set_message('required', ':labelを入力してください');
                $val->set_message('max_length', ':labelは:param:1文字以内で入力してください');
                $val->set_message('min_length', ':labelは:param:1文字以上で入力してください');
                $val->set_message('valid_email', ':labelの形式が正しくありません');

                if ($val->run()) {
                    // ここに3・4（保存とログイン）を書く。今は確認用の仮の表示
                    $errors['form'] = '入力チェックOK（保存はまだ）';
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
        ));
    }
}
