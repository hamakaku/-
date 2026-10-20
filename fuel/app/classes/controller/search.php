<?php
/**
 * The Search Controller.
 *
 * 予約実行選択（reserve）に進むための仮の予約フォームを表示する。
 * 仕様書「2 検索画面」は追加課題で、作るときはこのコントローラを置き換える。
 *
 * @package  app
 * @extends  Controller
 */
class Controller_Search extends Controller
{
	/**
	 * 仮の予約フォーム
	 *
	 * GET search
	 *
	 * @access  public
	 * @return  Response
	 */
	public function action_index()
	{
		// 予約途中の Session を削除（最初からやり直す）
		Session::delete('data');
		Session::delete('userData');

		return Response::forge(Presenter::forge('search/index'));
	}

}
