<?php
/**
 * 自定义异常类
 *
 */

abstract class MyException extends Exception
{
    function __construct($message)
    {
        global $g_config;

        $args = func_get_args();

        //parent::__construct($message,$args[1]);

        $this->g_config = $g_config;
    }

    function __destruct()
    {
        System::toEnd();
    }
}

class MySqlException extends MyException
{
    /**
     * 得到异常错误信息
     *
     * @return string
     */
    function getError()
    {
        //error message
        $errorMsg = 'Error number : <b>' . mysql_errno() . '</b><br>Error message : <b>' . mysql_error() . '</b>';
        return $errorMsg;
    }

    /**
     * 得到异常错误请求语句
     *
     * @return string
     */
    function getQuery()
    {
        //error message
        if($this->getMessage())
        {
            $errorMsg =  'Error query : <b>' . $this->getMessage() . '</b>';
            return $errorMsg;
        }
    }
}

class UrlException extends MyException
{
}

class AppException extends MyException
{
}
