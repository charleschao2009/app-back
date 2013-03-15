<?php
 /**
 * 主框架控制器,api框架
 * @author      warlee<kalcaddle@qq.com>
 * @package	    controller
 * @version     $Id: index.class.php  $
 * @copyright   TaoMee, Inc. Shanghai China. All rights reserved.
 */
class user extends Controller
{
	public $user;	//用户相关信息
    /**
     * 构造函数
     */
    function __construct()
    {
        parent::__construct();
    }

	public function upload()
    {
		Log::write(__CLASS__.'->'.__FUNCTION__.'[L'.__LINE__."] |"."\n get=>【".
		json_encode($_GET)."】\n post=>【".json_encode($_POST)."】\n file=>【".json_encode($_FILES)."】\n", 'ios', 'debug');
		if ($this->in['vid'] =='') return;


		$reply_data_file = DATE_DIR.'reply_data.json';		
		$reply_data = json_decode(file_get_contents($reply_data_file),1);
		if (empty($reply_data[$this->in['vid']])){
			$reply_data[$this->in['vid']] = array();
		}

		$user_reply = $this->_get_list();
		$user_reply['type'] = $this->in['type'];
		$user_reply['time'] = time();

		if ($this->in['type'] == 0){//文字
			$user_reply['reply'] = $this->in['reply'];
		}else{
			$save_path	= STATIC_DIR.'/upload/';
			$save_file	= time().'.amr';
			$sound_path = 'http://10.1.14.224/ios/app/static/upload/';
			if (!@move_uploaded_file($_FILES["sound"]["tmp_name"], $save_path.$save_file)) {
				//...		
			}
			$user_reply['sound'] = $sound_path.$save_file;	
		}
		array_unshift($reply_data[$this->in['vid']],$user_reply);
		echo json_encode($reply_data);
		file_put_contents($reply_data_file,json_encode($reply_data));
    }
	
	public function _get_list(){		
		$name_array = array('tucao','田野纯子','阿凡达的国王','摩乐乐','么么公主','侠客');
		$cover_array = array(
			'http://img1.v.tmcdn.net/img/h000/h47/img20121225164140bd3280.jpg',
			'http://img1.v.tmcdn.net/img/h000/h49/img20130107105417678790.jpg',
			'http://img1.v.tmcdn.net/screenshot/20306/a219232_120_160.jpg',
			'http://img3.v.tmcdn.net/screenshot/top500/2790.jpg',
			'http://vres.61.com/images/public/apple-touch-icon-114x114-precomposed.png',
			'http://img1.v.tmcdn.net/img/h000/h44/img20121130111057602360.jpg',
			'http://img1.v.tmcdn.net/img/h000/h46/img20121211160539f93570.jpg',
			'http://img1.v.tmcdn.net/img/h000/h46/img20121211160552ae2110.jpg',
			'http://img1.v.tmcdn.net/img/h000/h46/img20121211160604262480.jpg');
		$sound_array = array(
			'http://10.1.14.224/ios/app/static/upload/1356686848.amr',
			'http://10.1.14.224/ios/app/static/upload/1356686866.amr',
			'http://10.1.14.224/ios/app/static/upload/1356686953.amr',
			'http://10.1.14.224/ios/app/static/upload/1356687053.amr'
		);
		$reply_array = array('我了个擦','不是吧','好看好看','啊啊啊啊','撒旦法全文','瞎扯');
		
		$time = time() - mt_rand(0,100);
		$name = $name_array[mt_rand(0,count($name_array)-1)];
		$cover= $cover_array[mt_rand(0,count($cover_array)-1)];
		$sound= $sound_array[mt_rand(0,count($sound_array)-1)];
		$reply= $reply_array[mt_rand(0,count($reply_array)-1)];
		
		return array(
			'id'		=> mt_rand(100,10000),
			'time'		=> $time,
			'name'		=> $name,			
			'cover'		=> $cover,
			'reply'		=> $reply,
			'sound'		=> $sound,
			'type'		=> mt_rand(0,1),	//type为0则表示文字，1表示语音
			'up'		=> mt_rand(0,100),
			'down'		=> mt_rand(0,100),
			'reply_num'	=> mt_rand(0,100),
		);
	}

	public function itemList()
    {
		$reply_data_file = DATE_DIR.'reply_data.json';		
		$reply_data = json_decode(file_get_contents($reply_data_file),1);
		$list_arr	= $reply_data[$this->in['vid']];
		if (empty($list_arr)){
			$num = mt_rand(0,10);			
			for($i=0; $i<$num; $i++){
				$list_arr[] = $this->_get_list();
			}
		}
		echo json_encode($list_arr);
    }
	
	/**
     * 添加
     */
	public function add_history()
    {
		$model = $this->loadModel('push');
		if ($this->in['version']!='' && $this->in['device_token']!='' && $this->in['vid']!=''){
			$history  = $model->add_favourite(
				$this->in['version'],
				$this->in['device_token'],
				intval($this->in['vid'])
			);
		}else{
			echo 'null';
		}
    }
	/**
     * 删除
     */
	public function del_history()
    {
		$model = $this->loadModel('push');
		if ($this->in['version']!='' && $this->in['device_token']!='' && $this->in['vid']!=''){
			$history  = $model->del_favourite(
				$this->in['version'],
				$this->in['device_token'],
				intval($this->in['vid'])
			);
		}else{
			echo 'null';
		}
    }
}