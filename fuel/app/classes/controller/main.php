<?php

/**
 * メイン画面
 */
class Controller_Main extends Controller_Base
{
    /**
     * メイン画面（左：カテゴリ一覧、右：選択中カテゴリの欲しいもの一覧）
     *
     * @param string|null $category_id URLで指定されたカテゴリID（なければ未分類を表示）
     */
    public function action_index($category_id = null)
    {
        $user_id = Session::get('user_id');

        // 表示するカテゴリを決める
        if ($category_id === null) {
            $category = \Model\Category::find_default_by_user_id($user_id);
        } else {
            // URLのIDは書き換えられるため、本人のカテゴリかを確かめる
            $category = \Model\Category::find_by_id_and_user_id($category_id, $user_id);

            // 他人のカテゴリ・存在しないIDのときは未分類に戻す
            if ($category === null) {
                Response::redirect('main/index');
            }
        }

        $items = \Model\Item::find_by_category_id($category['id']);
        $this->template->title = 'ホーム';
        $this->template->content = View::forge('main/index', array(
            'category' => $category,
            'items'    => $items,
        ));
    }
}
