<?php
namespace infrajs\mem;
use infrajs\path\Path;

if (!is_file('vendor/autoload.php')) chdir('../../../');
require_once('vendor/autoload.php');

Path::req('*path/install.php');

Path::mkdir(Mem::$conf['cache']);
