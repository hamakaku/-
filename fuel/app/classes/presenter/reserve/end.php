<?php
/**
 * 予約完了画面（ゲスト・会員 共通）
 *
 * @package  app
 * @extends  Presenter
 */
class Presenter_Reserve_End extends Presenter
{
	protected $_view = 'reserve/end.twig';

	public function view()
	{
		$this->set('title', 'オンライン予約');
	}
}
