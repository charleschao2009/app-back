<?php

/**
 * 系统函数：			filesize(),file_exists(),pathinfo(),rname(),unlink(),filemtime(),is_readable(),is_wrieteable();
 * 获取扩展名			file_type($file_name)
 * 获取文件详细信息		file_info($file_name,$date="Y/m/d H:i",$list='')
 * 获取文件夹详细信息	path_info($dir,$date="Y/m/d H:i")
 * 递归获取文件夹信息	path_info_more($dir,&$file_num=0,&$path_num=0,&$size=0)
 * 获取文件夹下文件列表	path_list($dir)
 * 路径当前文件[夹]名	get_path_this($path)
 * 获取路径父目录		get_path_father($path)
 * 删除文件				del_file($file)
 * 递归删除文件夹		del_dir($dir)
 * 递归复制文件夹		copy_dir($source, $dest)
 * 创建目录				mk_dir($dir, $mode = 0777)
 * 文件大小格式化		size_format($bytes, $precision = 2)
 * 判断是否绝对路径		path_is_absolute( $path ) 
 * 扩展名的文件类型		ext_type($ext)
 * 文件下载				file_download($file) 
 * 获取文件(夹)权限		get_mode($file)  //rwx_rwx_rwx 
 * 获取配置文件项		get_config($file, $ini, $type="string")
 * 修改配置文件项		update_config($file, $ini, $value,$type="string")
 * 写日志,写到data下	write_log($debug)
 */


// config部分
// 传入参数为程序编码时，有传出，则用程序编码，
// 传入参数没有和输出无关时，则传入时处理成系统编码。
$config['system_charset'] = 'gbk'; //系统编码
$config['app_charset'] = 'utf-8'; //该程序整体统一编码 

/**
 * 获取文件扩展名
 */
function file_type($file_name)
{
	if (file_exists($file_name)) {
		$extend = explode("." , $file_name);
		return end($extend);
	} 
} 

/**
 * 获取文件详细信息
 * 文件名从程序编码转换成系统编码,传入utf8，系统函数需要为gbk
 */
function file_info($file_name, $date = "Y/m/d H:i", $list = '')
{
	global $config;
	$file_name_app = $file_name; //系统编码，文件编码，文件列表时用的名字保证不乱码
	$file_name = iconv($config['app_charset'], $config['system_charset'], $file_name);
	$pathinfo = pathinfo($file_name_app); //文件列表时用的名字保证不乱码  
	// 转为win与linux通用地址类型
	$fileinfo['path'] = iconv($config['system_charset'], $config['app_charset'], strtolower(str_replace('\\', '/', $pathinfo['dirname'])));
	$fileinfo['ext'] = $pathinfo['extension']; 
	// 将系统编码转化成程序编码,以便输出页面不乱码
	if ($config['system_os'] == 'windows') {
		$fileinfo['name'] = $pathinfo['basename'];
	} else {
		$fileinfo['name'] = get_path_this($file_name);
	} 

	if ($list == '') { // 此时获取属性
		$fileinfo['size'] = @filesize($file_name);
		$fileinfo['size_friendly'] = size_format($fileinfo['size'], 2);
		$fileinfo['mode'] = get_mode($file_name_app);
		$fileinfo['atime'] = date($date, @fileatime($file_name)); //访问时间
		$fileinfo['ctime'] = date($date, @filectime($file_name)); //创建时间
		$fileinfo['mtime'] = date($date, @filemtime($file_name)); //最后修改时间
	} else if ($list == 'list') { // 此时是被列表调用。需要传入utf8编码文件名。
		$file_name_system = iconv($config['system_charset'], $config['app_charset'], $file_name_app);

		$fileinfo['is_readable'] = intval(is_readable($file_name));
		$fileinfo['is_writeable'] = intval(is_writeable($file_name));

		$fileinfo['size'] = @filesize($file_name);
		$fileinfo['size_friendly'] = size_format($fileinfo['size'], 2);
		$fileinfo['mode'] = get_mode($file_name_system); //需要程序编码，则先转换。	
		$fileinfo['atime'] = date($date, @fileatime($file_name));
		$fileinfo['ctime'] = date($date, @filectime($file_name));
		$fileinfo['mtime'] = date($date, @filemtime($file_name));
	} 
	return $fileinfo;
} 

