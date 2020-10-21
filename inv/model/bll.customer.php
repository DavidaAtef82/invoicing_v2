<?php
namespace NsINV;

class ClsBllCustomer extends \NsFWK\ClsBll{

    public function __set($name, $value) {
        switch ($name) {
            case 'intID':
            case 'strEncryptedID':
                return false;
                break;
        }
        
        return parent::__set($name, $value);
    }

    public function __construct(){
        $this->_strClsDalLoad = '\NsINV\ClsDalCustomer';
        $this->_strClsDalSave = '\NsINV\ClsDalCustomer';
        $this->_data = array(
            'intID'=>-1,
            'intCityID'=>-1,
            'strName'=>'',
            'strAddress'=>'',
            'strPhone'=>'',
            'strEmail'=>'',
            'strNotes'=>'',
            'strTimestamp'=>'',

        );
        parent::__construct(func_get_args());
    }

    public function __get($name){
        switch($name){
            case 'objCity':
                if(!isset($this->_data[$name])){
                    $obj = new \NsCMN\ClsBllCity();
                    $obj->LoadByID($this->_data['intCityID']);
                    $obj->objCountry;
                    $this->_data[$name] = $obj;
                }
                break;
            case 'arrInvoice':
                if(!isset($this->_data[$name])){
                    $objFilter = new ClsFilterInvoice();
                    $objFilter->intCustomerID = $this->_data['intID'];
                    $objInvoice = new ClsBllInvoice();
                    $this->_data[$name] = $objInvoice->GetDataAssociative($objFilter);
                }           
            case 'arrPayment':
                if(!isset($this->_data[$name])){
                    $objFilter = new \NsFWK\ClsFilter();
                    $objFilter->intCustomerID = "fkCustomerID=".$this->_data['intID'];
                    $objPayment = new ClsBllPayment();
                    $this->_data[$name] = $objPayment->GetDataAssociative($objFilter);
                }

            case 'arrInvoiceTotalDueAnount':
                if(!isset($this->_data['arrInvoiceTotalDueAnount'])){
                    $arrResult = $this->calculateDueAmount();
                    $this->_data['arrInvoiceTotalDueAnount'] = $arrResult['arrInvoiceTotalDueAnount'];
                    $this->_data['decTotalDueAmount'] = $arrResult['decTotalDueAmount'];
                    $this->_data['intNumberOfDueInvoices'] = $arrResult['intNumberOfDueInvoices']; 
                }
                break;
            case 'decTotalDueAmount':
                if(!isset($this->_data['decTotalDueAmount'])){
                    $arrResult = $this->calculateDueAmount();
                    $this->_data['decTotalDueAmount'] = $arrResult['decTotalDueAmount'];
                    $this->_data['arrInvoiceTotalDueAnount']=$arrResult['arrInvoiceTotalDueAnount'];
                    $this->_data['intNumberOfDueInvoices'] = $arrResult['intNumberOfDueInvoices'];
                }
                break;
            case 'intNumberOfDueInvoices':
                if(!isset($this->_data['intNumberOfDueInvoices'])){
                    $arrResult = $this->calculateDueAmount();
                    $this->_data['intNumberOfDueInvoices'] = $arrResult['intNumberOfDueInvoices'];
                    $this->_data['arrInvoiceTotalDueAnount']=$arrResult['arrInvoiceTotalDueAnount'];
                    $this->_data['decTotalDueAmount'] = $arrResult['decTotalDueAmount'];
                }
                break;

        }
        return parent::__get($name);
    }


    protected function _load(\ADODB_Active_Record $objDAL){
        $this->_data['intID'] = $objDAL->pkCustomerID;
        $this->_data['intCityID'] = $objDAL->fkCityID;
        $this->_data['strName'] = $objDAL->fldName;
        $this->_data['strAddress'] = $objDAL->fldAddress;
        $this->_data['strPhone'] = $objDAL->fldPhone;
        $this->_data['strEmail'] = $objDAL->fldEmail;
        $this->_data['strNotes'] = $objDAL->fldNotes;
        $this->_data['strTimestamp'] = $objDAL->fldTimestamp;
        $this->_data['strEncryptedID'] = \NsFWK\ClsHlpHelper::Encrypt($objDAL->pkCustomerID);
    }

