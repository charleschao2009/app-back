<?php

/**
 * ios数据控制器
 * @author      charleschao<charleschao@taomee.com>
 * @package	    controller
 * @subpackage	no
 * @version      $Id$
 * @copyright   TaoMee, Inc. Shanghai China. All rights reserved.
 */

class iosseries extends Controller {
	public function __construct() {
		parent::__construct ();
	}
	
	public function indexs(){
		$url = $this->in['url'];
		$result = file_get_contents($url);
		$array = json_decode($result);
	}
	
	
	/**
	 * tab标签
	 */
	
	public function listTab(){
		$model = $this->loadModel ( 'ios' );
		
		$result = $model->listTab ();
		echo json_encode ( $result );
	}
	
	
	
	
	/**
	 * 按类型年龄获取分类数据
	 */
	
	public function listType() {
		$type = trim ( $this->in ['type'] );
		$count = ( int ) trim($this->in ['count']) ? ( int ) $this->in ['count'] : 15;
		$page = ( int ) trim($this->in ['page']) ? ( int ) $this->in ['page'] : 1;
		$age = ( int ) trim($this->in ['age']) ? ( int ) $this->in ['age'] : 0;
		
		$model = $this->loadModel ( 'ios' );
		$result = $model->type ( $type, $count, $page, $age );
		
		/**
		 *上线后删除下面这个
		 * 上线后删除下面这个
		 * 上线后删除下面这个
		 * 上线后删除下面这个
		 * 上线后删除下面这个
		 * 上线后删除下面这个
		 */
		//$result = $model->type1 ( $type, $count, $page, $age );
		Log::write(__CLASS__.'->'.__FUNCTION__."--".$type."--".$age."(url=".$_SERVER['REQUEST_URI'].")\r\n", 'listType', 'debug');
		echo json_encode ( $result );
	}
	
	/**
	 * 获取剧信息和剧的首页集信息
	 */
	public function seriesinfo() {
		$vid = ( int ) trim($this->in ['vid']);
		$ncount = ( int ) trim($this->in ['new_num']) ? ( int ) $this->in ['new_num'] : 2;
		$model = $this->loadModel ( 'ios' );
		$result = $model->seriesinfo ( $vid,  $ncount );
		Log::write(__CLASS__.'->'.__FUNCTION__."--".$vid."(url=".$_SERVER['REQUEST_URI'].")\r\n", 'series', 'debug');
		echo json_encode ( $result );
	}
	
	
	/**
	 * 根据制定的ids获取剧list
	 */
	public function seriesArray(){
		$vids	= trim($this->in['vid']);
		$model = $this->loadModel('ios');
		$result	=	$model->getComicsByIds($vids);
		Log::write(__CLASS__.'->'.__FUNCTION__."--".$vids."(url=".$_SERVER['REQUEST_URI'].")\r\n", 'seriesArray', 'debug');
		echo json_encode ( $result );
	
	}
	
	/**
	 * 历史记录
	 */
	public function historyList(){
		$c_v_ids = trim($this->in['c_v_ids']);
		
		if(empty($c_v_ids)){
			exit;
		}
		$model = $this->loadModel('ios');
		$result	=	$model->historyList($c_v_ids);
		Log::write(__CLASS__.'->'.__FUNCTION__."--".$c_v_ids."(url=".$_SERVER['REQUEST_URI'].")\r\n", 'history', 'debug');
		
		echo json_encode ( $result );
	}

	/**
	 * 获取推荐人物信息
	 */

	public function player(){
		$count = (int) $this->in['count'] ? (int) $this->in['count'] : 29;
		$model = $this->loadModel('ios');
		
		$result	=	$model->player($count);
		
		echo json_encode ( $result );
	}
	
	/**
	 * 获取轮播剧
	 */
	public function get_about_series(){
		$model = $this->loadModel('ios');
		$result	=	$model->get_about_series();
		
		/**
		 *上线后删除下面这个
		 * 上线后删除下面这个
		 * 上线后删除下面这个
		 * 上线后删除下面这个
		 * 上线后删除下面这个
		 * 上线后删除下面这个
		 */
		//$result	=	$model->get_about_series1();
		echo json_encode ( $result );
	}
	
}