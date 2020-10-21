<?php
namespace NsINV;
class ClsBllPayment extends \NsFWK\ClsBll{

    public function __get($name){
        switch($name){
            case 'objCustomer':
                if(!isset($this->_data['objCustomer'])){
                    $obj = new \NsINV\ClsBllCustomer();
                    $obj->LoadByID($this->_data['intCustomerID']);
                    $obj->objCustomer;
                    $this->_data['objCustomer'] = $obj;
                }
                break;
            case 'objMethod':
                if(!isset($this->_data['objMethod'])){
                    $obj = new \NsINV\ClsBllPaymentMethod();
                    $obj->LoadByID($this->_data['intPaymentMethodID']);
                    $obj->objMethod;
                    $this->_data['objMethod'] = $obj;
                }
                break;
            case 'objUser':
                if(!isset($this->_data['objUser'])){
                    $obj = new \NsCMN\ClsBllUser();
                    $obj->LoadByID($this->_data['intCreatedByUserID']);
                    $obj->objUser;
                    $this->_data['objUser'] = $obj;
                }
                break;
        }
        return parent::__get($name);
    }

    public function __construct(){
        $this->_strClsDalLoad = '\NsINV\ClsDalPayment';
        $this->_strClsDalSave = '\NsINV\ClsDalPayment';
        $this->_data = array(
            'intID'=>-1,
            'intPaymentMethodID'=>-1,
            'intCustomerID'=>-1,
            'intPaymentNumber'=>-1,
            'strReference'=>'',
            'dtDate'=>'',
            'decAmount'=>-1,
            'intCreatedByUserID'=>'',
            'dmCreatedOn'=>''
        );
        @parent::__construct(func_get_args());
    }

    protected function _save(\ADODB_Active_Record $objDAL){
    }
    protected function _delete(\ADODB_Active_Record $objDAL){
        $DB = &\ADODB_Connection_Manager::GetConnection('customer');
        return $DB->Execute('DELETE FROM inv_payment WHERE pkPaymentID = ? ', array($this->_data['intID']));
    }

    protected function _load(\ADODB_Active_Record $objDAL){
        $this->_data['intID'] = $objDAL->pkPaymentID;
        $this->_data['intPaymentMethodID'] = $objDAL->fkPaymentMethodID;
        $this->_data['intCustomerID'] = $objDAL->fkCustomerID;
        $this->_data['intPaymentNumber'] = $objDAL->fldPaymentNumber;
        $this->_data['strReference'] = $objDAL->fldReference;
        $this->_data['dtDate'] = $objDAL->fldDate;
        $this->_data['decAmount'] = $objDAL->fldAmount;
        $this->_data['intCreatedByUserID'] = $objDAL->fkCreatedByUserID;
        $this->_data['dmCreatedOn'] = $objDAL->fldCeratedOnTimestamp;
    }

    public function LoadByID($intID){
        $objFilter = new \NsFWK\ClsFilter();
        $objFilter->intID = "pkPaymentID = $intID";
        return $this->Load($objFilter);
    }

    public function GetDataPageAssociative($intPageNo, $intPageSize, $strWhere, $strOrder, &$intPageCount, &$intRowCount){
        $arrData = parent::GetDataPageAssociative($intPageNo, $intPageSize, $strWhere, $strOrder, $intPageCount, $intRowCount);
        if (!empty($arrData)){
            $this->loadBindingAssociative($arrData, 'intCustomerID', 'objCustomer', new \NsINV\ClsBllCustomer(), 'pkCustomerID', 'intID');
            $this->loadBindingAssociative($arrData, 'intPaymentMethodID', 'objMethod', new \NsINV\ClsBllPaymentMethod(), 'pkPaymentMethodID', 'intID');
            $this->loadBindingAssociative($arrData, 'intCreatedByUserID','objUser', new \NsCMN\ClsBllUser(), 'pkUserID', 'intID');
        }
        return $arrData; 
    }

    public function GetDataAssociative(\NsFWK\ClsFilter $objFilter, $strOrder = '', $strGroup = '', $intOffset = false, $intCount = false){
        $arrData =  parent::GetDataAssociative($objFilter);
        if (!empty($arrData)){
            $this->loadBindingAssociative($arrData, 'intCustomerID', 'objCustomer', new \NsINV\ClsBllCustomer(), 'pkCustomerID', 'intID');
            $this->loadBindingAssociative($arrData, 'intPaymentMethodID', 'objMethod', new \NsINV\ClsBllPaymentMethod(), 'pkPaymentMethodID', 'intID');
            $this->loadBindingAssociative($arrData, 'intCreatedByUserID','objUser', new \NsCMN\ClsBllUser(), 'pkUserID', 'intID');
        }
        return $arrData;
    }


