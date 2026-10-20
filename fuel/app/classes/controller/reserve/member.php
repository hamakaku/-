<?php

use Fuel\Core\Controller;
use Fuel\Core\Input;
use Fuel\Core\Log;
use Fuel\Core\Presenter;
use Fuel\Core\Response;
use Fuel\Core\Session;
/**
 * 【必須課題】The Reserve_Member Controller.
 *
 * 会員登録して予約へ（仕様書「2) 会員登録して予約へ」）
 *   reserve/member（会員登録情報入力）
 *     → reserve/member/check（入力チェック → 入力確認）
 *     → reserve/member/confirm（予約確認）
 *     → reserve/member/end（予約確定）
 *
 * 見本: controller/reserve/guest.php
 *
 * @package  app
 * @extends  Controller
 */
class Controller_Reserve_Member extends Controller
{
	/**
	 * 会員登録情報入力
	 *
	 * GET reserve/member
	 *
	 * @access  public
	 * @return  Response
	 */
	public function action_index()
	{
		// 予約内容を選択してから入力画面へ進む。
		if ( ! Session::get('data'))
		{
			return Response::forge(Presenter::forge('errors/error'));
		}

		return Response::forge(Presenter::forge('reserve/member/index'));
	}

	/**
	 * 入力チェック → 入力確認
	 *
	 * POST reserve/member/check
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

		if ( ! Security::check_token())
		{
			Log::warning('action_check: csrf error.');
			return Response::forge(Presenter::forge('errors/csr'));
		}

		// 予約確認
		if ( ! Session::get('data'))
		{
			return Response::forge(Presenter::forge('errors/error'));
		}

		// 配列などの不正な入力は空文字として扱い、必要な項目だけ取り込む。
		$input = array();
		foreach (array('familyName', 'givenName', 'familyName_kana', 'givenName_kana',
			'email', 'email2', 'password', 'password2', 'tel', 'zip', 'pref', 'city',
			'address', 'sex', 'birth_year', 'birth_month', 'birth_day', 'mail_magazine') as $field)
		{
			$value = Input::post($field, '');
			$input[$field] = is_string($value) ? trim($value) : '';
		}
		$input['email']  = strtolower($input['email']);
		$input['email2'] = strtolower($input['email2']);

		$input['birthday'] = '';
		
		if ($input['birth_year'] !== '' || $input['birth_month'] !== '' || $input['birth_day'] !== '')
		{
			$input['birthday'] = $input['birth_year'].'-'.$input['birth_month'].'-'.$input['birth_day'];
		}

		// 入力値を Session に保存（入力画面に戻ったときの再表示・登録処理で使う）
		$userData = $input;
		unset($userData['password'], $userData['password2']);
		Session::set('userData', $userData);

		// 入力チェック（ルールは Model_Member::validate('entry') に書く）
		$val = Model_Member::validate('entry');
		if ( ! $val->run($input))
		{
			// NG → エラーメッセージを渡して入力画面を再表示
			return Response::forge(Presenter::forge('reserve/member/index')->set('errors', $val->error_message()));
		}

		// 画面上でも重複を知らせる。最終登録時にもDBのUNIQUE制約で競合を防ぐ。
		if (Model_Member::find_one_by('mail_address', $input['email']))
		{
			return Response::forge(Presenter::forge('reserve/member/index')->set('errors', array(
				'email' => 'このメールアドレスはすでに登録されています。',
			)));
		}

		$password_hash = password_hash($input['password'], PASSWORD_DEFAULT);
		if ($password_hash === false)
		{
			Log::error('action_check: password hash error.');
			return Response::forge(Presenter::forge('errors/error'));
		}

		$userData['password_hash'] = $password_hash;
		Session::set('userData', $userData);
		Session::delete('member_submission_token');
		return Response::forge(Presenter::forge('reserve/member/check'));
	}

	/**
	 * 予約確認（ご要望の入力）
	 *
	 * GET reserve/member/confirm
	 *
	 * @access  public
	 * @return  Response
	 */
	public function action_confirm()
	{
		$user_data = Session::get('userData');
		if ( ! Session::get('data') or ! $user_data or ! Arr::get($user_data, 'password_hash'))
		{
			Log::warning('action_confirm: session not found.');
			return Response::forge(Presenter::forge('errors/csr'));
		}

		return Response::forge(Presenter::forge('reserve/member/confirm'));
	}

