<?php
/**
 * The Top Controller.
 *
 * @package  app
 * @extends  Controller
 */
class Controller_Top extends Controller
{
	/**
	 * トップページ
	 *
	 * @access  public
	 * @return  Response
	 */
	public function action_index()
	{
		return Response::forge(Presenter::forge('top/index'));
	}

}
