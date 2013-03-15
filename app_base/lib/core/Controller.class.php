<?php
/**
 * 控制器抽象类
 */
abstract class Controller {
	public $tpl;
	public $in;
	public $db;
	public $g_config;
	protected $tpl_data; // 模板变量都存放于该变量中，统一输出
	protected static $TEMPLATE_DATA = 'view';

	/**
	 * 构造函数
	 */
	function __construct()
	{
		global $in,$g_config,$db;
		// 声明模板处理器
		if (!defined('API')) {
			$this -> tpl = Template :: getInstance();
			$this -> tpl -> template_dir = SYS_PATH . 'template'.DS.strtolower(get_class($this)) . DS;
			$this -> tpl -> assign('in', $in);
			$this -> tpl_data = array();
		}
		$this -> in = $in;		
		$this -> g_config = $g_config;
		$this -> db = $db;
	} 

	/**
	 * 加载模型
	 * 
	 * @param string $class 
	 */
	public function loadModel($class)
	{
		$args = func_get_args();
		$this -> $class = call_user_func_array('init_model', $args);
		return $this -> $class;
	} 

	/**
	 * 加载类库文件
	 * 
	 * @param string $class 
	 */
	public function loadClass($class)
	{
		if (1 === func_num_args()) {
			$this -> $class = new $class;
		} else {
			$reflectionObj = new ReflectionClass($class);
			$args = func_get_args();
			array_shift($args);
			$this -> $class = $reflectionObj -> newInstanceArgs($args);
		}
		return $this -> $class;
	} 
	/**
	 * 模板注入变量
	 * 
	 * @param
	 */	
	protected function assign($key, $val)
	{
		$this -> tpl_data[$key] = $val;
	} 
	/**
	 * 显示模板
	 * 
	 * @param
	 */	
	protected function display($tpl_file)
	{
		$this -> tpl -> assign(self :: $TEMPLATE_DATA, $this -> tpl_data);
		$this -> tpl -> display($tpl_file);
	}
	
	

	public function __destruct()
	{
	} 
} 
