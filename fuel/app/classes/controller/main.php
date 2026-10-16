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

        // 次回ログインしたときに開けるよう、このカテゴリのIDを cookie に記憶する
        Cookie::set(
            Config::get('wishlist.last_category_cookie.name'),
            $category['id'],
            Config::get('wishlist.last_category_cookie.expiration'),
            null,  // path（config.php の設定どおり）
            null,  // domain（同上）
            false, // secure（開発環境は http のため）
            true   // http_only（JSから読めないようにする）
        );

        // 一覧の表示は knockout で行うので、データをJSONにして渡す
        $items = \Model\Item::find_by_category_id($category['id']);
        $this->template->title = 'ホーム';
        $this->template->page_js = 'item_list.js';
        $this->template->content = View::forge('main/index', array(
            'category'   => $category,
            'items_json' => json_encode($items),
        ));
    }

    /**
     * カテゴリ削除（ポップアップのフォームから送信される）
     *
     * mode=move       … 欲しいものを未分類に移してから、カテゴリを削除する
     * mode=delete_all … 欲しいものも削除してから、カテゴリを削除する
     *
     * @param string|null $id URLで指定されたカテゴリID
     */
    public function post_delete_category($id = null)
    {
        if (! Security::check_token()) {
            Session::set_flash('error', 'ページの有効期限が切れました。もう一度お試しください');
            Response::redirect('main/index');
        }
        $user_id = Session::get('user_id');

        // 本人のカテゴリで、未分類でないものだけ削除できる
        $category = \Model\Category::find_by_id_and_user_id($id, $user_id);
        if ($category === null || $category['is_default'] == 1) {
            Session::set_flash('error', 'このカテゴリは削除できません');
            Response::redirect('main/index');
        }

        $mode = Input::post('mode');
        if ($mode !== 'move' && $mode !== 'delete_all') {
            Session::set_flash('error', '削除の方法を選んでください');
            Response::redirect('main/index');
        }

        // 欲しいものの移動（または削除）とカテゴリの削除を、まとめて成功させるか、まとめて取り消す
        $image_paths = array();
        \DB::start_transaction();
        try {
            if ($mode === 'move') {
                $default = \Model\Category::find_default_by_user_id($user_id);
                \Model\Item::move_all_to_category($category['id'], $default['id']);
            } else {
                // 行を消すとファイル名が分からなくなるので、先に取っておく
                $image_paths = \Model\Item::find_image_paths_by_category_id($category['id']);
                \Model\Item::delete_by_category_id($category['id']);
            }
            if (\Model\Category::delete_by_id_and_user_id($category['id'], $user_id) !== 1) {
                throw new \Exception('カテゴリを削除できませんでした');
            }
            \DB::commit_transaction();
        } catch (\Exception $e) {
            \DB::rollback_transaction();
            Session::set_flash('error', '削除に失敗しました。もう一度お試しください');
            Response::redirect('main/index');
        }

        // 画像ファイルは、DBの削除が確定（コミット）してから消す。先に消すと、DBが取り消しになったときに画像だけ失われる
        foreach ($image_paths as $image_path) {
            ItemImage::delete($image_path);
        }

        Session::set_flash('success', '『' . $category['name'] . '』を削除しました');
        Response::redirect('main/index');
    }
}
