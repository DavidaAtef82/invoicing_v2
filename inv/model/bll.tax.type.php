<?php
namespace NsINV;

class ClsBllTaxType extends \NsFWK\ClsBll{
    public function __construct(){
        $this->_strClsDalLoad = '\NsINV\ClsDalTax';
        $this->_strClsDalSave = '\NsINV\ClsDalTax';
        $this->_data = array(
            'intID'=>-1,
            'strTaxType'=>'',
            'strType'=>'',
            'intValue'=>-1
        );
        parent::__construct(func_get_args());
    }

    public function __set($name, $value){
        switch ($name) {
            case 'intID':
                // Read only attributes
                return false;
            case 'strTaxType':
                // Allow edits only if tax type is used 
                if ($this->IsReservedByInvoice()) {
                    return false;
                }
                break;
        }
        return parent::__set($name, $value);
    }

    public function __get($name){
           switch($name){
           }
     return parent::__get($name);
    }

    protected function _save(\ADODB_Active_Record $objDAL){
        if($this->getIsLoaded()){
            $rslt = $objDAL->Load('pkTaxTypeID = ?',array($this->_data['intID']));
            if(!$rslt){
                return 'Could not load object!';
            }
        }

        $objDAL->fldTaxType = $this->_data['strTaxType'];
        $objDAL->fldType = $this->_data['strType'];//$temp;
        $objDAL->fldValue = $this->_data['intValue'];
        $rslt = $objDAL->Save();
        if($rslt){
            $this->_data['intID'] = $objDAL->pkTaxTypeID;
        }
        return $rslt;
    }

    protected function _delete(\ADODB_Active_Record $objDAL){
        $DB = &\ADODB_Connection_Manager::GetConnection('customer');
        return $DB->Execute('DELETE FROM inv_tax_type WHERE pkTaxTypeID = ? ', array($this->_data['intID']));
    }

    protected function _load(\ADODB_Active_Record $objDAL){
        $this->_data['intID'] = $objDAL->pkTaxTypeID;
        $this->_data['strTaxType'] = $objDAL->fldTaxType;
        $this->_data['strType'] = $objDAL->fldType; 
        $this->_data['intValue'] =$objDAL->fldValue;
    }

    protected function IsReservedByInvoice(){
        $intID = $this->intID;
        $objInvoiceRow = new ClsBllInvoiceRow();
        $objFilter = new \NsFWK\ClsFilter();
        $objFilter->intID = "fkTaxTypeID = $intID";
        $arr = $objInvoiceRow->GetData($objFilter);  
        if(empty($arr)){
            return false;
        }
        return true;
    }

    public function IsExist($strTaxType){
        $objFilter = new \NsFWK\ClsFilter();
        $objFilter->$strTaxType = "fldTaxType LIKE '$strTaxType'";
        $rslt = $this->GetData($objFilter);
        if(empty($rslt)){
            return false;
        }
        return true;
    }

    public function LoadByID($intID){
        $objFilter = new \NsFWK\ClsFilter();
        $objFilter->intID = "pkTaxTypeID = $intID";
        return $this->Load($objFilter);
    }

    public function GetTaxes(){
        $objFilter = new \NsFWK\ClsFilter();
        $strWhere = $objFilter->GetWhereStatement();
        $arrData = $this->GetDataAssociative($objFilter);
        return $arrData;
    }

}