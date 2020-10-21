<?php
namespace NsINV;

class ClsBllInvoice extends \NsFWK\ClsBll{
    public function __construct(){
        $this->_strClsDalLoad = '\NsINV\ClsDalInvoice';
        $this->_strClsDalSave = '\NsINV\ClsDalInvoice';
        $this->_data = array(
            'intID'=>-1,
            'strInvoiceNumber'=>'',
            'strReference'=>'',
            'dtIssueDate'=>'',
            'dtDueDate'=>'',
            'tsCreatedOnTimestamp'=>'',
            'intCreatedByUserID'=>-1,
            'intCustomerID'=>-1,
            'intPaymentTermID'=>-1,
            'intStatusID'=>ClsBllStatus::STATUS_DRAFT,
            'boolOverDue'=>false,

            'intItemCount'=>0,
            'decTotalAmount'=>0,
            'decTotalDiscount'=>0,
            'decNetAmount'=>0,
            'decTotalTax'=>0,
            'decGrossAmount'=>0,

            'decTotalPayment'=>0
        );
        parent::__construct(func_get_args());
    }

    public function __set($name, $value){
        switch ($name) {
            case 'intID':
            case 'strInvoiceNumber':
            case 'dtIssueDate':
            case 'tsCreatedOnTimestamp':
            case 'intCreatedByUserID':
            case 'intStatusID':
            case 'strEncryptedID':
                // Read only attributes
                return false;
            case 'strReference':
            case 'dtDueDate':
            case 'intCustomerID':
            case 'intPaymentTermID':
                // Allow edits only if invoice is at an editable status
                if (!$this->intEditable) {
                    return false;
                }
                break;
        }

        return parent::__set($name, $value);
    }

    public function __get($name){
        switch ($name) {
            case 'intEditable':
                if (!isset($this->_data[$name])) {
                    $this->_data[$name] = $this->objStatus->intEditable;
                }
                break;
            case 'arrInvoiceRow':
                if (!isset($this->_data[$name])) {
                    $this->_data[$name] = $this->getRows();
                }
                break;
            case 'arrInvoicePayment':
                if(!isset($this->_data[$name])){
                    $this->_data[$name] = $this->getPayments();
                }
                break;
            case 'objUser':
                if(!isset($this->_data[$name])){
                    $obj = new \NsCMN\ClsBllUser();
                    $obj->LoadByID($this->_data['intCreatedByUserID']);
                    $this->_data[$name] = $obj;
                }
                break;
            case 'objCustomer':
                if(!isset($this->_data[$name])){
                    $obj = new \NsINV\ClsBllCustomer();
                    $obj->LoadByID($this->_data['intCustomerID']);
                    $this->_data[$name] = $obj;
                }
                break;
            case 'objPaymentTerm':
                if(!isset($this->_data[$name])){
                    $obj = new \NsINV\ClsBllPaymentTerm();
                    $obj->LoadByID($this->_data['intPaymentTermID']);
                    $this->_data[$name] = $obj;
                }
                break;
            case 'objStatus':
                if(!isset($this->_data[$name])){
                    $obj = new \NsINV\ClsBllStatus();
                    $obj->LoadByID($this->_data['intStatusID']);
                    $this->_data[$name] = $obj;
                }
                break; 
        }
        return parent::__get($name);
    }


    protected function _save(\ADODB_Active_Record $objDAL){
        if ($this->getIsLoaded()) {
            // Update
            $rslt = $objDAL->Load('pkInvoiceID = ?',array($this->_data['intID']));
            if(!$rslt){
                return 'Could not load object!';
            }
        } else {
            // New
            $objDAL->fldInvoiceNumber = $this->_data['strInvoiceNumber'];
            $objDAL->fldIssueDate = date('Y-m-d h:i:s', strtotime($this->_data['dtIssueDate']));
            $objDAL->fkStatusID = $this->_data['intStatusID'];
            $objDAL->fkCreatedByUserID = $this->_data['intCreatedByUserID'];
            $objDAL->fldCreatedOnTimestamp = date("Y-m-d H:i:s");
        }

        $objDAL->fldReference = $this->_data['strReference'];
        $objDAL->fldDueDate = date('Y-m-d h:i:s', strtotime($this->_data['dtDueDate']));
        $objDAL->fkCustomerID = $this->_data['intCustomerID'];
        $objDAL->fkPaymentTermID = $this->_data['intPaymentTermID'];
        $objDAL->fkStatusID = $this->_data['intStatusID'];

        $rslt = $objDAL->Save();
        if ($rslt) {
            $this->_data['intID'] = $objDAL->pkInvoiceID;
        }
        return $rslt;
    }

