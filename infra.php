<?php
namespace infrajs\mem;
use infrajs\infra\Config;

$conf=Infra::config('mem');

Mem::$conf=array_merge(Mem::$conf, $conf);
