<?php
/**
 * Custom validation rules
 *
 * Usage: $val->add_callable('MyRules');
 */

class MyRules
{
	public static function _validation_member_password($val)
	{
		return is_string($val) && preg_match('/^[!-~]{8,72}$/D', $val) === 1;
	}

	public static function _validation_member_pref($val)
	{
		Config::load('pref', true);
		return $val === '' || $val === null || in_array($val, Config::get('pref'), true);
	}

	public static function _validation_member_birthday($val)
	{
		if ($val === '' || $val === null)
		{
			return true;
		}
		if ( ! is_string($val) || ! preg_match('/^([0-9]{4})-([0-9]{1,2})-([0-9]{1,2})$/D', $val, $parts))
		{
			return false;
		}
		return checkdate((int) $parts[2], (int) $parts[3], (int) $parts[1])
			&& sprintf('%04d-%02d-%02d', $parts[1], $parts[2], $parts[3]) <= date('Y-m-d');
	}

	// note this is a static method
	public static function _validation_unique($val, $options)
	{
		list($table, $field) = explode('.', $options);

		$result = DB::select(DB::expr('LOWER ("'.$field.'")'))
			->where($field, '=', Str::lower($val))
			->from($table)->execute();

		return ! ($result->count() > 0);
	}

	public static function _validation_birthday($val)
	{
		if ( ! $val)
		{
			return true;
		}

		$time  = new DateTime($val);
		$time2 = new DateTime(date('Y-m-d'));

		return ! ($time > $time2);
	}

	public static function _validation_sex($val)
	{
		if ( ! $val)
		{
			return true;
		}

		return ($val == '男' or $val == '女');
	}

	public static function _validation_mail_magazine($val)
	{
		if ( ! $val)
		{
			return true;
		}

		return ($val == 0 or $val == 1);
	}

	public static function _validation_valid_zenkaku($val)
	{
		$val = self::refine_val($val);
		if (is_null($val) or $val == '')
		{
			return true;
		}

		// 全角チェック（UTF-8用、全角以外を探す正規表現なので最後に反転（否定）しています。）
		if ( ! preg_match('/(?:\xEF\xBD[\xA1-\xBF]|\xEF\xBE[\x80-\x9F])|[\x20-\x7E]|[\x00-\x1f\x7f]/', $val))
		{
			return true;
		}
		return false;
	}

	public static function _validation_valid_katakana($val)
	{
		$val = self::refine_val($val);
		if (is_null($val) or $val == '')
		{
			return true;
		}

		// 空白を除去
		$val = preg_replace('/( |　)/', '', $val);

		// 全角カナ＆横棒＆中黒「・」のみチェック
		if (preg_match('/^[ァ-ヶー\・\ー\―\－\‐\-]+$/u', $val))
		{
			return true;
		}
		return false;
	}

	private static function refine_val($val)
	{
		if (is_null($val))
		{
			return $val;
		}
		if (is_array($val))
		{
			$val = implode($val);
		}
		return $val;
	}

}
