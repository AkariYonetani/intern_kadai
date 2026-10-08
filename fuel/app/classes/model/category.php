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
     /**
     * 新しいカテゴリを作成する
     *
     * @param int $user_id
     * @param string $name 空白除去・バリデーション済みの名前
     * @return int 追加したカテゴリのID
     */
    public static function create($user_id, $name)
    {
        $now = date('Y-m-d H:i:s');

        list($id, $rows) = \DB::insert('categories')
            ->set(array(
                'user_id'    => $user_id,
                'name'       => $name,
                'is_default' => 0,
                'created_at' => $now,
                'updated_at' => $now,
            ))
            ->execute();

        return $id;
    }
     /**
     * 同名チェック
     *
     * @param int $user_id
     * @param string $name 空白除去・バリデーション済みの名前
     * @return bool
     */
    public static function exists_by_user_id_and_name($user_id,$name)
    {
        $result = \DB::select(\DB::expr('COUNT(*) AS cnt'))
            ->from('categories')
            ->where('user_id', '=',$user_id)
            ->where('name', '=',$name)
            ->execute()
            ->current();
        return $result['cnt'] > 0;
    }
}
