<?php
/**
 * ios业务模型
 * @author      charleschao<charleschao@taomee.com>
 * @package     model
 * @subpackage  no
 * @version     $Id$
 * @copyright   TaoMee, Inc. Shanghai China. All rights reserved.
 */
class iosModel extends Model {
	const HOT_SEARCH_VIDEO = 't_hot_search_video';
	public $sdb;
	public $db;
	public $db_dynamic;
	function __construct() {
		$this->sdb = null;
		$this->db = null;
		$this->db_dynamic = null;
		parent::__construct ();
	}
	function db_init() {
		if ($this->db == null) {
			$this->db = new mysql ( $this->g_config ['db'] );
		}
	}
	function dbs_init() {
		if ($this->sdb == null) {
			$this->sdb = new mysql ( $this->g_config ['db_app_video'] );
		}
	}
	function db_dynamic_init() {
		if ($this->db_dynamic == null) {
			$this->db_dynamic = new mysql ( $this->g_config ['db_video_dynamic'] );
		}
	}
	
	/**
	 * tab标签内容
	 */
	
	/* public function listTab(){
	$tab_lists =  array(
    "1" => "赛尔号动画片",
    "2" => "赛尔号电影",
    "3" => "摩尔庄园动画片",
    "4" => "摩尔庄园电影",
	"5" =>"全部"
);
    return $tab_lists;	
	}
	 */
	public function getVideosInfo($ep, $vid, $limit=1)
	{
		
		$sql = 'select * from t_content_1 t1,t_content_index i where t1.ContentID=i.ContentID and t1.ContentID='.$ep;
		$videos = $this->get_list($sql);
		
		
		if(!$videos[0]['videos']){
			return array();
		}
		$sql = 'select i.URL, t3.* from t_content_index i, t_content_3 t3 
				where t3.ContentID=i.ContentID and i.state =1 and i.NodeID='.$videos[0]['videos'].' 
				and video_id>='.$vid.' order by video_id asc limit '.$limit;

		$list = $this->get_list($sql);
		foreach ($list as $key=>$val){
			unset($list[$key]);
			$list[$key]['video_id'] = $val['video_id'];
			$list[$key]['URL'] = $val['URL'];
			$list[$key]['video_title'] = $val['video_title'];
			$list[$key]['from_type'] = $val['from_type'];
			$list[$key]['vid'] = $val[$list[$key]['from_type'].'_vid'];
		}
		$list	=	$this->Null2str($list); //对 null 处理
		return $list;
	}
	
	
	/**
	 * 添加反馈信息
	 */
	public function feedback($content, $contact, $device_id) {
		$this->dbs_init ();
		
		$feedback_list = "insert into tm_feedback(content,contact,device_id) values ('" . $content . "','" . $contact . "','" . $device_id . "')";
		
		
		$this->sdb->insert ( $feedback_list );
	}
	
	/**
	 * 剧集的历史记录
	 * @param unknown_type $cid        	
	 * @param unknown_type $vid        	
	 */
	public function historyList($c_v_ids) {
		$this->db_init ();
		$players = explode ( ',', $c_v_ids );
		
		$players = array_flip ( array_flip ( $players ) );
		$list ["total_count"] = count ( $players );
		
		$historyList = array ();
		foreach ( $players as $k => $v ) {
			$c_v = explode ( ':', $v );
			$cid = $c_v [0];
			$vid = $c_v [1];
			$cur_num_sql = "select c3.video_id from t_content_3 c3,t_content_index ci,t_content_1 t1 where ci.ContentID = c3.ContentID and t1.videos = ci.NodeID and t1.ContentID = " . $cid . " and ci.State = 1 and c3.video_id >" . $vid . " order by video_id ASC limit 1";
			$cur_num = $this->db->get_one ( $cur_num_sql );
			if (! empty ( $cur_num )) {
				$next_vid = $cur_num;
				/*
				 * 判断下一集
				 */
				$historyListTem ["next_video_id"] = $next_vid;
				$historyListTem ["next_vid_url"] = LOCAL_HOST.'index.php?m=index&c=index&ep=' . $cid . "&vid=" . $next_vid;
				$historyList [] = $historyListTem;
			} else {
				$historyListTem ["next_video_id"] = "";
				$historyListTem ["next_vid_url"] = "";
				$historyList [] = $historyListTem;
			}
		}
		$list ["list"] = $historyList;
		
		
		$list	=	$this->Null2str($list); //对 null 处理
		return $list;
	}
	
	/**
	 * 获取用户收藏的剧ID
	 * @param unknown_type $uid        	
	 */
	/* public function getFavList($uid) {
		$this->dbs_init ();
		$fav_list_sql = "select ju_id from tm_fav where fav_user_id = " . $uid;
		$fav_list = $this->sdb->get_all ( $fav_list_sql );
		$list_fav = array ();
		foreach ( $fav_list as $v ) {
			$temp = $this->getFavs ( $v ["ju_id"], $uid );
			if (count ( $temp ) > 0) {
				array_push ( $list_fav, $temp );
			}
		}
		$list ["total_count"] = count ( $list_fav );
		$list ["list"] = $list_fav;
		return $list;
	} */
	/*
	 * 获取用户收藏的某剧ID的详细信息
	 */
	/* public function getFavs($vid, $uid) {
		$sql = 'select t.ContentID,t.cover,t.name, t.condition, t.cur_num from t_content_1 as t where t.ContentID = ' . $vid;
		$result = $this->get_list ( $sql );
		$result [0] ["fav"] = $this->getFavNum ( $result [0] ["ContentID"] );
		$result [0] ["topic_num"] = $this->getTopicNum ( $result [0] ["ContentID"] );
		$result [0] ["last_episode"] = $this->getLastEpisode ( $result [0] ["ContentID"], $uid );
		return $result [0];
	}
	 */
	/**
	 * 获得用户收藏该剧的最后集
	 *
	 * @param unknown_type $vid        	
	 */
/* 	public function getLastEpisode($vid, $uid) {
		$this->dbs_init ();
		$last_episode_sql = "select last_episode from tm_fav where ju_id = " . $vid . " and fav_user_id = " . $uid;
		$num = $this->sdb->get_one ( $last_episode_sql );
		return $num;
	} */
	
	/**
	 * 获取该剧的主题数
	 *
	 * @param unknown_type $cid        	
	 * @return multitype:
	 */
	/* public function getTopicNum($cid) {
		$this->dbs_init ();
		$topic_num_sql = "select count(1) from tm_topic where ju_id = " . $cid;
		$num = $this->sdb->get_one ( $topic_num_sql );
		return $num;
	} */
	/**
	 * 获取该剧的收藏人数
	 *
	 * @param unknown_type $cid        	
	 * @return multitype:
	 */
	/* public function getFavNum($cid) {
		$this->dbs_init ();
		$fav_num_sql = "select count(1) from tm_fav where ju_id = " . $cid;
		$num = $this->sdb->get_one ( $fav_num_sql );
		return $num;
	} */
	
	/**
	 * 判断该用户是否收藏该剧
	 */
	/* public function isFaved($uid, $vid) {
		$this->dbs_init ();
		$fav_num_sql = "select count(1) from tm_fav where ju_id = " . $vid . " and fav_user_id = " . $uid;
		$num = $this->sdb->get_one ( $fav_num_sql );
		return $num;
	} */
	/**
	 * 添加收藏
	 *
	 * @param unknown_type $uid        	
	 * @param unknown_type $vid        	
	 */
	/* public function addFavourite($uid, $vid, $last_episode) {
		$this->dbs_init ();
		$time_fav = 111111; // 获得系统时间
		$addFav_sql = "insert into tm_fav (fav_user_id,ju_id,time_fav,last_episode) values (" . $uid . "," . $vid . "," . $time_fav . "," . $last_episode . ")";
		$this->sdb->insert ( $addFav_sql );
	} */
	