    protected function _delete(\ADODB_Active_Record $objDAL){
        $DB = &\ADODB_Connection_Manager::GetConnection('customer');
        $DB->StartTrans();
        $strSQL = 'DELETE FROM inv_invoice_row WHERE fkInvoiceID = ? ';
        $rslt = $DB->Execute($strSQL, array($this->_data['intID'])); 
        if(!$rslt){
            $DB->CompleteTrans(false);
            return false;
        }
        $strSQL = 'DELETE FROM inv_invoice WHERE pkInvoiceID = ? ';

        $rslt = $DB->Execute($strSQL, array($this->_data['intID']));
        if(!$rslt){
            $DB->CompleteTrans(false);
            return false;
        }

        $ok = $this->setLog('DELETE');
        if(!$ok){
            $DB->CompleteTrans($rslt);
            return false;
        }

        $ok = $DB->CompleteTrans();
        if(!$ok){
            return false;
        }
        return true;
    }

    protected function _load(\ADODB_Active_Record $objDAL){
        $this->_data['intID'] = $objDAL->pkInvoiceID;
        $this->_data['strInvoiceNumber'] = $objDAL->fldInvoiceNumber;
        $this->_data['strReference'] = $objDAL->fldReference;
        $this->_data['dtIssueDate'] = $objDAL->fldIssueDate;
        $this->_data['dtDueDate'] = $objDAL->fldDueDate;
        $this->_data['tsCreatedOnTimestamp'] = $objDAL->fldCreatedOnTimestamp;
        $this->_data['intCreatedByUserID'] = $objDAL->fkCreatedByUserID;
        $this->_data['intCustomerID'] = $objDAL->fkCustomerID;
        $this->_data['intPaymentTermID'] = $objDAL->fkPaymentTermID;
        $this->_data['intStatusID'] = $objDAL->fkStatusID;
        $this->_data['strEncryptedID'] = \NsFWK\ClsHlpHelper::Encrypt($objDAL->pkInvoiceID);
    }


    protected function getRows(){ 
        $intInvoiceID = $this->_data['intID'];   
        $objInvoiceRow = new ClsBllInvoiceRow();
        $arrInvoiceRow = $objInvoiceRow->GetByInvoiceID($intInvoiceID);
        return $arrInvoiceRow;
    }

    protected function getPayments(){ 
        $intInvoiceID = $this->_data['intID'];  
        $objPayment = new ClsBllPayment();
        $arrPayment = $objPayment->GetByInvoiceID($intInvoiceID);
        return $arrPayment;
    } 

    protected function loadBindingCustomParams(&$arrInvoices){
        if(empty($arrInvoices)){
            return;
        }
        $arrID = array();
        foreach($arrInvoices as $objInvoice){
            $arrID[] = $objInvoice['intID'];
        }

        $arrAssocAmount = ClsBllInvoiceRow::GetInvoicesAmounts($arrID);
        $arrAssocPaymentAmount = ClsBllInvoicePayment::GetAmounts($arrID); 

        foreach ($arrInvoices as &$arrItem) {
            if (!empty($arrAssocAmount[$arrItem['intID']])) {
                $arrItem['intItemCount'] = $arrAssocAmount[$arrItem['intID']]['intItemCount'];
                $arrItem['decTotalAmount'] = $arrAssocAmount[$arrItem['intID']]['decTotalAmount'];
                $arrItem['decTotalDiscount'] = $arrAssocAmount[$arrItem['intID']]['decTotalDiscount'];
                $arrItem['decNetAmount'] = $arrItem['decTotalAmount'] - $arrItem['decNetAmount'];
                $arrItem['decTotalTax'] = $arrAssocAmount[$arrItem['intID']]['decTotalTax'];
                $arrItem['decGrossAmount'] = $arrItem['decNetAmount'] +  $arrItem['decTotalTax'];
            }
            if (!empty($arrAssocPaymentAmount[$arrItem['intID']])) {
                $arrItem['decTotalPayment'] = $arrAssocPaymentAmount[$arrItem['intID']];
            }
            if($arrItem['dtDueDate'] < date('Y-m-d H:i:s')){
                if (in_array($arrItem['intStatusID'], array(ClsBllStatus::STATUS_SENT, ClsBllStatus::STATUS_PARTIAL))) {
                    $arrItem['boolOverDue'] = true;
                }
            }
        }
    }

