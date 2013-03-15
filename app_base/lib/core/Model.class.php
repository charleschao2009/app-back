<?php
/**
 * 模型抽象类
 * 一个关于各种模型的基本行为类，每个模型都必须继承这个类的方法
 */
abstract class Model {
	var $ins_data;
	var $db = null;
	var $dbs = null;
	var $in;
	var $g_config;

	/**
	 * 构造函数
	 * @return Null 
	 */
	function __construct()
	{
		global $g_config, $in, $logger,$db;
		$this -> in = $in;
		$this -> g_config = $g_config;
		$this -> db = $db;
	}
	/**
	 * 初始化memcache
	 *
	 * @param
	 */
	protected function initMemcache()
	{
		$mem = new Memcache();
		$mem->connect($this -> g_config['memcache']['host'], $this -> g_config['memcache']['port']);
		return $mem;
	}
	/**
	 * 加载类库文件
	 *
	 * @param string $class
	 */
	function loadClass($class)
	{
		if ( 1 === func_num_args() )
		{
			
			$this->$class = new $class;
		}
		else
		{
			$reflectionObj = new ReflectionClass($class);
			$args = func_get_args();
			array_shift($args);
	
			$this->$class = $reflectionObj->newInstanceArgs($args);
		}
	}
	
	/**
	 * 加载模型
	 *
	 * @param string $class
	 */
	function loadModel($class)
	{
		$args = func_get_args();
		$this->$class = call_user_func_array('init_model',$args);
		return $this->$class;
	}
}