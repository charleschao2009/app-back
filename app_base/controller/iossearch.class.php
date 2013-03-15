<?php
/**
 * ios搜索 控制器
 * @author      charleschao<charleschao@taomee.com>
 * @package     controller
 * @subpackage  no
 * @version     $Id$
 * @copyright   TaoMee, Inc. Shanghai China. All rights reserved.
 */
class iossearch extends Controller {
	public function __construct() {
		parent::__construct ();
	}
	
	/**
	 * 关键词剧搜索
	 */
	
	public function search() {
		$keywords = trim ( $this->in ['words'] );
		
		$hot_count = ( int ) trim ( $this->in ['hot_count'] );
		$page_size = ( int ) trim ( $this->in ['page_size'] ) ? ( int ) $this->in ['page_size'] : 15;
		$page = ( int ) trim ( $this->in ['page'] ) ? ( int ) $this->in ['page'] : 1;
		
		$model = $this->loadModel ( 'ios' );
		
		$result = $model->search( $keywords, $hot_count,$page_size,$page );
		
		/**
		*上线后删除下面这个
		* 上线后删除下面这个
		* 上线后删除下面这个
		* 上线后删除下面这个
		* 上线后删除下面这个
		* 上线后删除下面这个
		 */
		//$result = $model->searchTest ( $keywords, $hot_count,$page_size,$page );
		
		Log::write(__CLASS__.'->'.__FUNCTION__."--".$keywords."(url=".$_SERVER['REQUEST_URI'].")\r\n", 'search', 'debug');
		
		echo json_encode ( $result );
	}
	/**
	 * 热门搜索词
	 */
	public function hot_words(){
		$count	= (int) $this->in['count'] ? (int) $this->in['count'] : 20;
		$model = $this->loadModel('ios');
		$result	=	$model->hot_words($count);
		echo json_encode ( $result );
	}
	
}