<?php

/**
 * メイン画面
 */
class Controller_Main extends Controller_Base
{
    public function action_index($category_id = null)
    {
        $user_id = Session::get('user_id');
        if ($category_id === null) {
            $category = \Model\Category::find_default_by_user_id($user_id);
        } else {
            $category = \Model\Category::find_by_id_and_user_id($category_id, $user_id);
            if ($category === null) {
                Response::redirect('main/index');
            }
        }
        $this->template->title = 'ホーム';
        $this->template->content = View::forge('main/index');
    }
}
