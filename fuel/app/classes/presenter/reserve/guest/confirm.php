<?php
/**
 * 【見本】ゲスト予約：最終確認画面
 *
 * @package  app
 * @extends  Presenter
 */
class Presenter_Reserve_Guest_Confirm extends Presenter
{
	protected $_view = 'reserve/guest/confirm.twig';

	public function view()
	{
		$this->set('title', 'オンライン予約');
		$this->set('data', Session::get('data'));
		$this->set('userData', Session::get('userData'));

		// CSRF トークン（POST するフォームに hidden で入れる → コントローラで Security::check_token()）
		$this->set('csrf', array(
			'key'   => Config::get('security.csrf_token_key'),
			'token' => Security::fetch_token(),
		));
	}
}
