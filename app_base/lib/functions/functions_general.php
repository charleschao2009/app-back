<?php



/**
 * 判断函数入口
 * @return string 
 */
function checkEntrance()
{
	return ENTRANCE;
}


function request($url,$method,$data,$file=array(),$headers =array()){
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_HEADER, false);
		curl_setopt($ch, CURLOPT_TIMEOUT, 240);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
		curl_setopt($ch, CURLINFO_HEADER_OUT, TRUE );
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	
		$method = strtolower($method);
		switch ($method) {
			case 'get':
				$url = $url . '?' . http_build_query($data);
				break;
			default:
				curl_setopt($ch, CURLOPT_POST, TRUE);
				if(empty($file)){
					$data = http_build_query($data);
				}else{
					foreach($file as $key =>$f){
						$data[$key] = '@'.$f;
					}
				}
				curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
				break;
		}
		curl_setopt($ch, CURLOPT_URL, $url );
		$response = curl_exec($ch);
		return $response;
}

/**
 * 将obj深度转化成array
 * @param  $obj 要转换的数据 可能是数组 也可能是个对象 还可能是一般数据类型
 * @return array|| 一般数据类型
 */
function my_get_object_vars($obj){
    if(is_array($obj)){
    	foreach($obj as &$value){
    		$value = my_get_object_vars($value);
    	}
    	return $obj;
    }elseif(is_object($obj)){
    	$obj =  get_object_vars($obj);
    	return my_get_object_vars($obj);
    }else{
    	return $obj;
    }
}

/**
 * 公共函数文件
 * @author Jerry
 * @version $Id: functions_general.php 17835 2012-02-07 10:01:26Z janeywu $
 */
function getCurTime()
{
    $t    = time();
    $week = array(
        0=>"星期天",
        1=>"星期一",
        2=>"星期二",
        3=>"星期三",
        4=>"星期四",
        5=>"星期五",
        6=>"星期六"
    );

    return array(
        'date'=>array(
            'y'=>date("Y",$t),
            'm'=>date("m",$t),
            'd'=>date("d",$t)
        ),
        'weekday_num'=>date("N",$t),
        'weekday'=>$week[date("w",$t)],
        'yw'=>date("W",$t),
        'zone'=>date("T",$t)
    );
}


/**
 * 带签算法加密
 * @param $hexdata
 * @return unknown_type
 */
function hex2bin_pri($hexdata)
{
    $bindata = '';
    for($i=0; $i < strlen($hexdata); $i += 2) {
        $bindata .= chr(hexdec(substr($hexdata, $i, 2)));
    }
    return $bindata;
}

/**
 * 带签session
 * @return unknown_type
 */
function bin2hex_s($session)
{
    $tmp = array();

    for ($i = 0; $i < strlen($session); $i++)
    {
        //echo (ord($session[$i]) >> 4) . "\n";
        //echo (ord($seesion[$i]) & 0xF) . "\n";
        $tmp[] = dechex(ord($session[$i]) >> 4);
        $tmp[] = dechex(ord($session[$i]) & 0xF);
    }

    return implode('', $tmp);

}

/**
 * 带签session
 * @param $session
 * @return unknown_type
 */
function hex2bin_s($verify_id)
{
    $tmp = '';

    for ($i = 0; $i < strlen($verify_id); $i += 2)
    {
        $low  = hexdec($verify_id[$i]) << 4;
        $high = hexdec($verify_id[$i + 1]);
        $tmp .= pack('C', $high | $low);
    }

    return $tmp;
}


function __autoload($className)
{
    if (is_file(UTIL_DIR . strtolower($className) . '.class.php'))
    {
        require_once (UTIL_DIR. strtolower($className) . '.class.php');
    }
    elseif (is_file(UTIL_DIR . $className . '.class.php'))
    {
        require_once (UTIL_DIR. $className . '.class.php');
    }
    elseif(preg_match('/.*_in/',$className)&&is_file(PROTO_DIR.substr($className, 0, -3).'.php'))
    {
    	require_once(PROTO_DIR.substr($className, 0, -3).'.php');
    }
    elseif(is_file(PROTO_DIR.$className.'.php'))
    {
    	require_once(PROTO_DIR.$className.'.php');
    }
    else
    {
        /* Error Generation Code Here */
    }
}







