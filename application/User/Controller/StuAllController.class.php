<?php
namespace User\Controller;
use Common\Controller\MemberbaseController;

class StuALLController extends MemberbaseController {
    protected $model;
    private $params;

    public function _initialize() {
        parent::_initialize();
        $this->model = D('Think');
        $this->params = I('params.');
        $rss = sp_get_current_user();
        $this->id = $rss['rele_id'];
        $message = $this->user;
        $rolemdl = M('role_user');
        $this->role = $rolemdl->where('user_id='.$message['id'])->getField('role_id');
    }

    public function index(){
        $stu_id = $this->id;

        $Student = M("Student");
        $sel1 = $Student->field('stu_name')->where(array('stu_id'=>$stu_id))->find();

        $stu_name = $sel1['stu_name'];

        $score = $this->model->where(array('to_stu_id'=>$stu_id))->avg('score');
//        print_r($score);die;

        if($score > 4){
            $lv = 'A++';
        }
        if($score >= 3.5 && $score <= 4){
            $lv = 'A+';
        }
        if($score >= 3 && $score < 3.5){
            $lv = 'A';
        }
        if($score >= 2.5 && $score < 3){
            $lv = 'A-';
        }
        if($score >= 2 && $score < 2.5){
            $lv = 'B+';
        }
        if($score >= 1.5 && $score < 2){
            $lv = 'B';
        }
        if($score >= 1 && $score < 1.5){
            $lv = 'B-';
        }
        if($score >= 0.5 && $score < 1){
            $lv = 'C+';
        }
        if($score >= 0 && $score < 0.5){
            $lv = 'C';
        }

        $this->assign("stu_name",$stu_name);
        $this->assign("score",$score);
        $this->assign("lv",$lv);


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

        $this -> display();
    }


}
