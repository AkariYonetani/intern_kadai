<?php

namespace Model;

/**
 * itemsテーブルを扱うモデル
 */
class Item
{
    /**
     * カテゴリに入っている欲しいものを、新しい順に取得する
     *
     * @param int $category_id カテゴリID
     * @return array
     */
    public static function find_by_category_id($category_id)
    {
        $result = \DB::select('id', 'name', 'desire_level', 'price', 'image_path')
            ->from('items')
            ->where('category_id', '=', $category_id)
            ->order_by('created_at', 'DESC')
            ->order_by('id', 'DESC')
            ->execute()
            ->as_array();
        return $result;
    }

    /**
     * 本人の欲しいものを1件取得する（詳細表示用）
     *
     * items には user_id がないので、categories をたどって持ち主を確かめる
     *
     * @param int $id 欲しいもののID
     * @param int $user_id ユーザーID
     * @return array|null 欲しいもの（カテゴリ名 category_name を含む）。他人のもの・存在しないIDは null
     */
    public static function find_by_id_and_user_id($id, $user_id)
    {
        $result = \DB::select(
                'items.id',
                'items.category_id',
                'items.name',
                'items.desire_level',
                'items.reason',
                'items.url',
                'items.price',
                array('categories.name', 'category_name')
            )
            ->from('items')
            ->join('categories', 'INNER')
            ->on('categories.id', '=', 'items.category_id')
            ->where('items.id', '=', $id)
            ->where('categories.user_id', '=', $user_id)
            ->execute()
            ->current();
        return $result;
    }

    /**
     * 欲しいものを登録する
     *
     * @param array $values category_id, name, desire_level, reason, url, price（バリデーション済み。任意項目の未入力は null）
     * @return int 追加した欲しいもののID
     */
    public static function create(array $values)
    {
        $now = date('Y-m-d H:i:s');

        list($id, $rows) = \DB::insert('items')
            ->set(array(
                'category_id'  => $values['category_id'],
                'name'         => $values['name'],
                'desire_level' => $values['desire_level'],
                'reason'       => $values['reason'],
                'url'          => $values['url'],
                'price'        => $values['price'],
                'created_at'   => $now,
                'updated_at'   => $now,
            ))
            ->execute();

        return $id;
    }

    /**
     * カテゴリに入っている欲しいものを、すべて別のカテゴリへ移す（カテゴリ削除の「未分類に移動」用）
     *
     * @param int $from_category_id 移動元のカテゴリID
     * @param int $to_category_id 移動先のカテゴリID
     * @return int 移動した件数
     */
    public static function move_all_to_category($from_category_id, $to_category_id)
    {
        return \DB::update('items')
            ->set(array(
                'category_id' => $to_category_id,
                'updated_at'  => date('Y-m-d H:i:s'),
            ))
            ->where('category_id', '=', $from_category_id)
            ->execute();
    }

    /**
     * カテゴリに入っている欲しいものを、すべて削除する（カテゴリ削除の「すべて削除」用）
     *
     * @param int $category_id カテゴリID
     * @return int 削除した件数
     */
    public static function delete_by_category_id($category_id)
    {
        return \DB::delete('items')
            ->where('category_id', '=', $category_id)
            ->execute();
    }
}
