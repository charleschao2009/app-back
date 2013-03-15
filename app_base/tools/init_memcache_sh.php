<?php

/**
 * 清除缓存
 */
$mem = new Memcache();
$mem->connect("10.1.1.72", 11211);
$mem->flush();
/**
 * 读入list缓存
 */

/*
 * age
 */

require_once '../config/init.php';

require_once '../model/iosModel.class.php';
echo "\r\n begin at".date("Y-m-d H:i:s");
for($i=0;$i<4;$i++){
	//
	$app_cache_all_newest_key = 'app_cache_all_newest'. '_' . $i . '_key';
	//最新
	$newest_sql = 'select t1.ContentID, t1.videos, t1.name, t1.clear, t1.cur_num,
			t1.condition, t1.ios_album_pic, t1.cover from t_content_1 t1,
			t_content_index i where t1.ContentID = i.ContentID and i.State = 1 and i.NodeID = 11
			and t1.total_num>0 ';
	$area_sql = 'select t1.ContentID, t1.videos, t1.area, t1.name, t1.clear, t1.cur_num,
			t1.condition, t1.ios_album_pic, t1.cover from t_content_1 t1, t_content_index i
    		where t1.ContentID = i.ContentID and i.State = 1 and i.NodeID = 11 and t1.total_num > 0 ';
	$china_sql =  $area_sql.'and t1.area = "大陆" ';
	$japan_sql = $area_sql.'and t1.area = "日本" ';
	$usa_sql = $area_sql.'and t1.area = "欧美" ';
	$movie_sql = $area_sql.'and t1.video_type = "电影" ';
	$age_0_sql = 'order by i.PublishDate DESC';
	$age_1_sql = 'and age_level = "3-6" order by i.PublishDate DESC ';
	$age_2_sql = 'and age_level = "7-9" order by i.PublishDate DESC ';
	$age_3_sql = 'and age_level = "10-15" order by i.PublishDate DESC ';
	if($i==0){
		$newest_sql .= 'order by i.PublishDate DESC limit 400';
		$china_sql .= $age_0_sql;
		$japan_sql .=$age_0_sql;
		$usa_sql .=$age_0_sql;
		$movie_sql .= $age_0_sql;
	}else if($i==1){
		$newest_sql .= 'and age_level = "3-6" order by i.PublishDate DESC limit 280 ';
		$china_sql .= $age_1_sql;
		$japan_sql .= $age_1_sql;
		$usa_sql .= $age_1_sql;
		$movie_sql .= $age_1_sql;
	}else if($i==2){
		$newest_sql .= ' and age_level = "7-9" order by i.PublishDate DESC limit 280 ';
		$china_sql .= $age_2_sql;
		$japan_sql .= $age_2_sql;
		$usa_sql .= $age_2_sql;
		$movie_sql .= $age_2_sql;
	}else if($i==3){
		$newest_sql .= ' and age_level = "10-15" order by i.PublishDate DESC limit 280 ';
		$china_sql .= $age_3_sql;
		$japan_sql .= $age_3_sql;
		$usa_sql .= $age_3_sql;
		$movie_sql .= $age_3_sql;
	}
	
	$model = new iosModel();
	
	$result_newest = $model->delComicByTotalNum ( $model->get_list ( $newest_sql ) ); // 去掉集为空的剧
	$result_china = $model->reSortArray ( $model->delComicByTotalNum ( $model->get_list ( $china_sql ) ) ); //国产
	$result_japan = $model->reSortArray ( $model->delComicByTotalNum ( $model->get_list ( $japan_sql ) ) ); //日漫
	$result_usa = $model->reSortArray ( $model->delComicByTotalNum ( $model->get_list ( $usa_sql ) ) ); //欧美
	$result_movie = $model->reSortArray ( $model->delComicByTotalNum ( $model->get_list ( $movie_sql ) ) ); //电影
	$total_newest_num = count ( $result_newest );
	
	$total_newest_pages = ceil($total_newest_num/15);
	for($page = 1;$page<=$total_newest_pages;$page++){
		
		$app_cache_key = 'app_cache_newest' . '_15' . '_' . $page . '_' . $i;
		$result_newest_tmp = $model->Null2str ( $model->cover2IosPic ( array_slice ( $result_newest, ($page - 1) * 15, 15 ) ) );
		$list = array (
				'total_num' => $total_newest_num,
				'list' => $result_newest_tmp
		);
		$list	=	$model->Null2str($list); //对 null 处理
		$mem->set ( $app_cache_key, $list );
	}
	
	$type =array("china","japan","usa","movie");
	//foreach()
	
	/*
	 * 国产
	 */
	$total_china_num = count ( $result_china );
	$total_china_pages = ceil($total_china_num/15);
	for($page = 1;$page<=$total_china_pages;$page++){
	
		$app_cache_key = 'app_cache_china' . '_15' . '_' . $page . '_' . $i;
		$result_china_tmp = $model->Null2str ( $model->cover2IosPic ( array_slice ( $result_china, ($page - 1) * 15, 15 ) ) );
		$list = array (
				'total_num' => $total_china_num,
				'list' => $result_china_tmp
		);
		$list	=	$model->Null2str($list); //对 null 处理
		$mem->set ( $app_cache_key, $list );
	}
	
	/*
	 * 日漫
	*/
	$total_japan_num = count ( $result_japan );
	$total_japan_pages = ceil($total_japan_num/15);
	for($page = 1;$page<=$total_japan_pages;$page++){
	
		$app_cache_key = 'app_cache_japan' . '_15' . '_' . $page . '_' . $i;
		$result_japan_tmp = $model->Null2str ( $model->cover2IosPic ( array_slice ( $result_japan, ($page - 1) * 15, 15 ) ) );
		$list = array (
				'total_num' => $total_japan_num,
				'list' => $result_japan_tmp
		);
		$list	=	$model->Null2str($list); //对 null 处理
		$mem->set ( $app_cache_key, $list );
	}
	/*
	 * 欧美
	*/
	$total_usa_num = count ( $result_usa );
	$total_usa_pages = ceil($total_usa_num/15);
	for($page = 1;$page<=$total_usa_pages;$page++){
	
		$app_cache_key = 'app_cache_usa' . '_15' . '_' . $page . '_' . $i;
		$result_usa_tmp = $model->Null2str ( $model->cover2IosPic ( array_slice ( $result_usa, ($page - 1) * 15, 15 ) ) );
		$list = array (
				'total_num' => $total_usa_num,
				'list' => $result_usa_tmp
		);
		$list	=	$model->Null2str($list); //对 null 处理
		$mem->set ( $app_cache_key, $list );
	}
	
	/*
	 * 电影
	*/
	$total_movie_num = count ( $result_movie );
	$total_movie_pages = ceil($total_movie_num/15);
	for($page = 1;$page<=$total_movie_pages;$page++){
	
		$app_cache_key = 'app_cache_movie' . '_15' . '_' . $page . '_' . $i;
		$result_movie_tmp = $model->Null2str ( $model->cover2IosPic ( array_slice ( $result_movie, ($page - 1) * 15, 15 ) ) );
		$list = array (
				'total_num' => $total_movie_num,
				'list' => $result_movie_tmp
		);
		$list	=	$model->Null2str($list); //对 null 处理
		$mem->set ( $app_cache_key, $list );
	}
}
echo "-------end at".date("Y-m-d H:i:s")."\n";
?>