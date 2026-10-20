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
        // 文字数の上限は独自config（wishlist.category）から取る。サイドバーの入力欄の maxlength と同じ値になる
        $val->add_field('name', 'カテゴリ名', 'required|max_length[' . Config::get('wishlist.category.name_max') . ']');
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

    /**
     * カテゴリ名の変更
     *
     * @param string|null $id URLで指定されたカテゴリID
     */
    public function post_update($id = null)
    {
        if (!Security::check_token()) {
            return $this->response(array('error' => 'ページを再読み込みしてからもう一度お試しください'), 403);
        }
        $user_id = Session::get('user_id');

        // 本人のカテゴリか確認する。他人のカテゴリと存在しないIDは同じ返し方にして、存在を知られないようにする
        $category = Model\Category::find_by_id_and_user_id($id, $user_id);
        if ($category === null) {
            return $this->response(array(
                'error' => 'カテゴリが見つかりません',
                'token' => Security::fetch_token(),
            ), 404);
        }
        if ($category['is_default'] == 1) {
            return $this->response(array(
                'error' => '未分類の名前は変更できません',
                'token' => Security::fetch_token(),
            ), 403);
        }

        $name = preg_replace('/\A[\s　]+|[\s　]+\z/u', '', Input::post('name', ''));
        $val = Validation::forge();
        $val->add_field('name', 'カテゴリ名', 'required|max_length[' . Config::get('wishlist.category.name_max') . ']');
        $val->set_message('required', ':labelを入力してください');
        $val->set_message('max_length', ':labelは:param:1文字以内で入力してください');
        if (!$val->run(array('name' => $name))) {
            return $this->response(array(
                'error' => $val->error('name')->get_message(),
                'token' => Security::fetch_token(),
            ), 422);
        }

        if (Model\Category::exists_by_user_id_and_name($user_id, $name, $category['id'])) {
            return $this->response(array(
                'error' => '同じ名前のカテゴリがあります',
                'token' => Security::fetch_token(),
            ), 422);
        }
        Model\Category::update_name($category['id'], $user_id, $name);

        return $this->response(array(
            'id'    => $category['id'],
            'name'  => $name,
            'token' => Security::fetch_token(),
        ), 200);
    }
}
