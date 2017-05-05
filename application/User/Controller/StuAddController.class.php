<?php
namespace User\Controller;
use Common\Controller\MemberbaseController;

class StuAddController extends MemberbaseController {
    protected $model;
    private $params;

    public function _initialize() {
        parent::_initialize();
        $this->model = D('Add_point');
        $this->params = I('params.');
        $rss = sp_get_current_user();
        $this->id = $rss['rele_id'];
        $message = $this->user;
        $rolemdl = M('role_user');
        $this->role = $rolemdl->where('user_id='.$message['id'])->getField('role_id');
    }

    public function index(){
        $stu_id = $this->id;
        $where['disabled'] = 0;
        $where['stu_id'] = $stu_id;

        //搜索
        $type_name = trim(I('request.type_name'));
        if($type_name || ($type_name == 0 && $type_name != '')){
            $where['type_name'] = array('like',"%$type_name%");
            $this->assign('type_name',I('request.type_name'));
        }

        //分页
        $page = I('post.');
        $count=$this->model->where($where)->count();
        $total_page = ceil($count/2);
        if(!empty($page) && I('post.page')){
            $current_page = I('post.page');
        }else{
            $current_page = 1;
        }

        $Add_point_msg = M('Add_point_msg');

        $res = $this->model->limit(($current_page-1)*2,2)->where($where)->select();
        foreach($res as $k=>$v){
            $data[$k]['id'] = $v['id'];
            $data[$k]['type_name'] = $v['type_name'];

            $sel = $Add_point_msg->field('point')->where(array('type_name'=>$v['type_name']))->find();
            $data[$k]['point'] = $sel['point'];

            $data[$k]['add_res'] = $v['add_res'];
            $data[$k]['add_note'] = $v['add_note'];
            $data[$k]['create_time'] = date("Y/m/d H:i",$v['create_time']);
            if($v['status'] == 0){
                $data[$k]['status'] = '申请中';
            }
            if($v['status'] == 1){
                $data[$k]['status'] = '审批通过';
            }
            if($v['status'] == 2){
                $data[$k]['status'] = '审批拒绝';
            }
            if($v['status'] == 3){
                $data[$k]['status'] = '已取消';
            }

        }

        $Add_point_msg = M("Add_point_msg");
        $sel = $Add_point_msg->field('type_name')->select();
        foreach ($sel as $v){
            $type[] = $v['type_name'];
        }
//        print_r($type);die;
        $this->assign("type",$type);

        $this->assign('data',$data);


        switch($this->role) {
            case 2:
                $this->assign('role', 1);
                break;
            case 3:
                $this->assign('role', 2);
                break;
            case 4:
                $this->assign('role', 3);
                break;
        }
//        $this->assign($this->user);
        $this->assign("current_page",$current_page);
        $this->assign("total_page",$total_page);
        $this -> display();
    }

//    public function add(){
//        $Add_point_msg = M("Add_point_msg");
//        $sel = $Add_point_msg->field('type_name')->select();
//        foreach ($sel as $v){
//            $type[] = $v['type_name'];
//        }
////        print_r($type);die;
//        $this->assign("type",$type);
//        $this->display();
//    }

    public function add_post(){
        $data = I('post.');
        $stu_id = $this->id;
//        print_r($post);die;
        $data['create_time'] = time();
        $data['stu_id'] = $stu_id;

        //处理时间
        $data['start_time'] = strtotime($data['start_time']);
        $data['end_time'] = strtotime($data['end_time']);

        //开始时间必须小于结束时间
        if($data['start_time'] >= $data['end_time']){
            $this->error('时间有误!');
        }

        //检查是否重复添加
        $find = $this->model->where(array('type_name'=>$data['type_name'],'disabled'=>0,'stu_id'=>$data['stu_id']))->find();
        if ($find) {
            $this->error('已经申请过同类型加分项!');
        }

//        print_r($data);die;
        if ($this->model->create($data)!==false) {
            if ($this->model->add()!==false) {
                $this->success('提交成功!', U('StuAdd/index'));
            } else {
                $this->error('提交失败!');
            }
        } else {
            $this->error($this->model->getError());
        }


    }

    public function remove_add(){
        //取消申请
            $id = I("get.id",0,'intval');
        //判断状态是否为审批通过
        $status = $this->model->field('status')->where(array('id'=>$id))->find();

        if($status['status'] == 1){
            $this->error('审批已经通过，无法取消!');
        }else{
            //更改状态值为3（已取消）
            if ($this->model->where(array('id'=>$id))->save(array('status'=>3))!==false) {
                $this->success('取消成功');
            } else {
                $this->error('取消失败');
            }
        }

    }

}
