<?php
class Loan_TransfercashController extends Zend_Controller_Action {
    public function init()
    {    	
    	header('content-type: text/html; charset=utf8');
    	defined('BASE_URL')	|| define('BASE_URL', Zend_Controller_Front::getInstance()->getBaseUrl());
	}
	public function indexAction(){
		try{
		    if($this->getRequest()->isPost()){
 				$search = $this->getRequest()->getPost();
 				
 			}else{
				$search = array(
						'txt_search'=>'',
						'client_name'=> -1,
						'branch_id' => -1,
						'status' => -1,
						'start_date'=> date('Y-m-d'),
						'end_date'=>date('Y-m-d'),
						 );
			}
			$db = new Loan_Model_DbTable_Dbtransfercash();
			$rs_rows= $db->getAllChangeHouse($search,1);
			$glClass = new Application_Model_GlobalClass();
			$rs_rows = $glClass->getImgActive($rs_rows, BASE_URL, true);
			$list = new Application_Form_Frmtable();
			$collumns = array("BRANCH_NAME","CUSTOMER_NAME","PROPERTY_CODE","TRANSFER_AMOUNT","BRANCH_NAME","PROPERTY_CODE","TRANSFER_DATE",
				"STATUS");
			$link_info=array('module'=>'loan','controller'=>'transfercash','action'=>'edit',);
			$this->view->list=$list->getCheckList(0, $collumns, $rs_rows,array(),0);
		}catch (Exception $e){
			Application_Form_FrmMessage::message("Application Error");
			Application_Model_DbTable_DbUserLog::writeMessageError($e->getMessage());
		}	
		$frm = new Loan_Form_FrmSearchLoan();
		$frm = $frm->AdvanceSearch();
		Application_Model_Decorator::removeAllDecorator($frm);
		$this->view->frm_search = $frm;
  }
  function addAction()
  {
		if($this->getRequest()->isPost()){
			$_data = $this->getRequest()->getPost();
			try {
				// Check Session Expire
				$dbgb = new Application_Model_DbTable_DbGlobal();
				$checkses = $dbgb->checkSessionExpire();
				if (empty($checkses)){
					$dbgb->reloadPageExpireSession();
					exit();
				}
				
				$_dbmodel = new Loan_Model_DbTable_Dbtransfercash();
				$_dbmodel->addTransfercash($_data);
				if(!empty($_data['saveclose'])){
					Application_Form_FrmMessage::Sucessfull("INSERT_SUCCESS","/loan/transfercash");
				}else{
				Application_Form_FrmMessage::Sucessfull("INSERT_SUCCESS","/loan/transfercash/add");
				}
			}catch (Exception $e) {
				Application_Form_FrmMessage::message("INSERT_FAIL");
				$err =$e->getMessage();
				Application_Model_DbTable_DbUserLog::writeMessageError($err);
			}
		}
		$frm = new Loan_Form_FrmTransferproject();
		$frm_loan=$frm->FrmTransferProject();
		Application_Model_Decorator::removeAllDecorator($frm_loan);
		$this->view->frm_loan = $frm_loan;
        $db = new Application_Model_DbTable_DbGlobal();
	}	
	public function editAction(){
		$_dbmodel = new Loan_Model_DbTable_Dbchangehouse();
		
		if($this->getRequest()->isPost()){
			$_data = $this->getRequest()->getPost();
			try {
				$_dbmodel->UpdateChangeHouse($_data);
				if(!empty($_data['saveclose'])){
					Application_Form_FrmMessage::Sucessfull("UPDATE_SUCCESS","/loan/changproperty");
				}else{
					Application_Form_FrmMessage::message("INSERT_FAIL");
				}
			}catch (Exception $e) {
				Application_Form_FrmMessage::message("INSERT_FAIL");
				$err =$e->getMessage();
				Application_Model_DbTable_DbUserLog::writeMessageError($err);
			}
		}
		$id = $this->getRequest()->getParam('id');
		$rs = $_dbmodel->getTransferProject($id);
		if(empty($rs)){
			Application_Form_FrmMessage::Sucessfull("RECORD_NOTFUND","/loan/changproperty",2);
			exit();
		}
		$this->view->rs = $rs;
		
		$frm = new Loan_Form_FrmTransferproject();
		$frm_loan=$frm->FrmTransferProject();
		Application_Model_Decorator::removeAllDecorator($frm_loan);
		$this->view->frm_loan = $frm_loan;
        $db = new Application_Model_DbTable_DbGlobal();
        
	}
	
	function getalllandAction(){
		if($this->getRequest()->isPost()){
			$data = $this->getRequest()->getPost();
			$db = new Application_Model_DbTable_DbGlobal();
			$action = (!empty($data['action'])?$data['action']:null);
			$row = $db->getAllLandInfo($data['branch_id'],1,$action);
			print_r(Zend_Json::encode($row));
			exit();
		}
	}
	
}

