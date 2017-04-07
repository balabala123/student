<?php
    namespace Admin\Controller;

    use Common\Controller\AdminbaseController;
    use Common\Controller\ExcelController;

    class StummController extends AdminbaseController {

        // 参数
        private $params;
        private $ximdl;
        private $depmdl;
        private $classmdl;
        private $pageNum;
        private $excel;
        private $db_prefix;

        public function _initialize() {
            $this->params = I('param.');
            parent::_initialize();
            $this->model = M('student');
            $this->ximdl = M('xi');
            $this->depmdl = M('depart');
            $this->classmdl = M('class');
            $this->usermdl = M('users');
            $this->pageNum = 10;
            $this->excel = new ExcelController;
            $this->db_prefix = C('DB_PREFIX');
        }

        //查看学生信息
        function index() {
            $xi_data = $this->ximdl->select();
            $this->assign('xi',$xi_data);
            //搜索start
            if ($this->params['stu_name']) {
                $where['stu_name'] = $this->params['stu_name'] ;
            }
            if ($this->params['stu_xi']) {
                $where['xi_id'] = $this->params['stu_xi'] ;
            }
            if ($this->params['stu_dep']) {
                $where['depart_id'] = $this->params['stu_dep'] ;
            }
            if ($this->params['stu_class']) {
                $where['class_id'] = $this->params['stu_class'] ;
            }
            //搜索end
            $count = $this->model->where($where)->count();
            $page = $this->page($count, $this->pageNum);
            $data = $this->model->where($where)->limit($page->firstRow , $page->listRows)->order('class_id')->select();
            foreach($data as $key=>$item) {
                $xi = $this->ximdl->where("xi_id=" . $item['xi_id'])->field('xi_name')->find();
                $data[$key]['xi'] = $xi['xi_name'];
                $dep = $this->depmdl->where("depart_id=" . $item['depart_id'])->field('depart_name')->find();
                $data[$key]['depart'] = $dep['depart_name'];
                $class = $this->classmdl->where("class_id=" . $item['class_id'])->field('class_no')->find();
                $data[$key]['class'] = $class['class_no'];
            }
            $this->assign("page", $page->show('Admin'));
            $this->assign('list',$data);
            $this->display();
        }

        //添加学生页面
        public function add() {
            $xi_data = $this->ximdl->select();
            $this->assign('xi',$xi_data);
            $this->display();
        }

        //添加学生
        public function add_post() {
                !isset($this->params['stu_name']) && $this->error('请填写学生姓名');
                !isset($this->params['stu_pwd']) && $this->error('请填写学生身份证号');
                !isset($this->params['stu_xi']) && $this->error('请选择院系');
                !isset($this->params['stu_dep']) && $this->error('请选择专业');
                !isset($this->params['stu_class']) && $this->error('请选择班级');
                if($this->params['stu_xi'] == '--请选择院系--'){
                    $this->error('请选择院系');
                }
                if($this->params['stu_dep'] == '--请选择专业--'){
                    $this->error('请选择专业');
                }
                if($this->params['stu_class'] == '--请选择班级--'){
                    $this->error('请选择班级');
                }
                $data['stu_name'] = $this->params['stu_name'];
                $data['xi_id'] = $this->params['stu_xi'];
                $data['depart_id'] = $this->params['stu_dep'];
                $data['class_id'] = $this->params['stu_class'];
                $data['stu_pwd'] = sp_password($this->params['stu_pwd']);
            //学号start
            $class_no = $this->classmdl->where('class_id='.$data['class_id'])->field('class_no')->find();
            $stu_no = $this->model->where('class_id='.$data['class_id'])->max(stu_no);
            $stu_no = substr($stu_no,-2,2);
            if(!isset($stu_no) || empty($stu_no)) {
                $stu_no = '01';
            }else{
                $stu_no = ++$stu_no;
                if ($stu_no <= 9) {
                    $stu_no = '0' . $stu_no;
                }
            }
            $data['stu_no'] = $class_no['class_no']. $stu_no;
            //end
            if ($this->model->data($data)->add()){
                $id = $this->model->where('stu_no='.$data['stu_no'])->field('stu_id')->find();
                $data_logn['user_login'] = $data['stu_name'];
                $data_logn['rele_id'] = $id['stu_id'];
                $data_logn['user_type'] = 2;
                $data_logn['user_pass'] = $data['stu_pwd'];
                $data_logn['user_email'] = '823650031@qq.com';
                if($this->usermdl->data($data_logn)->add()) {
                    $id = $this->usermdl->where("user_login='".$data_logn['user_login']."'")->field('id')->find();
                    $role_mdl = M('role_user');
                    $data_role['role_id'] = 2;
                    $data_role['user_id'] = $id['id'];
                    if($role_mdl->data($data_role)->add()) {
                        $this->success("添加成功", U('Stumm/add'));
                    }else{
                        $this->error("添加角色用户失败");
                    }
                }else{
                    $this->error("添加用户失败");
                }
            } else {
                $this->error("添加失败");
            }

        }

        //导入添加
        public function import_add_post($params) {
            if($params) {
                $data['stu_name'] = $params['stu_name'];
                $data['xi_id'] = $params['xi_id'];
                $data['depart_id'] = $params['depart_id'];
                $data['class_id'] = $params['class_id'];
                $data['stu_pwd'] = sp_password($params['stu_pwd']);
                //学号start
                $class_no = $this->classmdl->where('class_id=' . $data['class_id'])->field('class_no')->find();
                $stu_no = $this->model->where('class_id=' . $data['class_id'])->max(stu_no);
                $stu_no = substr($stu_no, -2, 2);
                if (!isset($stu_no) || empty($stu_no)) {
                    $stu_no = '01';
                } else {
                    $stu_no = ++$stu_no;
                    if ($stu_no <= 9) {
                        $stu_no = '0' . $stu_no;
                    }
                }
                $data['stu_no'] = $class_no['class_no'] . $stu_no;
                //end
                if ($this->model->data($data)->add()) {
                    $id = $this->model->where('stu_no=' . $data['stu_no'])->field('stu_id')->find();
                    $data_logn['user_login'] = $data['stu_name'];
                    $data_logn['rele_id'] = $id['stu_id'];
                    $data_logn['user_type'] = 2;
                    $data_logn['user_pass'] = $data['stu_pwd'];
                    $data_logn['user_email'] = '823650031@qq.com';
                    if ($this->usermdl->data($data_logn)->add()) {
                        $id = $this->usermdl->where("user_login='" . $data_logn['user_login'] . "'")->field('id')->find();
                        $role_mdl = M('role_user');
                        $data_role['role_id'] = 2;
                        $data_role['user_id'] = $id['id'];
                        if ($role_mdl->data($data_role)->add()) {
                            return 1;
                        } else {
                            return 2;
                        }
                    } else {
                        return 2;
                    }
                } else {
                    return 2;
                }
            }

        }

        //删除学生信息
        public function delete() {
            $ids = $this->params['ids'];
            $id = $this->params['stu_id'];
            if(is_array($ids)) {
                $where['stu_id'] = array('in', $ids);
                $where_re['rele_id'] = array('in', $ids);
            } elseif(is_numeric($id)) {
                $where['stu_id'] = $id;
                $where_re['rele_id'] = $id;
            }
            $where || $this->error(L('NO_DATA'));
            //由于有成绩外键关联，所以在删除时，要将成绩表中的数据一起删除
            $grade_mdl = M('grade');
            $grade_ids = $grade_mdl->where($where)->field('grade_id')->select();
            if($grade_ids) {
                foreach ($grade_ids as $k => $v) {
                    $grade_id[] = $v['grade_id'];
                }
                $where_grade['grade_id'] = array('in', $grade_id);
                $grade_mdl->where($where_grade)->delete();
            }
            $no = $this->model->where($where)->field('stu_no,class_id')->select();
            //得到用户表中的id
            $user_id = $this->usermdl->where($where_re)->field('id')->select();
            foreach($user_id as $k=>$v) {
                $user_ids[] = $v['id'];
            }
            $www['user_id'] = array('in',$user_ids);
            $role = M('role_user');
            $role_id = $role->where($www)->getfield('user_id,role_id');
            foreach($role_id as $k=>$v) {
                if($v!=2) {
                    foreach($user_ids as $kk=>$vv) {
                        if($vv == $k){
                            unset($user_ids[$kk]);
                        }
                    }
                }
            }

            $where_user['id'] = array('in',$user_ids);
            $res = $this->model->where($where)->delete();
            if($res) {
                $this->usermdl->where($where_user)->delete();
                foreach($no as $value) {
                    $where_no['stu_no'] = array('gt',$value['stu_no']);
                    $where_no['class_id'] = $value['class_id'];
                    $bool[] = $this->model->where($where_no)->setDec('stu_no',1);
                }
                if(array_key_exists('false',$bool)) {
                    $this->error("失败");
                }else {
                    $this->success("删除成功");
                }
            }else{
                $this->error("删除失败");
            }
        }
        //三级联动
        public function change()
        {
            $data = $this->depmdl->where('xi_id=' . $this->params['xi_id'])->field('depart_name,depart_id')->select();
            $this->ajaxReturn($data);
        }

        //三级联动2
        public function change_two()
        {
            $data = $this->classmdl->where('depart_id='.$this->params['depart_id'])->field('class_no,class_id')->select();
            $this->ajaxReturn($data);
        }

        public function get_data() {
            $data = $this->model->where('stu_id='.$this->params['stu_id'])->find();
            $data['c_id'] = $data['id'];
            $this->ajaxReturn($data);
        }

        //修改学生信息
        public function up_data() {
            !isset($this->params['stu_name']) && $this->error('请填写学生姓名');
            !isset($this->params['xi_id']) && $this->error('请选择院系');
            !isset($this->params['depart_id']) && $this->error('请选择专业');
            !isset($this->params['class_id']) && $this->error('请选择班级');
            $stu_id = $this->params['stu_id'];
            $xi_id = $this->params['xi_id'];
            $depart_id = $this->params['depart_id'];
            $class_id = $this->params['class_id'];
            $stu_name = $this->params['stu_name'];
            $no = $this->model->getFieldBystu_id($stu_id, 'stu_no');
            //从数据库中拿到原有的depart_id
            $mdepart = $this->model->getFieldBystu_id($stu_id, 'depart_id');
            //从数据库中拿到姓名
            $mname = $this->model->getFieldBystu_id($stu_id, 'stu_name');
            //从数据库中拿到class_id
            $mclass = $this->model->getFieldBystu_id($stu_id, 'class_id');
            //从数据库中拿到原有的xi_id
            $mxi = $this->model->getFieldBystu_id($stu_id, 'xi_id');
            //当修改院系、专业或者班级时
            if ($depart_id != $mdepart || $mclass != $class_id || $mxi != $xi_id) {
                $class_no = $this->classmdl->where('class_id='.$class_id)->field('class_no')->find();
                $stu_no = $this->model->where('class_id='.$class_id)->max('stu_no');
                $stu_no = substr($stu_no,-2,2);
                if(!isset($stu_no) || empty($stu_no)) {
                    $stu_no = '01';
                }else{
                    $stu_no = ++$stu_no;
                    if ($stu_no <= 9) {
                        $stu_no = '0' . $stu_no;
                    }
                }
                $data = array();
                $data['stu_pwd'] = sp_password($class_no['class_no']. $stu_no);
                $data['stu_no'] = $class_no['class_no']. $stu_no;
                $data['class_id'] = $class_id;
                $data['depart_id'] = $depart_id;
                $data['stu_name'] = $stu_name;
                $data['xi_id'] = $xi_id;
                $bool = $this->model->where('stu_id=' . $stu_id)->save($data);
                if ($bool) {
                    //修改班级其他学生的学号
                    $where_no['stu_no'] = array('gt',$no);
                    $where_no['class_id'] = $mclass;
                    $this->model->where($where_no)->setDec('stu_no',1);
                        $where_u['user_login'] = $mname;
                        $where_u['rele_id'] = $stu_id;
                        if($this->usermdl->where($where_u)->save(array('user_login'=>$stu_name))) {
                            $this->success("修改成功", U('Stumm/add'));
                        }else {
                            $this->error("修改用户失败");
                        }
                } else {
                    $this->error("修改失败");
                }
            } elseif ($mname != $stu_name) {
                $data['stu_name'] = $stu_name;
                $bool = $this->model->where('stu_id=' . $stu_id)->save($data);
                if ($bool) {
                    $where_u['user_login'] = $mname;
                    $where_u['rele_id'] = $stu_id;
                    if($this->usermdl->where($where_u)->save(array('user_login'=>$stu_name))) {
                        $this->success("修改成功", U('Stumm/index'));
                    }else{
                        $this->error("修改用户失败");
                    }
                    $this->success("修改成功", U('Stumm/index'));
                } else {
                    $this->error("修改失败");
                }
            } else {
                $this->success("修改成功", U('Stumm/index'));
            }
        }

        public function Import_import() {
            $url = CONTROLLER_NAME."/import_export";
            $this->assign("url",$url);
            $this->display('Import/import');
        }

        public function import_export(){
            if(IS_POST){
                $config=array(
                        'exts'=>array('xlsx','xls'),
                        'rootPath' =>  './data/',
                        'savePath'=>'excel/import/',
                        'saveRule'=>'time',
                    );
                    $upload = new \Think\Upload($config);
                    $info = $upload->upload();
                    if($info){
                        $params['info'] = $info;
                    }else{
                        $this->error($upload->getError());
                    }
                }
                $arr = $this->excel->importExecl($info);
                $data = $arr['data'][0];
            if($data['Content'][1][0] != '学生姓名' || $data['Content'][1][1] != '身份证号' || $data['Content'][1][2] != '院系' || $data['Content'][1][3] != '专业' ||$data['Content'][1][4] != '班级') {
                $this->error('请导入正确格式的文件！');
            }
            unset($data['Content'][1]);
            foreach($data['Content'] as $k=>$v) {
                if(!$v[0]) {
                    unset($data['Content'][$k]);
                }
                foreach ($v as $kk => $vv) {
                    if ($kk > 4) {
                        unset($data['Content'][$k][$kk]);
                    }
                }
            }

            foreach($data['Content'] as $k=>$v) {
                if($v[0]){
                    $list[$k]['stu_name'] = $v[0];
                }else{
                    $this->error('导入的学生姓名数据不合法！');
                }
                if(preg_match("/^\d{6}(18|19|20)?\d{2}(0[1-9]|1[12])(0[1-9]|[12]\d|3[01])\d{3}(\d|X)$/",$v[1])){
                    $list[$k]['stu_pwd'] = $v[1];
                }else{
                    $this->error('导入的身份证号数据不合法！');
                }

                if(preg_match("/^[\x7f-\xff]+$/",$v[2])){
                    $xi = $this->ximdl->where('xi_name='."'".$v[2]."'")->getfield('xi_id');
                    $list[$k]['xi_id'] = $xi;
                }else{
                    $this->error('导入的院系数据不合法！');
                }
                if(preg_match("/^[\x7f-\xff]+$/",$v[3])){
                    $dep = $this->depmdl->where('depart_name='."'".$v[3]."'")->getfield('depart_id');
                    $list[$k]['depart_id'] = $dep;
                }else{
                    $this->error('导入的专业数据不合法！');
                }
                if(is_numeric($v[4])){
                    $class = $this->classmdl->where('class_no='."'".$v[4]."'")->getfield('class_id');
                    $list[$k]['class_id'] = $class;
                }else{
                    $this->error('导入的班级数据不合法！');
                }
            }
          foreach($list as $v) {
              $res[] = $this->import_add_post($v);
          }
            if(in_array(2,$res)) {
                $this->error('导入部分数据失败');
            }else{
                $this->success('导入成功');
            }
        }


        public function export() {
            !isset($this->params['choose_data']) && $this->error('请选择要导出的数据');
            $ids = explode(',',$this->params['choose_data']);
            $where['s.stu_id'] = array('in',$ids);
            $list = $this->model->alias('s')
                ->join("left join {$this->db_prefix}depart as d on d.depart_id = s.depart_id")
                ->join("left join {$this->db_prefix}class as c on c.class_id = s.class_id")
                ->join("left join {$this->db_prefix}xi as x on x.xi_id = s.xi_id")
                ->where($where)
                ->field('s.stu_name,s.stu_no,d.depart_name,c.class_no,x.xi_name')
                ->order('s.stu_no')
                ->select();

            $xlsName  = "学生信息表";
            $xlsCell  = array(
                array('stu_no','学生学号'),
                array('stu_name','学生姓名'),
                array('xi','院系'),
                array('depart','专业'),
                array('class','班级'),
            );
            $xlsData = $list;
            $export = new ExcelController;
            $export->exportExcelForLabel('学生信息汇总',$xlsName,$xlsCell,$xlsData);
        }
    }