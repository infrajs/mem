<?php

use infrajs\rest\Rest;
use infrajs\mem\Mem;
use akiyatkin\fs\FS;
use infrajs\ans\Ans;

Rest::get( function (){
	$mem = Mem::memcache();
	if ($mem) {
		echo 'Есть memcache';
		$ar = $mem->getStats();
		echo '<pre>';
		print_r($ar);
	} else {
		echo 'Используется файловая система<br><form method="post"><input type="hidden" name="clear" value="yes"><button type="submit">Очистить</button></form>';
		echo Mem::$conf['cache'];
		echo '<hr>';
		$size = 0;
		$list = array();
		
		if (Ans::REQ('clear')) {
			FS::scandir(Mem::$conf['cache'], function($file) {
				FS::unlink(Mem::$conf['cache'].$file);
			});	
		}

		FS::scandir(Mem::$conf['cache'], function($file) use (&$size, &$list){

			$list[$file] = FS::filemtime(Mem::$conf['cache'].$file);
			$size += FS::filesize(Mem::$conf['cache'].$file);
		});
		
		arsort($list);
		echo round($size/1000000,2).' Mb<br>';
		echo sizeof($list).' записей';
		echo '<pre>';
		foreach ($list as $file => $key) {
			echo '<a href="/'.Mem::$conf['cache'].$file.'">'.$file.'</a>'."\n";
		}

	}
});