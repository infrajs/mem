<?php
namespace infrajs\mem;
use infrajs\mem\Mem;
use infrajs\ans\Ans;
use infrajs\config\Config;

if (!is_file('vendor/autoload.php')) {
	chdir('../../../../');
	require_once('vendor/autoload.php');
}

$ans = array();
$ans['title'] = 'Проверка доступности сервера';


$conf = Mem::$conf;

if ($conf['type'] != 'mem') {
	$ans['class']="bg-warning";
	return Ans::ret($ans, 'memcache не используется config.mem.mem');
}

if (!class_exists('Memcache')) return Ans::err($ans, 'Нет класса Memcache');

$mem = Mem::memcache();
if (!$mem) {
	return Ans::err($ans, 'Сервер не доступен');
}
Mem::set('test', true);
$val = Mem::get('test');
if (!$val) return Ans::err($ans, 'Неудалось восстановить значение. Требуется F5');

return Ans::ret($ans, 'сервер доступен');