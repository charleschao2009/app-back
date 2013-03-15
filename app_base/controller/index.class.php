<?php
/**
 * 主框架控制器
 * @author      charles<charleschao@taomee.com>
 * @package	    controller
 * @version     $Id: index.class.php  $
 * @copyright   TaoMee, Inc. Shanghai China. All rights reserved.
 */
class index extends Controller {
	
	/**
	 * 构造函数
	 */
	public function __construct() {
		session_start();
		parent::__construct ();
		$this->tpl->template_dir = TEMPLATE_DIR . 'index/';
	}
	public function default_page(){
		header("Location: http://v.61.com");
	}
	/**
	 * ios视频播放页
	 */
	public function index()
	{
		$ep =  ( int ) trim ( $this->in ['ep'] );
		$vid =  ( int ) trim ( $this->in ['vid'] );
		$model = $this->loadModel('ios');
		$a_list = $model->getVideosInfo($ep, $vid, $limit=2);
		$info['ConentID'] = $ep;
		$info['from_type'] = $a_list[0]['from_type'];
		$info['vid'] = $a_list[0]['vid'];
		if(count($a_list)==2){
			$info['next_vid'] = $a_list[1]['video_id'];
			$info['next_page'] = LOCAL_HOST.'index.php?m=index&c=index&ep='.$ep.'&vid='.$a_list[1]['video_id'];
		}
		$this->tpl->assign('play_info', $info);
		$this->tpl->display('play.html');
	}
	
	
}
