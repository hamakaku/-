<?php
/**
 * 【見本】The Reserve_Guest Controller.
 *
 * 会員登録せず予約へ（仕様書「1) 会員登録せず予約へ」）
 *   reserve/guest（予約者基本情報入力）
 *     → reserve/guest/check（入力チェック → 入力確認）
 *     → reserve/guest/confirm（予約確認）
 *     → reserve/guest/end（予約確定）
 *
 * @package  app
 * @extends  Controller
 */
class Controller_Reserve_Guest extends Controller
{
	/**
	 * 予約者基本情報入力
	 *
	 * GET reserve/guest
	 *
	 * @access  public
	 * @return  Response
	 */
	public function action_index()
	{
		// 予約内容（reserve で保存）がなければエラー
		if ( ! Session::get('data'))
		{
			return Response::forge(Presenter::forge('errors/error'));
		}

		return Response::forge(Presenter::forge('reserve/guest/index'));
	}

	/**
	 * 入力チェック → 入力確認
	 *
	 * POST reserve/guest/check
	 *
	 * @access  public
	 * @return  Response
	 */
	public function action_check()
	{
		if (Input::method() !== 'POST')
		{
			Log::warning('action_check: GET access error.');
			return Response::forge(Presenter::forge('errors/csr'));
		}

		if ( ! Session::get('data'))
		{
			return Response::forge(Presenter::forge('errors/error'));
		}

		// 生年月日（年・月・日のプルダウン）を1つの日付にまとめる
		$input             = Input::post();
		$input['birthday'] = '';
		if (Input::post('birth_year') or Input::post('birth_month') or Input::post('birth_day'))
		{
			$input['birthday'] = Input::post('birth_year').'-'.Input::post('birth_month').'-'.Input::post('birth_day');
		}

		// 入力値を Session に保存（入力画面に戻ったときの再表示・登録処理で使う）
		Session::set('userData', $input);

		// 入力チェック（ルールは Model_Guest::validate() に書く）
		$val = Model_Guest::validate();
		if ( ! $val->run($input))
		{
			// NG → エラーメッセージを渡して入力画面を再表示
			return Response::forge(Presenter::forge('reserve/guest/index')->set('errors', $val->error_message()));
		}

		return Response::forge(Presenter::forge('reserve/guest/check'));
	}

	/**
	 * 予約確認（ご要望の入力）
	 *
	 * GET reserve/guest/confirm
	 *
	 * @access  public
	 * @return  Response
	 */
	public function action_confirm()
	{
		if ( ! Session::get('data') or ! Session::get('userData'))
		{
			Log::warning('action_confirm: session not found.');
			return Response::forge(Presenter::forge('errors/csr'));
		}

		return Response::forge(Presenter::forge('reserve/guest/confirm'));
	}

	/**
	 * 予約確定（ゲストと予約を登録 → 完了画面）
	 *
	 * POST reserve/guest/end
	 *
	 * @access  public
	 * @return  Response
	 */
	public function action_end()
	{
		if (Input::method() !== 'POST')
		{
			Log::warning('action_end: GET access error.');
			return Response::forge(Presenter::forge('errors/csr'));
		}

		// CSRF チェック（フォームに hidden でトークンを入れておく → reserve/guest/confirm.twig）
		if ( ! Security::check_token())
		{
			Log::warning('action_end: csrf error.');
			return Response::forge(Presenter::forge('errors/csr'));
		}

		$data      = Session::get('data');
		$user_data = Session::get('userData');

		// 登録後に Session を消しているので、二重送信（再読み込み）はここで止まる
		if ( ! $data or ! $user_data)
		{
			return Response::forge(Presenter::forge('errors/error'));
		}

		// トランザクション開始
		$db = Database_Connection::instance();
		$db->start_transaction();

		try
		{
			// ゲストを登録（created_at は Model_Guest の $_created_at で自動で入る）
			$birthday = Arr::get($user_data, 'birthday');

			$guest = Model_Guest::forge(array(
				'name'         => Arr::get($user_data, 'familyName').Arr::get($user_data, 'givenName'),
				'name_kana'    => Arr::get($user_data, 'familyName_kana').Arr::get($user_data, 'givenName_kana'),
				'tel'          => Arr::get($user_data, 'tel'),
				'mail_address' => Arr::get($user_data, 'email'),
				'address'      => Arr::get($user_data, 'pref').Arr::get($user_data, 'city').Arr::get($user_data, 'address'),
				'sex'          => Arr::get($user_data, 'sex'),
				'birthday'     => $birthday ? $birthday : null,
			));
			$guest->save();

			// 予約を登録
			$reserve = Model_Reserve::forge(array(
				'guest_id'      => $guest->id,
				'instructor_id' => Arr::get($data, 'instructor.id'),
				'course_id'     => Arr::get($data, 'course.id'),
				'day'           => sprintf('%04d-%02d-%02d', Arr::get($data, 'date.year'), Arr::get($data, 'date.month'), Arr::get($data, 'date.day')),
				'week'          => Arr::get($data, 'date.week'),
				'time'          => Arr::get($data, 'time'),
				'opinion'       => Input::post('opinion'),
			));
			$reserve->save();

			// コミット
			$db->commit_transaction();
		}
		catch (Exception $e)
		{
			// ロールバック（ゲストも予約も登録されない）
			$db->rollback_transaction();

			Log::error('action_end: regist data error. '.$e->getMessage());
			return Response::forge(Presenter::forge('errors/error'));
		}

		// 登録が終わったら予約途中の Session を削除
		Session::delete('data');
		Session::delete('userData');

		// 完了画面に予約番号を渡す
		return Response::forge(Presenter::forge('reserve/end')->set('reserve_id', $reserve->id));
	}

}
