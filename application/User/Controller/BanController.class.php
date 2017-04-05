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
//            print_r($arr);die;
            $post = I('post.');
            if($post){//导出Excel
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
                $this->exportExcel($xlsName,$xlsCell,$xlsData);

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


            foreach($arr as $k=>$v){
                $arr[$k]['paiming'] = $k+1;
            }
//            print_r($arr);die;
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
                $this->exportExcel($xlsName,$xlsCell,$xlsData);

        }


        function exportExcel($expTitle,$expCellName,$expTableData){

            $xlsTitle = iconv('utf-8', 'gb2312', $expTitle);//文件名称
            $fileName = $_SESSION['loginAccount'].date('_YmdHis');//or $xlsTitle 文件名称可根据自己情况设定
            $cellNum = count($expCellName);
            $dataNum = count($expTableData);
            vendor("PHPExcel.PHPExcel");
            $objPHPExcel = new \PHPExcel();
            $cellName = array('A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z','AA','AB','AC','AD','AE','AF','AG','AH','AI','AJ','AK','AL','AM','AN','AO','AP','AQ','AR','AS','AT','AU','AV','AW','AX','AY','AZ');

            $objPHPExcel->getActiveSheet(0)->mergeCells('A1:'.$cellName[$cellNum-1].'1');//合并单元格
            $objPHPExcel->getActiveSheet() -> getColumnDimension() -> setAutoSize(true);
//            $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(15);
            $objPHPExcel->setActiveSheetIndex(0)->getStyle()->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('A1', $expTitle.'         导出时间:'.date('Y-m-d'));
            for($i=0;$i<$cellNum;$i++){
                $objPHPExcel->setActiveSheetIndex(0)->setCellValue($cellName[$i].'2', $expCellName[$i][1]);
            }
            // Miscellaneous glyphs, UTF-8
            for($i=0;$i<$dataNum;$i++){
                for($j=0;$j<$cellNum;$j++){
                    $objPHPExcel->getActiveSheet(0)->setCellValue($cellName[$j].($i+3), $expTableData[$i][$expCellName[$j][0]]);
                }
            }

            header('pragma:public');
            header('Content-type:application/vnd.ms-excel;charset=utf-8;name="'.$xlsTitle.'.xls"');
            header("Content-Disposition:attachment;filename=$fileName.xls");//attachment新窗口打印inline本窗口打印
            $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
            $objWriter->save('php://output');
            exit;

        }
    }