	/**
	 * 插入首页推荐
	 */
	public function addRec() {
		$title = '天线宝宝';
		$picture = 'http://img1.v.tmcdn.net/img//h000/h44/img20121126153649c21420.jpg';
		$sex = 1;
		$age = 0;
		$introduction = "这是一朵奇葩";
		$weight = 1;
		$this->dbs_init ();
		$addRec_sql = "insert into tm_recomend (title,picture,introduction,sex,age,weight) values ('" . $title . "','" . $picture . "','" . $introduction . "'," . $sex . "," . $age . "," . $weight . ")";
		$this->sdb->insert ( $addRec_sql );
	}
	
	/**
	 * 取消收藏
	 *
	 * @param unknown_type $uid        	
	 * @param unknown_type $vid        	
	 */
	/* public function delFavourite($uid, $vid) {
		$this->dbs_init ();
		$delFav_sql = "delete from tm_fav where fav_user_id =" . $uid . " and ju_id = " . $vid;
		$this->sdb->execute ( $delFav_sql );
	}
	public function isExistedFav($uid, $vid) {
		$current_num_sql = "select cur_num from t_content_1 where ContentID = " . $vid;
		$this->db_init ();
		$this->dbs_init ();
		
		$last_episode = $this->db->get_one ( $current_num_sql );
		
		
		$isExist_sql = "select fav_id from tm_fav where fav_user_id = " . $uid;
		
		$isExisted_id = $this->sdb->get_one ( $isExist_sql );
		if ($isExisted_id > 0) {
			$update_sql = "update t_fav set last_episode = " . $last_episode . " where fav_user_id = " . $uid . " and ju_id = " . $vid;
			$this->sdb->update ( $update_sql );
		}
	} */
	/**
	 * 取剧的首页信息
	 *
	 * @param int $vid        	
	 * @param int $count        	
	 * @param int $ncount        	
	 * @return array Ambigous array>
	 */
	public function seriesinfo($cid, $ncount) {
		$app_cache_key = 'app_cache_series_info_' . $cid;
		// 读取缓存
		// 缓存排序后的结果
		$memcache = $this->initMemcache ();
		$list = $memcache->get ( $app_cache_key );
		if (! empty ( $list )) {
			return $list;
		}
		//$start = microtime();
		$comic_info_sql = 'select t1.ContentID, t1.showtime, t1.videos, t1.ios_album_pic, t1.cover, t1.area, t1.name, t1.condition,t1.cur_num, t1.introduction, t1.update_notice 
				    				from t_content_1 t1, t_content_index i 
				    				where i.ContentID = '.$cid." and t1.ContentID = i.ContentID and i.State = 1 
				    				and i.NodeID = 11" ;
		//print($comic_info_sql);
		$comic_sd_sql = 'select total_v , grade from db_video_dynamic.series_dynamic_info where type=1 and id=' . $cid;
		$temp = $this->get_list ( $comic_info_sql );
		
		if (empty ( $temp )) {
			exit ();
		}
		$result = $this->cover2IosPic ( $temp );
		 foreach($result as &$v){
					$v["introduction"] = strip_tags(html_entity_decode($v["introduction"],ENT_QUOTES,"UTF-8"));
				}  
		$total_num = $this->getTotalNumByVid ( $result [0] ['videos'] );
		if ($total_num == 0) {
			return "";
		}
		unset ( $result [0] ['videos'] ); // 释放无用字段
		$res = $this->get_list ( $comic_sd_sql );
		$comic = $result [0];
		$comic ['total_num'] = $total_num;
		
		$comic ['condition'] == '连载中' ? $comic ['new_list'] = $this->get_new_comic ( $cid, $ncount ) : "";
		if (! empty ( $res )) {
			$comic ['total_v'] = $res [0] ['total_v'];
			$comic ['score'] = $res [0] ['grade'];
		} else {
			$comic ['total_v'] = '1000';
			$comic ['score'] = '7.0';
		}
		$comic ['play_list'] = $this->episodeList ( $cid ); // 默认取第一页数据
		$comic = $this->Null2str ( $comic );
		
		$comic	=	$this->Null2str($comic); //对 null 处理
		//echo microtime() - $start;
		$memcache->set ( $app_cache_key, $comic );
		return $comic;
	}
	
	/**
	 * 获取剧每一页的数据
	 *
	 * @param int $vid        	
	 * @param int $count        	
	 * @param int $page        	
	 */
	public function episodeList($cid) {
		$app_episode_list_cache_key = 'app_cache_episode_list_' . $cid;
		$memcache = $this->initMemcache ();
		
		$list = $memcache->get ( $app_episode_list_cache_key );
		
		if (! empty ( $list )) {
			return $list;
		}
		
		$sql = 'select c3.video_id from t_content_3 c3,t_content_index ci,t_content_1 t1  where ci.ContentID = c3.ContentID and t1.videos = ci.NodeID and c3.from_type not in("sina","qq","pptv","co_pptv") and t1.ContentID = ' . $cid . ' 
				  and ci.State = 1 order by video_id ASC';
		
		$result = $this->get_list ( $sql );
		
		$result = $this->Null2str ( $this->ios_url ( $result, $cid ) );
		
		
		$result	=	$this->Null2str($result); //对 null 处理
		$memcache->set ( $app_episode_list_cache_key, $result );
		return $result;
	}
	
	/**
	 * 获取连载剧中最新的集
	 *
	 * @param int $vid        	
	 * @param int $ncount        	
	 */
	private function get_new_comic($cid, $ncount) {
		$app_cache_key = 'app_cache_get_new_comic_' . $cid . '_' . $ncount;
		// 读取缓存
		$memcache = $this->initMemcache ();
		$list = $memcache->get ( $app_cache_key );
		
		if (! empty ( $list )) {
			return $list;
		}
		
		$sql = 'select c3.video_id from t_content_3 c3,t_content_index ci,t_content_1 t1 where ci.ContentID = c3.ContentID and t1.videos = ci.NodeID and t1.ContentID = ' . $cid . ' and ci.State = 1 order by video_id DESC limit '. $ncount;
		
		$result = $this->ios_url ( $this->get_list ( $sql ), $cid );
		$result = $this->Null2str ( $result );
		$memcache->set ( $app_cache_key, $result );
		
		return $result;
	}
	
	/**
	 * 处理IOS播放的url
	 *
	 * @param array $result        	
	 */
	private function ios_url($result, $cid) {
		foreach ( $result as $key => &$v ) {
			$vid = $v ["video_id"];
			$v ['URL'] = LOCAL_HOST.'index.php?m=index&c=index&ep=' . $cid . "&vid=" . $vid;
		}
		return $result;
	}
	
