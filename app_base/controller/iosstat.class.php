<?php

/**
 * ios统计 控制器
 * @author      charleschao<charleschao@taomee.com>
 * @package     controller
 * @subpackage  no
 * @version     $Id$
 * @copyright   TaoMee, Inc. Shanghai China. All rights reserved.
 */
class iosstat extends Controller {
	public function __construct() {
		parent::__construct ();
	}
	
	/*
	 * 更新登录时间
	*/
	
	public function log(){
		$time = strtotime ( CURRENT_TIME );
		$device_token = trim ( $this->in ['device_token'] );
		$version = trim ( $this->in ['version'] );
		$model = $this->loadModel ( 'stat' );
	
		$model->log($time,$device_token,$version);
	
	}
	
	/**
	 * 剧集统计eventSeries
	 */
	public function eventSeries() {
		$time = strtotime ( CURRENT_TIME );
		
		$device_id = trim ( $this->in ['device_id'] );
		$main_category = ( int ) trim ( $this->in ['main_category'] )? ( int ) $this->in ['main_category'] : 0;
		$list_search_type = ( int ) trim ( $this->in ['list_search_type'] )? ( int ) $this->in ['list_search_type'] : 0;
		$age = ( int ) trim ( $this->in ['age'] ) ? ( int ) $this->in ['age'] : 4;
		$series_id = ( int ) trim ( $this->in ['series_id'] ) ? ( int ) $this->in ['series_id'] : 0;
		
		$chapter_id = ( int ) trim ( $this->in ['chapter_id'] )? ( int ) $this->in ['chapter_id'] : 0;
		$model = $this->loadModel ( 'stat' );
		Log::write(__CLASS__.'->'.__FUNCTION__."--".$main_category."(url=".$_SERVER['REQUEST_URI'].")\r\n", 'stat', 'debug');
		$model->statSeries ( $device_id, $time,$main_category,$list_search_type,$age,$series_id,$chapter_id );
		
	}
	
	/**
	 * 关键词统计
	 */
	public function eventSearch() {
		$time = strtotime ( CURRENT_TIME );
		$list_search_type = trim ( $this->in ['list_search_type'] );
		$device_id = trim ( $this->in ['device_id'] );
		$key_words = trim ( $this->in ['key_words'] )? $this->in ['key_words'] : 0;
		$model = $this->loadModel ( 'stat' );
		Log::write(__CLASS__.'->'.__FUNCTION__."--".$list_search_type."(url=".$_SERVER['REQUEST_URI'].")\r\n", 'stat', 'debug');
		$model->statSearch( $device_id, $time,$key_words,$list_search_type );
	}
	/**
	 * 其他统计
	 */
	public function eventElse() {		
		

		$time = strtotime ( CURRENT_TIME );
		
		$device_id = trim ( $this->in ['device_id'] );
		
		$count_category = trim ( $this->in ['count_category'] )? $this->in ['count_category'] : 0;
		Log::write(__CLASS__.'->'.__FUNCTION__."--".$count_category."(url=".$_SERVER['REQUEST_URI'].")\r\n", 'stat', 'debug');
		$model = $this->loadModel ( 'stat' );
		
		$model->statElse( $device_id, $time,$count_category );
	}
}