/**
 * 获取文件夹详细信息,文件夹属性时调用，包含子文件夹数量，文件数量，总大小
 */
function path_info($dir, $date = "Y/m/d H:i")
{
	global $config; //编码配置项读取，防止中文,文件夹乱码
	$dir_app = $dir;
	$dir = iconv($config['app_charset'], $config['system_charset'], $dir);

	$fileinfo['path'] = str_replace('\\', '/', $dir);
	if (!is_dir($dir)) return false;
	$pathinfo = path_info_more($dir);
	$pathinfo['mode'] = get_mode($dir_app);
	$pathinfo['is_readable'] = intval(is_readable($dir));
	$pathinfo['is_writeable'] = intval(is_writeable($dir));
	$pathinfo['atime'] = date($date, fileatime($dir));
	$pathinfo['ctime'] = date($date, filectime($dir));
	$pathinfo['mtime'] = date($date, filemtime($dir));
	return $pathinfo;
} 

/**
 * 递归获取文件夹信息： 子文件夹数量，文件数量，总大小
 */
function path_info_more($dir, &$file_num = 0, &$path_num = 0, &$size = 0)
{
	if (!$dh = @opendir($dir)) return false;
	while (false !== ($file = @readdir($dh))) {
		if ($file != "." && $file != "..") {
			$fullpath = $dir . "/" . $file;
			if (!@is_dir($fullpath)) {
				$file_num ++;
				$size += @filesize($fullpath);
			} else {
				path_info_more($fullpath, $file_num, $path_num, $size);
				$path_num ++;
			} 
		} 
	} 
	closedir($dh);
	$pathinfo['file_num'] = $file_num;
	$pathinfo['folder_num'] = $path_num;
	$pathinfo['size'] = $size;
	$pathinfo['size_friendly'] = size_format($size);
	return $pathinfo;
} 

/**
 * 获取文件夹下列表信息
 * dir 包含结尾/   d:/wwwroot/test/
 */
function path_list($dir) // 传入需要读取的文件夹路径,为程序编码
{
	global $config; //编码配置项读取，防止中文,文件夹乱码
	$dir_app = $dir;
	$dir = iconv($config['app_charset'], $config['system_charset'], $dir);

	if (!is_dir($dir)) return false;
	$dh = opendir($dir);
	$i = $j = 0; //文件夹与文件
	while (false !== ($file = readdir($dh))) {
		if ($file != "." && $file != "..") {
			$fullpath = $dir . $file;
			if (is_dir($fullpath)) {
				$fullpath = $dir . $file . '/';
				$folderlist[$i] = array(
					// 将程序编码转化成系统编码，用于系统
					'name' => iconv($config['system_charset'], $config['app_charset'], $file),
					'atime' => date("Y/m/d H:i", fileatime($fullpath)),
					'ctime' => date("Y/m/d H:i", filectime($fullpath)),
					'mtime' => date("Y/m/d H:i", filemtime($fullpath))
					);
				$i++;
			} else {
				// 组合字符串为app编码
				$fullpath = $dir_app . iconv($config['system_charset'], $config['app_charset'], $file);
				$filelist[$j] = file_info($fullpath, 'Y/m/d H:i', 'list');
				$j++;
			} 
		} 
	} 
	closedir($dh);
	$list = array('folderlist' => $folderlist, 'filelist' => $filelist);
	return $list;
} 
/**
 * 获取一个路径(文件夹&文件) 当前文件[夹]名
 * D:/www/test/11/	==>11
 * D:/www/test/1.c	==>1.c
 */
function get_path_this($path)
{
	$len = strlen($path);
	if ($path[$len-1] == "/") { // 去掉结尾/符号
		$path = substr($path, 0, $len-1);
	} 
	$arr = explode('/', $path);
	return end($arr);
} 

/**
 * 获取一个路径(文件夹&文件) 父目录
 * D:/www/test/11/	==>D:/www/test/
 * D:/www/test/1.c	==>D:/www/test/
 */
