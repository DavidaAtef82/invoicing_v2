<?php
namespace NsINV;

class ClsBllInvoiceRow extends \NsFWK\ClsBll{
    public function __construct(){
        $this->_strClsDalLoad = '\NsINV\ClsDalInvoiceRow';
        $this->_strClsDalSave = '\NsINV\ClsDalInvoiceRow';
        $this->_data = array(
            'intID'=>-1,
            'intInvoiceID'=>-1,
            'intItemID'=>-1,
            'intTaxTypeID'=>-1,
            'strType'=>'',
            'intQuantity'=>-1,
            'intUnitPrice'=>-1,
            'decOrder'=>-1,
            'strDescription'=>'',
        );
        parent::__construct(func_get_args());
    }

    public function __get($name){
        switch($name){
            case 'objTaxType':
                if(!isset($this->_data[$name])){
                    $obj = new \NsINV\ClsBllTaxType();
                    $obj->LoadByID($this->_data['intTaxTypeID']);
                    $this->_data[$name] = $obj;
                }
                break;
        }
        return parent::__get($name);
    }

    protected function _save(\ADODB_Active_Record $objDAL){
        return true;
    }

    protected function _delete(\ADODB_Active_Record $objDAL){
        return true;
    }

    protected function _load(\ADODB_Active_Record $objDAL){
        $this->_data['intID'] = $objDAL->pkInvoiceRowID;
        $this->_data['intInvoiceID'] = $objDAL->fkInvoiceID;
        $this->_data['intItemID'] = $objDAL->fkItemID;
        $this->_data['intTaxTypeID'] = $objDAL->fkTaxTypeID;
        $this->_data['strType'] = $objDAL->fldType;
        $this->_data['intQuantity'] = $objDAL->fldQuantity;
        $this->_data['intUnitPrice'] = $objDAL->fldUnitPrice;
        $this->_data['decOrder'] = $objDAL->fldOrder;
        $this->_data['strDescription'] = $objDAL->fldDescription;
    }

    public function LoadByID($intID){
        $objFilter = new \NsFWK\ClsFilter();
        $objFilter->intID = "pkInvoiceRowID = $intID";
        return $this->Load($objFilter);
    }

    public function GetByInvoiceID($intInvoiceID){
        $objFilter = new \NsFWK\ClsFilter();
        $objFilter->intInvoiceID = "fkInvoiceID = $intInvoiceID";
        return $this->GetDataAssociative($objFilter, "fldOrder ASC");
    }

    public function GetDataAssociative(\NsFWK\ClsFilter $objFilter, $strOrder = '', $strGroup = '', $intOffset = false, $intCount = false){
        $arrData = parent::GetDataAssociative($objFilter ,$strOrder);

        if (!empty($arrData)) {
            $this->loadBindingAssociative($arrData, 'intTaxTypeID', 'objTaxType', new ClsBllTaxType(), 'pkTaxTypeID', 'intID');
            $this->loadBindingAssociative($arrData, 'intItemID', 'objCatalogue', new ClsBllCatalogue(), 'pkItemID', 'intID');
        }
        return $arrData;
    }

    static public function GetInvoicesAmounts($arrInvoiceID){ 

        if(empty($arrInvoiceID)){
            return array();
        }

        $strInvoiceID = implode(',', $arrInvoiceID);

        $DB = &\ADODB_Connection_Manager::GetConnection('customer');
        $strSQL = "SELECT fkInvoiceID as intInvoiceID,  
                    SUM(CASE WHEN fldType = 'LNG_ENUM_ITEM' THEN fldQuantity ELSE 0 END) as intItemCount, 
                    SUM(CASE WHEN fldType = 'LNG_ENUM_ITEM' THEN fldUnitPrice*fldQuantity ELSE 0 END) as decTotalAmount,
                    SUM(CASE WHEN fldType = 'LNG_ENUM_DISCOUNT' THEN fldUnitPrice*fldQuantity ELSE 0 END) as decTotalDiscount, 
                    SUM(CASE WHEN fldType = 'LNG_ENUM_TAX' THEN fldUnitPrice*fldQuantity ELSE 0 END) as decTotalTax
                    FROM inv_invoice_row 
                    WHERE fkInvoiceID in($strInvoiceID)
                    GROUP BY fkInvoiceID";

        $arrTotal = $DB->GetAssoc($strSQL);

        if (empty($arrTotal)) {
            return array();
        }    
        foreach ($arrTotal as &$arr) { 
            $arr['decNetAmount'] = $arr['decTotalAmount'] - $arr['decTotalDiscount'];
            $arr['decGrossAmount'] =  $arr['decNetAmount'] +  $arr['decTotalTax'];
        }
        return $arrTotal;
    } 

}
 