<?php
namespace infrajs\mem;
use infrajs\path\Path;

class Mem {
	public static $conf = array();
	public static $pref = false;
	public static function memprefix() {
		if (Mem::$pref) return Mem::$pref;
		$str = md5(__DIR__);
		$str = substr($str,0,5);
		Mem::$pref = $str;
		return Mem::$pref;
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
			//$key = Path::encode($key);
			$key = md5($key);
			if (!Path::$conf['fs']) die('Filesystem protected by Path::$conf[fs]=false set it on true');
			//$v = serialize($val);
			$v = json_encode($val, JSON_INVALID_UTF8_SUBSTITUTE | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
			$r = file_put_contents(Mem::$conf['cache'].$key.'.json', $v);
			if (!$r) {
				echo '<pre>';
				debug_print_backtrace();
				die('Отстуствует папка или нет доступа к файловой системе.');
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
			//$key = Path::encode($key);
			$key = md5($key);

			$dir = Path::theme($conf['cache']);
			if ($dir && is_file($dir.$key.'.json')) {
				$r = file_get_contents($dir.$key.'.json');
				//$r = unserialize($r);
				$r = json_decode($r, true);
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
			//$key = Path::encode($key);
			$key = md5($key);
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
			if (is_file($dir.$key.'.json')) {
				$r = unlink($dir.$key.'.json');
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
	public static $mem = null;
	public static function &memcache()
	{
		if (is_null(Mem::$mem)) {
			$conf = Mem::$conf;
			if ($conf['type'] != 'mem') {
				Mem::$mem = false;
				return Mem::$mem;
			}
			if (!class_exists('Memcache')) {
				Mem::$mem = false;
				return Mem::$mem;
			}
			if (!$conf['memcache']) {
				Mem::$mem = false;
				return Mem::$mem;
			}
			
			Mem::$mem = new \Memcache();
			Mem::$mem->connect($conf['memcache']['host'], $conf['memcache']['port']) or die('Could not connect');
		}
		return Mem::$mem;
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
