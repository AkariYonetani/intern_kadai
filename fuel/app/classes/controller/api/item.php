<?php

/**
 * 欲しいもののAjax処理
 */
class Controller_Api_Item extends Controller_Api
{
    /**
     * 欲しいもの削除
     *
     * @param string|null $id URLで指定された欲しいもののID
     */
    public function post_delete($id = null)
    {
        if (!Security::check_token()) {
            return $this->response(array('error' => 'ページを再読み込みしてからもう一度お試しください'), 403);
        }
        $user_id = Session::get('user_id');

        // 本人の欲しいものか確かめる。他人のもの・存在しないIDは同じ返し方にする
        $item = Model\Item::find_by_id_and_user_id($id, $user_id);
        if ($item === null) {
            return $this->response(array(
                'error' => '欲しいものが見つかりません',
                'token' => Security::fetch_token(),
            ), 404);
        }

        Model\Item::delete_by_id_and_user_id($item['id'], $user_id);

        // category_id は、画面でサイドバーの件数を減らすために返す
        return $this->response(array(
            'id'          => $item['id'],
            'category_id' => $item['category_id'],
            'token'       => Security::fetch_token(),
        ), 200);
    }
}
