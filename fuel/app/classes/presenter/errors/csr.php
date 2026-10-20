<?php
/**
 * The Errors_Csr Presenter.
 *
 * @package  app
 * @extends  Presenter
 */
class Presenter_Errors_Csr extends Presenter
{
	protected $_view = 'errors/csr.twig';

	/**
	 * Prepare the view data, keeping this in here helps clean up
	 * the controller.
	 *
	 * @return void
	 */
	public function view()
	{
		// ページタイトル
		$this->set('title', '不正遷移エラー');
	}
}
