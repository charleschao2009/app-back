<?php
/**
 * 此文件是初始化程序文件
 * 主要是用于路径宏定义，相关函数、类的引入。
 */
// 定义目录分隔符
if(!defined('DS')) define('DS','/');
system("del /Q ".dirname(dirname(__FILE__))."\data\compile");//清除模板缓存

//程序根目录
define('HOST','http://'.$_SERVER['HTTP_HOST'].'/');
define('SYS_PATH',str_replace('\\','/',dirname(dirname(__FILE__))));//程序根目录
define('WEB_ROOT',str_replace('\\','/',str_replace($_SERVER["PHP_SELF"],'',$_SERVER["SCRIPT_FILENAME"]).'/'));
define('APPHOST',HOST.str_replace(WEB_ROOT,'',SYS_PATH).'/');	// url根目录

define('CONFIG_DIR',	SYS_PATH .'/config/');		//config目录
define('LIB_DIR',		SYS_PATH .'/lib/');			//lib目录
define('TEMPLATE_DIR',	SYS_PATH .'/template/');	//配置文件目录
define('STATIC_DIR',	SYS_PATH .'/static/');		//静态资源文件目录
define('FONT_DIR',		STATIC_DIR .'font/');		//字体文件目录

define('FUNCTION_DIR',	LIB_DIR . 'functions/');	//function 目录
define('CORE_DIR',		LIB_DIR . 'core/');			//核心类目录
define('CLASS_DIR',		LIB_DIR . 'class/');		//工具类目录
define('DEBUG_DIR',		LIB_DIR . 'debug/');
define('DB_DIR',         LIB_DIR . 'db' . DS);
define('DATE_DIR',		SYS_PATH .'/data/');		//缓存目录
define('COMPILE_DIR',	DATE_DIR .'compile/');		//compile目录

define('CONTROLLER_DIR',SYS_PATH .'/controller/');	//控制器目录
define('MODEL_DIR',		SYS_PATH .'/model/');		//模型目录
define('SYSDATA_LOG_PATH', DATE_DIR .'log/');		//log目录

define('PAGE_NUM',12);

define('CACHE_LIFE_TIME',0);	//compile编译间隔时间，相对mktime
define('ENABLE_GZIP',false);	//模板是否gzip压缩输出，服务器不支持会退出
define('SPHINX_SERVER_HOST',  '10.1.1.122');
define('SPHINX_SERVER_PORT',  9312);
define('CURRENT_TIME',date ( "Y-m-d   h:i:s" ));
//app 引入的类
$include_files = array(
	CONFIG_DIR .'config.php',
	DEBUG_DIR . 'FirePHP.class.php',	// 调试类文件
	DEBUG_DIR . 'FirePHP.class.php',	// firebug 调试类文件

	CLASS_DIR . 'System.class.php', 
	CLASS_DIR . 'Log.class.php',		// 日志类文件

	CORE_DIR . 'Application.class.php',	// 程序路由类
	CORE_DIR . 'Controller.class.php',	// 控制器父类
	CORE_DIR . 'Model.class.php',		// 模型父类
	CORE_DIR . 'Exception.class.php',	// 自定义异常处理文件
	CORE_DIR . 'Logger.class.php',		// 系统调式类文件

	CLASS_DIR . 'mysql.class.php',		// 数据库操作类
	CLASS_DIR . 'template.class.php',	// 模板引擎文件

	FUNCTION_DIR . 'common.function.php',	//公共函数库
	FUNCTION_DIR . 'web.function.php',		//web相关库
	FUNCTION_DIR . 'file.function.php',		//文件操作相关函数库
	FUNCTION_DIR . 'template.function.php',	//模板函数库
	FUNCTION_DIR . 'functions_general.php'	//
);

//api 引入的类
$api_include_files = array(
	CONFIG_DIR .'config.php',

	CLASS_DIR . 'System.class.php', 
	CLASS_DIR . 'Log.class.php',		// 日志类文件

	CORE_DIR . 'Application.class.php',	// 程序路由类
	CORE_DIR . 'Controller.class.php',	// 控制器父类
	CORE_DIR . 'Model.class.php',		// 模型父类
	CORE_DIR . 'Exception.class.php',	// 自定义异常处理文件

	CLASS_DIR . 'mysql.class.php',		// 数据库操作类

	FUNCTION_DIR . 'common.function.php',	//公共函数库
	FUNCTION_DIR . 'file.function.php',		//文件操作相关函数库
	FUNCTION_DIR . 'web.function.php',		//web相关库
);

if(defined('API')) {
	$include_files = $api_include_files ;
}
foreach($include_files as $key=>$val){
	require_once($val);
}
//set_error_handler(array('System', 'ErrorHandler'));
//register_shutdown_function(array('System', 'ShutDownHandler'));

$in = parse_incoming();
$db = null;
//$db = new mysql($g_config['db']);