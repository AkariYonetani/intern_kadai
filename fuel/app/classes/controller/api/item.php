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

        // DBの行を消してから、画像ファイルを消す（行を消しても、ファイルは自動では消えない）
        if (Model\Item::delete_by_id_and_user_id($item['id'], $user_id) === 1) {
            ItemImage::delete($item['image_path']);
        }

        // category_id は、画面でサイドバーの件数を減らすために返す
        return $this->response(array(
            'id'          => $item['id'],
            'category_id' => $item['category_id'],
            'token'       => Security::fetch_token(),
        ), 200);
    }

    /**
     * 欲しいものの画像を返す（<img src="api/item/image/{ID}"> で使う）
     *
     * 画像は public/ の外に置いているので、URLで直接は開けない。ここで本人のものか確かめてから返す
     * Controller_Item ではなくここに置くのは、Controller_Base の before() がサイドバーの取得やフラッシュメッセージの読み出しをするため（画像1枚ごとに不要）
     *
     * @param string|null $id URLで指定された欲しいもののID
     */
    public function get_image($id = null)
    {
        $user_id = Session::get('user_id');

        // 他人のもの・存在しないID・画像のないものは、すべて同じ 404 にする
        $item = Model\Item::find_by_id_and_user_id($id, $user_id);
        $path = $item === null ? null : ItemImage::path($item['image_path']);
        if ($path === null || ! is_file($path)) {
            return Response::forge('', 404);
        }

        return Response::forge(file_get_contents($path), 200, array(
            'Content-Type'           => ItemImage::mime_type($item['image_path']),
            // Content-Type のとおりに扱わせる（ブラウザが中身から別の形式と推測して、HTMLとして動かすのを防ぐ）
            'X-Content-Type-Options' => 'nosniff',
            // 本人の画像なので、共有のキャッシュ（プロキシなど）には残さない
            'Cache-Control'          => 'private, max-age=3600',
        ));
    }
}