    protected function setLog($strAction){
        $objLog = new \NsCMN\ClsBllLog();
        $objLog->strObjectType = 'Invoice';
        $objLog->strAction = $strAction;
        $objLog->objObjectID = json_encode(array('intID'=>$this->_data['intID']));;
        $objLog->intUserID = $this->_data['intCreatedByUserID'];

        if($strAction == 'DELETE'){
            $objLog->strObject = '{}';
        }else{
            $this->arrInvoiceRow;
            $objLog->strObject = $this->ToJson();
        }
        $rslt = $objLog->Save();
        return $rslt;
    }


    public function LoadByID($intID){
        $objFilter = new \NsFWK\ClsFilter();
        $objFilter->intID = "pkInvoiceID = $intID";
        $ok = $this->Load($objFilter);
        if (!$ok) {
            return false;
        }
     
        // set invoice over due or not 
        if($this->_data['dtDueDate'] < date('Y-m-d H:i:s')){
              
            if($this->_data['intStatusID'] == ClsBllStatus::STATUS_SENT || $this->_data['intStatusID'] == ClsBllStatus::STATUS_PARTIAL){
                $this->_data['boolOverDue'] = true;
            }
        }
        // set invoice amounts like total items , amounts , discount, tax
        $arr = ClsBllInvoiceRow::GetInvoicesAmounts(array($this->intID));
                
        if (!empty($arr)) {
            foreach ($arr as $intInvoiceID=>$arrTotal) {
                foreach ($arrTotal as $strKey=>$decAmount) {
                    $this->_data[$strKey] = $decAmount;
                }
            }
        }
        
        // set invoice payment amount
        $arr = ClsBllInvoicePayment::GetAmounts(array($this->intID));
        if (!empty($arr)) {
            foreach ($arr as $intInvoiceID=>$decAmount) {
                $this->_data['decTotalPayment'] = $decAmount;
            }
        }
        return true;
    }

    public function UpdateInvoiceRows($arrInvoiceRow){

        if (empty($arrInvoiceRow)) {
            return false;
        }

        $DB = &\ADODB_Connection_Manager::GetConnection('customer');

        $DB->StartTrans();

        $strSQL = "DELETE FROM inv_invoice_row WHERE fkInvoiceID = {$this->_data['intID']};";    
        $rslt = $DB->Execute($strSQL);  

        if(!$rslt){
            $DB->CompleteTrans(false);
            return false;
        }
        $strSQL = "INSERT INTO inv_invoice_row 
                    (fkInvoiceID,fkItemID ,fkTaxTypeID ,fldType ,
                    fldQuantity ,fldUnitPrice ,fldOrder ,fldDescription )
                    VALUES";
        foreach($arrInvoiceRow as $objInvoiceRow){
            $strSQL .= '(';
            $strSQL .= $this->_data['intID'];
            $strSQL .= ',';
            if(!empty($objInvoiceRow['objCatalogue'])){
                $strSQL .= $objInvoiceRow['objCatalogue']['intID'];  
            }else{
                $strSQL .= 'NULL';  
            }
            $strSQL .= ',';
            if(!empty($objInvoiceRow['objTaxType'])){
                $strSQL .= $objInvoiceRow['objTaxType']['intID']; 
            }else{
                $strSQL .= 'NULL';
            }
            $strSQL .= ',';
            $strSQL .= "'".$objInvoiceRow['strType']."'";
            $strSQL .= ',';
            $strSQL .= $objInvoiceRow['intQuantity'];
            $strSQL .= ',';
            $strSQL .= $objInvoiceRow['intUnitPrice'];
            $strSQL .= ',';
            $strSQL .= $objInvoiceRow['decOrder'];
            $strSQL .= ',';
            $strSQL .= "'".$objInvoiceRow['strDescription']."'";
            $strSQL .= '),';
        }
        $strSQL = substr($strSQL, 0, -1);
        $ok = $DB->Execute($strSQL);
        if(!$ok){
            $DB->CompleteTrans($rslt);
            return false;
        } 

        $ok = $DB->CompleteTrans();
        if(!$ok){
            return false;
        }
        return true;
    }