	/**
	 * 予約確定（会員と予約を登録 → 完了画面）
	 *
	 * POST reserve/member/end
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

		if ( ! Security::check_token())
		{
			Log::warning('action_end: csrf error.');
			return Response::forge(Presenter::forge('errors/csr'));
		}

		$data             = Session::get('data');
		$user_data        = Session::get('userData');
		$submission_token = Session::get('member_submission_token');
		$posted_token     = Input::post('submission_token', '');

		// 完了後はセッションを削除するため、再読み込み・戻る操作による二重送信もここで止まる。
		if ( ! $data or ! $user_data or ! is_string($submission_token) or
			 ! is_string($posted_token) or ! hash_equals($submission_token, $posted_token))
		{
			Log::warning('action_end: invalid or already used submission token.');
			return Response::forge(Presenter::forge('errors/error'));
		}

		$password_hash = Arr::get($user_data, 'password_hash');
		if ( ! is_string($password_hash) or $password_hash === '')
		{
			Log::warning('action_end: password hash not found.');
			return Response::forge(Presenter::forge('errors/error'));
		}

		$opinion = Input::post('opinion', '');
		$opinion = is_string($opinion) ? trim($opinion) : '';
		$db = Database_Connection::instance();
		$db->start_transaction();

		try
		{
			// 入力確認後に別リクエストが同じメールアドレスを登録した場合も防止する。
			if (Model_Member::find_one_by('mail_address', Arr::get($user_data, 'email')))
			{
				throw new DomainException('mail address already registered');
			}

			$birthday = Arr::get($user_data, 'birthday');
			if ($birthday)
			{
				$birthday = sprintf('%04d-%02d-%02d',
					(int) Arr::get($user_data, 'birth_year'),
					(int) Arr::get($user_data, 'birth_month'),
					(int) Arr::get($user_data, 'birth_day')
				);
			}

			$member = Model_Member::forge(array(
				'mail_address'    => Arr::get($user_data, 'email'),
				'password'        => $password_hash,
				'familyName'      => Arr::get($user_data, 'familyName'),
				'givenName'       => Arr::get($user_data, 'givenName'),
				'familyName_kana' => Arr::get($user_data, 'familyName_kana'),
				'givenName_kana'  => Arr::get($user_data, 'givenName_kana'),
				'tel'             => Arr::get($user_data, 'tel'),
				'zipcode'         => Arr::get($user_data, 'zip') ?: null,
				'pref'            => Arr::get($user_data, 'pref') ?: null,
				'city'            => Arr::get($user_data, 'city') ?: null,
				'address'         => Arr::get($user_data, 'address') ?: null,
				'sex'             => Arr::get($user_data, 'sex') ?: null,
				'birthday'        => $birthday ?: null,
				'mail_magazine'   => (int) Arr::get($user_data, 'mail_magazine', 0),
			));
			$member->save();

			$reserve = Model_Reserve::forge(array(
				'member_id'     => $member->id,
				'instructor_id' => Arr::get($data, 'instructor.id'),
				'course_id'     => Arr::get($data, 'course.id'),
				'day'           => sprintf('%04d-%02d-%02d', Arr::get($data, 'date.year'), Arr::get($data, 'date.month'), Arr::get($data, 'date.day')),
				'week'          => Arr::get($data, 'date.week'),
				'time'          => Arr::get($data, 'time'),
				'opinion'       => $opinion,
			));
			$reserve->save();

			$db->commit_transaction();
		}
		catch (DomainException $e)
		{
			$db->rollback_transaction();
			unset($user_data['password_hash']);
			Session::set('userData', $user_data);
			Session::delete('member_submission_token');

			return Response::forge(Presenter::forge('reserve/member/index')->set('errors', array(
				'email' => 'このメールアドレスはすでに登録されています。',
			)));
		}
		catch (Exception $e)
		{
			$db->rollback_transaction();
			Log::error('action_end: regist data error. '.$e->getMessage());
			return Response::forge(Presenter::forge('errors/error'));
		}

		Session::delete('data');
		Session::delete('userData');
		Session::delete('member_submission_token');

		return Response::forge(Presenter::forge('reserve/end')->set('reserve_id', $reserve->id));
	}

}
