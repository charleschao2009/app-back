<?php
/**
 * API入口文件
 */

header('P3P: CP=CAO PSA OUR');
header("Cache-Control: maxage=1");
define('API','api');

// if (isset($_SERVER['HTTP_ACCEPT_ENCODING']) && stristr($_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip')){
//     ob_start('ob_gzhandler');
// }
$s_start_time = microtime();
include ('./config/init.php');
//pr($_SERVER);
// 调用分发器
$app = new Application();
$app->setDefaultController ( 'index' ); // 设置默认控制器
$app->setDefaultAction ( 'index' ); // 设置默认控制器函数
$app->run();

require_once FUNCTION_DIR . 'functions_general.php';
require_once 'config/msglog.php';
require_once 'config/config.php';
//统计响应事件
$s_end_time = microtime();
get_load_time($s_start_time, $s_end_time);
//ios 添加统计
if(in_array(strtolower($in['m']), $g_config['need_mgs']) ){
	$tmp = $in['c'];
	global $g_msglog;
	$a_begin_time 		= explode(' ', $s_start_time);
	$a_end_time 		= explode(' ', $s_end_time);
	$s_begin_time = $a_begin_time[1] + $a_begin_time[0];
	$s_end_time   = $a_end_time[1] + $a_end_time[0];
	$f_diff_time = number_format(($s_end_time  - $s_begin_time), 6) * 1000;
	msglog($g_msglog['ios'][$tmp], array((time()%86400).rand(0, 10000),$f_diff_time));
	Log::write($f_diff_time."--".$tmp."(url=".$_SERVER['REQUEST_URI'].")\r\n", 'message', 'ios');
	
	//Log::write($in['uuid'].'|'.$in['r_key'].'|'.($f_diff_time), $tmp, 'log' ,'ios');
} 
spend_time($s_start_time);