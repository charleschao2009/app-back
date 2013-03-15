<?php
/*
 * 模板中使用的函数。
 */


/**
 * 得到数组中的一个值.
 * 
 * @param  $array 
 * @param  $key 
 * @return mixed 
 */
function array_get($array, $key, $prekey = '')
{
	$result = $array[$key];
	if (!empty($prekey)) {
		return $result[ $prekey ];
	} 
	return $result;
}

function  minus($a,$b){
	return $a - $b;
}
