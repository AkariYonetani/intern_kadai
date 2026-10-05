<?php

/**
 * メイン画面
 */
class Controller_Main extends Controller_Base
{
    public function action_index()
    {
        $user_id = Session::get('user_id');
        $categories = \Model\Category::find_by_user_id($user_id);

        $this->template->title = 'ホーム';
        $this->template->content = View::forge('main/index');
    }
}
