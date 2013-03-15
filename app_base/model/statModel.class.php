<?php
/**
 * ios业务模型
 * @author      charleschao<charleschao@taomee.com>
 * @package     model
 * @subpackage  no
 * @version     $Id$
 * @copyright   TaoMee, Inc. Shanghai China. All rights reserved.
 */
class statModel extends Model {
	public $sdb;
	function __construct() {
		$this->sdb = null;
		parent::__construct ();
	}
	function dbs_init() {
		if ($this->sdb == null) {
			$this->sdb = new mysql ( $this->g_config ['db_app_video'] );
		}
	}
	
	/**
	 * 更新登录时间
	 */
	
	public function log($time,$device_token,$version){
		$this->dbs_init ();
		
		// 从list进入 1
		$log_sql = "select count(1) from tm_push_device where device_token ='".$device_token."'";
		$log_count = $this->sdb->get_one ( $log_sql );
		if($log_count==0){
			$log_insert_sql = "insert into tm_push_device(version,device_token,log_time) values ('".$version."','".$device_token."',".$time.")";
			$this->sdb->insert($log_insert_sql);
		}else{
			$log_update_sql = "update tm_push_device set version ='".$version."',log_time = '".$time."' where device_token = '".$device_token."'";
			$this->sdb->update($log_update_sql);
		}
	}
	

	
	/**
	 * 统计剧信息
	 */
	public function statSeries($device_id, $time, $main_category, $list_type, $age, $series_id, $chapter_id) {
		$this->dbs_init ();
		$stat_series_sql = "insert into tm_stat(device_id,time_view,main_category,list_search_type,age,series_id,chapter_id
				) values('" . $device_id . "'," . $time . "," . $main_category . "," . $list_type . "," . $age . "," . $series_id . "," . $chapter_id . ")";
		$this->sdb->insert ( $stat_series_sql );
	}
	
	/**
	 * 统计搜索关键字信息
	 */
	public function statSearch($device_id, $time, $key_words,$list_search_type) {
		$this->dbs_init ();
		$stat_search_sql = "insert into tm_stat(device_id,time_view,key_words,list_search_type) 
				values('" . $device_id . "'," . $time . ",'" . $key_words ."',".$list_search_type. ")";
		$this->sdb->insert ( $stat_search_sql );
	}
	
	/**
	 * 统计搜索关键字信息
	 */
	public function statElse($device_id, $time, $count_category) {
		$this->dbs_init ();
		$stat_else_sql = "insert into tm_stat(device_id,time_view,count_type) values('" . $device_id . "'," . $time . "," . $count_category . ")";
		$this->sdb->insert ( $stat_else_sql );
	}
}
