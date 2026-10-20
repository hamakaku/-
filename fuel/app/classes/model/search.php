<?php
/**
 * The Search Model.
 *
 * テーブルを持たない、予約日時のチェック用の処理をまとめるクラス
 */
class Model_Search extends \Model
{
	/**
	 * 入力チェック
	 *
	 * @param  string  $proc  'reserve'（予約する講師・コース・日時）
	 * @return Validation
	 */
	public static function validate($proc)
	{
		$val = Validation::forge();

		if ($proc === 'reserve')
		{
			$val->add_field('instructorId', '講師', 'required|valid_string[numeric]');
			$val->add_field('course', 'コース', 'required|valid_string[numeric]');
			$val->add_field('date', '予約日', 'required|valid_date[Y-m-d]');
			$val->add_field('time', '時間', 'required|valid_string[numeric]|numeric_between[9,20]');
		}

		return $val;
	}

	/**
	 * 予約できる日付か（翌日〜来月末）
	 *
	 * @param  string  $date  例）'2026-10-05'
	 * @return bool
	 */
	public static function date_validation($date)
	{
		$target = strtotime($date);
		$from   = strtotime('tomorrow');
		$to     = strtotime(date('Y-m-t', strtotime(date('Y-m-01').' +1 month')));

		return ($target !== false and $from <= $target and $target <= $to);
	}

	/**
	 * 日付から曜日（日〜土）を返す
	 *
	 * @param  string  $date  例）'2026-10-05'
	 * @return string|false
	 */
	public static function get_date_to_week($date)
	{
		$week = array('日', '月', '火', '水', '木', '金', '土');
		$time = strtotime($date);

		return $time ? $week[date('w', $time)] : false;
	}
}
