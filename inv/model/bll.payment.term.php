<?php
namespace NsINV;

class ClsBllPaymentTerm extends \NsFWK\ClsBll{
    public function __construct(){
        $this->_strClsDalLoad = '\NsINV\ClsDalPaymentterm';
        $this->_strClsDalSave = '\NsINV\ClsDalPaymentterm';
        $this->_data = array(
            'intID'=>-1,
            'strName'=>'',
            'strDescription'=>'',

            'boolIsReserved'=>false,
        );
        parent::__construct(func_get_args());
    }

    public function __get($name){
        switch($name){
        }
        return parent::__get($name);
    }

    protected function _save(\ADODB_Active_Record $objDAL){
        if($this->getIsLoaded()){
            $rslt = $objDAL->Load('pkPaymentTermID = ?',array($this->_data['intID']));
            if(!$rslt){
                return 'Could not load object!';
            }
        }
        $objDAL->fldPaymentTermName = $this->_data['strName'];
        $objDAL->fldDescription = $this->_data['strDescription'];

        $rslt = $objDAL->Save();
        if($rslt){
            $this->_data['intID'] = $objDAL->pkPaymentTermID;
        }
        return $rslt;
    }

    protected function _delete(\ADODB_Active_Record $objDAL){
        $DB = &\ADODB_Connection_Manager::GetConnection('customer');
        return $DB->Execute('DELETE FROM inv_payment_term WHERE pkPaymentTermID = ? ', array($this->_data['intID']));
    }

    protected function _load(\ADODB_Active_Record $objDAL){
        $this->_data['intID'] = $objDAL->pkPaymentTermID;
        $this->_data['strName'] = $objDAL->fldPaymentTermName;
        $this->_data['strDescription'] = $objDAL->fldDescription;
    }


    public function LoadByID($intID){
        $objFilter = new \NsFWK\ClsFilter();
        $objFilter->intID = "pkPaymentTermID = $intID";
        $rslt = $this->Load($objFilter);
        if(!$rslt){
            return false;
        }
        $this->_data["boolIsReserved"] = $this->IsReservedByInvoice();
        return $rslt; 
    }

    protected function IsReservedByInvoice(){
        $intID = $this->intID;
        $objInvoice = new ClsBllInvoice();
        $objFilter = new \NsFWK\ClsFilter();
        $objFilter->intID = "fkPaymentTermID = $intID";
        $arr = $objInvoice->GetData($objFilter);  
        if(empty($arr)){
            return false;
        }
        return true;
    }
    public function GetAllPaymentTerms(){
        $objFilter = new \NsFWK\ClsFilter();
        $arrData = $this->GetDataAssociative($objFilter);
        return $arrData;
    }

    protected function loadBindingCustomParams(&$arrPaymentTerm){
        if(empty($arrPaymentTerm)){
            return;
        }
        $arrReserved =ClsBllPaymentTerm::GetAllReservedID();
        foreach ($arrPaymentTerm as &$arrItem) {
            if(in_array($arrItem['intID'] ,$arrReserved)){
                $arrItem['boolIsReserved'] = true; 
            }
        }
    }

    public function GetDataPageAssociative($intPageNo, $intPageSize, $strWhere, $strOrder, &$intPageCount, &$intRowCount){
        $arrData = parent::GetDataPageAssociative($intPageNo, $intPageSize, $strWhere, $strOrder, $intPageCount, $intRowCount);
        if (!empty($arrData)) {
            $this->loadBindingCustomParams($arrData);
        }
        return $arrData;
    }

    static public function GetAllReservedID(){
        $DB = &\ADODB_Connection_Manager::GetConnection('customer');
        $strSQL = "SELECT DISTINCT fkPaymentTermID FROM inv_invoice ";
        $arr = $DB->GetArray($strSQL);
        $arrID = array();
        foreach($arr as $obj){
            $arrID[] = $obj['fkPaymentTermID'];
        }
        return $arrID;
    }
}