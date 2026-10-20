<?php
/**
 * 【必須課題】会員登録して予約へ：入力確認
 *
 * @package  app
 * @extends  Presenter
 */
class Presenter_Reserve_Member_Check extends Presenter
{
	protected $_view = 'reserve/member/check.twig';

	public function view()
	{
		$this->set('title', 'オンライン予約');

		$this->set('data', Session::get('data'));
		$this->set('userData', Session::get('userData', array()));
	}
}
