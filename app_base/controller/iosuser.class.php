<?php
/**
 * ios用户 控制器
 * @author      charleschao<charleschao@taomee.com>
 * @package     controller
 * @subpackage  no
 * @version     $Id$
 * @copyright   TaoMee, Inc. Shanghai China. All rights reserved.
 */
class iosuser extends Controller {
	public function __construct() {
		parent::__construct ();
	}
	/**
	 * 用户收藏
	 */
	public function getFavList(){
		$user_id = trim ( $this->in ['uid'] );
		$model = $this->loadModel( 'ios' );
		$result = $model->getFavList($user_id);
		echo htmlspecialchars ( json_encode ( $result ) );
	}
	/**
	 * 添加收藏
	 */
	public function addFavourite(){
		$uid = trim($this->in["uid"]);
		$vid = trim ( $this->in ['vid'] );
		$last_episode = trim ( $this->in ['last_episode'] );
		$model = $this->loadModel( 'ios' );
		$result = $model->addFavourite($uid,$vid,$last_episode);
		//echo htmlspecialchars ( json_encode ( $result ) );
	}
	/**
	 * 取消收藏
	 */
	public function delFavourite(){
		$uid = trim($this->in["uid"]);
		$vid = trim ( $this->in ['vid'] );
		$model = $this->loadModel( 'ios' );
		$result = $model->delFavourite($uid,$vid);
	}
	/**
	 * 添加推荐
	 */
	public function addRec(){
		$model = $this->loadModel( 'ios' );
		$result = $model->addRec();
	}
	/**
	 * 添加反馈
	 */
	public function feedback(){
		$content = trim($this->in["content"]);
		$contact = trim ( $this->in ['contact'] );
		$device_id = trim ( $this->in ['device_id'] );
		$model = $this->loadModel( 'ios' );
		$result = $model->feedback($content,$contact,$device_id);
	}
	
}