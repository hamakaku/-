<?php
// Run from the project root: php fuel/app/tests/member_entry.php
$root = dirname(__DIR__, 3).DIRECTORY_SEPARATOR;
define('DOCROOT', $root.'public/');
define('APPPATH', $root.'fuel/app/');
define('PKGPATH', $root.'fuel/packages/');
define('COREPATH', $root.'fuel/core/');
define('FUEL_START_TIME', microtime(true));
define('FUEL_START_MEM', memory_get_usage());
require COREPATH.'classes/autoloader.php';
class_alias('Fuel\\Core\\Autoloader', 'Autoloader');
require APPPATH.'bootstrap.php';

$valid = array(
    'familyName' => '', 'givenName' => '',
    'familyName_kana' => 'ヤマダ', 'givenName_kana' => 'タロウ',
    'email' => 'test@example.com', 'email2' => 'test@example.com',
    'password' => 'Example123!', 'password2' => 'Example123!',
    'tel' => '09012345678', 'zip' => '', 'pref' => '', 'city' => '',
    'address' => '', 'sex' => '', 'birthday' => '', 'mail_magazine' => '0',
);
$cases = array(
    'required fields only' => array(array(), true),
    'valid leap day' => array(array('birthday' => '2000-2-29'), true),
    'valid optional fields' => array(array('zip' => '1234567', 'pref' => '東京都', 'sex' => '女', 'mail_magazine' => '1'), true),
    'missing kana' => array(array('familyName_kana' => ''), false),
    'invalid kana' => array(array('givenName_kana' => 'たろう'), false),
    'email mismatch' => array(array('email2' => 'other@example.com'), false),
    'invalid email' => array(array('email' => 'invalid'), false),
    'phone with hyphens' => array(array('tel' => '090-1234-5678'), false),
    'short postcode' => array(array('zip' => '123'), false),
    'invalid prefecture' => array(array('pref' => '不明'), false),
    'invalid sex' => array(array('sex' => 'invalid'), false),
    'partial birthday' => array(array('birthday' => '2000--'), false),
    'non leap day' => array(array('birthday' => '2001-2-29'), false),
    'future birthday' => array(array('birthday' => '9999-1-1'), false),
    'short password' => array(array('password' => 'short', 'password2' => 'short'), false),
    'password mismatch' => array(array('password2' => 'Different123'), false),
    'password with space' => array(array('password' => 'Example 123', 'password2' => 'Example 123'), false),
    'invalid newsletter' => array(array('mail_magazine' => '2'), false),
);
$validation = Model_Member::validate('entry');
foreach ($cases as $name => $case)
{
    if ($validation->run(array_replace($valid, $case[0])) !== $case[1])
    {
        throw new RuntimeException('Failed: '.$name);
    }
    echo 'PASS: '.$name.PHP_EOL;
}

// Render both templates to verify syntax, retained values and HTML escaping.
$twig = new \Twig\Environment(new \Twig\Loader\FilesystemLoader(APPPATH.'views'));
$twig->addFunction(new \Twig\TwigFunction('base_url', function () { return '/'; }));
$context = array('userData' => array_replace($valid, array('familyName' => '<script>alert(1)</script>')),
	'data' => array(
		'instructor' => array('id' => 1, 'name' => 'Mary'),
		'course' => array('id' => 1, 'name' => '基本英会話コース'),
		'date' => array('year' => '2026', 'month' => '10', 'day' => '21', 'week' => '水'),
		'time' => 10,
	),
	'prefs' => array('東京都'), 'errors' => array('test' => '入力エラー'),
	'csrf' => array('key' => 'fuel_csrf_token', 'token' => 'csrf-test-token'),
	'submission_token' => 'submission-test-token');
foreach (array('index', 'check', 'confirm') as $page)
{
    $html = $twig->render('reserve/member/'.$page.'.twig', $context);
    if (strpos($html, '<script>alert(1)</script>') !== false || strpos($html, '&lt;script&gt;') === false)
    {
        throw new RuntimeException('HTML escaping failed: '.$page);
    }
    if (strpos($html, 'Example123!') !== false)
    {
        throw new RuntimeException('Password exposed: '.$page);
    }
    echo 'PASS: render and escape '.$page.PHP_EOL;
}

$confirm = $twig->render('reserve/member/confirm.twig', $context);
if (strpos($confirm, 'name="fuel_csrf_token" value="csrf-test-token"') === false ||
	strpos($confirm, 'name="submission_token" value="submission-test-token"') === false ||
	strpos($confirm, 'name="opinion"') === false)
{
	throw new RuntimeException('Confirmation form security fields are missing.');
}
echo 'PASS: confirmation form security fields'.PHP_EOL;
