<?php
$s_start_time = microtime ();
include ('config/init.php');
try {
	$app = new Application (); // 声明控制器类
	$app->setDefaultController ( 'index' ); // 设置默认控制器
	$app->setDefaultAction ( 'default_page' ); // 设置默认控制器函数
	$app->run ();
} catch ( Exception $e ) {
	if (! ($e instanceof UrlException)) {
		Log::write ( $e->__toString (), 'system', 'error' );
	}
}

// 统计响应事件
spend_time ( $s_start_time );