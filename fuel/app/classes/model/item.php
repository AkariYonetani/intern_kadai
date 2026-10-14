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