/**
 * 显示调式信息
 *
 * @param unknown_type $str
 */
function trace($str = '')
{
    global $db,$in,$logger;

    echo '<br>';
    print_r($in);
    echo '<br>Time : ' . $logger->dumpTime();

    if($db) echo $db->logger->dumpQueries();

    if($str)
    {
        echo '<hr>';
        print_r($str);
    }

}

/**
 * 跳转到指定页面
 * @param $url
 * @param $type
 * @param $target
 * @param $referer 是否附加referer字符串
 * @param $time_out 页面等待时间(当 $type = 'js'
 * @return unknown_type
 */
function goto_url($url,$type = 'header',$target = 'document',$referer = true,$time_out = 0)
{
    if ($referer)
    {
        $url = make_url($url,'referer',rawurlencode($_SERVER['REQUEST_URI']));
    }

    if($type == 'header')
    {
        header("location: " . $url);
        System::toEnd();
    }
    elseif($type == 'js')
    {
        if (0 >= $time_out)
        {
            echo "<script>" . $target . ".location='" . $url . "'\n" . "</script>";
            System::toEnd();
        }
        else if (0 < $time_out)
        {
            echo "<script>function wait_go(){  " . $target . ".location='" . $url . "'\n" . " ;}</script>";
            echo "<script>setTimeout('wait_go()',". $time_out .")</script>";
            System::toEnd();
        }
    }
}

function page($page_num,$current_page,$send_var,$item_num = 10)
{
    $page = '';
    $next_url = '';
    $next_10_url = '';
    $previous_url = '';
    $previous_10_url = '';

    if($page_num == '') return false;

    $start = $current_page - 4;
    $end   = $current_page + 5;

    if ($start <= 1)
    {
        $offset = 1 - $start;
        $start  = 1;
        $end   += $offset;

        if ($end > $page_num)
        {
            $end = $page_num + 1;
        }
        else
        {
            $end += 1;
        }
    }
    else
    {
        if ($end > $page_num)
        {
            $offset = $page_num - $end;
            $start += $offset;
            if ( $start< 1)
            {
                $start = 1;
            }
            $end    = $page_num + 1;
        }
        else
        {
            $end += 1;
        }
    }

    for($i= $start;$i<$end;$i++)
    {
        $link = str_replace("{Page}", $i,$send_var);

        if($current_page == $i)
        {
            $page.= "<b>".$i."</b>&nbsp;";
        }
        else
        {
            $page.= "<a href='".$link."'>[".$i."]</a>&nbsp;";
        }

        $pages[] = array('page_num'=>$i,'page_link'=>$link);
    }

	//上一页/下一页url
    if ($current_page < $page_num)
    {
        $link1= str_replace("{Page}", $current_page+1 ,$send_var);
        $page = $page . "&nbsp;<b><a href='".$link1."' >下一页</a></b>";

        $next_url = $link1;
    }
    
    if($current_page > 1) {
        if(($current_page-1) == 0)
        $link1 = str_replace("{Page}", 0,$send_var);
        else
        $link1= str_replace("{Page}" , $current_page-1 ,$send_var);
        $page= "<b><a href='".$link1."' >上一页</a></b>&nbsp;&nbsp;".$page;

        $previous_url = $link1;
    }

    // 获取首页/末页url
    $first_page_url = str_replace("{Page}" , 1 ,$send_var);
    $last_page_url  = str_replace("{Page}" , $page_num ,$send_var);

    $return = array('page' => $page , 'pages' => $pages , 'next_url' => $next_url , 'previous_url' => $previous_url);

    $return['first_page_url'] = $first_page_url;
    $return['last_page_url']  = $last_page_url;

    return $return;
}

/**
 * 字符替换
 * Enter description here ...
 * @param unknown_type $str
 * @param unknown_type $start
 * @param unknown_type $end
 * @param unknown_type $sss
 */