function get_path_father($path)
{
	$len = strlen($path);
	if ($path[$len-1] == "/") {
		$path = substr($path, 0, $len-1);
		$len = $len-1;
	} 
	$name_len = strlen(end(explode('/', $path)));
	return substr($path, 0, $len - $name_len-1) . '/';
} 

/**
 * 删除文件 传入参数编码为操作系统编码. win--gbk
 */
function del_file($file)
{
	if (file_exists($file)) {
		if (!@unlink($fullpath)) { // 删除不了，尝试修改文件权限
			chmod($fullpath, 0777);
			if (!@unlink($fullpath)) {
				return false;
			} 
		} else {
			return true;
		} 
	} else {
		return false; //文件不存在
	} 
} 

/**
 * 删除文件夹 传入参数编码为操作系统编码. win--gbk
 */
function del_dir($dir)
{
	if (!$dh = @opendir($dir)) return false;
	while (false !== ($file = @readdir($dh))) {
		if ($file != "." && $file != "..") {
			$fullpath = $dir . '/' . $file;
			if (!is_dir($fullpath)) {
				if (!@unlink($fullpath)) { // 删除不了，尝试修改文件权限
					chmod($fullpath, 0777);
					if (!@unlink($fullpath)) {
						return false;
					} 
				} 
			} else {
				if (!del_dir($fullpath)) {
					chmod($fullpath, 0777);
					if (!del_dir($fullpath)) return false;
				} 
			} 
		} 
	} 
	@closedir($dh);
	if (@rmdir($dir)) {
		return true;
	} else {
		return false;
	} 
} 

/**
 * 复制文件夹 
 * eg:将D:/wwwroot/下面wordpress复制到
	D:/wwwroot/www/explorer/0000/del/1/
 * 末尾都不需要加斜杠，复制到地址如果不加源文件夹名，
 * 就会将wordpress下面文件复制到D:/wwwroot/www/explorer/0000/del/1/下面
 * $from = 'D:/wwwroot/wordpress';
 * $to = 'D:/wwwroot/www/explorer/0000/del/1/wordpress';
 */

function copy_dir($source, $dest)
{
	$result = false;
	if (is_file($source)) {
		if ($dest[strlen($dest)-1] == '/') {
			$__dest = $dest . "/" . basename($source);
		} else {
			$__dest = $dest;
		} 
		$result = @copy($source, $__dest); 
		// echo iconv( $config['app_charset'],$config['system_charset'], $source);
		@chmod($__dest, 0755);
	} elseif (is_dir($source)) {
		if ($dest[strlen($dest)-1] == '/') {
			$dest = $dest . basename($source);
			@mkdir($dest);
			@chmod($dest, 0755);
		} else {
			@mkdir($dest, 0755);
			@chmod($dest, 0755);
		} 
		$dirHandle = opendir($source);
		while (false !== ($file = readdir($dirHandle))) {
			if ($file != "." && $file != "..") {
				if (!is_dir($source . "/" . $file)) {
					$__dest = $dest . "/" . $file;
				} else {
					$__dest = $dest . "/" . $file;
				} 
				$result = copy_dir($source . "/" . $file, $__dest);
			} 
		} 
		closedir($dirHandle);
	} else {
		$result = false;
	} 
	return $result;
} 

/**
 * 创建目录
 * 
 * @param string $dir 
 * @param int $mode 
 * @return bool 
 */
function mk_dir($dir, $mode = 0777)
{
	if (is_dir($dir) || @mkdir($dir, $mode))
		return true;
	if (! mk_dir(dirname($dir), $mode))
		return false;
	return @mkdir($dir, $mode);
} 


/**
 * 文件大小格式化
 * 
 * @param  $ :$bytes, int 文件大小
 * @param  $ :$precision int  保留小数点
 * @return :string
 */
function size_format($bytes, $precision = 2)
{ 
	// $unit = array('B','KB','MB','GB','TB','PB');
	// $i = floor(log($bytes, 1024));
	// return @round ($bytes / pow(1024, $i), $precision).' '.$unit[$i];
	if ($bytes == 0) {
		return "0 B";
	} 
	// 优化，不使用幂函数
	$unit = array('TB' => 1099511627776, // pow( 1024, 4)
		'GB' => 1073741824,		// pow( 1024, 3)
		'MB' => 1048576,		// pow( 1024, 2)
		'kB' => 1024,			// pow( 1024, 1)
		'B ' => 1,				// pow( 1024, 0)
	);
	foreach ($unit as $un => $mag) {
		if (doubleval($bytes) >= $mag)
			return round($bytes / $mag, $precision) . ' ' . $un;
	} 
} 

