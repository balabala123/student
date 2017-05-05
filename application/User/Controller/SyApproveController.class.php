<?php
namespace User\Controller;
use Common\Controller\MemberbaseController;

class SyApproveController extends MemberbaseController {
    protected $model;
    private $params;

    public function _initialize() {
        parent::_initialize();
        $this->model = D('Help');
        $this->params = I('params.');
    }

    public function index(){
//        $stu_id =get_current_admin_id();
//        $stu_id = get_current_userid();
//        print_r($stu_id);die;
        $where['cmf_help.disabled'] = 0;
        $where['cmf_help.status'] = 0;
        //搜索
        $xi_name = trim(I('request.xi_name'));
        $depart_name = trim(I('request.depart_name'));
        $class_id = trim(I('request.class_id'));
        $type_name = trim(I('request.type_name'));
        $stu_name = trim(I('request.stu_name'));
        $stu_no = trim(I('request.stu_no'));
        if($xi_name != ''){
            $where['xi_name'] = array('like',"%$xi_name%");
        }
        if($depart_name != ''){
            $where['depart_name'] = array('like',"%$depart_name%");
        }
        if($class_id != ''){
            $where['class_id'] = array('like',"%$class_id%");
        }
        if($type_name != ''){
            $where['cmf_help.type_name'] = array('like',"%$type_name%");
        }
        if($stu_name != ''){
            $where['stu_name'] = array('like',"%$stu_name%");
        }
        if($stu_no != ''){
            $where['stu_no'] = array('like',"%$stu_no%");
        }

        //分页
        $page = I('post.');
        $count=$this->model->where($where)->count();
        $total_page = ceil($count/6);
        if(!empty($page) && I('post.page')){
            $current_page = I('post.page');
        }else{
            $current_page = 1;
        }

        $res = $this->model->field('stu_name,cmf_xi.xi_name,cmf_depart.depart_name,class_id,cmf_help.id,cmf_help.type_name,cmf_student.stu_no')
            ->join('cmf_student on cmf_student.stu_id = cmf_help.stu_id')
            ->join('cmf_xi on cmf_xi.xi_id = cmf_student.xi_id')
            ->join('cmf_depart on cmf_depart.depart_id = cmf_student.depart_id')
            ->limit(($current_page-1)*6,6)
            ->where($where)
            ->select();
//        print_r($this->model->getLastsql());die;
        foreach($res as $k=>$v){
            $data[$k]['id'] = $v['id'];
            $data[$k]['stu_name'] = $v['stu_name'];
            $data[$k]['depart_name'] = $v['depart_name'];
            $data[$k]['class_id'] = $v['class_id'];
            $data[$k]['xi_name'] = $v['xi_name'];
            $data[$k]['stu_no'] = $v['stu_no'];
            $data[$k]['type_name'] = $v['type_name'];

        }

        $this->assign('data',$data);
        $this->assign("current_page",$current_page);
        $this->assign("total_num",$total_page);
        $this -> display();
    }

    public function check(){
        $id = I("get.id",0,'intval');
        $sel = $this->model->field('sy_note')->where(array('id'=>$id))->find();
        $sy_note = $sel['sy_note'];
        $this->assign('sy_note',$sy_note);
        $this->display();
    }

    public function pass(){
        //通过
        $id = I("get.id",0,'intval');

        if ($this->model->where(array('id'=>$id))->save(array('status'=>1))!==false) {
            $this->success('成功！');
        }else{
            $this->error('失败！');
        }

    }

    public function rebut(){
        //驳回
        $id = I("get.id",0,'intval');

        if ($this->model->where(array('id'=>$id))->save(array('status'=>2))!==false) {
            $this->success('成功！');
        }else{
            $this->error('失败！');
        }

    }

}