function CsubStr($str,$start,$end,$sss='...') {
    /*
     UTF-8 version of substr(), for people who can't use mb_substr() like me.
     Length is not the count of Bytes, but the count of UTF-8 Characters

     Author: Windix Feng
     Bug report to: windix(AT)263.net, http://www.douzi.org/blog

     - History -
     1.0 2004-02-01 Initial Version
     2.0 2004-02-01 Use PREG instead of STRCMP and cycles, SPEED UP!
     */
    preg_match_all("/[\x01-\x7f]|[\xc2-\xdf][\x80-\xbf]|\xe0[\xa0-\xbf][\x80-\xbf]|[\xe1-\xef][\x80-\xbf][\x80-\xbf]|\xf0[\x90-\xbf][\x80-\xbf][\x80-\xbf]|[\xf1-\xf7][\x80-\xbf][\x80-\xbf][\x80-\xbf]/", $str, $ar);

    if(func_num_args() >= 3)
    {
        $end = func_get_arg(2);
        if(count($ar[0])>$end)
        {
            $str = join("",array_slice($ar[0],$start,$end)).$sss;
        }
        else
        {
            $str = $str;
        }
    }
    else
    {
        $str = join("",array_slice($ar[0],$start)).$sss;
    }

    $patt = "/(<[^>]*\.\.\.)/";
    preg_match_all($patt, $str, $matches);

    if ( isset($matches[1]) && isset($matches[1][0]) ) {
        $replace_str = $matches[1][0];
        $str = str_replace($replace_str, "...", $str);
    }

    return $str;
}

/**
 * 匹配url
 * Enter description here ...
 * @param unknown_type $str
 */
function replace_cdn_url($str)
{
    global $g_config;

    $str = str_replace('.//', '', $str);
    $str = str_replace('./', '', $str);

    return $str;
}



/**
 * msglog, raw_data
 */
function raw_msglog($type, Array $raw_data_array)
{
    global $g_config;
    $log_file = $g_config['msglog']['log_file'];
    $timestamp = time();

    if(count($raw_data_array) <= 0)
    {
        return false;
    }

    $len = 24;
    $hlen = 24;
    $flag0 = 0;
    $flag = 0;
    $saddr = 0;
    $seqno = 0;

    $packed_body = '';
    foreach($raw_data_array as $data_row)
    {
        $packed_body .= $data_row;
        $len += strlen($data_row);
    }

    $packed_header = pack('vCCV5',$len,$hlen,$flag0,$flag,$saddr,$seqno,$type,$timestamp);
    $packed_data = $packed_header . $packed_body;

    umask(0000);
    $log_file_fd = fopen($log_file,'ab');
    if(!$log_file_fd)
    {
        return false;
    }

    if(!fwrite($log_file_fd,$packed_data))
    {
        return false;
    }

    fclose($log_file_fd);
    return true;
}

/**
 * 记录动态
 * @author Henry <henry@taomee.com>
 * @param int $type
 * @param array $data_array
 */
function newslog($type, $data_array)
{
    if(count($data_array) <= 0)
    {
        return false;
    }

    if($data_array[1] == NEWS_OPEN_APP){
        $s_string = '';
        foreach($data_array as $key => $val)
        {
            if($key > 5){
                $s_string .= 'a'.strlen($val);
            }
        }
        $packed_body = call_user_func_array('pack', array_merge(array('vvVCVV'.$s_string), $data_array));
    }else{
        $packed_body = call_user_func_array('pack', array_merge(array('vvVCV*'), $data_array));
    }

    return raw_msglog($type, array($packed_body));
}

/**
 * 记录日志
 * @author Henry <henry@taomee.com>
 * @param int $type
 * @param array $data_array
 */
function msglog($type, $data_array)
{
    if(count($data_array) <= 0)
    {
        return false;
    }

    $packed_body = '';
    foreach($data_array as $data_row)
    {
        $packed_body .= pack('V',$data_row);
    }

    return raw_msglog($type, array($packed_body));
}

/**
 * 向客户端返回 HTTP 错误
 */
