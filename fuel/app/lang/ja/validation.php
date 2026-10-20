<?php

return array(
	'required'        => ':label を入力してください。',
	'min_length'      => ':label は :param:1 文字以上で入力して下さい。',
	'max_length'      => ':label は :param:1 文字以下で入力して下さい。',
	'exact_length'    => ':label は :param:1 文字で入力して下さい。',
	'match_value'     => ':label は使用できない文字が含まれています。使用可能な文字 [:param:1] ',
	'match_pattern'   => ':label は :param:2 で入力して下さい。',
	'match_field'     => ':label は :param:1 と異なっています。',
	'valid_email'     => ':label は正しいメールアドレスではありません。',
	'valid_emails'    => ':label に不正なメールアドレスが含まれています。',
	'valid_url'       => ':label は正しいURLではありません。',
	'valid_ip'        => ':label は正しいIPアドレスではありません。',
	'numeric_min'     => ':label は :param:1 より大きい値を入力してください。',
	'numeric_max'     => ':label は :param:1 より小さい値を入力してください。',
	'numeric_between' => ':label は :param:1 以上 :param:2 以内の値を入力してください。',
	'valid_string'    => '正しく入力してください。',
	'required_with'   => ':label は :param:1 を1つ以上選択する必要があります。',
	'valid_date'      => ':label は 日付として正しくありません。',

	// MyRules（classes/extensions/myrules.php）
	'member_password' => ':label は半角英数字・記号の8〜72文字で入力してください（空白不可）。',
	'member_pref'     => ':label を一覧から選択してください。',
	'member_birthday' => ':label は年月日をすべて入力し、今日以前の実在する日付を指定してください。',
	'unique'          => 'この:label は 使用できません。',
	'birthday'        => ':label を正しく入力してください。',
	'sex'             => ':label を正しく選択してください。',
	'mail_magazine'   => ':label を正しく選択してください。',
	'valid_zenkaku'   => ':label は全角で入力してください。',
	'valid_katakana'  => ':label は全角カタカナで入力してください。',
);
