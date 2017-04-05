<?php
// +----------------------------------------------------------------------
// | ThinkCMF [ WE CAN DO IT MORE SIMPLE ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013-2014 http://www.thinkcmf.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: Dean <zxxjjforever@163.com>
// +----------------------------------------------------------------------
namespace Portal\Controller;
use Common\Controller\HomebaseController;

class ListController extends HomebaseController {

	// 前台文章列表
	public function index() {
	    $term_id=I('get.id',0,'intval');
		$term=sp_get_term($term_id);
		$post = M('posts');
		$post_hits = $post->where('id='.$term_id)->field('post_hits')->find();
		$post_hits = ++$post_hits['post_hits'];
		$arr = array('id'=>$term_id,
					'post_hits'=>$post_hits);
		$post->where('id='.$term_id)->save($arr);
		$list = $post->where('id='.$term_id)->find();
		$smeta= json_decode($list['smeta']);
		$aaa = $smeta->photo;
		foreach($aaa as $k=>$v) {
			$list['photo'][] = $v->url;
		}
		$this->assign('list',$list);
		$tplname=$term["list_tpl"];
		$tplname=sp_get_apphome_tpl($tplname, "list");
		$this->assign($term);
		$this->assign('cat_id', $term_id);
    	$this->display(":$tplname");
	}
	
	// 文章分类列表接口,返回文章分类列表,用于后台导航编辑添加
	public function nav_index(){
		$navcatname="文章分类";
        $term_obj= M("Terms");

        $where=array();
        $where['status'] = array('eq',1);
        $terms=$term_obj->field('term_id,name,parent')->where($where)->order('term_id')->select();
		$datas=$terms;
		$navrule = array(
		    "id"=>'term_id',
            "action" => "Portal/List/index",
            "param" => array(
                "id" => "term_id"
            ),
            "label" => "name",
		    "parentid"=>'parent'
        );
		return sp_get_nav4admin($navcatname,$datas,$navrule) ;
	}
}
