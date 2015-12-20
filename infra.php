<?php
namespace infrajs\mem;
use infrajs\event\Event;
use infrajs\infra\Config;
use infrajs\path\Path;

$conf=&Config::get('mem');
Mem::$conf=array_merge(Mem::$conf, $conf);
$conf=Mem::$conf;


Event::handler('oninstall', function () {
	Path::mkdir(Mem::$conf['cache']);
},'mem');

