<?php
/**
 * 【見本】ゲスト予約：入力画面
 *
 * Presenter の役割: ビューに渡すデータを用意する（DB・Session からの取得など）
 * コントローラから ->set('errors', ...) で渡した値もビューで使える。
 *
 * @package  app
 * @extends  Presenter
 */
class Presenter_Reserve_Guest_Index extends Presenter
{
	protected $_view = 'reserve/guest/index.twig';

	public function view()
	{
		$this->set('title', 'オンライン予約');

		// 予約内容（reserve/index で保存したもの）
		$this->set('data', Session::get('data'));

		// 入力値（エラーのとき・確認画面から戻ったときの再表示用）
		$this->set('userData', Session::get('userData', array()));

		// 都道府県リスト（config/pref.php）
		Config::load('pref', true);
		$this->set('prefs', Config::get('pref'));
	}
}