	/**
	 * 根据vid做相关推荐
	 *
	 * @param int $vid        	
	 * @param int $count        	
	 */
	public function aboutSeries($vid, $count) {
		$app_cache_key = 'app_cache_series_about_' . $vid . '_' . $count;
		// 读取缓存
		$this->db_dynamic_init ();
		$this->db_init ();
		$max_count = $count * 2;
		$memcache = $this->initMemcache ();
		$vids = $memcache->get ( $app_cache_key );
		
		if (! $vids) {
			
			$rec_sql = 'select album2 from t_recommend_video_matrix where album1 = ' . $vid . ' order by similarity desc limit ' . $count;
			
			$vids = $this->db_dynamic->get_all ( $rec_sql );
			$memcache->set ( $app_cache_key, array (
					'status' => 0,
					'vids' => $vids 
			) );
		} else {
			$vids = $vids ['vids'];
		}
		$return = array ();
		foreach ( $vids as $v ) {
			/**
			 * 迭代相关剧的ID
			 */
			$comic_info_sql = 'select t1.ContentID, t1.cover,t1.area, t1.name, t1.condition, t1.total_num 
				    				from t_content_1 t1, t_content_index i
				    				where t1.ContentID = i.ContentID and i.State = 1
				    				and i.NodeID = 11 and i.ContentID = ' . $v ["album2"];
			$temp = $this->get_list ( $comic_info_sql );
			array_push ( $return, $temp [0] );
		}
		$return = array_filter ( $return );
		$count_return = count ( $return );
		/**
		 * 如果从库中查出来的推荐结果小于4条时则从top_url接口中任意选出少的条数补全四条
		 */
		if ($count_return < $max_count) {
			
			/**
			 * 此处之所以用$max_count是因为array_rand($array,1)；如果是1会得不到随机子
			 */
			$top_cache_key = 'play_top_key_' + $vid;
			$data = $memcache->get ( $top_cache_key );
			if (! $data) {
				$data = json_decode ( file_get_contents ( $this->g_config ['top_url'] ), true );
				$memcache->set ( $top_cache_key, $data );
			}
			/**
			 * 根据vid的area从top_url接口中选择相应的条数
			 */
			switch ($return [0] ['area']) {
				case '大陆' :
					$info = $data ['ch'];
				case '欧美' :
					$info = $data ['eu'];
				default :
					$info = $data ['jp'];
			}
			$rand = array_rand ( $info, $max_count - $count_return );
			$rand_rec_list = array ();
			foreach ( $rand as $k ) {
				$rand_rec_list_temp ["ContentID"] = $info [$k] ["id"];
				$rand_rec_list_temp ["cover"] = $info [$k] ["cover"];
				$rand_rec_list_temp ["name"] = $info [$k] ["name"];
				$rand_rec_list_temp ["cur_num"] = $info [$k] ["cur_num"];
				$rand_rec_list_temp ["condition"] = $this->db->get_one ( "select t.condition from t_content_1 t where ContentID=" . $info [$k] ["id"] );
				$rand_rec_list [] = $rand_rec_list_temp;
			}
			
			$return = array_merge ( $return, $rand_rec_list );
			$return = array_slice ( $return, 0, $count );
			foreach ( $return as $k => &$v ) {
				unset ( $v ['area'] );
			}
		}
		
		$result	=	$this->Null2str($result); //对 null 处理
		return $return;
	}
	/**
	 * 首次进入列出30个动画角色，用户选择5个后推荐12部剧信息放到收藏夹
	 * @param int $count
	 */
	public function player($count) {
		$this->dbs_init ();
		$sql = 'select pic_id,title,small_picture from tm_recomend';
		$result = $this->sdb->get_all ( $sql );
		return $this->Null2str ( $result );
	}
	
	public function get_about_series($player) {
		include "lib/class/imageThumb.class.php";
		$this->dbs_init ();
		/**
		 * 先获得用户选中剧的id的向量
		 */
		
		$sql = "select pic_id,title,large_picture,introduction,sex,age from tm_recomend order by weight desc";
		
		$result_selected = $this->sdb->get_all ( $sql );
		$list ["total_num"] = count ( $result_selected );
		$list ["rec_list"] = $result_selected;
		
		$cm=new CreatMiniature();
		$cm->SetVar("http://img1.v.tmcdn.net/img/h000/h51/img201302041650051b3960.jpg",'file');
		$cm->Prorate(SYSDATA_LOG_PATH.'haiyao2.jpg',180,168);//附带
		$list	=	$this->Null2str($list); //对 null 处理
		
		return $list;
	}
	
	/**
	 *   上线后把这个给删了
	 * @param unknown_type $player
	 * @return Ambigous <string, unknown>
	 */
	public function get_about_series1($player) {
		$this->dbs_init ();
		/**
		 * 先获得用户选中剧的id的向量
		 */
		$sql = "select pic_id,title,large_picture,introduction,sex,age from tm_recomend_temp order by weight desc";
		
		$result_selected = $this->sdb->get_all ( $sql );
		
		$list ["total_num"] = count ( $result_selected );
		$list ["rec_list"] = $result_selected;
		
		$list	=	$this->Null2str($list); //对 null 处理
		return $list;
	}
	
	
	/**
	 * 临时搜索tablist,上线后注释掉
	 */
	public function temp_search($keywords,$page) {
		$id_list =  $keywords ;
		if(!empty($id_list)){
			$result_all = $this->delComicBySearchTotalNum ( $this->getComicsInfoByIds ( $id_list) );
			$list = $this->Null2str ( $result_all );
			
			$result["total_num"]=count($list);
			
			if($page==1){
				$result["list"] = array_slice($list, 0,15);
			}else{
				$result["list"] = array_slice($list, 15,1);
			}
			
			return $result;
		}else{
			return array();
		}
		/**
		 *删除集为0的剧
		*/
		
		
	}
	public function temp_search2($keywords,$page) {
		$id_list =  $keywords ;
		if(!empty($id_list)){
			$result_all = $this->delComicBySearchTotalNum ( $this->getComicsInfoByIds ( $id_list) );
			$list = $this->Null2str ( $result_all );
				
			$result["total_num"]=count($list);
				$result["list"] = $list;
			return $result;
		}else{
			return array();
		}
		/**
		 *删除集为0的剧
			*/
	
	
	}
	/**
	 * 临时type,上线后注释掉
	 */
	public function type1($type, $count, $page, $age) {
		switch ($type) {
			case 'newest' :
				switch ($age) {
					case 0 :
						$newest=array(7623,1992,7916);
						break;
					case 1 :
						$newest=array(7623);
						break;
					case 2 :
						$newest=array(1992);
						break;
					case 3 :
						$newest=array(7916);
						break;
				}
				return $this->temp_search2($newest);
				break;
			case 'china' :
				switch ($age) {
					case 0 :
						$china=array(4274,2184,2670);
						break;
					case 1 :
						$china=array(4274);
						break;
					case 2 :
						$china=array(2184);
						break;
					case 3 :
						$china=array(2670);
						break;
				}
				return $this->temp_search2($china);
				break;
			case 'japan' :
				
				switch ($age) {
					case 0 :
						$japan=array(2772,6039,1992);
						break;
					case 1 :
						$japan=array(2772);
						break;
					case 2 :
						$japan=array(6039);
						break;
					case 3 :
						$japan=array(1992);
						break;
				}
				return $this->temp_search2($japan);
				break;
			case 'movie' :
					switch ($age) {
						case 0 :
							$movie=array(7623,1992,2772,6039,4274,2184,2060,2670,2189,7896,7916,3542,7968,2023,7967,7969);
							return $this->temp_search($movie,$page);
							break;
						case 1 :
							$movie=array(7623,4274,2772,2670,2023);
							return $this->temp_search2($movie);
							break;
						case 2 :
							$movie=array(6039,2184,7896,7968,7967);
							return $this->temp_search2($movie);
							break;
						case 3 :
							$movie=array(1992,2060,2189,7916,3542,7969);
							return $this->temp_search2($movie);
							break;
					}
					
				break;
			case 'usa' :
				switch ($age) {
					case 0 :
						$usa=array(2060,2670,4274);
						break;
					case 1 :
						$usa=array(2670);
						break;
					case 2 :
						$usa=array(2060);
						break;
					case 3 :
						$usa=array(4274);
						break;
				}
				return $this->temp_search2($usa);
				break;
			default :
				return '';
		}
	}
	
