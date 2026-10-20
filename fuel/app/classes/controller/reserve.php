<?php
/**
 * The Reserve Controller.
 *
 * 予約の流れ（仕様書「3 予約実行選択」以降）
 *   search（仮の予約フォーム）
 *     → reserve（予約実行選択）                                       … このファイル
 *     → 会員登録せず予約へ: reserve/guest  → check → confirm → end  … controller/reserve/guest.php【見本】
 *     → 会員登録して予約へ: reserve/member → check → confirm → end  … controller/reserve/member.php【必須課題】
 *
 * @package  app
 * @extends  Controller
 */
class Controller_Reserve extends Controller
{
	/**
	 * 予約実行選択
	 *
	 * POST reserve（instructorId / course / date / time）
	 *
	 * @access  public
	 * @return  Response
	 */
	public function action_index()
	{
		if (Input::method() !== 'POST')
		{
			return Response::forge(Presenter::forge('errors/error'), 404);
		}

		// 入力チェック NG → 仮の予約フォームを再表示
		$val = Model_Search::validate('reserve');
		if ( ! $val->run())
		{
			return Response::forge(Presenter::forge('search/index')->set('errors', $val->error_message()));
		}

		$day        = Input::post('date');
		$time       = (int) Input::post('time');
		$instructor = Model_Instructor::find_by_pk(Input::post('instructorId'));
		$course     = Model_Course::find_by_pk(Input::post('course'));

		if ( ! $instructor or ! $course or ! Model_Search::date_validation($day))
		{
			return Response::forge(Presenter::forge('search/index')->set('errors', array('date' => '選択した内容では予約できません。')));
		}

		// 同じ講師・日時の予約がすでにあれば予約できない
		$reserved = Model_Reserve::find_one_by(array(
			array('instructor_id', '=', $instructor->id),
			array('day', '=', $day),
			array('time', '=', $time),
		));
		if ($reserved)
		{
			return Response::forge(Presenter::forge('search/index')->set('errors', array('time' => 'この時間はすでに予約が入っています。')));
		}

		// 予約内容を Session に保存（この後の画面・登録処理で使う）
		Session::set('data', array(
			'instructor' => array('id' => $instructor->id, 'name' => $instructor->name),
			'course'     => array('id' => $course->id, 'name' => $course->name),
			'date'       => array(
				'year'  => date('Y', strtotime($day)),
				'month' => date('n', strtotime($day)),
				'day'   => date('j', strtotime($day)),
				'week'  => Model_Search::get_date_to_week($day),
			),
			'time'       => $time,
		));
		Session::delete('userData');

		return Response::forge(Presenter::forge('reserve/index'));
	}

}
