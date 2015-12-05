<?php
namespace infrajs\mem;
use infrajs\path\Path;
require_once(__DIR__.'/../../../vendor/autoload.php');
require_once(__DIR__.'/../path/install.php');

Path::mkdir(Mem::$conf['cache']);
