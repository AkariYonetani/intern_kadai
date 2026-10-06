<?php

/**
 * カテゴリのAjax処理
 */
class Controller_Category extends Controller_Api
{
    /**
     * カテゴリ作成
     */
    public function post_create()
    {
        if (!Security::check_token()) {
            return $this->response(array('error' => 'ページを再読み込みしてからもう一度お試しください'), 403);
        }
        $user_id = Session::get('user_id');
        $name = preg_replace('/\A[\s　]+|[\s　]+\z/u', '', Input::post('name', ''));
        $val = Validation::forge();
        $val->add_field('name', 'カテゴリ名', 'required|max_length[50]');
        if (!$val->run(array('name' => $name))) {
            return $this->response(array('error' => $val->error('name')->get_message()), 422);
        }

        return $this->response(array('name' => $name));
    }
}
