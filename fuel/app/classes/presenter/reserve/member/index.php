<?php
/**
 * 【必須課題】会員登録して予約へ：会員登録情報入力
 *
 * @package  app
 * @extends  Presenter
 */
class Presenter_Reserve_Member_Index extends Presenter
{
	protected $_view = 'reserve/member/index.twig';

	public function view()
	{
		$this->set('title', '会員登録情報入力');
		Config::load('pref', true);
		$this->set('prefs', Config::get('pref'));

		$this->set('data', Session::get('data'));
		$this->set('userData', Session::get('userData', array()));
		$this->set('csrf', array(
			'key'   => Config::get('security.csrf_token_key'),
			'token' => Security::fetch_token(),
		));
	}
}
