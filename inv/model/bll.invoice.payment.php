<?php
namespace NsINV;
class ClsBllInvoicePayment extends \NsFWK\ClsBll{
    public function __set($name, $value) {
        switch ($name) {
            // Read-only attributes
            case ('intInvoiceID'):
                return false;
            case ( 'intPaymentID'):
                return false;
            case 'decAmount':
                return false;
        }
        return parent::__set($name, $value);
    }
    public function __construct(){
        $this->_strClsDalLoad = '\NsINV\ClsDalInvoicePayment';
        $this->_strClsDalSave = '\NsINV\ClsDalInvoicePayment';
        $this->_data = array(
            'intInvoiceID'=>-1,
            'intPaymentID'=>-1,
            'decAmount'=>-1
        );
        parent::__construct(func_get_args());
    }
    protected function _save(\ADODB_Active_Record $objDAL){
        if($this->getIsLoaded()){
            // UPDATE is not allowed
            return false;
        }
        $objDAL->pfInvoiceID = $this->_data['intInvoiceID'];
        $objDAL->pfPaymentID = $this->_data['intPaymentID'];
        $objDAL->fldPaymentAmount = $this->_data['decAmount'];  
        $rslt = $objDAL->Save();
        return $rslt; 
    }

    protected function _delete(\ADODB_Active_Record $objDAL){
        return true;
    }
    protected function _load(\ADODB_Active_Record $objDAL){
        $this->_data['intInvoiceID'] = $objDAL->pfInvoiceID;
        $this->_data['intPaymentID'] = $objDAL->pfPaymentID;
        $this->_data['decAmount'] = $objDAL->fldPaymentAmount;
    }

    static public function GetAmounts($arrInvoiceID){  
        if (empty($arrInvoiceID)) {
            return;
        }

        $strInvoiceID = implode(',', $arrInvoiceID);

        $DB = &\ADODB_Connection_Manager::GetConnection('customer');
        $strSQL = "SELECT pfInvoiceID as intInvoiceID, SUM(fldPaymentAmount) as decTotalPayment
        FROM inv_invoice_payment
        WHERE pfInvoiceID in($strInvoiceID)
        GROUP BY pfInvoiceID";

        $arrTotal = $DB->GetAssoc($strSQL);
        return $arrTotal;
    }

    static function Create($intPaymentID, $intInvocieID, $decAmount){
        $objPaymentInvoice = new ClsBllInvoicePayment();
        $objPaymentInvoice->_data['intInvoiceID'] = $intInvocieID; 
        $objPaymentInvoice->_data['intPaymentID'] = $intPaymentID; 
        $objPaymentInvoice->_data['decAmount'] = $decAmount;
        $rslt = $objPaymentInvoice->Save();
        if(!$rslt){
            return false;
        }
        return true;
    }
}