	/**
	 * 获取list
	 * @param string $type        	
	 * @param int $count        	
	 * @param int $page        	
	 * @param int $age
	 * 0=>all, 1=>3-6, 2=>7-9, 3=>10-15,
	 * 上线后把1去掉
	 */
	public function type($type, $count, $page, $age) {
		$this->db_init ();
		
		$app_cache_key = 'app_cache_' . $type . '_' . $count . '_' . $page . '_' . $age;
		// 读取缓存
		// 缓存排序后的结果
		$app_cache_all_key = 'app_cache_all_' . $type . '_' . $age . '_key';
		$memcache = $this->initMemcache ();
		$list = $memcache->get ( $app_cache_key );
		
		if (! empty ( $list )) {
			return $list;
		}
		
		switch ($type) {
			case 'newest' :
				$sql = 'select t1.ContentID, t1.videos, t1.name, t1.clear, t1.cur_num, t1.condition, t1.ios_album_pic, t1.cover from t_content_1 t1, t_content_index i where t1.ContentID = i.ContentID and i.State = 1 and i.NodeID = 11 and t1.total_num>0';
				switch ($age) {
					case 1 :
						$sql .= ' and age_level = "3-6" order by i.PublishDate DESC limit 130 ';
						break;
					case 2 :
						$sql .= ' and age_level = "7-9" order by i.PublishDate DESC limit 130 ';
						break;
					case 3 :
						$sql .= ' and age_level = "10-15" order by i.PublishDate DESC limit 140 ';
						break;
					default :
						$sql .= ' order by i.PublishDate DESC limit 400';
						break;
				}
				break;
			case 'china' :
				$sql = $this->make_sql ( $type, $age );
				break;
			case 'japan' :
				$sql = $this->make_sql ( $type, $age );
				break;
			case 'movie' :
				$sql = $this->make_sql ( $type, $age );
				break;
			case 'usa' :
				$sql = $this->make_sql ( $type, $age );
				break;
			case 'best' :
				$list = $this->best_collection_list ( $count, $page );
				return $list;
				break;
			default :
				return '';
		}
		
		// 读取缓存
		
		$result_all = $memcache->get ( $app_cache_all_key );
		
		if (! empty ( $result_all )) {
			$result = $result_all;
		} else {
			if ($type == 'newest') {
				$result = $this->delComicByTotalNum ( $this->get_list ( $sql ) ); // 去掉集为空的剧
			} else {
				$result = $this->reSortArray ( $this->delComicByTotalNum ( $this->get_list ( $sql ) ) );
			}
			$memcache->set ( $app_cache_all_key, $result ); // 缓存排序结构，提高分页速度
		}
		
		// 查询数据库设置缓存
		$total_num = ( string ) count ( $result );
		
		$result = $this->Null2str ( $this->cover2IosPic ( array_slice ( $result, ($page - 1) * $count, $count ) ) );
		
		$list = array (
				'total_num' => $total_num,
				'list' => $result 
		);
		$list	=	$this->Null2str($list); //对 null 处理
		$memcache->set ( $app_cache_key, $list );
		
		/**
		 * 此处不应该用缓存
		 * 添加该剧的收藏数和专题数量
		 */
		/* foreach ( $list ['list'] as $key => &$v ) {
			$v ["fav"] = $this->getFavNum ( $v ["ContentID"] );
			$v ["topic_num"] = $this->getTopicNum ( $v ["ContentID"] );
		} */
		
		return $list;
	}
	
	
	
	
	/**
	 * 根据vid获取剧的总集数
	 *
	 * @param
	 *        	$vid
	 */
	private function getTotalNumByVid($videos) {
		if (! $videos) {
			return 0;
		}
		
		$key = 'video_total_num_' . $videos;
		$memcache = $this->initMemcache ();
		$num = $memcache->get ( $key );
		
		if ($num !== false) {
			return $num;
		}
		
		$sql = 'select count(1) as total_num from t_content_index where NodeID = ' . $videos . ' and State = 1';
		$tmp = $this->get_list ( $sql );
		$num = $tmp [0] ['total_num'];
		$memcache->set ( $key, $num, 3600 );
		return $num;
	}
	
	/**
	 * 构建sql
	 *
	 * @param string $type        	
	 * @param int $age        	
	 */
	private function make_sql($type, $age) {
		$sql = 'select t1.ContentID, t1.videos, t1.area, t1.name, t1.clear, t1.cur_num, t1.condition, t1.ios_album_pic, t1.cover
    				from t_content_1 t1, t_content_index i
    				where t1.ContentID = i.ContentID and i.State = 1 and i.NodeID = 11 and t1.total_num > 0';
		
		switch ($type) {
			case 'china' :
				$sql .= ' and t1.area = "大陆"';
				break;
			case 'japan' :
				$sql .= ' and t1.area = "日本"';
				break;
			case 'movie' :
				$sql .= ' and t1.video_type = "电影"';
				break;
			case 'usa' :
				$sql .= ' and t1.area = "欧美"';
				break;
			default :
				break;
		}
		
		switch ($age) {
			case 1 :
				$sql .= ' and age_level = "3-6" order by i.PublishDate DESC';
				break;
			case 2 :
				$sql .= ' and age_level = "7-9" order by i.PublishDate DESC';
				break;
			case 3 :
				$sql .= ' and age_level = "10-15" order by i.PublishDate DESC';
				break;
			default :
				$sql .= ' order by i.PublishDate DESC';
				break;
		}
		
		return $sql;
	}
	
	/**
	 * 根据vid 使用otis算法，给剧打分，根据此分数排序
	 *
	 * @param int $vid        	
	 */
	public function getComicScoreByVid($vid) {
		$sql = 'select score from db_video_dynamic.t_video_comics_score where vid = ' . $vid;
		$tmp = $this->get_list ( $sql );
		
		return $tmp [0] ['score'] ? $tmp [0] ['score'] : 0;
	}
	
	/**
	 * 生成加入排序字段数组,完成排序并且去掉排序字段
	 *
	 * @param array $list        	
	 */
	public function reSortArray($list) {
		foreach ( $list as $key => &$v ) {
			$v ['score'] = $this->getComicScoreByVid ( $v ['ContentID'] );
		}
		
		// 排序
		$list = $this->array_sort ( $list, 'score', 'desc' );
		// 释放无用的排序字段
		foreach ( $list as $key => $v ) {
			unset ( $v ['score'] );
			$data [] = $v;
		}
		
		return $data;
	}
	
	/**
	 * 按指定字段排序
	 *
	 * @param array $arr        	
	 * @param string $keys        	
	 * @param string $type        	
	 * @return multitype:unknown
	 */
	function array_sort($arr, $keys, $type = 'asc') {
		$keysvalue = $new_array = array ();
		foreach ( $arr as $k => $v ) {
			$keysvalue [$k] = $v [$keys];
		}
		if ($type == 'asc') {
			asort ( $keysvalue );
		} else {
			arsort ( $keysvalue );
		}
		reset ( $keysvalue );
		foreach ( $keysvalue as $k => $v ) {
			$new_array [$k] = $arr [$k];
		}
		
		return $new_array;
	}
	
	/**
	 * 更具首字母返回数据
	 *
	 * @param string $first_words        	
	 */
	public function first_char($first_words) {
		$app_cache_key = 'app_cache_' . $first_words . '_key';
		// 读取缓存
		$memcache = $this->initMemcache ();
		$list = $memcache->get ( $app_cache_key );
		
		if (! empty ( $list )) {
			return $list;
		}
		
		$sql = 'select t1.ContentID, t1.videos, t1.name, t1.clear
    				from t_content_1 t1, t_content_index i
    				where t1.ContentID = i.ContentID and i.State = 1 and i.NodeID = 11
    				and t1.total_num >0 and t1.first_char = "' . $first_words . '" order by i.PublishDate DESC';
		
		// 查询数据库设置缓存
		$result = $this->delComicByTotalNum ( $this->Null2str ( $this->get_list ( $sql ) ) );
		$total_num = ( string ) count ( $result );
		
		$list = array (
				'total_num' => $total_num,
				'list' => $result 
		);
		
		$memcache->set ( $app_cache_key, $list );
		
		return $list;
	}
	
