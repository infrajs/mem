<?php
namespace infrajs\mem;
use infrajs\ans\Ans;

if (!is_file('vendor/autoload.php')) {
	chdir('../../../../');
}

require_once('vendor/autoload.php');


$ans = array();
$ans['title'] = 'Проверка кэша';

$conf = Mem::$conf;
if ($conf['type'] != 'fs') {
	return Ans::err($ans, 'Mem не настроен на файловую систему');
}

$val = Mem::get('test');
if (!$val) {
	Mem::set('test', true);
	return Ans::err($ans, 'Неудалось восстановить значение. Требуется F5');
}

return Ans::ret($ans, 'Кэш работает');