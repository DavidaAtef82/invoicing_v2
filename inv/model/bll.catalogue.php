<?php
namespace NsINV;

class ClsBllCatalogue extends \NsFWK\ClsBll{
    public function __construct(){
        $this->_strClsDalLoad = '\NsINV\ClsDalCatalogue';
        $this->_strClsDalSave = '\NsINV\ClsDalCatalogue';
        $this->_data = array(
            'intID'=>-1,
            'strName'=>'',
            'strDescription'=>'',
            'floatPrice'=>-1,
            'strCode'=>'',
            'intTaxTypeID'=>-1
        );
        parent::__construct(func_get_args());
    }

    public function __set($name, $value){
        switch ($name) {
            case 'intID':
                // Read only attributes
                return false;
            case 'strName':
            case 'strCode':
                // Allow edits only if catalogue is not used at any invoice
                if ($this->IsReservedByInvoice()) {
                    return false;
                }
                break;
        }
        return parent::__set($name, $value);
    }
    
    public function __get($name){
        switch($name){
            case 'objTaxType':
                if(!isset($this->_data['objTaxType'])){
                    $obj = new \NsINV\ClsBllTaxType();
                    $obj->LoadByID($this->_data['intTaxTypeID']);
                    $this->_data['objTaxType'] = $obj;
                }
                break;
        }
        return parent::__get($name);
    }

    protected function _save(\ADODB_Active_Record $objDAL){
        if($this->getIsLoaded()){
            $rslt = $objDAL->Load('pkItemID = ?',array($this->_data['intID']));
            if(!$rslt){
                return 'Could not load object!';
            }
        }
        $objDAL->fldName = $this->_data['strName'];
        $objDAL->fldDescription = $this->_data['strDescription'];
        $objDAL->fldPrice = (float)$this->_data['floatPrice'];
        $objDAL->fldCode = $this->_data['strCode'];
        $objDAL->fkTaxTypeID = $this->_data['intTaxTypeID'];

        $rslt = $objDAL->Save();
        if($rslt){
            $this->_data['intID'] = $objDAL->pkItemID;
        }
        return $rslt;
    }

    protected function _delete(\ADODB_Active_Record $objDAL){
        $DB = &\ADODB_Connection_Manager::GetConnection('customer');
        return $DB->Execute('DELETE FROM inv_catalogue WHERE pkItemID = ? ', array($this->_data['intID']));
    }

    protected function _load(\ADODB_Active_Record $objDAL){
        $this->_data['intID'] = $objDAL->pkItemID;
        $this->_data['strName'] = $objDAL->fldName;
        $this->_data['strDescription'] = $objDAL->fldDescription;
        $this->_data['floatPrice'] = $objDAL->fldPrice;
        $this->_data['strCode'] = $objDAL->fldCode;
        $this->_data['intTaxTypeID'] = $objDAL->fkTaxTypeID;
    }

    protected function IsReservedByInvoice(){
        $intID = $this->intID;
        $objInvoiceRow = new ClsBllInvoiceRow();
        $objFilter = new \NsFWK\ClsFilter();
        $objFilter->intID = "fkItemID = $intID";
        $arr = $objInvoiceRow->GetData($objFilter);  
        if(empty($arr)){
            return false;
        }
        return true;
    }

    public function IsExist($strCode , $strName){
        $objFilter = new \NsFWK\ClsFilter();
        $objFilter->strCodeName = "fldCode LIKE '$strCode' OR fldName LIKE '$strName' ";
        $rslt = $this->GetData($objFilter);
        if(empty($rslt)){
            return false;
        }
        return true;
    }

    public function LoadByID($intID){
        $objFilter = new \NsFWK\ClsFilter();
        $objFilter->intID = "pkItemID = $intID";
        return $this->Load($objFilter);
    }
    public function GetAllCatalogues(){
        $objFilter = new \NsFWK\ClsFilter();
        $arrData = $this->GetDataAssociative($objFilter);
        return $arrData;
    }

    public function GetDataAssociative(\NsFWK\ClsFilter $objFilter, $strOrder = '', $strGroup = '', $intOffset = false, $intCount = false){
        $arrData =  parent::GetDataAssociative($objFilter);
        if (isset($arrData)){
            $this->loadBindingAssociative($arrData, 'intTaxTypeID', 'objTaxType', new ClsBllTaxType(), 'pkTaxTypeID', 'intID');
            return $arrData;
        }else{
            return null;
        }
    }

    public function GetDataPageAssociative($intPageNo, $intPageSize, $strWhere, $strOrder, &$intPageCount, &$intRowCount){
        $arrData = parent::GetDataPageAssociative($intPageNo, $intPageSize, $strWhere, $strOrder, $intPageCount, $intRowCount);
        if (!empty($arrData)) {
            $this->loadBindingAssociative($arrData, 'intTaxTypeID', 'objTaxType', new ClsBllTaxType(), 'pkTaxTypeID', 'intID');
            return $arrData;
        } 
        return $arrData;
    }






}