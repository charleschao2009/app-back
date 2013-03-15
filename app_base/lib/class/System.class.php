<?php
/**
 * 系统处理类
 *
 * @author     bianwei <bianwei@taomee.com>
 * @package    Core
 * @subpackage System
 * @version    $Id: System.class.php 14630 2011-11-01 12:15:45Z becky $
 * @copyright  TaoMee, Inc. Shanghai China. All rights reserved.
 */
class System
{
	/**
	 * 错误处理函数
	 *
	 * @param $type
	 * @param $message
	 * @param $file
	 * @param $line
	 */
	public static function ErrorHandler($type, $message, $file, $line)
	{
	    switch($type){
	        case E_ERROR:
	        case E_USER_ERROR:
	        case E_PARSE:
	            $s_log = "ERROR: [$type] $message " . basename($file) . " 第  $line 行.";
	            Log::write($s_log, 'system', 'error');
	            break;
	        case E_WARNING:
	        case E_USER_WARNING:
	        	if(!defined('SYSTEM_CONFIG_ATTR') || SYSTEM_CONFIG_ATTR != 'release') {
	            	$s_log = "WARNING: [$type] $message " . basename($file) . " 第  $line 行.";
	            	Log::write($s_log, 'system', 'warning');
	        	}
	            break;
	        case E_STRICT:
	        case E_NOTICE:
	        case E_USER_NOTICE:
	            if(!defined('SYSTEM_CONFIG_ATTR') || SYSTEM_CONFIG_ATTR != 'release') {
	                $s_log = "NOTICE: [$type] $message " . basename($file) . " 第  $line 行.";
	                Log::write($s_log,'system', 'notice');
	            }
	            break;
	        default:
	            $s_log = "DEFAULT: [$type] $message " . basename($file) . " 第  $line 行.";
	            Log::write($s_log, 'system', 'default');
	            break;
	    }
	}

	public static function ShutDownHandler()
	{
		$error = error_get_last();
	    if ($error['type'] === E_ERROR) {
			$s_log = 'ERROR: ['.$error['type'].']' . $error['message']  . basename($error['type']) . ' 第  '.$error['line'] .'行.';
            Log::write($s_log,'system', 'error');
	    }
	}

	public static function toEnd(){
		global $s_start_time;
		spend_time($s_start_time);
		exit;
	}
}
