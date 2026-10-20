<?php
/**
 * The Errors_Error Presenter.
 *
 * @package  app
 * @extends  Presenter
 */
class Presenter_Errors_Error extends Presenter
{
	protected $_view = 'errors/error.twig';

	/**
	 * Prepare the view data, keeping this in here helps clean up
	 * the controller.
	 *
	 * @return void
	 */
	public function view()
	{
		// ページタイトル
		$this->set('title', 'エラー');
	}
}
