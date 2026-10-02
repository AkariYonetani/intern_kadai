<?php

/**
 * メイン画面
 */
class Controller_Main extends Controller_Base
{
    public function action_index()
    {
        $this->template->title = 'ホーム';
        $this->template->content = View::forge('main/index');
    }
}
