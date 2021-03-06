<?php
namespace User\Controller;
use Common\Controller\MemberbaseController;

class StuThinkController extends MemberbaseController {
    protected $model;
    private $params;

    public function _initialize() {
        parent::_initialize();
        $this->model = M('Think');
        $this->params = I('params.');
        $rss = sp_get_current_user();
        $this->id = $rss['rele_id'];
        $message = $this->user;
        $rolemdl = M('role_user');
        $this->role = $rolemdl->where('user_id='.$message['id'])->getField('role_id');
    }

    public function index(){
        //判断该学生是否提交过评价
        $stu_id = $this->id;
//        print_r($_COOKIE);
        $sel = $this->model->where(array('from_stu_id'=>$stu_id))->find();

        if($sel){
            $this->assign('status',1);
        }
        $Student = M('Student');
        $class_res = $Student->field('class_id')->where(array('stu_id'=>$stu_id))->find();
        $class = $class_res['class_id'];

        //班级所有学生
        $where['class_id'] = $class;
        $where['stu_id'] = array('neq',$stu_id);
        $stu_all_res = $Student->field('stu_id,stu_name')->where($where)->select();


        foreach($stu_all_res as $k=>$v){
            $stu_all[$k]['stu_name'] = $v['stu_name'];
            $stu_all[$k]['stu_id'] = $v['stu_id'];
        }


        for($i=0;$i<ceil(count($stu_all)/3);$i++)
        {
            $stu_three[] = array_slice($stu_all, $i * 3 ,3);
        }

        $this->assign("data",$stu_three);

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

        $this -> display();
    }

    public function add(){
        $post = I('post.');
//        print_r($post);die;

        if(IS_POST){
            foreach($post as $k=>$v){
                $data[$k]['to_stu_id'] = $k;
                $data[$k]['from_stu_id'] = $this->id;
                if($v == 'A'){
                    $data[$k]['score'] = 4;
                }
                if($v == 'B'){
                    $data[$k]['score'] = 3;
                }
                if($v == 'C'){
                    $data[$k]['score'] = 2;
                }
                if($v == 'D'){
                    $data[$k]['score'] = 1;
                }
            }
//        print_r($data);die;
            foreach($data as $v){
                $sel[] = $v;
            }
//        print_r($sel);die;
            if($this->model->addALL($sel) !== false){
                $this->success('提交成功', U('Center/index'));
            }else{
                $this->error('提交失败');
            }
        }

    }


}
