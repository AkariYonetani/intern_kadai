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
}
