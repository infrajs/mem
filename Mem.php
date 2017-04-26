<?php
namespace infrajs\mem;
use infrajs\once\Once;
use infrajs\path\Path;

class Mem {
	public static $conf = array();
	public static function memprefix() {
		return Once::exec(__FILE__, function () {
			$str = md5(__DIR__);
			$str = substr($str,0,5);
			return $str;
		});
	}
	public static function set($key, $val, $savetofile = false)
	{
		if (!$savetofile) $mem = &static::memcache();
		if (!$savetofile && $mem) {
			$mem->delete(static::memprefix().$key);
			$r = $mem->set(static::memprefix().$key, $val);
			if (!$r) error_log('Слишком большой объём данных для хранения в memcache '.$key);
		} else {
			$conf = static::$conf;
			$key = Path::encode($key);
			if (!Path::$conf['fs']) throw new \Exception('Filesystem protected by Path::$conf[fs]=false set it on true');
			$v = serialize($val);
			
			$r=file_put_contents(static::$conf['cache'].$key.'.ser', $v);
			if(!$r){
				echo '<pre>';
				throw new \Exception('Отстуствует папка или нет доступа к файловой системе.');
			}
			
		}
	}
	public static function get($key, $savetofile = false)
	{
		if (!$savetofile) $mem = &static::memcache();
		if (!$savetofile && $mem) {
			$r = $mem->get(static::memprefix().$key);
		} else {
			$conf = static::$conf;
			$key = Path::encode($key);

			$dir = Path::theme($conf['cache']);

			if ($dir&&is_file($dir.$key.'.ser')) {
				$r = file_get_contents($dir.$key.'.ser');
				$r = unserialize($r);
			} else {
				$r = null;
			}
		}

		return $r;
	}
	public static function delete($key)
	{
		$conf = static::$conf;
		$mem = &static::memcache();
		if ($mem) {
			$r = $mem->delete(static::memprefix().$key);
		} else {
			$conf = static::$conf;
			$key = Path::encode($key);
			$dir = Path::theme($conf['cache']);
			if (!$dir) {
				return;
				//echo '<pre>';
				//throw new \Exception('Not found dir for cache "'.$conf['cache'].'"');
			}
			if (!Path::$conf['fs']) {
				echo '<pre>';
				throw new \Exception('Filesystem protected by Path::$conf[fs]=false set it on true');
			}
			if (is_file($dir.$key.'.ser')) {
				$r = unlink($dir.$key.'.ser');
			} else {
				$r = true;
			}
		}

		return $r;
	}
	public static function flush()
	{
		$mem = &static::memcache();
		if ($mem) {
			$mem->flush();
		} else {
			$conf = static::$conf;
			if (!$conf['cache']) throw new \Exception('Set up cache folder Mem::$conf[cache]');
			$dir = Path::theme($conf['cache']);
			if ($dir) {
				if (!Path::$conf['fs']) throw new \Exception('Filesystem protected by Path::$conf[fs]=false set it on true');
				foreach (glob($dir.'*.*') as $filename) {
					@unlink($filename);
				}
			}
		}
	}
	public static function &memcache()
	{
		
		return Once::exec('Mem::memcache', function () {
			$conf = Mem::$conf;
			if ($conf['type'] != 'mem') return false;
			if (!class_exists('Memcache')) return false;
			if (!$conf['memcache']) return false;
			
			$infra_mem = new \Memcache();
			$infra_mem->connect($conf['memcache']['host'], $conf['memcache']['port']) or die('Could not connect');

			return $infra_mem;
		});
	}
};

Mem::$conf = array(
	'type' => 'fs', //'fs', 'mem'
	'memcache' => array(
		'host' => 'localhost',
		'port' => 23
	), 
	'cache' => Path::resolve('!mem/')
);
