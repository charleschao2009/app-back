<?php
/**
 * 日志类
 *
 * @author     bianwei<bianwei@vip.qq.com>
 * @package    log
 * @version    2.0 $Id: Log.class.php 13836 2011-10-17 11:38:54Z melodyzhang $
 * @copyright  TaoMee, Inc. Shanghai China. All rights reserved.
 */
class Log
{
    /**
     * @var int 单个日志文件大小限制字节数
     */
    private static $i_log_size = 5242880; // 1024 * 1024 * 5 = 5M

    /**
     * 写日志
     *
     * @param string $s_log   日志信息
     * @param string $s_type  日志类型 [system|app|...]
     * @param string $s_level 日志级别
     * @return boolean
     */
    public static function write($s_log, $s_type = 'default', $s_level = 'log')
    {
        $s_now_time = date('[y-m-d H:i:s]');
        $s_now_day  = date('Y_m_d');
        // 根据类型设置日志目标位置
        $s_target   = SYSDATA_LOG_PATH . strtolower($s_type) . DS;
        mk_dir($s_target, 0755);
		// 检查日志目录是否可写
		
        if (! is_writable($s_target)) exit('日志目录不可写!');
        // 分级写日志
        switch($s_level)
        {
		case 'error':
                $s_target .= 'Error_' . $s_now_day . '.log';
                break;
            case 'warning':
                $s_target .= 'Warning_' . $s_now_day . '.log';
                break;
            case 'notice':
                $s_target .= 'Notice_' . $s_now_day . '.log';
                break;
            case 'debug':
                $s_target .= 'Debug_' . $s_now_day . '.log';
                break;
            case 'info':
                $s_target .= 'Info_' . $s_now_day . '.log';
                break;
            case 'cache':
                $s_target .= 'Cache_' . $s_now_day . '.log';
                break;
            case 'db':
                $s_target .= 'Db_' . $s_now_day . '.log';
                break;
            default:
				$s_target .= 'Log_' . $s_now_day . '.log';
                break;
        }
        //检测日志文件大小, 超过配置大小则重命名
        if (file_exists($s_target) && self::$i_log_size <= filesize($s_target)) {
            $s_file_name = substr(basename($s_target), 0, strrpos(basename($s_target), '.log')) . '_' . time() . '.log';
			rename($s_target, dirname($s_target) . DS . $s_file_name);
        }
        clearstatcache();
        // 写日志, 返回成功与否
        return error_log("$s_now_time $s_log\n", 3, $s_target);
    }
}
