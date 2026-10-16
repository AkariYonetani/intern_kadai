<?php

namespace Model;

/**
 * usersテーブルを扱うモデル
 */
class User
{
    /**
     * メールアドレスが登録済みかどうか
     *
     * @return bool
     */
    public static function email_exists($email)
    {
        $result = \DB::select(\DB::expr('COUNT(*) AS cnt'))
            ->from('users')
            ->where('email', '=', $email)
            ->execute()
            ->current();

        return $result['cnt'] > 0;
    }

    /**
     * メールアドレスでユーザーを探す
     *
     * @return array|null
     *見つかったらidとpasswordの配列、見つからなければnull
     */
    public static function find_by_email($email)
    {
        $result = \DB::select('id', 'password')
            ->from('users')
            ->where('email', '=', $email)
            ->execute()
            ->current();

        return $result;
    }

    /**
     * IDでユーザーを探す（ヘッダーにユーザー名を出すため）
     *
     * @param int $id ユーザーID
     * @return array|null 見つかったらidとnameの配列、見つからなければnull
     */
    public static function find_by_id($id)
    {
        $result = \DB::select('id', 'name')
            ->from('users')
            ->where('id', '=', $id)
            ->execute()
            ->current();

        return $result;
    }

    /**
     * ユーザーと「未分類」カテゴリを作成し、新しいユーザーのIDを返す
     *
     * @return int
     */
    public static function create($name, $email, $password)
    {
        \DB::start_transaction();

        try {
            list($user_id,) = \DB::insert('users')
                ->set(array(
                    'name'     => $name,
                    'email'    => $email,
                    'password' => password_hash($password, PASSWORD_DEFAULT),
                ))
                ->execute();

            \DB::insert('categories')
                ->set(array(
                    'user_id'    => $user_id,
                    'name'       => '未分類',
                    'is_default' => 1,
                ))
                ->execute();

            \DB::commit_transaction();

            return $user_id;
        } catch (\Exception $e) {
            \DB::rollback_transaction();
            throw $e;
        }
    }
}
