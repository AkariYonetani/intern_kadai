<?php

/**
 * 欲しいものの登録・詳細・編集画面
 */
class Controller_Item extends Controller_Base
{
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
            $input = $this->read_input();

            if (static::is_post_too_large()) {
                $errors['form'] = static::post_too_large_message();
            } elseif (! Security::check_token()) {
                $errors['form'] = 'ページの有効期限が切れました。もう一度送信してください。';
            } else {
                $errors = $this->validate($input);
                $selected = $this->find_selected_category($input['category_id'], $user_id);
                if ($selected === null) {
                    $errors['category_id'] = 'カテゴリを選び直してください';
                }
                $image = $this->read_image($errors);

                if (empty($errors)) {
                    $values = $this->to_item_values($input, $selected['id']);
                    $values['image_path'] = $image === null ? null : ItemImage::save($image);
                    try {
                        \Model\Item::create($values);
                    } catch (\Exception $e) {
                        // DBに入らなかったので、保存した画像はどこからも使われない。残さずに消す
                        ItemImage::delete($values['image_path']);
                        throw $e;
                    }

                    Session::set_flash('success', '『' . $input['name'] . '』を登録しました');
                    Response::redirect('main/index/' . $selected['id']);
                }
            }
        }

        // サイドバーで、登録先の初期値のカテゴリの行を強調する
        $this->template->sidebar->set('current_category_id', $category['id']);
        $this->template->title = '欲しいもの登録';
        $this->template->page_js = 'item_form.js';
        $this->template->content = View::forge('item/create', array(
            'input'          => $input,
            'errors'         => $errors,
            'category'       => $category,
            'desire_max'     => Config::get('wishlist.desire_level.max'),
            'image_max_size' => Config::get('wishlist.image.max_size'),
            'limits'         => Config::get('wishlist.item'), // 入力欄の maxlength に使う
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

        // サイドバーで、この欲しいものが入っているカテゴリの行を強調する
        $this->template->sidebar->set('current_category_id', $item['category_id']);
        $this->template->title = $item['name'];
        $this->template->content = View::forge('item/detail', array(
            'item'        => $item,
            'url_is_link' => $url_is_link,
            'desire_max'  => Config::get('wishlist.desire_level.max'),
        ));
    }

    /**
     * 欲しいもの編集（表示と保存の両方を受ける）
     *
     * @param string|null $id URLで指定された欲しいもののID
     */
    public function action_edit($id = null)
    {
        $user_id = Session::get('user_id');

        // 本人の欲しいものか確かめる。他人のもの・存在しないIDは同じ扱いにする
        $item = \Model\Item::find_by_id_and_user_id($id, $user_id);
        if ($item === null) {
            Session::set_flash('error', '欲しいものが見つかりません');
            Response::redirect('main/index');
        }

        // 登録済みの値をフォームに入れる（未入力の項目は null なので空文字にする）
        $input = array(
            'name'         => $item['name'],
            'category_id'  => $item['category_id'],
            'desire_level' => $item['desire_level'],
            'reason'       => (string) $item['reason'],
            'url'          => (string) $item['url'],
            'price'        => (string) $item['price'],
        );
        $errors = array();

        if (Input::method() === 'POST') {
            $input = $this->read_input();

            if (static::is_post_too_large()) {
                $errors['form'] = static::post_too_large_message();
            } elseif (! Security::check_token()) {
                $errors['form'] = 'ページの有効期限が切れました。もう一度送信してください。';
            } else {
                $errors = $this->validate($input);
                $selected = $this->find_selected_category($input['category_id'], $user_id);
                if ($selected === null) {
                    $errors['category_id'] = 'カテゴリを選び直してください';
                }
                $image = $this->read_image($errors);

                if (empty($errors)) {
                    // 画像を選ばなかったときは、今の画像をそのまま使う
                    $values = $this->to_item_values($input, $selected['id']);
                    $values['image_path'] = $image === null ? $item['image_path'] : ItemImage::save($image);

                    // 差し替えは「新しい画像を保存 → DBを更新 → 古い画像を削除」の順。途中で失敗しても、DBが指す画像は必ずある
                    try {
                        \Model\Item::update($item['id'], $user_id, $values);
                    } catch (\Exception $e) {
                        if ($image !== null) {
                            ItemImage::delete($values['image_path']);
                        }
                        throw $e;
                    }
                    if ($image !== null) {
                        ItemImage::delete($item['image_path']);
                    }

                    // カテゴリを変えた場合は移動先の一覧に戻るので、移動したことがすぐ分かる
                    Session::set_flash('success', '『' . $input['name'] . '』を保存しました');
                    Response::redirect('main/index/' . $selected['id']);
                }
            }
        }

        // サイドバーで、この欲しいものが入っているカテゴリ（編集前）の行を強調する
        $this->template->sidebar->set('current_category_id', $item['category_id']);
        $this->template->title = '欲しいもの編集';
        $this->template->page_js = 'item_form.js';
        $this->template->content = View::forge('item/edit', array(
            'input'          => $input,
            'errors'         => $errors,
            'item'           => $item,
            'desire_max'     => Config::get('wishlist.desire_level.max'),
            'image_max_size' => Config::get('wishlist.image.max_size'),
            'limits'         => Config::get('wishlist.item'), // 入力欄の maxlength に使う
        ));
    }

    /**
     * 選ばれた画像を受け取って確かめる（登録・編集で共通）
     *
     * @param array $errors ほかの項目の入力エラー。画像のエラーもここに足す
     * @return array|null 保存してよい画像（Input::file の値）。選ばれていない・エラーのときは null
     */
    protected function read_image(array &$errors)
    {
        $file = Input::file('image');
        if ($file === null || $file['error'] === UPLOAD_ERR_NO_FILE) {
            return null;
        }

        $error = ItemImage::validate($file);
        if ($error !== null) {
            $errors['image'] = $error;
            return null;
        }

        // ほかの項目にエラーがあると、この画像は保存しない。ファイル欄はブラウザの仕組み上もとに戻せないので、選び直してもらう
        if (! empty($errors)) {
            $errors['image'] = 'ほかの項目にエラーがあったため、画像は保存していません。もう一度選んでください';
            return null;
        }
        return $file;
    }

    /**
     * 送信されたデータが大きすぎて、PHPが中身を捨てたか
     *
     * php.ini の post_max_size（8MB）を超えると、$_POST も $_FILES も空になる。
     * そのままだと CSRF トークンもないので「有効期限切れ」と出てしまうため、先に見分ける
     *
     * @return bool
     */
    protected static function is_post_too_large()
    {
        return empty($_POST) && empty($_FILES) && (int) Input::server('CONTENT_LENGTH', 0) > 0;
    }

    /**
     * @return string
     */
    protected static function post_too_large_message()
    {
        return '画像のサイズが大きすぎます。' . (Config::get('wishlist.image.max_size') / 1024 / 1024) . 'MB 以内のファイルを選んでください';
    }

    /**
     * POSTされた値を整える（登録・編集で共通）
     *
     * @return array
     */
    protected function read_input()
    {
        return array(
            'name'         => static::trim_space(Input::post('name', '')),
            'category_id'  => Input::post('category_id', ''),
            'desire_level' => Input::post('desire_level', ''),
            'reason'       => static::trim_space(Input::post('reason', '')),
            'url'          => static::trim_space(Input::post('url', '')),
            // 全角数字は半角に、カンマは取り除く（「１，０００」→「1000」）
            'price'        => str_replace(array(',', '，'), '', mb_convert_kana(static::trim_space(Input::post('price', '')), 'n')),
        );
    }

    /**
     * 選ばれたカテゴリを確かめる（登録・編集で共通）
     *
     * @param string $category_id フォームから送られたカテゴリID
     * @param int $user_id ユーザーID
     * @return array|null 未選択なら未分類、本人のカテゴリならそのカテゴリ。他人のもの・存在しないIDは null
     */
    protected function find_selected_category($category_id, $user_id)
    {
        if ($category_id === '') {
            return \Model\Category::find_default_by_user_id($user_id);
        }
        return \Model\Category::find_by_id_and_user_id($category_id, $user_id);
    }

    /**
     * 入力をDBに保存する形にする（登録・編集で共通）
     *
     * @param array $input 入力チェック済みの値
     * @param int $category_id 確認済みのカテゴリID
     * @return array 任意項目の未入力は null
     */
    protected function to_item_values(array $input, $category_id)
    {
        return array(
            'category_id'  => $category_id,
            'name'         => $input['name'],
            'desire_level' => (int) $input['desire_level'],
            'reason'       => $input['reason'] === '' ? null : $input['reason'],
            'url'          => $input['url'] === '' ? null : $input['url'],
            'price'        => $input['price'] === '' ? null : (int) $input['price'],
        );
    }

    /**
     * 入力チェック
     *
     * @param array $input 空白除去済みの入力
     * @return array 項目名 => エラーメッセージ
     */
    protected function validate(array $input)
    {
        // 上限は独自config（wishlist）から取る。画面の maxlength・星の数と同じ値になる
        $desire_max = Config::get('wishlist.desire_level.max');
        $limits = Config::get('wishlist.item');

        $val = Validation::forge();
        $val->add_field('name', 'アイテム名', 'required|max_length[' . $limits['name_max'] . ']');
        $val->add_field('desire_level', '欲しい度合い', 'required|valid_string[numeric]|numeric_between[1,' . $desire_max . ']');
        $val->add_field('reason', '理由', 'max_length[' . $limits['reason_max'] . ']');
        // URLは http:// か https:// で始まるものだけ（javascript: などはエスケープでは防げないため）
        $val->add_field('url', 'URL', 'max_length[' . $limits['url_max'] . ']')
            ->add_rule('match_pattern', '/\Ahttps?:\/\/\S+\z/i');
        $val->add_field('price', '金額', 'valid_string[numeric]|numeric_between[0,' . $limits['price_max'] . ']');

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
