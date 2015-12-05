<?php
namespace infrajs\mem;
use infrajs\mem\Mem;
use infrajs\ans\Ans;

require_once('../../../../vendor/autoload.php');

$ans = array();
$ans['title'] = 'Проверка доступности сервера';

$conf = Mem::$conf;
if ($conf['cache'] != 'mem') return Ans::err($ans, 'memcache не используется');

if (!class_exists('Memcache')) return Ans::err($ans, 'Нет класса Memcache');

$mem = Mem::memcache();
if (!$mem) return Ans::err('Сервер не доступен');
	
$val = infra_mem_get('test');
if (!$val) {
	infra_mem_set('test', true);
	return Ans::err($ans, 'Неудалось восстановить значение. Требуется F5');
}

return Ans::ret($ans, 'сервер доступен');