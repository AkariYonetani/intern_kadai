<?php

namespace Model;

/**
 * categoriesテーブルを扱うモデル
 */
class Category
{
    /**
     * カテゴリーを取得する(サイドバー用)
     *
     * @param int $user_id ユーザーID
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

    /**
     * 本人のカテゴリを1件取得する(アイテム一覧表示用)
     *
     * @param int $id カテゴリID
     * @param int $user_id ユーザーID
     * @return array|null
     */
    public static function find_by_id_and_user_id($id, $user_id)
    {
        $result = \DB::select('id', 'name', 'is_default')
            ->from('categories')
            ->where('id', '=', $id)
            ->where('user_id', '=', $user_id)
            ->execute()
            ->current();
        return $result;
    }

    /**
     * ユーザーの未分類カテゴリを取得する
     *
     * @param int $user_id ユーザーID
     * @return array|null
     */
    public static function find_default_by_user_id($user_id)
    {
        $result = \DB::select('id', 'name', 'is_default')
            ->from('categories')
            ->where('user_id', '=', $user_id)
            ->where('is_default', '=', 1)
            ->execute()
            ->current();
        return $result;
    }
}
