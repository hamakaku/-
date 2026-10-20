<?php

/**
 * 【見本】The Guest Model.
 *
 * Model_Crud を継承すると、テーブル1つ分の基本操作が使える
 *   登録     : $obj = Model_Guest::forge(array('カラム' => 値, ...)); $obj->save();
 *   主キー   : Model_Guest::find_by_pk($id)                  1件（なければ null）
 *   条件     : Model_Guest::find_one_by('カラム', 値)         1件（なければ null）
 *              Model_Guest::find_by('カラム', 値)             複数件（なければ null）
 *   全件     : Model_Guest::find_all()
 *   件数     : Model_Guest::count()
 *   更新     : $obj = Model_Guest::find_by_pk($id); $obj->カラム = 値; $obj->save();
 *   削除     : $obj->delete();
 */
class Model_Guest extends \Model_Crud
{
	protected static $_table_name      = 'guest';
	protected static $_primary_key     = 'id';
	protected static $_mysql_timestamp = true;
	protected static $_created_at      = 'created_at';

	/**
	 * 入力チェック
	 *
	 * ルールの書き方: 'ルール1|ルール2[パラメータ]'
	 * FuelPHP 標準のルールに加えて、MyRules（classes/extensions/myrules.php）の
	 * valid_katakana / sex / birthday なども使える。
	 * エラーメッセージは lang/ja/validation.php
	 *
	 * @return Validation
	 */
	public static function validate()
	{
		$val = Validation::forge();
		$val->add_callable('MyRules');

		// 必須項目は仕様書の画面に合わせている（フリガナ・Email・Email確認用・電話番号）
		$val->add_field('familyName', '姓', 'max_length[16]');
		$val->add_field('givenName', '名', 'max_length[16]');
		$val->add_field('familyName_kana', 'セイ', 'required|valid_katakana|max_length[16]');
		$val->add_field('givenName_kana', 'メイ', 'required|valid_katakana|max_length[16]');
		$val->add_field('email', 'Email', 'required|valid_email|max_length[255]');
		$val->add_field('email2', 'Email（確認用）', 'required|match_field[email]');
		$val->add_field('tel', '電話番号', 'required|valid_string[numeric]|max_length[20]');
		$val->add_field('zip', '郵便番号', 'valid_string[numeric]|exact_length[7]');
		$val->add_field('pref', '都道府県', 'max_length[5]');
		$val->add_field('city', '市区町村', 'max_length[50]');
		$val->add_field('address', '番地・建物名', 'max_length[100]');
		$val->add_field('sex', '性別', 'sex');
		$val->add_field('birthday', '生年月日', 'valid_date[Y-m-d]|birthday');

		return $val;
	}
}
