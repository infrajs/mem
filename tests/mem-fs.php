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
	$ans['class']="bg-warning";
	return Ans::ret($ans, 'Mem не настроен на файловую систему');
}

Mem::set('test', true);
$val = Mem::get('test');
if (!$val) {
	return Ans::err($ans, 'Неудалось восстановить значение. Требуется F5');
}

return Ans::ret($ans, 'Кэш работает');