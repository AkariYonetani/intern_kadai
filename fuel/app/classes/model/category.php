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
     * @param int|null $exclude_id チェックから外すカテゴリID（編集時に自分自身を除くため）
     * @return bool
     */
    public static function exists_by_user_id_and_name($user_id, $name, $exclude_id = null)
    {
        $query = \DB::select(\DB::expr('COUNT(*) AS cnt'))
            ->from('categories')
            ->where('user_id', '=', $user_id)
            ->where('name', '=', $name);
        if ($exclude_id !== null) {
            $query->where('id', '!=', $exclude_id);
        }
        $result = $query->execute()->current();
        return $result['cnt'] > 0;
    }

    /**
     * カテゴリ名を変更する
     *
     * 本人のカテゴリで、未分類でないものだけを更新する（コントローラーでも確認済みだが、念のため条件に含める）
     *
     * @param int $id カテゴリID
     * @param int $user_id ユーザーID
     * @param string $name 空白除去・バリデーション済みの名前
     * @return int 更新した行数
     */
    public static function update_name($id, $user_id, $name)
    {
        return \DB::update('categories')
            ->set(array(
                'name'       => $name,
                'updated_at' => date('Y-m-d H:i:s'),
            ))
            ->where('id', '=', $id)
            ->where('user_id', '=', $user_id)
            ->where('is_default', '=', 0)
            ->execute();
    }

    /**
     * カテゴリを削除する
     *
     * 中身の欲しいものは、先に移動または削除しておくこと（外部キーにより、中身が残っていると削除できない）
     *
     * @param int $id カテゴリID
     * @param int $user_id ユーザーID
     * @return int 削除した行数
     */
    public static function delete_by_id_and_user_id($id, $user_id)
    {
        return \DB::delete('categories')
            ->where('id', '=', $id)
            ->where('user_id', '=', $user_id)
            ->where('is_default', '=', 0)
            ->execute();
    }
}
