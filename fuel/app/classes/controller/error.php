<?php

/**
 * エラーページ
 *
 * 存在しないURLを開くと、FuelPHP が routes.php の _404_ に書いた「error/404」をこのコントローラーで表示する
 */
class Controller_Error extends Controller_Base
{
    /**
     * ログインしていなくても表示する（ログイン前に存在しないURLを開くこともあるため）
     */
    protected $require_login = false;

    /**
     * ログイン前の画面と同じ、中央の白いカードの形にする
     */
    public $template = 'template_guest';

    /**
     * ページが見つからない（404）
     *
     * フレームワーク名やファイルの場所などの内部の情報は出さない（攻撃の手がかりを与えないため）
     */
    public function action_404()
    {
        // 応答の状態コードを 404 にする（ブラウザや検索エンジンに「このページはない」と正しく伝える）
        $this->response_status = 404;

        $this->template->title = 'ページが見つかりません';
        $this->template->content = View::forge('error/404');
    }
}
