<?php

/**
 * 此文件是接口的配置文件
 * @author melodyzhang
 * @version 1.0
 */

date_default_timezone_set('Asia/Shanghai');
$g_config = array();

$g_config['system']['root'] = dirname( dirname( __FILE__ ) );// 系统根目录

$g_config['session']['online_hold']  = 1800;	//session有效时间
$g_config['session']['save_type']	 = 1;		//session存放类型 =1 文件，=2 数据库
$g_config['session']['sid_lifetime'] = 3600;	//sid cookie有效时间（默认）
$g_config['msglog']['log_file'] = '/opt/taomee/stat/spool/inbox/app.log';
//系统基础配置

$g_config['system']['charset'] = 'utf-8';		//系统字符集
$g_config['system']['gzip']    = false;			//是否开启gzip压缩输出,只针对template输出
$g_config['system']['error']   = true;			//程序发生错误时显示出错信息,false 为不显示
$g_config['system']['lang']    = 'zh-cn';		//系统语言

$g_config['app']['controller_var']	= 'm';		//Post or Get 控制器变量
$g_config['app']['action_var']		= 'c';		//Post or Get 函数变量
$g_config['top_url'] = 'http://vms.61.com/api/top_play.php'; //针对推荐的剧少于4条的热门推荐剧
$g_config['memcache'] = array("host"=> "192.168.24.168","port"=>11211);

if (strtoupper(substr(PHP_OS, 0,3)) === 'WIN') {
	$g_config['system']['os']='windows';
	$g_config['system']['charset']='gbk';
} else {
	$g_config['system']['os']='linux';
	$g_config['system']['charset']='utf-8';
}

/**
 * 数据库相关配置
 */

$sys_enviroment = '1';

switch($sys_enviroment){
	case '1':
		define('SPHINX_ENV','10.1.15.17');//sphinx环境
		define('LOCAL_HOST',"http://vapi.61.com/");
		$g_config['memcache'] = array("host"=> "10.1.1.72","port"=>11211);//memcache环境
		$g_config['db_vms'] = array(
				'db_type'     => 'mysql',
				'db_name'     => 'cms_video',
				'db_charset'  => 'utf8',
				'db_host'     => '10.1.1.74',
				'db_user'     => 'appUser',
				'db_password' => 'video@api',
		);
		$g_config['db'] = array(
				'db_type'     => 'mysql',
				'db_host'     => '10.1.1.74',
				'db_port'     => '3306',
				'db_name'     => 'cms_video',
				'db_user'     => 'appUser',
				'db_password' => 'video@api',
				'db_charset'  => 'utf8'
		);
		$g_config['db_video_dynamic'] = array(
				'db_type'     => 'mysql',
				'db_name'     => 'db_video_dynamic',
				'db_charset'  => 'utf8',
				'db_host'     => '10.1.1.74',
				'db_user'     => 'appUser',
				'db_password' => 'video@api',
		);
		$g_config['db_app_video'] = array(
				'db_type'     => 'mysql',
				'db_host'     => '10.1.1.72',
				'db_port'     => '3306',
				'db_name'     => 'app_video',
				'db_user'     => 'root',
				'db_password' => 'charles',
				'db_charset'  => 'utf8'
		);
	break;
	


$g_config['need_mgs'] = array('iosseries','iossearch','iosuser');
//error_reporting(E_ALL);
/**
 * 系统自动运行控制器
 * controller:控制器名称, function:具体的函数
 */
$g_config['autorun'] = array();
if(!defined('API')) {
	$g_config['autorun'] = array(
		//array('controller'=>'user','function'=>'login_check'),
	);
}