function error($status_code=404)
{
    #@header('Status: '. $status_code, true, $status_code);

    throw new AppException('404');
    System::toEnd();
}

/**
 * 取出数组内的指定键对应的值，组成另一个数组
 * @param $array
 * @param $key
 * @param $callback
 * @return Array
 */
function array_flatten($array, $key, $callback=NULL)
{
    $result = array();
    foreach($array as $v)
    {
        if (isset($v[$key]))
        {
            if (function_exists($callback))
            {
                $result[] = call_user_func($callback, $v[$key]);
            }
            else
            {
                $result[] = $v[$key];
            }
        }
    }
    return $result;
}




/**
 * hex to binary, grabbed from proto.class.php
 */
if(!function_exists('hex2bin'))
{
    function hex2bin($hexdata) {
        $bindata = '';
        for($i=0; $i < strlen($hexdata); $i += 2) {
            $bindata .= chr(hexdec(substr($hexdata, $i, 2)));
        }
        return $bindata;
    }
}



//加载时间统计
function get_load_time($begin, $end)
{
 	$a_begin_time 		= explode(' ', $begin);
    $a_end_time 		= explode(' ', $end);

    $s_begin_time = $a_begin_time[1] + $a_begin_time[0];
    $s_end_time   = $a_end_time[1] + $a_end_time[0];

    $f_diff_time = number_format(($s_end_time  - $s_begin_time), 6) * 1000;

	$uid = 0;
	if(isset($_SESSION['login']['uid'])){
		$uid = $_SESSION['login']['uid'];
	}

    //当前url
    $http = isset($_SERVER["HTTPS"])&&$_SERVER["HTTPS"] ? 'https' : 'http';

	$s_current_url  =  $http.'://'.$_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];

    if($f_diff_time < 50)
    {
        msglog(MSGLOG_RESPONE_TIME_50,array(1));
    }
    elseif($f_diff_time > 50 && $f_diff_time <= 100)
    {
        msglog(MSGLOG_RESPONE_TIME_100,array(1));
    }
    elseif($f_diff_time >= 100 && $f_diff_time <= 150)
    {
        msglog(MSGLOG_RESPONE_TIME_150,array(1));
    }
    elseif($f_diff_time > 150 && $f_diff_time <= 200)
    {
        msglog(MSGLOG_RESPONE_TIME_200,array(1));
    }
    elseif($f_diff_time >= 200 && $f_diff_time <= 300)
    {
        msglog(MSGLOG_RESPONE_TIME_300,array(1));
    }
    elseif($f_diff_time > 300 && $f_diff_time <= 400)
    {
        msglog(MSGLOG_RESPONE_TIME_400,array(1));
    }
    elseif($f_diff_time > 400 && $f_diff_time <= 500)
    {
        msglog(MSGLOG_RESPONE_TIME_500,array(1));
    }
    elseif($f_diff_time > 500 && $f_diff_time <= 600)
    {
        msglog(MSGLOG_RESPONE_TIME_600,array(1));
    }
    elseif($f_diff_time > 600 && $f_diff_time <= 700)
    {
        msglog(MSGLOG_RESPONE_TIME_700,array(1));
    }
    elseif($f_diff_time >= 700 && $f_diff_time <= 800)
    {
        msglog(MSGLOG_RESPONE_TIME_800,array(1));
    }
    elseif($f_diff_time > 800 && $f_diff_time <= 900)
    {
        msglog(MSGLOG_RESPONE_TIME_900,array(1));
    }
    elseif($f_diff_time > 900 && $f_diff_time <= 1000)
    {
        msglog(MSGLOG_RESPONE_TIME_1000,array(1));
    }
    elseif($f_diff_time > 1000 && $f_diff_time <= 2000)
    {
        msglog(MSGLOG_RESPONE_TIME_2,array(1));
        Log::write('uid:'.$uid.';from:'.$s_current_url,'1', 'log', 'time');
    }
    elseif($f_diff_time > 2000 && $f_diff_time <= 3000)
    {
        msglog(MSGLOG_RESPONE_TIME_3,array(1));
        Log::write('uid:'.$uid.';from:'.$s_current_url,'2', 'log', 'time');
    }
    elseif($f_diff_time > 3000 && $f_diff_time <= 4000)
    {
        msglog(MSGLOG_RESPONE_TIME_4,array(1));
        Log::write('uid:'.$uid.';from:'.$s_current_url,'3', 'log', 'time');
    }
    elseif($f_diff_time > 4000 && $f_diff_time <= 5000)
    {
        msglog(MSGLOG_RESPONE_TIME_5,array(1));
        Log::write('uid:'.$uid.';from:'.$s_current_url,'4', 'log', 'time');
    }
    elseif($f_diff_time > 5000 && $f_diff_time <= 10000)
    {
        msglog(MSGLOG_RESPONE_TIME_10,array(1));
        Log::write('uid:'.$uid.';from:'.$s_current_url,'5', 'log', 'time');
    }
    elseif($f_diff_time > 10000){
        msglog(MSGLOG_RESPONE_TIME_11,array(1));
        Log::write('uid:'.$uid.';from:'.$s_current_url,'10', 'log', 'time');
    }
}

