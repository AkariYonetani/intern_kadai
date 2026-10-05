<?php

namespace Model;

/**
 * categoriesテーブルを扱うモデル
 */
class Category
{
    /**
     * カテゴリーを取得する
     *
     * @return array
     */
    public static function find_by_user_id($user_id)
    {
        $result = \DB::select('categories.id', 'categories.name', 'categories.is_default', \DB::expr('COUNT(items.id) AS item_count'))
            ->from('categories')
            ->join('items', 'LEFT')
            ->on('items.category_id', '=', 'categories.id')
            ->where('categories.user_id', '=', $user_id)
            ->group_by('categories.id')
            ->order_by('categories.is_default', 'DESC')
            ->order_by('categories.id', 'ASC')
            ->execute()
            ->as_array();
        return $result;
    }
}
