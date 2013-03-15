<?php
/**
 * 此文件是接口程序路由
 * 
 * @package class
 * @author Jerry 
 * @version $Id: application.class.php 18401 2012-03-06 09:29:58Z janeywu $
 */

/**
 * 程序路由处理类
 * 这里类判断外界参数调用内部方法
 */
class Application {
	public $controller = null;			//类名
	public $action = null;				//方法名
	public $default_controller = null;	//默认的类名
	public $default_do = null;			//默认的方法名
	public $controller_var = '';		//Post or Get 控制器变量
	public $action_var = '';			//Post or Get 函数变量
	public $model = '';					//控制器对应模型  对象。
	
	/**
	 * 设置默认的类名
	 * 
	 * @param string $default_controller 
	 */
	public function setDefaultController($default_controller)
	{
		$this -> default_controller = $default_controller;
	} 

	/**
	 * 设置默认的方法名
	 * 
	 * @param string $default_action 
	 */
	public function setDefaultAction($default_action)
	{
		$this -> default_action = $default_action;
	} 

	/**
	 * 设置控制器子目录
	 * 
	 * @param string $dir 
	 */
	public function setSubDir($dir)
	{
		$this -> sub_dir = $dir;
	} 

	/**
	 * 运行自动加载的控制器
	 */
	public function autorun()
	{
		global $g_config; 
		if (count($g_config['autorun']) > 0) {
			foreach ($g_config['autorun'] as $key => $var) {
				$sub_dir = $this -> sub_dir ? $this -> sub_dir . '/' : '';
				$class_file = CONTROLLER_DIR . $sub_dir . $var['controller'] . '.class.php';

				if (!is_file($class_file)) {
					throw new AppException('Invalid controller ' . $var['controller']);
				}

				require_once $class_file;
				$class = strtolower($var['controller']);
				if (!class_exists($class)) {
					throw new AppException('Invalid Class ' . $class);
				}

				$instance = new $class();
				$function = $var['function'];
				if (!method_exists($instance, $function)) {
					throw new AppException('Invalid Function ' . $function);
				}
				$instance -> $function();
			}
		} 
	} 

	/**
	 * 调用实际类和方式
	 */
	function run()
	{
		global $in;
		global $g_config;
		if (! empty($g_config['app'])) {
			$this->controller_var = $g_config['app']['controller_var'];
			$this->action_var	  = $g_config['app']['action_var'];
		}
		if(defined('API') 
			&& !isset($in[$this -> controller_var]) 
			&& !isset($in[$this -> action_var])
		){
			die('has not action or controller!');
		}

		$this -> controller = isset($in[$this -> controller_var]) 
		? $in[$this -> controller_var] : $this -> default_controller;
		$this -> action = isset($in[$this -> action_var]) ? $in[$this -> action_var] : $this -> default_action;
		$this -> controller = trim($this -> controller, " \t\r\n\/\\.");
		
		//自动加载运行类。
		$this->autorun();
		$instance  = init_controller($this -> controller); //加载模型
		$function = $this -> action;
		
		if(!defined('API')){ //not api ,then run the code about template
			$old_tpl_dir = $instance -> tpl -> template_dir;
			$instance -> tpl -> template_dir = $old_tpl_dir;
			$instance -> tpl -> assign('controller', $this -> controller);
			$instance -> tpl -> assign('action', $function);
		}
		$instance -> $function();
	}
} 
