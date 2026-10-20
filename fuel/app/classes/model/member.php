<?php
/**
 * The Member Model.
 */
class Model_Member extends \Model_Crud
{
	protected static $_table_name      = 'member';
	protected static $_primary_key     = 'id';
	protected static $_mysql_timestamp = true;
	protected static $_created_at      = 'created_at';

	/**
	 * 入力チェック
	 *
	 * @param  string  $proc  'entry'（会員登録）
	 * @return Validation
	 */
	public static function validate($proc)
	{
		$val = Validation::forge();
		$val->add_callable('MyRules');
		$val->set_message('match_pattern', ':label を正しく選択してください。');

		if ($proc === 'entry')
		{
			// 項目名は入力画面の name 属性と合わせる。
		$val->add_field('familyName', '姓', 'max_length[16]');
		$val->add_field('givenName', '名', 'max_length[16]');

		$val->add_field('familyName_kana', 'セイ', 'required|valid_katakana|max_length[16]');
		$val->add_field('givenName_kana', 'メイ', 'required|valid_katakana|max_length[16]');

		$val->add_field('email', 'Email', 'required|valid_email|max_length[255]');
		$val->add_field('email2', 'Email（確認用）', 'required|valid_email|max_length[255]|match_field[email]');
		$val->add_field('password', 'パスワード', 'required|min_length[8]|max_length[72]|member_password');
		$val->add_field('password2', 'パスワード（確認用）', 'required|max_length[72]|match_field[password]');

		$val->add_field('tel', '電話番号', 'required|valid_string[numeric]|max_length[20]');
		$val->add_field('zip', '郵便番号', 'valid_string[numeric]|exact_length[7]');
		$val->add_field('pref', '都道府県', 'member_pref');
		$val->add_field('city', '市区町村', 'max_length[50]');
		$val->add_field('address', '番地・建物名', 'max_length[100]');

		$val->add_field('sex', '性別', 'sex');
		$val->add_field('birthday', '生年月日', 'member_birthday');
		$val->add_field('mail_magazine', 'メールマガジン', 'required|match_pattern[/^[01]$/D]');
		}

		return $val;
	}
}
