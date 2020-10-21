<?php
namespace NsINV;

class ClsBllStatus extends \NsFWK\ClsBll{
    const STATUS_DRAFT = 1;
    const STATUS_SENT = 2;
    const STATUS_PARTIAL = 4;
    const STATUS_PAID = 5;
    const STATUS_WRITE_OFF = 6;
    const STATUS_VOID = 7;
    
    public function __construct(){
        $this->_strClsDalLoad = '\NsINV\ClsDalStatus';
        $this->_strClsDalSave = '\NsINV\ClsDalStatus';
        $this->_data = array(
            'intID'=>-1,
            'strStatus'=>'',
            'strNextStatus'=>'',
            'intEditable'=>-1,
            'intAcceptPayment'=>-1,
            'intFinalStatus'=>-1,
        );
        @parent::__construct(func_get_args());
    }

    public function __get($name){
        switch($name){
        }
        return parent::__get($name);
    }

    protected function _save(\ADODB_Active_Record $objDAL){
        if($this->getIsLoaded()){
            // UPDATE
            $rslt = $objDAL->Load('pkStatusID = ?',array($this->_data['intID']));
            if(!$rslt){
                return 'Could not load object!';
            }
        }

        $objDAL->fldStatus = $this->_data['strStatus'];
        $objDAL->fldNextStatus = $this->_data['strNextStatus'];
        $objDAL->fldEditable = $this->_data['intEditable'];
        $objDAL->fldAcceptPayment = $this->_data['intAcceptPayment'];
        $objDAL->fldFinalStatus = $this->_data['intFinalStatus'];  
        
        $rslt = $objDAL->Save();

        if($rslt){
            $this->_data['intID'] = $objDAL->pkStatusID;
        }
        return $rslt;
    }

    protected function _delete(\ADODB_Active_Record $objDAL){
        $DB = &\ADODB_Connection_Manager::GetConnection('customer');
        return $DB->Execute('DELETE FROM inv_invoice_status WHERE pkStatusID = ? ', array($this->_data['intID']));
    }

    protected function _load(\ADODB_Active_Record $objDAL){
        $this->_data['intID'] = $objDAL->pkStatusID;
        $this->_data['strStatus'] = $objDAL->fldStatus;
        $this->_data['strNextStatus'] = $objDAL->fldNextStatus;
        $this->_data['intEditable'] = $objDAL->fldEditable;
        $this->_data['intAcceptPayment'] = $objDAL->fldAcceptPayment;
        $this->_data['intFinalStatus'] = $objDAL->fldFinalStatus;
    }
    
    public function LoadByID($intID){
        $objFilter = new \NsFWK\ClsFilter();
        $objFilter->intID = "pkStatusID = $intID";
        return $this->Load($objFilter);
    }
    
    public function GetAllStatus(){
        $objFilter = new \NsFWK\ClsFilter();
        $strWhere = $objFilter->GetWhereStatement();
        $arrData = $this->GetDataAssociative($objFilter);
        return $arrData;
    }

}