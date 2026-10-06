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
        return $this->response(array('ok' => true));
    }
}