    public function Update($arrInvoiceRow){
        if (empty($arrInvoiceRow)) {
            return false;
        }
        if(!$this->intEditable){
            return false;
        }
        $DB = &\ADODB_Connection_Manager::GetConnection('customer');
        $DB->StartTrans();

        $ok = $this->Save();
        if(!$ok){
            $DB->CompleteTrans($ok);
            return false;
        }

        $ok = $this->UpdateInvoiceRows($arrInvoiceRow);
        if(!$ok){
            $DB->CompleteTrans($ok);
            return false;
        }

        $ok = $this->setLog('UPDATE');
        if(!$ok){
            $DB->CompleteTrans($ok);
            return false;
        }

        $ok = $DB->CompleteTrans();
        if(!$ok){
            return false;
        }

        return $this;
    }
    
    public function AddPayment(&$decPaymentAmount, $intPaymentID, $strPaymentMethod=''){
        $DB = &\ADODB_Connection_Manager::GetConnection('customer');
        $intInvocieID = $this->_data['intID'];
        $decDueAmount = $this->_data['decGrossAmount']-$this->_data['decTotalPayment'];
        if($strPaymentMethod == 'CARD'){
            $decAmount = $decPaymentAmount; 
        } else {
            $decAmount= min($decDueAmount, $decPaymentAmount);
        } 
        $rslt = ClsBllInvoicePayment::Create($intPaymentID, $intInvocieID, $decAmount);
        if(!$rslt){
            return false;
        }
        // Update invoice status.
        if($decDueAmount <= $decPaymentAmount){ // Mark it as paid
            $intStatusID = 5;
        }else{
            $intStatusID = 4; // Mark it as partial
        }
        $strSQL = "UPDATE inv_invoice SET fkStatusID = $intStatusID WHERE pkInvoiceID = $intInvocieID".";";
        $updateOk = $DB->Execute($strSQL);
        if(!$updateOk){
            return false;
        }
        // Decrease payment amount
        //if($decDueAmount < $decPaymentAmount){
            $decPaymentAmount -= $decDueAmount; 
        //}else{
            //$decPaymentAmount = 0;
        //}
        return true; 
    }

    
    public function GetDataPageAssociative($intPageNo, $intPageSize, $strWhere, $strOrder, &$intPageCount, &$intRowCount){
        $arrData = parent::GetDataPageAssociative($intPageNo, $intPageSize, $strWhere, $strOrder, $intPageCount, $intRowCount);
        if (!empty($arrData)) {
            $this->loadBindingAssociative($arrData, 'intCreatedByUserID', 'objUser', new \NsCMN\ClsBllUser(), 'pkUserID', 'intID');
            $this->loadBindingAssociative($arrData, 'intCustomerID', 'objCustomer', new \NsINV\ClsBllCustomer(), 'pkCustomerID', 'intID');
            $this->loadBindingAssociative($arrData, 'intPaymentTermID', 'objPaymentTerm', new \NsINV\ClsBllPaymentTerm(), 'pkPaymentTermID', 'intID');
            $this->loadBindingAssociative($arrData, 'intStatusID', 'objStatus', new \NsINV\ClsBllStatus(), 'pkStatusID', 'intID');

            $this->loadBindingCustomParams($arrData);
        }
        return $arrData;
    }

