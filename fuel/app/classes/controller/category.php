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
        $val->set_message('required', ':labelを入力してください');
        $val->set_message('max_length', ':labelは:param:1文字以内で入力してください');
        if (!$val->run(array('name' => $name))) {
            return $this->response(array(
                'error' => $val->error('name')->get_message(),
                'token' => Security::fetch_token(),
            ), 422);
        }

        if (Model\Category::exists_by_user_id_and_name($user_id, $name)) {
            return $this->response(array(
                'error' => '同じ名前のカテゴリがあります',
                'token' => Security::fetch_token(),
            ), 422);
        }
        $id = Model\Category::create($user_id, $name);

        return $this->response(array(
            'id'         => $id,
            'name'       => $name,
            'is_default' => '0',
            'item_count' => 0,
            'token'      => Security::fetch_token(),
        ), 201);
    }
}
