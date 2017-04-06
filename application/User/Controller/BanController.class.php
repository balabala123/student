<?php
    namespace User\Controller;
    use Common\Controller\MemberbaseController;
    use Common\Controller\ExcelController;

    class BanController extends MemberbaseController
    {

        function _initialize()
        {
            parent::_initialize();
            $this->rolemdl = M('role_user');
            $this->stumdl = M('student');
            $this->teamdl = M('teacher');
            $this->banmdl = M('counsellor');
            $this->classmdl = M('class');
            $this->depmdl = M('depart');
            $this->ximdl = M('xi');
            $this->submdl = M('subject');
            $this->mdl = M('grade');
            $message = $this->user;
            $this->id = $message['rele_id'];
        }
        public function index() {
            $class_id = $this->banmdl->where('ban_id='.$this->id)->getField('class_id');
            $stu = $this->stumdl->where('class_id='.$class_id)->field('stu_name,stu_id,stu_no')->select();
            foreach($stu as $k=>$v) {
                $data[$k] = $this->mdl->where('stu_id='.$v['stu_id'])->select();
                $data[$k]['name'] = $v['stu_name'];
                $data[$k]['no'] = $v['stu_no'];
            }
            foreach($data as $k=>$v) {
                foreach($v as $key=>$value) {
                    if($key != 'name' && $key != 'no') {
                        $arr[$k]['name'] = $v['name'];
                        $arr[$k]['no'] = $v['no'];
                        $subject = $this->submdl->where('subject_id=' . $value['subject_id'])->getfield('subject_name');
                        $arr[$k][$subject] = $value['grade'];
                        $arr[$k]['sum'] += $value['grade'];
                    }
                }
            }
//            print_r($arr);
            foreach($arr[0] as $k=>$v) {
                if($k != 'name' && $k != 'no' && $k != 'sum') {
                    $sub[] = $k;
                }
            }
            $newArr=array();
            for($j=0;$j<count($arr);$j++){
                $newArr[]=$arr[$j]['sum'];
            }
            array_multisort($newArr,SORT_DESC,$arr);
//            print_r($arr);die;

            $this->assign('data',$arr);
            $this->assign('sub_list',$sub);

            foreach($arr as $k=>$v){
                $arr[$k]['paiming'] = $k+1;
            }

            $this->display();
        }
        function export_grade(){
            $class_id = $this->banmdl->where('ban_id='.$this->id)->getField('class_id');
            $stu = $this->stumdl->where('class_id='.$class_id)->field('stu_name,stu_id,stu_no')->select();
            foreach($stu as $k=>$v) {
                $data[$k] = $this->mdl->where('stu_id='.$v['stu_id'])->select();
                $data[$k]['name'] = $v['stu_name'];
                $data[$k]['no'] = $v['stu_no'];
            }
            foreach($data as $k=>$v) {
                foreach($v as $key=>$value) {
                    if($key != 'name' && $key != 'no') {
                        $arr[$k]['name'] = $v['name'];
                        $arr[$k]['no'] = $v['no'];
                        $subject = $this->submdl->where('subject_id=' . $value['subject_id'])->getfield('subject_name');
                        $arr[$k][$subject] = $value['grade'];
                        $arr[$k]['sum'] += $value['grade'];
                    }
                }
            }

            foreach($arr[0] as $k=>$v) {
                if($k != 'name' && $k != 'no' && $k != 'sum') {
                    $sub[] = $k;
                }
            }
            $newArr=array();
            for($j=0;$j<count($arr);$j++){
                $newArr[]=$arr[$j]['sum'];
            }
            array_multisort($newArr,SORT_DESC,$arr);
            foreach($arr as $k=>$v){
                $arr[$k]['paiming'] = $k+1;
            }
                $xlsName  = "班级学生成绩";
                $xlsCell  = array(
                    array('no','学生学号'),
                    array('name','学生姓名'),
                );
                foreach($sub as $k=>$v){

                    $xlsCell[$k+2] = array($v,$v);
                }
                $cc = count($xlsCell);
                $xlsCell[$cc] = array('sum','总分');
                $xlsCell[$cc+1] = array('paiming','排名');
//                print_r($xlsCell);
//                $xlsModel = M('Post');
//                $xlsData  = $xlsModel->Field('id,account,nickname')->select();
                $xlsData = $arr;
            $export = new ExcelController;
            $export->exportExcelForLabel('班级学生成绩',$xlsName,$xlsCell,$xlsData);
        }
    }
