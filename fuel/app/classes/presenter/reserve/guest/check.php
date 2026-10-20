<?php
/**
 * 【見本】ゲスト予約：入力内容確認画面
 *
 * @package  app
 * @extends  Presenter
 */
class Presenter_Reserve_Guest_Check extends Presenter
{
	protected $_view = 'reserve/guest/check.twig';

	public function view()
	{
		$this->set('title', 'オンライン予約');
		$this->set('data', Session::get('data'));
		$this->set('userData', Session::get('userData'));
	}
}
