<?php
namespace infrajs\mem;
use infrajs\once\Once;
use infrajs\path\Path;
class Mem {
	public static $conf = array();
	public static function set($key, $val)
	{
		$mem = &static::memcache();
		if ($mem) {
			$mem->delete($key);
			$mem->set($key, $val);
		} else {
			$conf = static::$conf;
			$key = Path::encode($key);
			$dir = $conf['cache'];
			
			$dir = Path::theme($dir);
			if(!$dir) throw new \Exception('Not found '.$dir);

			$v = serialize($val);
			file_put_contents($dir.$key.'.ser', $v);
		}
	}
	public static function get($key)
	{
		$mem = &static::memcache();
		if ($mem) {
			$r = $mem->get($key);
		} else {
			$conf = static::$conf;
			$key = Path::encode($key);
			$dir = $conf['cache'];

			$dir = Path::theme($dir);

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
			$r = $mem->delete($key);
		} else {
			$conf = static::$conf;
			$key = Path::encode($key);
			
			$dir = Path::theme($conf['cache']);
			if(!$dir)die('Not found '.$conf['cache']);

			$r = @unlink($dir.$key.'.ser');
		}

		return $r;
	}
	public static function flush()
	{
		$mem = &static::memcache();
		if ($mem) {
			$mem->flush();
		} else {
			$dir = static::$conf['dir'];
			foreach (glob($dir.'*.*') as $filename) {
				@unlink($filename);
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
			
			$infra_mem = new Memcache();
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
	'cache' => Path::$conf['cache'].'mem/'
);
