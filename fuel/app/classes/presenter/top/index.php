<?php
/**
 * The Top_Index Presenter.
 *
 * @package  app
 * @extends  Presenter
 */
class Presenter_Top_Index extends Presenter
{
	protected $_view = 'top/index.twig';

	/**
	 * Prepare the view data, keeping this in here helps clean up
	 * the controller.
	 *
	 * @return void
	 */
	public function view()
	{
		// ページタイトル
		$this->set('title', 'トップ');
	}
}
