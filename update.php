<?php
namespace infrajs\mem;

use infrajs\path\Path;


Path::mkdir(Mem::$conf['cache']);
$mem = &Mem::memcache();
if ($mem) $mem->flush();