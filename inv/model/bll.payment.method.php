<?php
namespace NsINV;

class ClsBllPaymentMethod extends \NsFWK\ClsBll{
    public function __construct(){
        $this->_strClsDalLoad = '\NsINV\ClsDalPaymentMethod';
        $this->_strClsDalSave = '\NsINV\ClsDalPaymentMethod';
        $this->_data = array(
            'intID'=>-1,
            'strType'=>'',
            'boolManual'=>-1,
            'boolDisabled'=>-1,
            'strDetails'=>''
        );
        @parent::__construct(func_get_args());
    }

    public function __get($name){

    }

    protected function _save(\ADODB_Active_Record $objDAL){
        if($this->getIsLoaded()){
            $rslt = $objDAL->Load('pkPaymentMethodID = ?',array($this->_data['intID']));
            if(!$rslt){
                return 'Could not load object!';
            }
        }
        $objDAL->fldPaymentMethod = $this->_data['strType'];
        $objDAL->fldIsManual = $this->_data['boolManual']; 
        $objDAL->fldDisabled = $this->_data['boolDisabled']; 
        $objDAL->fldPaymentMethodDetails = $this->_data['strDetails']; 
        $rslt = $objDAL->Save();

        if($rslt){
            $this->_data['intID'] = $objDAL->pkPaymentMethodID;
        }
        return $rslt;
    }

    protected function _delete(\ADODB_Active_Record $objDAL){
        $DB = &\ADODB_Connection_Manager::GetConnection('customer');
        return $DB->Execute('DELETE FROM inv_payment_method WHERE pkPaymentMethodID = ? ', array($this->_data['intID']));
    }

    protected function _load(\ADODB_Active_Record $objDAL){
        $this->_data['intID'] = $objDAL->pkPaymentMethodID;
        $this->_data['strType'] = $objDAL->fldPaymentMethod;
        $this->_data['boolManual'] = $objDAL->fldIsManual;
        $this->_data['boolDisabled'] = $objDAL->fldDisabled;
        $this->_data['strDetails'] = $objDAL->fldPaymentMethodDetails;
    }

    public function LoadByID($intID){
        $objFilter = new \NsFWK\ClsFilter();
        $objFilter->intID = "pkPaymentMethodID = $intID"; 
        return $this->Load($objFilter);
    }

    public function GetPayments($bolManual){
        $objFilter = new \NsFWK\ClsFilter();
        if($bolManual){
            $objFilter->bolManual = "fldIsManual = $bolManual";  
        }
        $arrData = $this->GetDataAssociative($objFilter);
        return $arrData;
    }
}