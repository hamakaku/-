<?php
/**
 * 予約実行選択
 *
 * @package  app
 * @extends  Presenter
 */
class Presenter_Reserve_Index extends Presenter
{
	protected $_view = 'reserve/index.twig';

	public function view()
	{
		$this->set('title', 'オンライン予約');
		$this->set('data', Session::get('data'));
	}
}
