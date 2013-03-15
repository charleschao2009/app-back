<?php


function upload($fileInput, $path = './', $filename = ''){
	global $g_config;
	if (!isset($_FILES[$fileInput])) {
		echo json_encode(array('success'=>'0','info'=>'没有文件'));
		return false;
	} 
	$fileArr = $_FILES[$fileInput];
	if (is_array($fileArr['name'])) {
		for($i = 0; $i < count($fileArr['name']); $i++) {// 上传多个文件
			$file_name = iconv(
				'utf-8',
				$g_config['system']['charset'],
				$fileArr['name']
			);
			$save_path = $path.$file_name;
			//temp名，大小，保存路径
			$info[] = _upload($fileArr['tmp_name'][$i],$fileArr['size'][$i],$save_path);
		}
	}else { // 上传单个文件
		$file_name = iconv(			
			'utf-8',
			$g_config['system']['charset'],		
			$fileArr['name']
		);
		$info = _upload($fileArr['tmp_name'],$fileArr['size'],$path.$file_name);		
	}
	return $info;
} 
function _upload($tmp_name,$size,$save_path){
	$maxsize = 10 ; //Mb
	if($size > $maxsize * 1048576){
		return array('success'=>'0','info'=>'大小不超过'.$maxsize.'M');
	}
	if(file_exists($save_path)){
		return array('success'=>'0','info'=>'该文件已存在！');
	}
	if(move_uploaded_file($tmp_name,$save_path)){
		return array('success'=>'1','info'=>'上传成功','path'=>$save_path);
	}
	else  {
		return array('success'=>'0','info'=>'移动不成功');
	}
}
?>