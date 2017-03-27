<?php
namespace Common\Controller;

use Common\Controller\HomebaseController;

class MemberbaseController extends HomebaseController{
    
	protected $user_model;
	protected $user;
	protected $userid;
	
	function _initialize() {
		parent::_initialize();
		if(sp_is_user_login()){
			$this->userid=sp_get_current_userid();
			$this->users_model=D("Common/Users");
			$this->user=$this->users_model->where(array("id"=>$this->userid))->find();
		}else{
			redirect(__ROOT__."/index.php?g=user&m=login");
		}
	}
	
}