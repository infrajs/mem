<?php
namespace infrajs\mem;
use infrajs\infra\Config;

$conf=Config::get('mem');

Mem::$conf=array_merge(Mem::$conf, $conf);
