<?php
/**
 * 【必須課題】会員登録して予約へ：予約確認
 *
 * @package  app
 * @extends  Presenter
 */
class Presenter_Reserve_Member_Confirm extends Presenter
{
	protected $_view = 'reserve/member/confirm.twig';

	public function view()
	{
		$this->set('title', 'オンライン予約');
		$this->set('data', Session::get('data'));
		$this->set('userData', Session::get('userData'));
		$this->set('csrf', array(
			'key'   => Config::get('security.csrf_token_key'),
			'token' => Security::fetch_token(),
		));

		$submission_token = Session::get('member_submission_token');
		if ( ! $submission_token)
		{
			$submission_token = bin2hex(random_bytes(32));
			Session::set('member_submission_token', $submission_token);
		}
		$this->set('submission_token', $submission_token);
	}
}