function _safe_msglog_get_id($s_key)
{
    if (!$s_key) {
        return false;
    }

    require CONFIG_DIR . 'msglog' . DS . 'all.php';
    global $msglog_conf;

    // parse $s_key
    $a_key = explode('.', $s_key);
    $m_target = $msglog_conf;
    foreach($a_key as $s_key) {
        if (!isset($m_target[$s_key])) {
            if (isset($m_target['other'])) {    // *magic* key
                $m_target = $m_target['other'];
            } else {
                return false;
            }
        } else {
            $m_target = $m_target[$s_key];
        }
    }

    $i_msg_type = (int) $m_target;
    if (!$i_msg_type) {
        return false;
    }

    return $i_msg_type;
}

function safe_msglog_base($s_base_key, $i_delta, $a_value)
{
    if (!$s_base_key || !$a_value) {
        return false;
    }

    $i_msg_type_base = _safe_msglog_get_id($s_base_key);
    if (!$i_msg_type_base) {
        return NULL;
    }
    return msglog($i_msg_type_base + $i_delta, $a_value);
}

function safe_msglog($s_key, $a_value)
{
    if (!$s_key || !$a_value) {
        return false;
    }

    $i_msg_type = _safe_msglog_get_id($s_key);
    if (!$i_msg_type) {
        return NULL;
    }

    return msglog($i_msg_type, $a_value);
}



/**
 * 落日志公共方法
 *
 * @param string $s_log
 * @param string $s_level
 */
function Alog($s_log, $s_type='default', $s_level='debug' , $s_dir = 'app')
{
	if($s_level == 'debug' && !D('IS_DEBUG')){
		return false;
	}

	LOG::write($s_log, $s_type, $s_level, $s_dir);
}


/**
 *将表情标签转换成图片地址 
 */
function tag2img($input)
{
	for($i=1;$i<=20;$i++){
		if(strpos($_SERVER['HTTP_USER_AGENT'],'MSIE 6.0')){
			$faces["{:$i:}"] = "<img src='http://vres.61.com/images/public/face_ie6/face_" . str_pad($i, 2, 0, STR_PAD_LEFT) . ".png'/>";
		}else{
			$faces["{:$i:}"] = "<img src='http://vres.61.com/images/public/face/face_" . str_pad($i, 2, 0, STR_PAD_LEFT) . ".png'/>";
		}
		$faces["{:mole_$i:}"] = "<img src='http://vres.61.com/images/public/mole_face/smile_" . str_pad($i, 3, 0, STR_PAD_LEFT) . ".png'/>";
	} 
	$input = strtr($input, $faces);
	
	return $input;
}

/**
 * 分割播放次数
 * @param int $tims 视频播放次数
 */
function divide_times($times, $op=',', $length = 3)
{
	$times = strrev($times);
	$s_str = strrev(chunk_split($times, $length, $op));

	return substr($s_str, 1);
}