	/**
	 * 获取精品合集列表
	 *
	 * @param int $count        	
	 * @param int $page        	
	 */
	public function best_collection_list($count, $page) {
		$app_cache_key = 'app_cache_best_collection_list_' . $count . '_' . $page . '_key';
		// 读取缓存
		$memCache = $this->initMemcache ();
		$list = $memCache->get ( $app_cache_key );
		
		if (! empty ( $list )) {
			return $list;
		}
		
		$app_cache_all_key = 'app_cache_best_collection_list_all_key';
		
		$result_all = $memCache->get ( $app_cache_all_key );
		
		if (! empty ( $result_all )) {
			$data = $result_all;
		} else {
			$sql = 'select t2.ContentID, t2.title as name, t2.picture as cover, t2.jianjie as introduction
    					from t_content_2 t2, t_content_index i, t_site t
    					where t2.ContentID = i.ContentID and i.State = 1 and i.NodeID = t.NodeID
    					and t.NodeGUID = "ios_good_comic_collection" order by i.Sort DESC';
			// 查询数据库设置缓存
			$result = $this->get_list ( $sql );
			foreach ( $result as $key => &$v ) {
				$tmp = $this->boutique_list ( $v ['ContentID'], 1, 1 );
				if ($tmp ['total_num'] == 0) {
					unset ( $v );
					continue;
				}
				$data [] = $v;
			}
			$memCache->set ( $app_cache_all_key, $data );
		}
		
		$total_num = ( string ) count ( $data );
		$result = $this->Null2str ( array_slice ( $data, ($page - 1) * $count, $count ) );
		$list = array (
				'total_num' => $total_num,
				'list' => $result 
		);
		$list	=	$this->Null2str($list); //对 null 处理
		$memCache->set ( $app_cache_key, $list );
		
		return $list;
	}
	
	/**
	 * 返回精品具体信息
	 *
	 * @param int $vid        	
	 * @param int $count        	
	 * @param int $page        	
	 */
	public function boutique_list($cid, $count, $page) {
		$app_cache_key = 'app_cache_best_list_' . $cid . '_' . $count . '_' . $page . '_key';
		
		// 读取缓存
		$mem = $this->initMemcache ();
		$list = $mem->get ( $app_cache_key );
		
		if (! empty ( $list )) {
			/* foreach ( $list ['list'] as $key => &$v ) {
				
				  //添加该剧的收藏数和专题数量
				 
				$v ["fav"] = $this->getFavNum ( $v ["ContentID"] );
				$v ["topic_num"] = $this->getTopicNum ( $v ["ContentID"] );
			} */
			return $list;
		}
		
		$sql = 'select t2.ContentID, t2.out_link as name, t2.out_extend_pic as cover , t2.picture as cover_little,
    				 t2.jianjie as introduction, t2.out_extend
    				from t_content_2 t2, t_content_index i, t_site t 
    				where t2.ContentID = i.ContentID and i.State = 1 and i.NodeID = t.NodeID 
    				and t.NodeGUID = "ios_good_comic_collection" and i.ContentID = ' . $cid;
		
		// 查询数据库设置缓存
		
		$tmp = $this->get_list ( $sql );
		if (empty ( $tmp )) {
			exit ();
		}
		$collection_info = $tmp [0];
		$comic_ids = explode ( ',', $collection_info ['out_extend'] );
		
		foreach ( $comic_ids as $key => $v ) {
			if ($v != '') {
				$comic_sql = 'select t1.ContentID, t1.videos, t1.area, t1.name, t1.clear, t1.cur_num, t1.condition,t1.ios_album_pic, t1.cover
    								from t_content_1 t1, t_content_index i
    								where t1.ContentID = i.ContentID and i.State = 1 and i.NodeID = 11 and i.ContentID = ' . $v;
				$comic_info = $this->get_list ( $comic_sql );
				$comics [] = $comic_info [0];
			}
		}
		
		$comics = $this->delComicByTotalNum ( $comics ); // 过滤空集的剧
		$total_num = count ( $comics );
		$comic_list = $this->cover2IosPic ( array_slice ( $comics, ($page - 1) * $count, $count ) );
		
		$collection_info ['total_num'] = $total_num;
		$collection_info ['list'] = $comic_list;
		
		$collection_info = $this->Null2str ( $collection_info );
		$collection_info	=	$this->Null2str($collection_info); //对 null 处理
		$mem->set ( $app_cache_key, $collection_info );
		
		/**
		 * 此处不应该用缓存
		 * 添加该剧的收藏数和专题数量
		 */
		/* foreach ( $collection_info ['list'] as $key => &$v ) {
			
			$v ["fav"] = $this->getFavNum ( $v ["ContentID"] );
			$v ["topic_num"] = $this->getTopicNum ( $v ["ContentID"] );
		} */
		
		return $collection_info;
	}
	
	/**
	 * 返回热门搜索词
	 *
	 * @param int $count        	
	 */
	public function hot_words($count) {
		$this->db_dynamic_init ();
		$this->db_init ();
		$memcache = $this->initMemcache ();
		$rec_sql = 'select vid from t_hot_search_video order by count desc limit ' . $count;
		$data = $this->db_dynamic->get_all ( $rec_sql );
		$ids = array ();
		foreach ( $data as $v ) {
			$vids [] = $v ['vid'];
		}
		
		$return = array ();
		foreach ( $vids as $v ) {
			/**
			 * 迭代相关剧的ID
			 */
			$comic_info_sql = 'select t1.ContentID, t1.cover,t1.name, t1.introduction,t1.condition, t1.cur_num
				    				from t_content_1 t1, t_content_index i
				    				where t1.ContentID = i.ContentID and i.State = 1
				    				and i.NodeID = 11 and i.ContentID = ' . $v;
			$temp = $this->get_list ( $comic_info_sql );
			$return [] = $temp [0];
		}
		
		return $return;
	}
	
	/**
	 * 添加热门搜索词
	 *
	 * @param int $count        	
	 */
	public function add_search_words($vid) {
		$vid = ( int ) $vid;
		$this->selectDynamicDB ();
		$this->where ( 'vid = ' . $vid );
		$data = $this->getDataInfo ( self::HOT_SEARCH_VIDEO, 'count' );
		
		if (! $data) {
			$this->addData ( array (
					'vid' => $vid,
					'count' => 1 
			) );
			return $this->dataInsert ( self::HOT_SEARCH_VIDEO );
		} else {
			$this->where ( 'vid = ' . $vid );
			$this->addData ( array (
					'count' => $data ['count'] + 1 
			) );
			return $this->dataUpdate ( self::HOT_SEARCH_VIDEO );
		}
	}
	/**
	 * 过滤掉集数为0的剧
	 *
	 * @param array $list
	 */
	public function delComicByTotalNum($list) {
		$list = array_filter($list);
		
		$tmp='';
		foreach ( $list as $v ) {
			$tmp .= ($v["videos"].",");
		}
		
		$check_videos = substr($tmp,0,strlen($tmp)-1);
		
		
		/**
		 *删除集为0的剧
		 */
		$sql = 'select distinct NodeID from t_content_index where NodeID in (' . $check_videos . ') and State = 1';

		$tmp = $this->get_list ( $sql );
		
		
		foreach($tmp as $v){
				$not_null_chapter[] = $v["NodeID"];
		}
		foreach($list as $v){
			if( in_array($v["videos"], $not_null_chapter)){
				$result_list[] = $v;
			}else{
				continue;
			}
		}
		return $result_list;
	}
	/**
	 * 过滤掉搜索后集数为0的剧
	 *
	 * @param array $list
	 */
	private function delComicBySearchTotalNum($list) {
	
		$list = array_filter($list);
		foreach ( $list as $v ) {
			$tmp .= ($v["videos"].",");
		}
		$check_videos = substr($tmp,0,strlen($tmp)-1);
		/**
		 *删除集为0的剧
		*/
		$sql = 'select distinct NodeID from t_content_index where NodeID in (' . $check_videos . ') and State = 1';
	
		$tmp = $this->get_list ( $sql );
	
	
		foreach($tmp as $v){
			$not_null_chapter[] = $v["NodeID"];
		}
		foreach($list as $v){
			if( in_array($v["videos"], $not_null_chapter)){
				$result_list[] = $v;
			}else{
				continue;
			}
		}
		$result_list = array_slice($result_list, 0,83);
		return $result_list;
	}
	
	
	
	
	