    protected function _save(\ADODB_Active_Record $objDAL){
        if($this->getIsLoaded()){
            // UPDATE
            $rslt = $objDAL->Load('pkCustomerID = ?',array($this->_data['intID']));
            if(!$rslt){
                return 'Could not load object!';
            }
        } else {
            // NEW
            $objDAL->fldTimestamp = date("Y-m-d H:i:s");
        }

        $objDAL->fldName = $this->_data['strName'];
        $objDAL->fldAddress = $this->_data['strAddress'];
        $objDAL->fldPhone = $this->_data['strPhone'];
        $objDAL->fldEmail = $this->_data['strEmail'];
        $objDAL->fldNotes = $this->_data['strNotes'];
        $objDAL->fkCityID = $this->_data['intCityID'];  
        $rslt = $objDAL->Save();

        if($rslt){
            $this->_data['intID'] = $objDAL->pkCustomerID;
        }
        return $rslt;
    }

    protected function _delete(\ADODB_Active_Record $objDAL){
        $DB = &\ADODB_Connection_Manager::GetConnection('customer');
        return $DB->Execute('DELETE FROM inv_customer WHERE pkCustomerID = ? ', array($this->_data['intID']));
    }


    protected function calculateDueAmount(){
        $DB = &\ADODB_Connection_Manager::GetConnection('customer');
        $DB->StartTrans();
        //  Get total amount for invoices that accept payment
        $intCustomerID = $this->_data['intID'];
        $strSQL = 'SELECT fkInvoiceID, SUM(
        CASE
        WHEN fldtype = "LNG_ENUM_ITEM" OR fldtype = "LNG_ENUM_TAX" THEN (fldQuantity * fldUnitPrice)
        WHEN fldtype = "LNG_ENUM_DISCOUNT" THEN (-(fldQuantity * fldUnitPrice))
        END
        )
        AS TotalAmount FROM inv_invoice_row, inv_invoice 
        WHERE fkInvoiceID=pkInvoiceID AND fkCustomerID = '.$intCustomerID.' AND fkStatusID IN(2,4)
        GROUP BY fkInvoiceID
        ORDER BY fldDueDate ASC
        ';
        $arrTotalAmount = $DB->GetAssoc($strSQL);

        // Get total paid amount for invoices that accept payment
        $strSQL = 'SELECT pfInvoiceID, SUM(fldPaymentAmount
        )
        AS TotalAmount FROM inv_invoice_payment, inv_invoice 
        WHERE pfInvoiceID=pkInvoiceID AND fkCustomerID = '.$intCustomerID.' AND fkStatusID IN(2,4)
        GROUP BY pfInvoiceID
        ';
        $arrTotalPaid = $DB->GetAssoc($strSQL);        
        $DB->CompleteTrans();
        //Calculate remaining due for each invocie
        $arrTotalDueAmount = [];
        foreach($arrTotalAmount as $intInvoiceID => $decTotalAmount){
            if(array_key_exists($intInvoiceID, $arrTotalPaid)){
                $arrTotalDueAmount[$intInvoiceID]= $decTotalAmount - $arrTotalPaid[$intInvoiceID];
            }else{
                $arrTotalDueAmount[$intInvoiceID] = $decTotalAmount;
            }
        }
        $arrResult['arrInvoiceTotalDueAnount']= $arrTotalDueAmount;
        $arrResult['decTotalDueAmount']= array_sum($arrTotalDueAmount);
        $arrResult['intNumberOfDueInvoices']= count($arrTotalDueAmount);
        return $arrResult;
    }

    
    public function LoadByID($intID){
        $objFilter = new \NsFWK\ClsFilter();
        $objFilter->intID = "pkCustomerID = $intID";
        return $this->Load($objFilter);
    }
    public function LoadByName($strName){
        $objFilter = new \NsFWK\ClsFilter();
        $objFilter->strName = "fldName LIKE '$strName'";
        return $this->Load($objFilter);
    }

    public function GetAllCustomers(){
        $objFilter = new \NsFWK\ClsFilter();
        $strWhere = $objFilter->GetWhereStatement();
        $arrData = $this->GetDataAssociative($objFilter, 'fldName ASC');
        $this->loadBindingAssociative($arrData, 'intCityID', 'objCity',  new \NsCMN\ClsBllCity(), 'pkCityID', 'intID'); 
        return $arrData;

    }

    public function GetDataPageAssociative($intPageNo, $intPageSize, $strWhere, $strOrder, &$intPageCount, &$intRowCount){
        $arrData = parent::GetDataPageAssociative($intPageNo, $intPageSize, $strWhere, $strOrder, $intPageCount, $intRowCount);
        if (!empty($arrData)){
            $this->loadBindingAssociative($arrData, 'intCityID', 'objCity', new \NsCMN\ClsBllCity(), 'pkCityID', 'intID');
            return $arrData;
        }
        return $arrData;
    }
}