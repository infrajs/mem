<?php
namespace infrajs\mem;
use infrajs\event\Event;
use infrajs\infra\Infra;
use infrajs\path\Path;

$conf=&Infra::config('mem');
Mem::$conf=array_merge(Mem::$conf, $conf);
$conf=Mem::$conf;

Event::wheng('update', function () {
	Path::mkdir(Mem::$conf['cache']);
});

