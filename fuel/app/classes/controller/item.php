<?php

/**
 * 欲しいものの登録・詳細画面
 */
class Controller_Item extends Controller_Base
{
    public function before()
    {
        parent::before();

        // 独自config（星の段階数・金額の上限など）を読み込む
        Config::load('wishlist', true);
    }

    /**
     * 欲しいもの登録（表示と登録の両方を受ける）
     *
     * @param string|null $category_id URLで指定されたカテゴリID（カテゴリの初期値になる）
     */
    public function action_create($category_id = null)
    {
        $user_id = Session::get('user_id');

        // カテゴリの初期値：URLのカテゴリが本人のものならそれ、違えば未分類
        $category = null;
        if ($category_id !== null) {
            $category = \Model\Category::find_by_id_and_user_id($category_id, $user_id);
        }
        if ($category === null) {
            $category = \Model\Category::find_default_by_user_id($user_id);
        }

        $input = array(
            'name'         => '',
            'category_id'  => $category['id'],
            'desire_level' => Config::get('wishlist.desire_level.default'),
            'reason'       => '',
            'url'          => '',
            'price'        => '',
        );
        $errors = array();

        if (Input::method() === 'POST') {
            $input = array(
                'name'         => static::trim_space(Input::post('name', '')),
                'category_id'  => Input::post('category_id', ''),
                'desire_level' => Input::post('desire_level', ''),
                'reason'       => static::trim_space(Input::post('reason', '')),
                'url'          => static::trim_space(Input::post('url', '')),
                // 全角数字は半角に、カンマは取り除く（「１，０００」→「1000」）
                'price'        => str_replace(array(',', '，'), '', mb_convert_kana(static::trim_space(Input::post('price', '')), 'n')),
            );

            if (! Security::check_token()) {
                $errors['form'] = 'ページの有効期限が切れました。もう一度送信してください。';
            } else {
                $errors = $this->validate($input);

                // カテゴリ：未選択なら未分類、選ばれていれば本人のものか確かめる
                if ($input['category_id'] === '') {
                    $selected = \Model\Category::find_default_by_user_id($user_id);
                } else {
                    $selected = \Model\Category::find_by_id_and_user_id($input['category_id'], $user_id);
                    if ($selected === null) {
                        $errors['category_id'] = 'カテゴリを選び直してください';
                    }
                }

                if (empty($errors)) {
                    \Model\Item::create(array(
                        'category_id'  => $selected['id'],
                        'name'         => $input['name'],
                        'desire_level' => (int) $input['desire_level'],
                        'reason'       => $input['reason'] === '' ? null : $input['reason'],
                        'url'          => $input['url'] === '' ? null : $input['url'],
                        'price'        => $input['price'] === '' ? null : (int) $input['price'],
                    ));

                    Session::set_flash('success', '『' . $input['name'] . '』を登録しました');
                    Response::redirect('main/index/' . $selected['id']);
                }
            }
        }

        $this->template->title = '欲しいもの登録';
        $this->template->page_js = 'item_form.js';
        $this->template->content = View::forge('item/create', array(
            'input'       => $input,
            'errors'      => $errors,
            'category'    => $category,
            'desire_max'  => Config::get('wishlist.desire_level.max'),
        ));
    }

    /**
     * 欲しいもの詳細
     *
     * @param string|null $id URLで指定された欲しいもののID
     */
    public function action_detail($id = null)
    {
        $user_id = Session::get('user_id');

        // 本人の欲しいものか確かめる。他人のもの・存在しないIDは同じ扱いにする
        $item = \Model\Item::find_by_id_and_user_id($id, $user_id);
        if ($item === null) {
            Session::set_flash('error', '欲しいものが見つかりません');
            Response::redirect('main/index');
        }

        // 保存時にも確認しているが、DBに直接入ったデータに備えて、表示時にも http(s):// で始まるか確かめる
        $url_is_link = $item['url'] !== null && preg_match('/\Ahttps?:\/\//i', $item['url']) === 1;

        $this->template->title = $item['name'];
        $this->template->content = View::forge('item/detail', array(
            'item'        => $item,
            'url_is_link' => $url_is_link,
            'desire_max'  => Config::get('wishlist.desire_level.max'),
        ));
    }

    /**
     * 入力チェック
     *
     * @param array $input 空白除去済みの入力
     * @return array 項目名 => エラーメッセージ
     */
    protected function validate(array $input)
    {
        $desire_max = Config::get('wishlist.desire_level.max');
        $price_max = Config::get('wishlist.item.price_max');

        $val = Validation::forge();
        $val->add_field('name', 'アイテム名', 'required|max_length[100]');
        $val->add_field('desire_level', '欲しい度合い', 'required|valid_string[numeric]|numeric_between[1,' . $desire_max . ']');
        $val->add_field('reason', '理由', 'max_length[500]');
        // URLは http:// か https:// で始まるものだけ（javascript: などはエスケープでは防げないため）
        $val->add_field('url', 'URL', 'max_length[2048]')
            ->add_rule('match_pattern', '/\Ahttps?:\/\/\S+\z/i');
        $val->add_field('price', '金額', 'valid_string[numeric]|numeric_between[0,' . $price_max . ']');

        $val->set_message('required', ':labelを入力してください');
        $val->set_message('max_length', ':labelは:param:1文字以内で入力してください');
        $val->set_message('valid_string', ':labelは数字で入力してください');
        $val->set_message('numeric_between', ':labelは:param:1〜:param:2の範囲で入力してください');
        $val->set_message('match_pattern', ':labelは http:// または https:// で始まる形式で入力してください');

        $errors = array();
        if (! $val->run($input)) {
            foreach ($val->error() as $field => $error) {
                $errors[$field] = $error->get_message();
            }
        }
        return $errors;
    }

    /**
     * 前後の空白（全角スペースを含む）を取り除く
     *
     * @param string $value
     * @return string
     */
    protected static function trim_space($value)
    {
        return preg_replace('/\A[\s　]+|[\s　]+\z/u', '', $value);
    }
}
