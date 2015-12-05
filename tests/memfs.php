<?php
namespace infrajs\mem;
use infrajs\mem\Mem;
use infrajs\ans\Ans;

require_once('../../../../vendor/autoload.php');

Mem::$conf['dir']=sys_get_temp_dir().'/mem/';

if(!is_dir(Mem::$conf['dir'])){
	mkdir(Mem::$conf['dir']);
}

$ans = array();
$ans['dir']=Mem::$conf['dir'];
$ans['title'] = 'Проверка кэша';

$conf = Mem::$conf;
if ($conf['cache'] != 'fs') return Ans::err($ans, 'Mem не настроен на файловую систему');

	
$val = Mem::get('test');
if (!$val) {
	Mem::set('test', true);
	return Ans::err($ans, 'Неудалось восстановить значение. Требуется F5');
}

return Ans::ret($ans, 'Кэш работает');