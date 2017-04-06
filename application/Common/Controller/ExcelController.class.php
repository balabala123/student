<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/12/28
 * Time: 17:34
 */

namespace Common\Controller;


use Think\Controller;

class ExcelController extends Controller
{
    /**
     * +----------------------------------------------------------
     * Export Excel | 2013.08.23
     * Author:HongPing <hongping626@qq.com>
     * +----------------------------------------------------------
     * @param $expTitle     string File name
     * +----------------------------------------------------------
     * @param $expCellName  array  Column name
     * +----------------------------------------------------------
     * @param $expTableData array  Table data
     * +----------------------------------------------------------
     */
    public function exportExcelForReconciliation($fileName = 'export', $expTitle, $expCellName, $expTableData)
    {
        $xlsTitle = iconv('utf-8', 'gb2312', $expTitle);//文件名称
        $fileName = $fileName . date('_YmdHis');//or $xlsTitle 文件名称可根据自己情况设定
        $cellNum = count($expCellName);
        $dataNum = count($expTableData);
        vendor("PHPExcel.PHPExcel");
        $objPHPExcel = new \PHPExcel();
        $cellName = array('A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z', 'AA', 'AB', 'AC', 'AD', 'AE', 'AF', 'AG', 'AH', 'AI', 'AJ', 'AK', 'AL', 'AM', 'AN', 'AO', 'AP', 'AQ', 'AR', 'AS', 'AT', 'AU', 'AV', 'AW', 'AX', 'AY', 'AZ');

        
        for ($i = 0; $i < $cellNum; $i++) {
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue($cellName[$i] . '1', $expCellName[$i][0]);

            //设置单元格字体
            $objPHPExcel->getActiveSheet(0)->getStyle($cellName[$i] . '1')->getFont()->setBold(true); //字体加粗 
        }
        for ($i = 0; $i < $cellNum; $i++) {
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue($cellName[$i] . '2', $expCellName[$i][1]);

            //设置单元格字体
            $objPHPExcel->getActiveSheet(0)->getStyle($cellName[$i] . '2')->getFont()->setBold(true); //字体加粗 

        }



        // Miscellaneous glyphs, UTF-8
        $beginRow = 3;
        $endRow = 3;
        for ($i = 0; $i < $dataNum; $i++) {

            $one_count = count($expTableData[$i]['delivery_no']);
            $endRow += $one_count;
            for ($j = 0; $j < $cellNum; $j++) {
                

                $column_index = $cellName[$j];
                if($column_index != 'J' ){
                    if($one_count>1){
                        $objPHPExcel->getActiveSheet(0)->mergeCells($column_index.$beginRow.":".$column_index.$endRow);
                        //垂直居中
                        $objPHPExcel->getActiveSheet(0)->getStyle($column_index .$beginRow)->getAlignment()->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);
                    }  
                    $objPHPExcel->getActiveSheet(0)->setCellValue($column_index .$beginRow, $expTableData[$i][$expCellName[$j][0]]); 
                }else{
                    for($k = 0; $k < $one_count; $k++){
                        $objPHPExcel->getActiveSheet(0)->setCellValue('D'.($k+$beginRow), $expTableData[$i]['column3'][$k]);
                    }
                }      
                //表的列任意宽度
                $objPHPExcel->getActiveSheet(0)->getColumnDimension($cellName[$j])->setAutoSize(true);
            }
            $beginRow = $endRow + 1;
        }
        
        //列
        $cell_border_num = (int)$cellNum - 1;
        //行
        $row_border_num = (int)$dataNum + 2;

        //水平居中
         $objPHPExcel->getActiveSheet(0)->getStyle('A1:'.$cellName[$cell_border_num].$row_border_num
            )->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

        //加边框
        $objPHPExcel->getActiveSheet(0)->getStyle('A1:'.$cellName[$cell_border_num].$row_border_num)->getBorders()->getAllBorders()->setBorderStyle(\PHPExcel_Style_Border::BORDER_THIN);
      
