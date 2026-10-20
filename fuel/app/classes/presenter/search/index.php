<?php
/**
 * 仮の予約フォーム（仕様書「2 検索画面」の代わり）
 *
 * @package  app
 * @extends  Presenter
 */
class Presenter_Search_Index extends Presenter
{
	protected $_view = 'search/index.twig';

	public function view()
	{
		$this->set('title', 'オンライン予約');

		$this->set('instructors', Model_Instructor::find_all());
		$this->set('courses', Model_Course::find_all());

		// 入力値（エラーで戻ったときの再表示用）
		$this->set('input', Input::post());

		// 予約できる日付の範囲（翌日〜来月末）
		$this->set('min_date', date('Y-m-d', strtotime('tomorrow')));
		$this->set('max_date', date('Y-m-t', strtotime(date('Y-m-01').' +1 month')));
	}
}