/**
 * 判断路径是不是绝对路径
返回true('/foo/bar', 'c:\windows').
 * 
 * @return 返回true则为绝对路径，否则为相对路径
 */
function path_is_absolute($path)
{
	if (realpath($path) == $path)// *nux 的绝对路径 /home/my
		return true;
	if (strlen($path) == 0 || $path[0] == '.')
		return false;
	if (preg_match('#^[a-zA-Z]:\\\\#', $path))// windows 的绝对路径 c:\aaa\
		return true;
	return (bool)preg_match('#^[/\\\\]#', $path); //绝对路径 运行 / 和 \绝对路径，其他的则为相对路径
} 

/**
 * 获取扩展名的文件类型
 * 
 * @param  $ :$ext string 扩展名
 * @return :string;
 */
function ext_type($ext)
{
	$ext2type = array('text' => array('txt', 'ini', 'log', 'asc', 'csv', 'tsv', 'vbs', 'bat', 'cmd', 'inc', 'conf', 'inf'),
		'code'		=> array('css', 'htm', 'html', 'php', 'js', 'c', 'cpp', 'h', 'java', 'cs', 'sql', 'xml'),
		'picture'	=> array('jpg', 'jpeg', 'png', 'gif', 'ico', 'bmp', 'tif', 'tiff', 'dib', 'rle'),
		'audio'		=> array('mp3', 'ogg', 'oga', 'mid', 'midi', 'ram', 'wav', 'wma', 'aac', 'ac3', 'aif', 'aiff', 'm3a', 'm4a', 'm4b', 'mka', 'mp1', 'mx3', 'mp2'),
		'flash'		=> array('swf'),
		'video'		=> array('rm', 'rmvb', 'flv', 'mkv', 'wmv', 'asf', 'avi', 'aiff', 'mp4', 'divx', 'dv', 'm4v', 'mov', 'mpeg', 'vob', 'mpg', 'mpv', 'ogm', 'ogv', 'qt'),
		'document'	=> array('doc', 'docx', 'docm', 'dotm', 'odt', 'pages', 'pdf', 'rtf', 'xls', 'xlsx', 'xlsb', 'xlsm', 'ppt', 'pptx', 'pptm', 'odp'),
		'rar_achieve'	=> array('rar', 'arj', 'tar', 'ace', 'gz', 'lzh', 'uue', 'bz2'),
		'zip_achieve'	=> array('zip', 'gzip', 'cab', 'tbz', 'tbz2'),
		'other_achieve' => array('dmg', 'sea', 'sit', 'sqx')
	);
	foreach ($ext2type as $type => $exts) {
		if (in_array($ext, $exts)) {
			return $type;
		} 
	} 
} 

/**
 * 文件下载
 */
function file_download($file)
{
	if (file_exists($file)) {
		header('Content-Description: File Transfer');
		header('Content-Type: application/octet-stream');
		header('Content-Disposition: attachment; filename=' . get_path_this($file));
		header('Content-Transfer-Encoding: binary');
		header('Expires: 0'); //网页缓存过期时间
		header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
		header('Pragma: public');
		header('Content-Length: ' . filesize($file));

		ob_clean();
		flush();
		readfile($file);
		exit;
	} 
} 

/**
 * 获取文件(夹)权限 rwx_rwx_rwx
 */