        //设置工作簿名称
        $objPHPExcel->getActiveSheet(0)->setTitle($expTitle);

        $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');

        $fileName = iconv('utf-8', 'gb2312', $fileName);//文件名称
        $fileName = "./data/excel/export/$fileName.xls";
        $objWriter->save($fileName);
        return $fileName;
        
    }

    public function exportExcel($fileName = 'export', $expTitle, $expCellName, $expTableData)
    {
        $xlsTitle = iconv('utf-8', 'gb2312', $expTitle);//文件名称
        $fileName = $fileName . date('_YmdHis');//or $xlsTitle 文件名称可根据自己情况设定
        $cellNum = count($expCellName);
        $dataNum = count($expTableData);
        vendor("PHPExcel.PHPExcel");
        $objPHPExcel = new \PHPExcel();
        $cellName = array('A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z', 'AA', 'AB', 'AC', 'AD', 'AE', 'AF', 'AG', 'AH', 'AI', 'AJ', 'AK', 'AL', 'AM', 'AN', 'AO', 'AP', 'AQ', 'AR', 'AS', 'AT', 'AU', 'AV', 'AW', 'AX', 'AY', 'AZ');

        /*$objPHPExcel->getActiveSheet(0)->mergeCells('A1:' . $cellName[$cellNum - 1] . '1');//合并单元格
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('A1', $expTitle . '  Export time:' . date('Y-m-d H:i:s'));*/
        for ($i = 0; $i < $cellNum; $i++) {
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue($cellName[$i] . '1', $expCellName[$i][0]);

            $objPHPExcel->setActiveSheetIndex(0)->setCellValue($cellName[$i] . '2', $expCellName[$i][1]);

            //设置单元格字体 第1，2行字体加粗
            $objPHPExcel->getActiveSheet(0)->getStyle($cellName[$i] . '1')->getFont()->setBold(true); 

            $objPHPExcel->getActiveSheet(0)->getStyle($cellName[$i] . '2')->getFont()->setBold(true); 
        }

        // Miscellaneous glyphs, UTF-8
        for ($i = 0; $i < $dataNum; $i++) {
            for ($j = 0; $j < $cellNum; $j++) {
                $objPHPExcel->getActiveSheet(0)->setCellValue($cellName[$j] . ($i + 3), $expTableData[$i][$expCellName[$j][0]]);
                //表的列任意宽度
                $objPHPExcel->getActiveSheet(0)->getColumnDimension($cellName[$j])->setAutoSize(true);
            }
        }
        //列
        $cell_border_num = (int)$cellNum - 1;
        //行
        $row_border_num = (int)$dataNum + 2;

        //水平居中
         $objPHPExcel->getActiveSheet(0)->getStyle('A1:'.$cellName[$cell_border_num].$row_border_num
            )->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        //加边框
        $objPHPExcel->getActiveSheet(0)->getStyle('A1:'.$cellName[$cell_border_num].$row_border_num)->getBorders()->getAllBorders()->setBorderStyle(\PHPExcel_Style_Border::BORDER_THIN);
      
        //设置工作簿名称
        $objPHPExcel->getActiveSheet(0)->setTitle($expTitle);

        //header('pragma:public');
        //header('Content-type:application/vnd.ms-excel;charset=utf-8;name="' . $xlsTitle . '.xls"');
        //header("Content-Disposition:attachment;filename=$fileName.xls");//attachment新窗口打印inline本窗口打印
        $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
        //$objWriter->save('php://output');

        $fileName = iconv('utf-8', 'gb2312', $fileName);//文件名称
        $fileName = "./data/excel/export/$fileName.xls";
        $objWriter->save($fileName);
        return $fileName;
        
    }

    public function exportExcelForCostcenter($fileName = 'export', $expTitle, $expCellName, $expTableData,$max_no_count)
    {
        $xlsTitle = iconv('utf-8', 'gb2312', $expTitle);//文件名称
        $fileName = $fileName . date('_YmdHis');//or $xlsTitle 文件名称可根据自己情况设定
        $cellNum = count($expCellName);
        $dataNum = count($expTableData);
        vendor("PHPExcel.PHPExcel");
        $objPHPExcel = new \PHPExcel();
        $cellName = array('A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z', 'AA', 'AB', 'AC', 'AD', 'AE', 'AF', 'AG', 'AH', 'AI', 'AJ', 'AK', 'AL', 'AM', 'AN', 'AO', 'AP', 'AQ', 'AR', 'AS', 'AT', 'AU', 'AV', 'AW', 'AX', 'AY', 'AZ');

        //为成本中心列名重新赋值
        for($i = 0; $i < $max_no_count;$i++){
            $expCellName[$i+$cellNum][0] = 'delivery_no'.($i+1);
            $expCellName[$i+$cellNum][1] = '账号'.($i+1);
        }
        $cellNum = $cellNum + $max_no_count;

        for ($i = 0; $i < $cellNum; $i++) {
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue($cellName[$i] . '1', $expCellName[$i][0]);
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue($cellName[$i] . '2', $expCellName[$i][1]);

            //设置单元格字体
            $objPHPExcel->getActiveSheet(0)->getStyle($cellName[$i] . '2')->getFont()->setBold(true); //字体加粗
           
            $objPHPExcel->getActiveSheet(0)->getStyle($cellName[$i] . '1')->getFont()->setBold(true); //字体加粗 
        }
        
        // Miscellaneous glyphs, UTF-8
        for ($i = 0; $i < $dataNum; $i++) {
            for ($j = 0; $j < $cellNum; $j++) {
                if($j < 6){
                    $objPHPExcel->getActiveSheet(0)->setCellValue($cellName[$j] . ($i + 3), $expTableData[$i][$expCellName[$j][0]]);
                }else{
                    $objPHPExcel->getActiveSheet(0)->setCellValue($cellName[$j] . ($i + 3), $expTableData[$i]['delivery_no'][$j-6]);
                }

                //表的列任意宽度
                $objPHPExcel->getActiveSheet(0)->getColumnDimension($cellName[$j])->setAutoSize(true);
            }
        }
        //列
        $cell_border_num = (int)$cellNum - 1;
        //行
        $row_border_num = (int)$dataNum + 2;

        //水平居中
         $objPHPExcel->getActiveSheet(0)->getStyle('A1:'.$cellName[$cell_border_num].$row_border_num
            )->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        //加边框
        $objPHPExcel->getActiveSheet(0)->getStyle('A1:'.$cellName[$cell_border_num].$row_border_num)->getBorders()->getAllBorders()->setBorderStyle(\PHPExcel_Style_Border::BORDER_THIN);
      
        //设置工作簿名称
        $objPHPExcel->getActiveSheet(0)->setTitle($expTitle);

        $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');

        $fileName = iconv('utf-8', 'gb2312', $fileName);//文件名称
        $fileName = "./data/excel/export/$fileName.xls";
        $objWriter->save($fileName);
        return $fileName;
        
    }

     /**
     * +----------------------------------------------------------
     * Export Excel | 2013.08.23
     * Author:HongPing <hongping626@qq.com>
     * +----------------------------------------------------------
     * @param $expTitle     string File name
     * +----------------------------------------------------------
     * @param $expCellName  array  Column name
     * +----------------------------------------------------------
     * @param $expTableData array  Table data
     * +----------------------------------------------------------
     */
    public function exportExcelForLabel($fileName = 'export', $expTitle, $expCellName, $expTableData)
    {
        $xlsTitle = iconv('utf-8', 'gb2312', $expTitle);//文件名称
        $fileName = $fileName . date('Ymd');//or $xlsTitle 文件名称可根据自己情况设定
        $cellNum = count($expCellName);
        $dataNum = count($expTableData);
        vendor("PHPExcel.PHPExcel");
        $objPHPExcel = new \PHPExcel();
        $cellName = array('A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z', 'AA', 'AB', 'AC', 'AD', 'AE', 'AF', 'AG', 'AH', 'AI', 'AJ', 'AK', 'AL', 'AM', 'AN', 'AO', 'AP', 'AQ', 'AR', 'AS', 'AT', 'AU', 'AV', 'AW', 'AX', 'AY', 'AZ');
        $objPHPExcel->getActiveSheet(0)->mergeCells('A1:'.$cellName[$cellNum-1].'1');//合并单元格
        $objPHPExcel->getActiveSheet()->getStyle('2')->getFont()->setBold(true);//加粗
        $objPHPExcel->getActiveSheet()->getStyle('2')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);//居中

        $objPHPExcel->getActiveSheet() -> getColumnDimension() -> setAutoSize(true);
        $objPHPExcel->setActiveSheetIndex(0)->getStyle()->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('A1', $expTitle.'         导出时间:'.date('Y-m-d,H:m:s'));
       // $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(100);
        for($i=0;$i<$cellNum;$i++){
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue($cellName[$i].'2', $expCellName[$i][1]);
        }
        // Miscellaneous glyphs, UTF-8
        for($i=0;$i<$dataNum;$i++){
            for($j=0;$j<$cellNum;$j++){
                $objPHPExcel->getActiveSheet(0)->setCellValue($cellName[$j].($i+3), $expTableData[$i][$expCellName[$j][0]]);
                $objPHPExcel->getActiveSheet()->getStyle($i+3)->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);//居中
            }
        }
        //设置边框
        $columnnum = $dataNum+2;
        $objPHPExcel->getActiveSheet()->getStyle('A1:'.$cellName[$cellNum-1].$columnnum)->getBorders()->getAllBorders()->setBorderStyle(\PHPExcel_Style_Border::BORDER_THIN);

        header('pragma:public');
        header('Content-type:application/vnd.ms-excel;charset=utf-8;name="' . $xlsTitle . '.xls"');
        header("Content-Disposition:attachment;filename=$fileName.xls");//attachment新窗口打印inline本窗口打印
        $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
        $objWriter->save('php://output');

    }
    /**
     * +----------------------------------------------------------
     * Import Excel | 2013.08.23
     * Author:HongPing <hongping626@qq.com>
     * +----------------------------------------------------------
     * @param  $file   upload file $_FILES
     * +----------------------------------------------------------
     * @return array   array("error","message")
     * +----------------------------------------------------------
     */
     public function importExecl($info)
    {
        /*if (!file_exists($file)) {
            return array("error" => 0, 'message' => 'file not found!');
        }*/
        //Vendor("PHPExcel.PHPExcel.IOFactory");
        if($info){
            $file_name='./data/'.$info['file']['savepath'].$info['file']['savename'];
            if(file_exists($file_name)) {
                vendor("PHPExcel.PHPExcel");
                $objReader = \PHPExcel_IOFactory::createReader('Excel5');
                //try {
                   $PHPReader = $objReader->load($file_name,$encode='utf-8');
                /*} catch (Exception $e) {
                }*/
                if (!isset($PHPReader)){
                    $error = L("READ_EXCEL_FAILURE");
                    //return array("error" => 0, 'message' => 'read error!');
                    return array("error" => $error);
                } 
                $allWorksheets = $PHPReader->getAllSheets();
                $i = 0;
                foreach ($allWorksheets as $objWorksheet) {
                    $sheetname = $objWorksheet->getTitle();
                    $allRow = $objWorksheet->getHighestRow();//how many rows
                    $highestColumn = $objWorksheet->getHighestColumn();//how many columns
                    $allColumn = \PHPExcel_Cell::columnIndexFromString($highestColumn);
                    $array[$i]["Title"] = $sheetname;
                    $array[$i]["Cols"] = $allColumn;
                    $array[$i]["Rows"] = $allRow;
                    $arr = array();
                    $isMergeCell = array();
                    foreach ($objWorksheet->getMergeCells() as $cells) {//merge cells
                        foreach (\PHPExcel_Cell::extractAllCellReferencesInRange($cells) as $cellReference) {
                            $isMergeCell[$cellReference] = true;
                        }
                    }
                    for ($currentRow = 1; $currentRow <= $allRow; $currentRow++) {
                        $row = array();
                        for ($currentColumn = 0; $currentColumn < $allColumn; $currentColumn++) {
                            ;
                            $cell = $objWorksheet->getCellByColumnAndRow($currentColumn, $currentRow);
                            $afCol = \PHPExcel_Cell::stringFromColumnIndex($currentColumn + 1);
                            $bfCol = \PHPExcel_Cell::stringFromColumnIndex($currentColumn - 1);
                            $col = \PHPExcel_Cell::stringFromColumnIndex($currentColumn);
                            $address = $col . $currentRow;
                            $value = $objWorksheet->getCell($address)->getValue();
                            if (substr($value, 0, 1) == '=') {
                                //return array("error" => 0, 'message' => 'can not use the formula!');
                                $error = L("CANNOT_USE_FORMULA");
                                return array("error" =>$error);
                                //exit;
                            }
                            if ($cell->getDataType() == \PHPExcel_Cell_DataType::TYPE_NUMERIC) {
                                $cellstyleformat = $cell->getWorksheet()->getStyle($cell->getCoordinate())->getNumberFormat();
                                $formatcode = $cellstyleformat->getFormatCode();
                                if (preg_match('/^([$[A-Z]*-[0-9A-F]*])*[hmsdy]/i', $formatcode)) {
                                    $value = gmdate("Y-m-d", \PHPExcel_Shared_Date::ExcelToPHP($value));
                                } else {
                                    $value = \PHPExcel_Style_NumberFormat::toFormattedString($value, $formatcode);
                                }
                            }
                            if ($isMergeCell[$col . $currentRow] && $isMergeCell[$afCol . $currentRow] && !empty($value)) {
                                $temp = $value;
                            } elseif ($isMergeCell[$col . $currentRow] && $isMergeCell[$col . ($currentRow - 1)] && empty($value)) {
                                $value = $arr[$currentRow - 1][$currentColumn];
                            } elseif ($isMergeCell[$col . $currentRow] && $isMergeCell[$bfCol . $currentRow] && empty($value)) {
                                $value = $temp;
                            }
                            $row[$currentColumn] = $value;
                        }
                        $arr[$currentRow] = $row;
                    }
                    $array[$i]["Content"] = $arr;
                    $i++;
                }
                spl_autoload_register(array('Think\Think', 'autoload'));//must, resolve ThinkPHP and PHPExcel conflicts
                unset($objWorksheet);
                unset($PHPReader);
                unset($PHPExcel);
                unlink($file_name);
                //return array("error" => 1, "data" => $array); 
                return array("data" => $array); 
            }else{
                $error = L("FILE_NOT_EXIST");
                return array("error" => $error); 
            } 
        }
             
    }
    public function importExeclForReconciliation($info)
    {
        /*if (!file_exists($file)) {
            return array("error" => 0, 'message' => 'file not found!');
        }*/
        //Vendor("PHPExcel.PHPExcel.IOFactory");
        if($info){
            $file_name='./data/'.$info['file']['savepath'].$info['file']['savename'];
            if(file_exists($file_name)) {
                vendor("PHPExcel.PHPExcel");
                //获取文件扩展名
                $info1 = pathinfo($file_name);
                $extension = $info1['extension'];
                if( $extension =='xlsx' )
                {
                 $objReader = \PHPExcel_IOFactory::createReader('Excel2007');
                }
                else
                {
                 $objReader = \PHPExcel_IOFactory::createReader('Excel5');
                }

                //try {
                   $PHPReader = $objReader->load($file_name,$encode='utf-8');
                /*} catch (Exception $e) {
                }*/
                if (!isset($PHPReader)){
                    $error = L("READ_EXCEL_FAILURE");
                    //return array("error" => 0, 'message' => 'read error!');
                    return array("error" => $error);
                } 
                $allWorksheets = $PHPReader->getAllSheets();
                $i = 0;
                foreach ($allWorksheets as $objWorksheet) {
                    $sheetname = $objWorksheet->getTitle();
                    $allRow = $objWorksheet->getHighestRow();//how many rows
                    $highestColumn = $objWorksheet->getHighestColumn();//how many columns
                    $allColumn = \PHPExcel_Cell::columnIndexFromString($highestColumn);
                    $array[$i]["Title"] = $sheetname;
                    $array[$i]["Cols"] = $allColumn;
                    $array[$i]["Rows"] = $allRow;
                    $arr = array();
                    $isMergeCell = array();
                    foreach ($objWorksheet->getMergeCells() as $cells) {//merge cells
                        foreach (\PHPExcel_Cell::extractAllCellReferencesInRange($cells) as $cellReference) {
                            $isMergeCell[$cellReference] = true;
                        }
                    }
                    for ($currentRow = 1; $currentRow <= $allRow; $currentRow++) {
                        $row = array();
                        $status = 0;
                        for ($currentColumn = 0; $currentColumn < $allColumn; $currentColumn++) {
                            ;
                            $cell = $objWorksheet->getCellByColumnAndRow($currentColumn, $currentRow);
                            $afCol = \PHPExcel_Cell::stringFromColumnIndex($currentColumn + 1);
                            $bfCol = \PHPExcel_Cell::stringFromColumnIndex($currentColumn - 1);
                            $col = \PHPExcel_Cell::stringFromColumnIndex($currentColumn);
                            $address = $col . $currentRow;
                            $value = $objWorksheet->getCell($address)->getValue();
                            if (substr($value, 0, 1) == '=') {
                                /*$error = L("CANNOT_USE_FORMULA");
                                return array("error" =>$error);*/
                                $value = $objWorksheet->getCell($address)->getCalculatedValue();
                                //exit;
                            }
                            if ($cell->getDataType() == \PHPExcel_Cell_DataType::TYPE_NUMERIC) {
                                $cellstyleformat = $cell->getWorksheet()->getStyle($cell->getCoordinate())->getNumberFormat();
                                $formatcode = $cellstyleformat->getFormatCode();
                                if (preg_match('/^([$[A-Z]*-[0-9A-F]*])*[hmsdy]/i', $formatcode)) {
                                    $value = gmdate("Y-m-d", \PHPExcel_Shared_Date::ExcelToPHP($value));
                                } else {
                                    $value = \PHPExcel_Style_NumberFormat::toFormattedString($value, $formatcode);
                                }
                            }
                            if ($isMergeCell[$col . $currentRow] && $isMergeCell[$afCol . $currentRow] && !empty($value)) {
                                $temp = $value;
                            } elseif ($isMergeCell[$col . $currentRow] && $isMergeCell[$col . ($currentRow - 1)] && empty($value)) {
                                $value = $arr[$currentRow - 1][$currentColumn];
                            } elseif ($isMergeCell[$col . $currentRow] && $isMergeCell[$bfCol . $currentRow] && empty($value)) {
                                $value = $temp;
                            }
                            if($value){
                                $status = 1;
                            }
                            $row[$currentColumn] = $value;
                        }
                        //去除空行
                        if($status == 0){
                            unset($row);
                        }else{
                            //$arr[$currentRow] = $row;
                            $arr[] = $row;
                        }   
                    }
                    $array[$i]["Rows"] = count($arr);
                    $array[$i]["Content"] = $arr;
                    $i++;
                }

                spl_autoload_register(array('Think\Think', 'autoload'));//must, resolve ThinkPHP and PHPExcel conflicts
                unset($objWorksheet);
                unset($PHPReader);
                unset($PHPExcel);
                unlink($file_name);
                //return array("error" => 1, "data" => $array); 
                return array("data" => $array); 
            }else{
                $error = L("FILE_NOT_EXIST");
                return array("error" => $error); 
            } 
        }
             
    }
}