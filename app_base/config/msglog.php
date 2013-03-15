<?php
define('MSGLOG_RESPONE_TIME_50' ,0x0A003300);
define('MSGLOG_RESPONE_TIME_100',0x0A003301);
define('MSGLOG_RESPONE_TIME_150',0x0A003302);
define('MSGLOG_RESPONE_TIME_200',0x0A003303);
define('MSGLOG_RESPONE_TIME_300',0x0A003304);
define('MSGLOG_RESPONE_TIME_400',0x0A003305);
define('MSGLOG_RESPONE_TIME_500',0x0A003306);
define('MSGLOG_RESPONE_TIME_600',0x0A003307);
define('MSGLOG_RESPONE_TIME_700',0x0A003308);
define('MSGLOG_RESPONE_TIME_800',0x0A003309);
define('MSGLOG_RESPONE_TIME_900',0x0A00330A);
define('MSGLOG_RESPONE_TIME_1000',0x0A00330B);
define('MSGLOG_RESPONE_TIME_2',0x0A00330C);
define('MSGLOG_RESPONE_TIME_3',0x0A00330E);
define('MSGLOG_RESPONE_TIME_4',0x0A00330F);
define('MSGLOG_RESPONE_TIME_5',0x0A003310);
define('MSGLOG_RESPONE_TIME_10',0x0A003311);
define('MSGLOG_RESPONE_TIME_11',0x0A003312);

$g_msglog['ios']	=	array(	'seriesInfo'			=> 0x14000056,		//获取剧集信息
								'aboutSeries'			=> 0x14000057,		//获取剧的相关推荐剧
								'search' 				=> 0x14000058,		//关键词搜索
								'historyList'       	=> 0x14000059,      //历史记录
								'player'				=> 0x1400005A,		//获取首页全部动画角色
								'get_about_series'  	=> 0x1400005B,		//用户选择N个后，按顺序推荐其他所有的放入轮播图
								'listType' 				=> 0x1400005C,		//获取日漫，国产等频道页剧信息
								'feedback'          	=> 0x1400005D,      //用户反馈
								'seriesArray'      	    => 0x1400005E       //根据指定的vid获取一系列剧信息
);
