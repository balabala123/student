<?php
namespace Common\Model;
use Common\Model\CommonModel;
class HelpModel extends CommonModel {

    /**
     * 自动验证
     *
     */
    protected $_validate = array(
        //array(验证字段,验证规则,错误提示,验证条件,附加规则,验证时间)
        array('type_name', '请选择', '请选择助学金类型！', 1, 'notequal', CommonModel:: MODEL_INSERT ),
        array('sy_note', '', '请填写家庭状况！', 1, 'notequal', CommonModel:: MODEL_INSERT ),
    );



}