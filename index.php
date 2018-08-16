<?php

use infrajs\rest\Rest;
use infrajs\mem\Mem;
use akiyatkin\fs\FS;

Rest::get( function (){
	$mem = Mem::memcache();
	if ($mem) {
		echo 'Есть memcache';
		$ar = $mem->getStats();
		echo '<pre>';
		print_r($ar);
	} else {
		echo 'Используется файловая система<br>';
		echo Mem::$conf['cache'];
		echo '<hr>';
		$size = 0;
		$list = array();
		FS::scandir(Mem::$conf['cache'], function($file) use (&$size, &$list){
			$list[] = $file;
			$size += FS::filesize(Mem::$conf['cache'].$file);
		});
		echo round($size/1000000,2).' Mb<br>';
		echo sizeof($list).' записей';
		echo '<pre>';
		foreach ($list as $file) {
			echo '<a href="/'.Mem::$conf['cache'].$file.'">'.$file.'</a>'."\n";
		}

	}
});