    public function GetDataAssociative(\NsFWK\ClsFilter $objFilter, $strOrder = '', $strGroup = '', $intOffset = false, $intCount = false){
        $arrData =  parent::GetDataAssociative($objFilter);
        if (!empty($arrData)) {
            $this->loadBindingAssociative($arrData, 'intCreatedByUserID', 'objUser', new \NsCMN\ClsBllUser(), 'pkUserID', 'intID');
            $this->loadBindingAssociative($arrData, 'intCustomerID', 'objCustomer', new \NsINV\ClsBllCustomer(), 'pkCustomerID', 'intID');
            $this->loadBindingAssociative($arrData, 'intPaymentTermID', 'objPaymentTerm', new \NsINV\ClsBllPaymentTerm(), 'pkPaymentTermID', 'intID');
            $this->loadBindingAssociative($arrData, 'intStatusID', 'objStatus', new \NsINV\ClsBllStatus(), 'pkStatusID', 'intID');

            $this->loadBindingCustomParams($arrData);
        }
        return $arrData;
    }


    static public function ValidateInvoiceNumber($strInvoiceNumber) {
        if (count(explode(' ', $strInvoiceNumber)) > 1) {
            return false; 
        }
        $DB = &\ADODB_Connection_Manager::GetConnection('customer');
        $strSQL = "SELECT * FROM inv_invoice WHERE fldInvoiceNumber LIKE ? LIMIT 1";
        $arr = $DB->GetArray($strSQL, $strInvoiceNumber);
         if (empty($arr)) {
            return true; 
        }
        else{
            return false;
        }
    }

    static public function Create($arrInvoice, $arrInvoiceRow){
        if (empty($arrInvoiceRow)) {
            return false;
        }
        if (empty($arrInvoice['strInvoiceNumber'])) {
            $intStartNumber = \NsFWK\ClsConfiguration::GetInstance()->inv_InvoiceNumberStart;
            $intMaxDigits = \NsFWK\ClsConfiguration::GetInstance()->inv_InvoiceNumberDigits;
            $intMaxNumber = ClsBllInvoice::GetMaxNumber();
            if($intMaxNumber < $intStartNumber){
                $arrInvoice['strInvoiceNumber'] = str_pad($intStartNumber ,$intMaxDigits , "0", STR_PAD_LEFT );
            }else {
                $arrInvoice['strInvoiceNumber'] = str_pad($intMaxNumber + 1 ,$intMaxDigits , "0", STR_PAD_LEFT );   
            }
        }else if(!ClsBllInvoice::ValidateInvoiceNumber($arrInvoice['strInvoiceNumber'])){
             return false;
        }
        $DB = &\ADODB_Connection_Manager::GetConnection('customer');
        $DB->StartTrans();

        $objInvoice = new ClsBllInvoice();
        $objInvoice->_data['strInvoiceNumber'] = $arrInvoice['strInvoiceNumber'];
        $objInvoice->_data['strReference'] = $arrInvoice['strReference'];
        $objInvoice->_data['dtIssueDate'] = $arrInvoice['dtIssueDate'];
        $objInvoice->_data['dtDueDate'] = $arrInvoice['dtDueDate'];
        $objInvoice->_data['intCreatedByUserID'] = $arrInvoice['intCreatedByUserID'];
        $objInvoice->_data['intCustomerID'] = $arrInvoice['objCustomer']['intID'];
        $objInvoice->_data['intPaymentTermID'] = $arrInvoice['objPaymentTerm']['intID'];

        $ok = $objInvoice->Save();
       
        if(!$ok){
            $DB->CompleteTrans($ok);
            return false;
        }

        $ok = $objInvoice->UpdateInvoiceRows($arrInvoiceRow);

        if(!$ok){
            $DB->CompleteTrans($ok);
            return false;
        }

        $ok = $objInvoice->setLog('INSERT');


        if(!$ok){
            $DB->CompleteTrans($ok);
            return false;
        }

        $ok = $DB->CompleteTrans();
        if(!$ok){
            return false;
        }

        return $objInvoice;
    }

    static public function GetMaxNumber(){
        $DB = &\ADODB_Connection_Manager::GetConnection('customer');
        $strRegExp = '^([0-9]+,)*([0-9]+){1}$';
        $strSQL = "SELECT MAX(fldInvoiceNumber) AS MaxNumber FROM inv_invoice WHERE fldInvoiceNumber   REGEXP  '$strRegExp'";
        $rslt = $DB->GetRow($strSQL);
        return $rslt['MaxNumber'];
    }

}