    public function DeletePayment ($intPaymentID){
        $DB = &\ADODB_Connection_Manager::GetConnection('customer');
        $DB->StartTrans();
        // 0. Delete all records having pfPaymentID = $intPaymentID from table inv_invoice_payment BUT keep their IDs first!
        $strSQL = "SELECT pfInvoiceID FROM inv_invoice_payment WHERE pfPaymentID = $intPaymentID";
        $arrAffectedInvoice = $DB->GetCol($strSQL);
        $strSQL = "DELETE FROM inv_invoice_payment WHERE pfPaymentID = $intPaymentID";
        $rslt = $DB->Execute($strSQL);
        if(!$rslt){
            $objResult['result'] = false;
            $objResult['title'] = 'Failure';
            $objResult['message'] = $DB->errorMsg();
            return $objResult;
        }
        // 1. Check whether reflected invoices from step (1) still having other payments in inv_invoice_payment or not
        //    if no, change theiR status to "SENT" instead of "PARTIAL".
        foreach ($arrAffectedInvoice as $invoice){
            $strSQL = "SELECT pfInvoiceID FROM inv_invoice_payment WHERE pfInvoiceID = $invoice";
            $rslt = $DB->GetCol($strSQL);
            if(!count($rslt)){ // Change status to "SENT"
                $intStatusID = 2;
            }else{
                $intStatusID = 4;
            }
            $strSQL = "UPDATE inv_invoice SET fkStatusID = $intStatusID WHERE pkInvoiceID = $invoice".";";
            $rslt = $DB->Execute($strSQL);
            if(!$rslt){
                $objResult['result'] = false;
                $objResult['title'] = 'Failure';
                $objResult['message'] = $DB->errorMsg();
                return $objResult;
            }
        }
        // 2. Delete payment from table inv_payment
        $objPayment = new ClsBllPayment();
        $objPayment->LoadByID($intPaymentID);
        $rslt = $objPayment->Delete();
        if(!$rslt){
            $objResult['result'] = false;
            $objResult['title'] = 'Failure';
            $objResult['message'] = $DB->errorMsg();
            return $objResult;
        }
        $DB->CompleteTrans();
        $objResult['result'] = true;
        $objResult['title'] = 'Success';
        $objResult['message'] = "Payment Deleted Successfully!";
        return $objResult;
    }

    public function GetInvoices(){
        $DB = &\ADODB_Connection_Manager::GetConnection('customer');
        $strSQL ='SELECT * FROM inv_invoice_payment, inv_invoice, inv_invoice_status WHERE pfPaymentID='.$this->_data['intID'].' AND pfInvoiceID=pkInvoiceID
        AND pkStatusID=fkStatusID';
        $rslt = $DB->GetArray($strSQL);
        if($rslt){
            return $rslt;
        }else{
            return false;
        }
    }

    public function GetByInvoiceID($intInvoiceID){
        $DB = &\ADODB_Connection_Manager::GetConnection('customer');
        $strSQL ="SELECT fldPaymentNumber AS intPaymentNumber,fldDate AS dtDate ,
        fldPaymentAmount AS decAmount
        FROM inv_payment
        INNER JOIN 
        inv_invoice_payment ON inv_payment.pkPaymentID=inv_invoice_payment.pfPaymentID
        WHERE 
        pfInvoiceID = $intInvoiceID";
        $rslt = $DB->GetArray($strSQL);
        return $rslt;
    }
    // This function checks if this payment number exists.
    // Used to hanlde cowpay callback.
    static public function GetByPaymentNumber($intPaymentNumber){
        $DB = &\ADODB_Connection_Manager::GetConnection('customer');
        $strSQL ="SELECT fldPaymentNumber FROM inv_payment inv_invoice WHERE fldPaymentNumber = $intPaymentNumber";
        $rslt = $DB->GetArray($strSQL);
        return $rslt; 
    }
    static protected function getNextSearialNumber(){
        $DB = &\ADODB_Connection_Manager::GetConnection('customer');
        $strSQL = 'SELECT MAX(fldPaymentNumber) from inv_payment';
        $rslt = $DB->GetCol($strSQL);
        return $rslt[0]+1;
    }
}