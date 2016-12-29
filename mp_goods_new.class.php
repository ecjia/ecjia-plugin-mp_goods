<?php
/**
 * 微信登录
 */
defined('IN_ECJIA') or exit('No permission resources.');

RC_Loader::load_app_class('platform_abstract', 'platform', false);
class mp_goods_new extends platform_abstract
{   
	
	
	/**
	 * 获取插件配置信息
	 */
	public function local_config() {
		$config = include(RC_Plugin::plugin_dir_path(__FILE__) . 'config.php');
		if (is_array($config)) {
			return $config;
		}
		return array();
	}
	
	//获取最新产品
    public function event_reply() {
    	$goods_db = RC_Loader::load_app_model('goods_model','goods');
    	$data = $goods_db->where(array('is_new'=>1,'is_delete'=>0))->order('sort_order ASC')->limit(5)->select();
    	 
    	$articles = array();
    	foreach ($data as $key => $val) {
    		$articles[$key]['Title'] = $val['goods_name'];
    		$articles[$key]['Description'] = '';
    		$articles[$key]['PicUrl'] = RC_Upload::upload_url($val['goods_img']);
    		//$articles[$key]['Url'] = 'http://test.b2b2c.ecjia.com/sites/touch/index.php?m=goods&c=index&a=init&id='.$val['goods_id'];
    		$home_url =  RC_Uri::home_url();
    		if (strpos($home_url, 'sites')) {
    			$url = substr($home_url, 0, strpos($home_url, 'sites'));
    			$articles[$key]['Url'] = $url.'sites/m/index.php?m=goods&c=index&a=show&goods_id='.$val['goods_id'];
    		} else {
    			$articles[$key]['Url'] = $home_url.'/sites/m/index.php?m=goods&c=index&a=show&goods_id='.$val['goods_id'];
    		}
    	}
    	$count = count($articles);
    	$content = array(
    		'ToUserName' => $this->from_username,
    		'FromUserName' => $this->to_username,
    		'CreateTime' => SYS_TIME,
    		'MsgType' => 'news',
    		'ArticleCount'=>$count,
    		'Articles'=>$articles
    	);
    	return $content;
    }
}

// end