function get_mode($file)
{
	global $config;
	$file = iconv($config['app_charset'], $config['system_charset'], $file);

	$Mode = @fileperms($file);
	if ($Mode &0x1000) $Type = 'p'; // FIFO pipe
	elseif ($Mode &0x2000) $Type = 'c'; // Character special
	elseif ($Mode &0x4000) $Type = 'd'; // Directory
	elseif ($Mode &0x6000) $Type = 'b'; // Block special
	elseif ($Mode &0x8000) $Type = '-'; // Regular
	elseif ($Mode &0xA000) $Type = 'l'; // Symbolic Link
	elseif ($Mode &0xC000) $Type = 's'; // Socket
	else $Type = 'u'; // UNKNOWN 
	// Determine les permissions par Groupe
	$Owner['r'] = ($Mode &00400) ? 'r' : '-';
	$Owner['w'] = ($Mode &00200) ? 'w' : '-';
	$Owner['x'] = ($Mode &00100) ? 'x' : '-';
	$Group['r'] = ($Mode &00040) ? 'r' : '-';
	$Group['w'] = ($Mode &00020) ? 'w' : '-';
	$Group['e'] = ($Mode &00010) ? 'x' : '-';
	$World['r'] = ($Mode &00004) ? 'r' : '-';
	$World['w'] = ($Mode &00002) ? 'w' : '-';
	$World['e'] = ($Mode &00001) ? 'x' : '-'; 
	// Adjuste pour SUID, SGID et sticky bit
	if ($Mode &0x800) $Owner['e'] = ($Owner['e'] == 'x') ? 's' : 'S';
	if ($Mode &0x400) $Group['e'] = ($Group['e'] == 'x') ? 's' : 'S';
	if ($Mode &0x200) $World['e'] = ($World['e'] == 'x') ? 't' : 'T';

	$Mode = "$Type$Owner[r]$Owner[w]$Owner[e] $Group[r]$Group[w]$Group[e] $World[r]$World[w]$World[e]";
	return $Mode;
} 

/**
 * 兼容php4的file_put_contents
 */
if (!function_exists('file_put_contents')) {
	function file_put_contents($file, &$date)
	{
		if (!is_string($file)) return(false);
		if (!is_string($data) && !is_array($data)) return(false);
		if (!is_int($flags)) return(false);
		$fp = @fopen($file, "w");
		if (!$fp) {
			return false;
		} else {
			fwrite($fp, $date);
			fclose($fp);
			return true;
		} 
	} 
} 

/**
 * 配置文件操作(查询了与修改)
 * 默认没有第三个参数时，按照字符串读取提取''中或""中的内容
 * 如果有第三个参数时为int时按照数字int处理。
	调用demo
 * $name="admin";//kkkk
 * $bb='234';
 * 
 * $bb=getconfig("./2.php", "bb", "string");
 * updateconfig("./2.php", "name", "admin");
 */
function get_config($file, $ini, $type = "string")
{
	if (!file_exists($file)) return false;
	$str = file_get_contents($file);
	if ($type == "int") {
		$config = preg_match("/" . preg_quote($ini) . "=(.*);/", $str, $res);
		return $res[1];
	} else {
		$config = preg_match("/" . preg_quote($ini) . "=\"(.*)\";/", $str, $res);
		if ($res[1] == null) {
			$config = preg_match("/" . preg_quote($ini) . "='(.*)';/", $str, $res);
		} 
		return $res[1];
	} 
} 
function update_config($file, $ini, $value, $type = "string")
{
	if (!file_exists($file)) return false;
	$str = @file_get_contents($file);
	$str2 = "";
	if ($type == "int") {
		$str2 = preg_replace("/" . preg_quote($ini) . "=(.*);/", $ini . "=" . $value . ";", $str);
	} else {
		$str2 = preg_replace("/" . preg_quote($ini) . "=(.*);/", $ini . "=\"" . $value . "\";", $str);
	} 
	@file_put_contents($file, $str2);
} 

/**
 * 记录日志
 */
function write_log($debug)
{
	if (!define("LOG_PATH")){
		echo 'not define log_path!';
		return ;
	}
	$log_path = LOG_PATH . '/data/log/';
	$now_time = date('[y-m-d H:i:s]: ');
	$now_day  = date('__Y_m_d');
	if (is_dir($log_path)) {
		mk_dir();
	}
	$logfile = $log_path . $now_day . '.txt'; 
	if (! is_writable($s_target)) exit('日志目录不可写!');
	$fp = fopen($logfile, 'a');
	fwrite($fp, $now_time . $debug . "\n");
	fclose($fp);
} 