	/**
	 * 关键字搜索
	 *
	 * @param string $first_words        	
	 */
	
	
	
	
	public function search($keywords, $hot_count, $page_size, $page) {
		
		$id_list = $this->getIdList ( $keywords );
		/**
 		 *删除集为0的剧
		 */
		$result_all = $this->delComicBySearchTotalNum ( $this->getComicsInfoByIds ( $id_list) );
		$list = $this->Null2str ( $result_all );
		
		$list_all= count ( $list );
		
		if ($page == 1) {
			if ($list_all > $hot_count) {
				for($i = 0; $i < $hot_count; $i ++) {
					$tmp .= ($list [$i] ['ContentID'].",");
				}
				$cids = substr($tmp,0,strlen($tmp)-1);
				$comic_info_sql = 'select t1.ContentID, t1.showtime, t1.videos, t1.ios_album_pic, t1.cover, t1.area, t1.name, t1.condition, t1.cur_num, t1.introduction, t1.update_notice
				    				from t_content_1 t1, t_content_index i
				    				where i.ContentID in ('.$cids.") and t1.ContentID = i.ContentID and i.State = 1
				    				and i.NodeID = 11" ;
				$temp = $this->get_list ( $comic_info_sql );
				
				if (empty ( $temp )) {
					exit ();
				}
				$results = $this->cover2IosPic ( $temp );//未排序的hot_series
				
				 foreach($results as &$v){
					$v["introduction"] = strip_tags(html_entity_decode($v["introduction"],ENT_QUOTES,"UTF-8"));
				}  
				
			
				for($j = 0; $j < $hot_count; $j ++) {
					for($k = 0; $k < $hot_count; $k ++) {
						if ($list [$j] ["ContentID"] == $results [$k] ["ContentID"]) {
							$result [] = $results [$k];
						}
					}
				}
			
				foreach($result as $var){
					
					$total_num = $this->getTotalNumByVid ( $var ['videos'] );
					unset ( $var ['videos'] ); // 释放无用字段 
					
					$comic_sd_sql = 'select total_v , grade from db_video_dynamic.series_dynamic_info where type=1 and id =' . $var["ContentID"];
					
					$res = $this->get_list ( $comic_sd_sql );
					$comic = $var;
					$comic ['total_num'] = $total_num;
					
					if($var ['condition'] == '连载中'){
						$comic ['new_list'] = $this->get_new_comic ( $var["ContentID"], 2 );
					}else{
						$comic ['new_list'] ="";
					}
					
				//	$var ['condition'] == '连载中' ? $comic ['new_list'] = $this->get_new_comic ( $var["ContentID"], 2 ) : "";
					if (! empty ( $res )) {
						$comic ['total_v'] = $res [0] ['total_v'];
						$comic ['score'] = $res [0] ['grade'];
					} else {
						$comic ['total_v'] = '1000';
						$comic ['score'] = '7.0';
					}
					
					$comic ['play_list'] = $this->episodeList ( $var["ContentID"] ); // 默认取第一页数据
					$comics[] = $comic;
				}
				/*
				$comicsfinal;
				for($i = 0; $i < $hot_count; $i++){
					if(!strstr($comics[$i]['name'], "宣传片")){
						$comicsfinal[] = $comics[$i];
						continue;
					}
					for($j = $i+1; $j < $hot_count * 2; $j++){
						if(!strstr($comics[$j]['name'], "宣传片")){
							$tmep = $comics[$i];
							$comics[$i] = $comics[$j];
							$comicsfinal[] = $comics[$i];
							break;
						}
					}
				}
				*/
				$results_all['total_num'] =$list_all;
				$results_all["hot_series"] = $comics;
				$results_all['common_series'] = array_slice ( $list, $hot_count, min ( $list_all - $hot_count, $page_size ) );
				
			} else {
				for($i = 0; $i < $result ['total_num']; $i ++) {
						
					$tmp .= ($list [$i] ['ContentID'].",");
				}
				$cids = substr($tmp,0,strlen($tmp)-1);
				//$result ['hot_series'] [] = $this->seriesinfo ($cid, 2 );
				
				
				$comic_info_sql = 'select t1.ContentID, t1.showtime, t1.videos, t1.ios_album_pic, t1.cover, t1.area, t1.name, t1.condition, t1.cur_num, t1.introduction, t1.update_notice
				    				from t_content_1 t1, t_content_index i
				    				where i.ContentID in ('.$cids.") and t1.ContentID = i.ContentID and i.State = 1
				    				and i.NodeID = 11" ;
				
				$temp = $this->get_list ( $comic_info_sql );
				
				if (empty ( $temp )) {
					exit ();
				}
				
				$results = $this->cover2IosPic ( $temp );
			 foreach($results as &$v){
					$v["introduction"] = strip_tags(html_entity_decode($v["introduction"],ENT_QUOTES,"UTF-8"));
				}  
				for($j=0;$j<$hot_count;$j++){
					foreach($results as $v){
						if($v["ContentID"]==$list[$j]["ContentID"]){
							$result[] = $v;
						}else{
							continue;
						}
					}
				}
				
				foreach($result as $var){
					$total_num = $this->getTotalNumByVid ( $var ['videos'] );
					if ($total_num == 0) {
						return "";
					}
					unset ( $var ['videos'] ); // 释放无用字段
						
					$comic_sd_sql = 'select total_v , grade from db_video_dynamic.series_dynamic_info where type=1 and id =' . $var["ContentID"];
						
					$res = $this->get_list ( $comic_sd_sql );
						
					$comic ['total_num'] = $total_num;
						
					if($var ['condition'] == '连载中'){
						$comic ['new_list'] = $this->get_new_comic ( $var["ContentID"], 2 );
					}else{
						$comic ['new_list'] ="";
					}
						
					//	$var ['condition'] == '连载中' ? $comic ['new_list'] = $this->get_new_comic ( $var["ContentID"], 2 ) : "";
					if (! empty ( $res )) {
						$comic ['total_v'] = $res [0] ['total_v'];
						$comic ['score'] = $res [0] ['grade'];
					} else {
						$comic ['total_v'] = '1000';
						$comic ['score'] = '7.0';
					}
					$comic ['play_list'] = $this->episodeList ( $var["ContentID"] ); // 默认取第一页数据
					$comics[] = $comic;
				}
				$results_all['total_num'] =$list_all;
				$results_all["hot_series"] = $comics;
				$results_all['common_series'] = array_slice ( $list, $hot_count, min ( $list_all - $hot_count, $page_size ) );
			}
		} else {
			$results_all ['common_series'] = array_slice ( $list, ($page - 1) * $page_size + $hot_count, $page_size );
		}
		$results_all	=	$this->Null2str($results_all); //对 null 处理
		
		return $results_all;
	}
	
	/**
	 * 权值测试
	 *
	 * @param string $first_words        	
	 */
	public function searchTest($keywords, $hot_count, $page_size, $page) {
		if(strstr($keywords,"赛")){
			$keywords=1;
		}else if(strstr($keywords,"摩")){
			$keywords=2;
		}else if(strstr($keywords,"尔")){
			$keywords=3;
		}else if(strstr($keywords,"梦")){
			$keywords=4;
		}else if(strstr($keywords,"兽")||strstr($keywords,"怪")||strstr($keywords,"麦")){
			$keywords=5;
		}else if(strstr($keywords,"学")){
			$keywords=6;
		}
		switch ($keywords){
			case "赛尔号第一部":
				$id_list =array(1992,7623,7916,2772,6039);
				break;
			case "赛尔号第二部":
				$id_list=array(7623,7916,1992,6039,2772);
				break;
			case "摩尔庄园第一季":
				$id_list=array(4274,2184,2670,2060);
				break;
			case "摩尔庄园第二季":
				$id_list=array(4274,2060,2184,2670);
				break;
			case "摩尔庄园2海妖宝藏":
				$id_list=array(2060,4274,2184,2670);
				break;
			case "摩尔庄园冰世纪":
				$id_list=array(2060,2670,2184,4274);
				break;
			case "赛尔号大电影第二部":
				$id_list=array(2772,1992,6039,7623,7916);
				break;
			case "赛尔号大电影第一部":
				$id_list=array(2772,1992,6039,7623,7916);
				break;
			case 1:
				$id_list=array(1992,7623,7916,2772,6039);
				break;
			case 2:
				$id_list=array(4274,2060,2184,2670);
				break;
			case 3:
				$id_list=array(1992,7623,7916,2772,6039,4274,2060,2184,2670);
				break;
			case 4:
				$id_list=array(7896,7969,7968);
				break;
			case 5:
				$id_list=array(3542,7896,7969,7968);
				break;
			case 6:
				$id_list=array(7896,2189,7968);
				break;
			default:
				$id_list = array(7623,1992,2772,6039,4274,2184,2060,2670,2189,7896,7916,3542,7968,2023,7967,7969);
				break;
		}
		
		/**
 		 *删除集为0的剧
		 */
		
		$result_all = $this->delComicBySearchTotalNum ( $this->getComicsInfoByIds ( $id_list) );
		
		$list = $this->Null2str ( $result_all );
		
		$list_all= count ( $list );
		
		if ($page == 1) {
			if ($list_all >= $hot_count) {
				for($i = 0; $i < $hot_count; $i ++) {
					$tmp .= ($list [$i] ['ContentID'].",");
				}
				$cids = substr($tmp,0,strlen($tmp)-1);
				
				$comic_info_sql = 'select t1.ContentID, t1.showtime, t1.videos, t1.ios_album_pic, t1.cover, t1.area, t1.name, t1.condition, t1.cur_num, t1.introduction, t1.update_notice
				    				from t_content_1 t1, t_content_index i
				    				where i.ContentID in ('.$cids.") and t1.ContentID = i.ContentID and i.State = 1
				    				and i.NodeID = 11" ;
				
				$temp = $this->get_list ( $comic_info_sql );
				
				if (empty ( $temp )) {
					exit ();
				}
				$results = $this->cover2IosPic ( $temp );//未排序的hot_series
				
				 foreach($results as &$v){
					$v["introduction"] = strip_tags(html_entity_decode($v["introduction"],ENT_QUOTES,"UTF-8"));
				}  
			
				for($j = 0; $j < $hot_count; $j ++) {
					for($k = 0; $k < $hot_count; $k ++) {
						if ($list [$j] ["ContentID"] == $results [$k] ["ContentID"]) {
							$result [] = $results [$k];
						}
					}
				}
			
				foreach($result as $var){
					
					$total_num = $this->getTotalNumByVid ( $var ['videos'] );
					unset ( $var ['videos'] ); // 释放无用字段 
					
					$comic_sd_sql = 'select total_v , grade from db_video_dynamic.series_dynamic_info where type=1 and id =' . $var["ContentID"];
					
					$res = $this->get_list ( $comic_sd_sql );
					$comic = $var;
					$comic ['total_num'] = $total_num;
					
					if($var ['condition'] == '连载中'){
						$comic ['new_list'] = $this->get_new_comic ( $var["ContentID"], 2 );
					}else{
						$comic ['new_list'] ="";
					}
					
				//	$var ['condition'] == '连载中' ? $comic ['new_list'] = $this->get_new_comic ( $var["ContentID"], 2 ) : "";
					if (! empty ( $res )) {
						$comic ['total_v'] = $res [0] ['total_v'];
						$comic ['score'] = $res [0] ['grade'];
					} else {
						$comic ['total_v'] = '1000';
						$comic ['score'] = '7.0';
					}
					
					$comic ['play_list'] = $this->episodeList ( $var["ContentID"] ); // 默认取第一页数据
					$comics[] = $comic;
				}
				/*
				$comicsfinal;
				for($i = 0; $i < $hot_count; $i++){
					if(!strstr($comics[$i]['name'], "宣传片")){
						$comicsfinal[] = $comics[$i];
						continue;
					}
					for($j = $i+1; $j < $hot_count * 2; $j++){
						if(!strstr($comics[$j]['name'], "宣传片")){
							$tmep = $comics[$i];
							$comics[$i] = $comics[$j];
							$comicsfinal[] = $comics[$i];
							break;
						}
					}
				}
				*/
				$results_all['total_num'] =$list_all;
				$results_all["hot_series"] = $comics;
				$results_all['common_series'] = array_slice ( $list, $hot_count, min ( $list_all - $hot_count, $page_size ) );
				
			} else {
				for($i = 0; $i < $result ['total_num']; $i ++) {
						
					$tmp .= ($list [$i] ['ContentID'].",");
				}
				$cids = substr($tmp,0,strlen($tmp)-1);
				//$result ['hot_series'] [] = $this->seriesinfo ($cid, 2 );
				
				
				$comic_info_sql = 'select t1.ContentID, t1.showtime, t1.videos, t1.ios_album_pic, t1.cover, t1.area, t1.name, t1.condition, t1.cur_num, t1.introduction, t1.update_notice
				    				from t_content_1 t1, t_content_index i
				    				where i.ContentID in ('.$cids.") and t1.ContentID = i.ContentID and i.State = 1
				    				and i.NodeID = 11" ;
				
				$temp = $this->get_list ( $comic_info_sql );
				
				if (empty ( $temp )) {
					exit ();
				}
				
				$results = $this->cover2IosPic ( $temp );
			 foreach($results as &$v){
					$v["introduction"] = strip_tags(html_entity_decode($v["introduction"],ENT_QUOTES,"UTF-8"));
				}  
				for($j=0;$j<$hot_count;$j++){
					foreach($results as $v){
						if($v["ContentID"]==$list[$j]["ContentID"]){
							$result[] = $v;
						}else{
							continue;
						}
					}
				}
				
				foreach($result as $var){
					$total_num = $this->getTotalNumByVid ( $var ['videos'] );
					if ($total_num == 0) {
						return "";
					}
					unset ( $var ['videos'] ); // 释放无用字段
						
					$comic_sd_sql = 'select total_v , grade from db_video_dynamic.series_dynamic_info where type=1 and id =' . $var["ContentID"];
						
					$res = $this->get_list ( $comic_sd_sql );
						
					$comic ['total_num'] = $total_num;
						
					if($var ['condition'] == '连载中'){
						$comic ['new_list'] = $this->get_new_comic ( $var["ContentID"], 2 );
					}else{
						$comic ['new_list'] ="";
					}
						
					//	$var ['condition'] == '连载中' ? $comic ['new_list'] = $this->get_new_comic ( $var["ContentID"], 2 ) : "";
					if (! empty ( $res )) {
						$comic ['total_v'] = $res [0] ['total_v'];
						$comic ['score'] = $res [0] ['grade'];
					} else {
						$comic ['total_v'] = '1000';
						$comic ['score'] = '7.0';
					}
					$comic ['play_list'] = $this->episodeList ( $var["ContentID"] ); // 默认取第一页数据
					$comics[] = $comic;
				}
				$results_all['total_num'] =$list_all;
				$results_all["hot_series"] = $comics;
				$results_all['common_series'] = array_slice ( $list, $hot_count, min ( $list_all - $hot_count, $page_size ) );
			}
		} else {
			$results_all ['common_series'] = array_slice ( $list, ($page - 1) * $page_size + $hot_count, $page_size );
		}
		$results_all	=	$this->Null2str($results_all); //对 null 处理
		
		return $results_all;
	}
	
	/**
	 * 查询数据库
	 *
	 * @param string $sql        	
	 */
	public function get_list($sql) {
		$this->db_init ();
		$list = $this->db->get_all ( $sql );
		if (empty ( $list )) {
			return null;
		}
		return $list;
	}
	
	/**
	 * 初始化sphinx
	 */
	private function search_init() {
		require_once 'lib/functions/sphinxclient.class.php';
		static $sphinxclient;
		if (! isset ( $sphinxclient )) {
			$this->loadClass ( 'SphinxClient' );
			$this->SphinxClient->SetServer ( SPHINX_SERVER_HOST, SPHINX_SERVER_PORT );
			$this->SphinxClient->SetConnectTimeout ( 3 );
			$this->SphinxClient->SetMaxQueryTime ( 2000 );
			$sphinxclient = $this->SphinxClient;
		} else {
			$this->SphinxClient = $sphinxclient;
		}
	}
	
	/**
	 * 返回搜索的剧id
	 *
	 * @param string $keywords        	
	 * @return array
	 */
	private function getIdList($keywords) {
		$this->search_init ();
		
		$this->SphinxClient->SetArrayResult ( true );
		$this->SphinxClient->SetMatchMode ( SPH_MATCH_ANY );
		$this->SphinxClient->SetFieldWeights ( array (
				'name' => 100,
				'character' => 50,
				'introduction' => 10 
		));
		$result = $this->SphinxClient->Query ( $keywords, '*' ); // 此处为全部索引
		
		if (! empty ( $result ['matches'] )) {
			foreach ( $result ['matches'] as $key => $val ) {
				
				if ($val ['weight'] > 100) {
					$res ['all_id'] [] = $val ['id'];
				}
			}
		}
		if (empty ( $res )) {
			exit ();
		}
		else{
			$rest_all_id = array_slice($res["all_id"], 0,150);
		}
		return $rest_all_id;
	}
	
	/**
	 * 权值测试
	 *
	 * @param string $keywords        	
	 * @return array
	 */
	private function getIdListWeight($keywords, $weight) {
		$this->search_init ();
		
		$this->SphinxClient->SetArrayResult ( true );
		$this->SphinxClient->SetMatchMode ( SPH_MATCH_ANY );
		$this->SphinxClient->SetFieldWeights ( array (
				'name' => 100,
				'character' => 50,
				'introduction' => 10 
		) );
		$result = $this->SphinxClient->Query ( $keywords, '*' ); // 此处为全部索引
		
		if (! empty ( $result ['matches'] )) {
			foreach ( $result ['matches'] as $key => $val ) {
				
				if ($val ['weight'] > $weight) {
					$res ['all_id'] [] = $val ['id'];
				}
			}
		}
		if (empty ( $res )) {
			exit ();
		}
		return $res;
	}
	
	/**
	 * null to string
	 *
	 * @param
	 *        	$item
	 */
	public function Null2str($item) {
		if (! is_array ( $item )) {
			if ($item === null) {
				return '';
			} else {
				return $item;
			}
		} else {
			foreach ( $item as &$v ) {
				$v = $this->Null2str ( $v );
			}
			return $item;
		}
	}
	
	/**
	 * 处理剧集页的图片
	 *
	 * @param unknown_type $list        	
	 */
	public function cover2IosPic($list) {
		foreach ( $list as $key => &$v ) {
			if ($v ['ios_album_pic']) {
				$v ['cover'] = $v ['ios_album_pic'];
			}
			unset ( $v ['ios_album_pic'] );
		}
		
		return $list;
	}
	
	/**
	 * 获取剧的总数
	 */
	public function getComicsInfo() {
		$app_cache_key = 'app_cache_getComicsInfo_key';
		// 读取缓存
		$a_area = array (
				'japan' => '日本',
				'china' => '大陆',
				'usa' => '欧美',
				'movie' => '电影' 
		);
		
		foreach ( $a_area as $k => $v ) {
			
			$sql = 'select count(t1.ContentID) as ' . $k . ' from t_content_1 t1, t_content_index i
    					where t1.ContentID = i.ContentID and i.State = 1 and i.NodeID = 11 and total_num>0';
			switch ($k) {
				case 'china' :
					$tmp_sql = $sql . ' and t1.area = "大陆"';
					break;
				case 'japan' :
					$tmp_sql = $sql . ' and t1.area = "日本"';
					break;
				case 'movie' :
					$tmp_sql = $sql . ' and t1.video_type = "电影"';
					break;
				case 'usa' :
					$tmp_sql = $sql . ' and t1.area = "欧美"';
					break;
				default :
					break;
			}
			$tmp = $this->get_list ( $tmp_sql );
			$list [$k] = $tmp [0] [$k];
		}
		
		// $this->cache->setCache ( $app_cache_key, $list );
		
		return $list;
	}
	
	/**
	 * 获取指定剧集信息，无需缓存
	 *
	 * @param string $vids        	
	 * @return array
	 */
	public function getComicsByIds($vids) {
		$app_cache_key = 'app_cache_getComicsByIds_' . $vids . '_key';
		// 读取缓存
		$memcache = $this->initMemcache ();
		$list = $memcache->get ( $app_cache_key );
		
		if (! empty ( $list )) {
			return $list;
		}
		
		$players = array_unique ( explode ( ',', $vids ) );
		foreach ( $players as &$v ) {
			$v = ( int ) $v;
			if ($v == 0) {
				unset ( $v );
			}
		}

		$result_all = $this->delComicByTotalNum ( $this->getComicsInfoByIds ( $players ) );
		$list = $this->Null2str ( $this->cover2IosPic ( $result_all ) );

		$memcache->set ( $app_cache_key, $list );
		
		return $list;
	}
	
	/**
	 * 根据ids的数组获取剧信息
	 *
	 * @param array $Ids        	
	 */
	private function getComicsInfoByIds($Ids) {
		
		$memcache = $this->initMemcache();
		
		foreach ( $Ids as $v ) {
			$key = "series_".$v;
			$id_info = $memcache->get($key);
			if(empty($id_info)){
				$tmp .= ($v.",");
			}
		}
		
	
		$check_ids = substr($tmp,0,strlen($tmp)-1);
		
		$sql = 'select t1.ContentID, t1.videos, t1.name, t1.clear, t1.cur_num, t1.condition, t1.ios_album_pic, t1.cover
   				from t_content_1 t1, t_content_index i
   				where t1.ContentID = i.ContentID and i.State = 1 and i.NodeID = 11 and t1.ContentID in (' .$check_ids.")";
		$listtemp = $this->get_list ( $sql);
		
		foreach($listtemp as $v){
			$memcache->set($v['ContentID'],$v);
		}
		foreach($Ids as $v){
			$list[] = $memcache->get($v);
		}
		return $list;
	}
	
}
