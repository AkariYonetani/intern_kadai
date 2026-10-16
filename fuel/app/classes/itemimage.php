<?php

/**
 * 欲しいものの画像ファイルを扱う（確認・保存・削除・場所）
 *
 * DB には保存したファイル名（items.image_path）だけを入れ、ファイル本体は config の dir に置く
 * 登録・編集（Controller_Item）、削除と表示（Controller_Api_Item）、カテゴリ削除（Controller_Main）で使う
 */
class ItemImage
{
    /**
     * アップロードされた画像を確かめる（保存はしない）
     *
     * @param array $file Input::file('image') の値
     * @return string|null エラーメッセージ。問題なければ null
     */
    public static function validate(array $file)
    {
        // php.ini の upload_max_filesize（2MB）を超えると、PHPがファイルを受け取らずにエラーを入れる
        if ($file['error'] === UPLOAD_ERR_INI_SIZE || $file['error'] === UPLOAD_ERR_FORM_SIZE) {
            return static::size_error();
        }
        // is_uploaded_file：本当にこのリクエストでアップロードされたファイルか（サーバー上の別のファイルを指していないか）
        if ($file['error'] !== UPLOAD_ERR_OK || ! is_uploaded_file($file['tmp_name'])) {
            return '画像をアップロードできませんでした。もう一度選んでください';
        }
        if ($file['size'] > Config::get('wishlist.image.max_size')) {
            return static::size_error();
        }

        // 拡張子（名前）と中身の両方を確かめる。名前は利用者がいくらでも変えられるため、名前だけでは信用しない
        $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if (! in_array($extension, Config::get('wishlist.image.extensions'), true)
            || static::detect_type($file['tmp_name']) === null) {
            return '画像は jpg または png 形式のファイルを選んでください';
        }

        return null;
    }

    /**
     * 確認済みの画像を、ランダムな名前で保存する
     *
     * @param array $file validate() で確認済みの Input::file('image') の値
     * @return string 保存したファイル名（items.image_path に入れる値）
     * @throws \RuntimeException 保存できなかったとき
     */
    public static function save(array $file)
    {
        // 拡張子は利用者が付けた名前ではなく、中身から判定した形式で決める
        $types = Config::get('wishlist.image.types');
        $extension = $types[static::detect_type($file['tmp_name'])];

        // random_bytes は推測できない乱数を作る（16バイト → 32文字の16進数）
        $filename = bin2hex(random_bytes(16)) . '.' . $extension;

        $dir = Config::get('wishlist.image.dir');
        if (! is_dir($dir) && ! mkdir($dir, 0755, true)) {
            throw new \RuntimeException('画像の保存先を作れませんでした');
        }
        // 一時フォルダから保存先へ移す（move_uploaded_file はアップロードされたファイルしか動かさない）
        if (! move_uploaded_file($file['tmp_name'], $dir . $filename)) {
            throw new \RuntimeException('画像を保存できませんでした');
        }

        return $filename;
    }

    /**
     * 画像ファイルを削除する（ファイルがなければ何もしない）
     *
     * @param string|null $filename items.image_path の値
     */
    public static function delete($filename)
    {
        $path = static::path($filename);
        if ($path !== null && is_file($path)) {
            unlink($path);
        }
    }

    /**
     * 画像ファイルの場所を返す
     *
     * DB の値もそのままは信用しない。save() が付ける形の名前でなければ使わない
     * （「../../config/db.php」のような値で、保存先の外のファイルを読み書きさせないため）
     *
     * @param string|null $filename items.image_path の値
     * @return string|null ファイルの場所。画像がない・名前の形が違うときは null
     */
    public static function path($filename)
    {
        if ($filename === null || preg_match('/\A[0-9a-f]{32}\.(jpg|png)\z/', $filename) !== 1) {
            return null;
        }
        return Config::get('wishlist.image.dir') . $filename;
    }

    /**
     * 画像を表示するURL（<img src> に入れる）
     *
     * 末尾の ?v=ファイル名 は、ブラウザのキャッシュ対策。差し替えるとファイル名が変わるので URL も変わり、
     * ブラウザが古い画像を使い回さずに取りに来る（サーバー側では v は使わない）
     *
     * @param int $id 欲しいもののID
     * @param string $filename items.image_path の値
     * @return string
     */
    public static function url($id, $filename)
    {
        return Uri::create('api/item/image/' . $id, array(), array('v' => $filename));
    }

    /**
     * 画像を返すときの形式（Content-Type）
     *
     * @param string $filename path() で確認済みのファイル名
     * @return string 例：image/jpeg
     */
    public static function mime_type($filename)
    {
        $extension = pathinfo($filename, PATHINFO_EXTENSION);
        return array_search($extension, Config::get('wishlist.image.types'), true);
    }

    /**
     * ファイルの中身から形式を判定する
     *
     * @param string $path ファイルの場所
     * @return string|null 許可している形式なら MIMEタイプ（例：image/png）、それ以外は null
     */
    protected static function detect_type($path)
    {
        // finfo：ファイルの先頭のバイト列（PNGなら「\x89PNG」など）から形式を判定する
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime = finfo_file($finfo, $path);
        finfo_close($finfo);

        if (! array_key_exists($mime, Config::get('wishlist.image.types'))) {
            return null;
        }

        // getimagesize：画像として読み込めるか（先頭だけ画像に見せかけたファイルを弾く）。壊れたファイルでは警告が出るので @ で抑える
        $size = @getimagesize($path);
        if ($size === false || $size['mime'] !== $mime) {
            return null;
        }
        return $mime;
    }

    /**
     * サイズ超過のエラーメッセージ
     *
     * @return string
     */
    protected static function size_error()
    {
        return '画像は ' . (Config::get('wishlist.image.max_size') / 1024 / 1024) . 'MB 以内のファイルを選んでください';
    }
}
