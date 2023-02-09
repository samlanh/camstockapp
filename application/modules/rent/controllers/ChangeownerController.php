<?php
class Rent_ChangeownerController extends Zend_Controller_Action {
    public function init()
    {    	
    	header('content-type: text/html; charset=utf8');
    	defined('BASE_URL')	|| define('BASE_URL', Zend_Controller_Front::getInstance()->getBaseUrl());
	}
	public function indexAction(){
		try{
		    if($this->getRequest()->isPost()){
 				$search = $this->getRequest()->getPost();
 			}
			else{
				$search = array(
						'adv_search'=>'',
						'status'=> -1,
						'start_date'=> date('Y-m-d'),
						'end_date'=>date('Y-m-d'),
						 );
			}
			$db = new Rent_Model_DbTable_DbChangeOwner();
			$rs_rows= $db->getAllChangeOwner($search);
			$list = new Application_Form_Frmtable();
			$collumns = array("BRANCH_NAME","PROPERTY","FROM_CUSTOMER","TO_CUSTOMER","CHANGE_DATE","BY_USER","STATUS");
			$link=array('module'=>'rent','controller'=>'changeowner','action'=>'edit',);
			$this->view->list=$list->getCheckList(0, $collumns, $rs_rows,array('branch_name'=>$link,'property'=>$link,'from_customer'=>$link),0);
		}catch (Exception $e){
			Application_Form_FrmMessage::message("Application Error");
			Application_Model_DbTable_DbUserLog::writeMessageError($e->getMessage());
		}	
		$frm_search = new Rent_Form_FrmChangeOwner();
		$frm = $frm_search->FrmSearchChangeOwner();
		Application_Model_Decorator::removeAllDecorator($frm);
		$this->view->frm_search = $frm;
  }
  function addAction()
  {
  		$_dbmodel = new Rent_Model_DbTable_DbChangeOwner();
		if($this->getRequest()->isPost()){
			// Check Session Expire
			$dbgb = new Application_Model_DbTable_DbGlobal();
			$checkses = $dbgb->checkSessionExpire();
			if (empty($checkses)){
				$dbgb->reloadPageExpireSession();
				exit();
			}
		   $_data = $this->getRequest()->getPost();
		   try {
				
				$_dbmodel->addChangeOwner($_data);
				if(!empty($_data['saveclose'])){
					Application_Form_FrmMessage::Sucessfull("INSERT_SUCCESS","/rent/changeowner");
				}else{
					Application_Form_FrmMessage::Sucessfull("INSERT_SUCCESS","/rent/changeowner/add");
				}
			}catch (Exception $e) {
				Application_Form_FrmMessage::message("INSERT_FAIL");
				Application_Model_DbTable_DbUserLog::writeMessageError($e->getMessage());
			}
		}
		
		$frm = new Rent_Form_FrmChangeOwner();
		$frm_loan=$frm->FrmAddChangeOwner();
		Application_Model_Decorator::removeAllDecorator($frm_loan);
		$this->view->frm = $frm_loan;
		
		
	}
	function editAction()
	{
		$_dbmodel = new Rent_Model_DbTable_DbChangeOwner();
		if($this->getRequest()->isPost()){
			// Check Session Expire
			$dbgb = new Application_Model_DbTable_DbGlobal();
			$checkses = $dbgb->checkSessionExpire();
			if (empty($checkses)){
				$dbgb->reloadPageExpireSession();
				exit();
			}
			$_data = $this->getRequest()->getPost();
			try {
				
				$_dbmodel->updateChangeOwner($_data);
				Application_Form_FrmMessage::Sucessfull("EDIT_SUCCESS","/rent/changeowner");
			}catch (Exception $e) {
				Application_Form_FrmMessage::message("EDIT_FAILE");
				Application_Model_DbTable_DbUserLog::writeMessageError($e->getMessage());
			}
		}
		$id = $this->getRequest()->getParam("id");
		$id = empty($id)?0:$id;
		$row = $_dbmodel->getChangeOwnerById($id);
		if(empty($row)){
			Application_Form_FrmMessage::Sucessfull("NO_RECORD","/rent/changeowner",2);
			exit();
		}
		$this->view->row =$row;
		
		$frm = new Rent_Form_FrmChangeOwner();
		$frm_loan=$frm->FrmAddChangeOwner($row);
		Application_Model_Decorator::removeAllDecorator($frm_loan);
		$this->view->frm = $frm_loan;
	
	
	}	
}