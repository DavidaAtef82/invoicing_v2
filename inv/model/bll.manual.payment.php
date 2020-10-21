<?php
namespace NsINV;
class ClsBllManualPayment extends ClsBllPayment{
    protected function _save(\ADODB_Active_Record $objDAL){
        if($this->getIsLoaded()){
            // UPDATE
            return false;
        } else {
            // NEW
            $objDAL->fldCreatedOnTimestamp = date("Y-m-d H:i:s");
        }

        $objDAL->fkPaymentMethodID = $this->_data['intPaymentMethodID'];
        $objDAL->fkCustomerID = $this->_data['intCustomerID'];
        $objDAL->fldPaymentNumber = $this->_data['intPaymentNumber'];
        $objDAL->fldReference = $this->_data['strReference'];
        $objDAL->fldDate = $this->_data['dtDate'];
        $objDAL->fldAmount = $this->_data['decAmount'];  
        $objDAL->fkCreatedByUserID = $this->_data['intCreatedByUserID'];  
        $rslt = $objDAL->Save();

        if($rslt){
            $this->_data['intID'] = $objDAL->pkPaymentID;
        }
        return $rslt;
    }
    /***
    * This function creates a new payment
    * 
    * @param mixed $arrPayment includes the following keys:
    *   intPaymentMethodID
    *   intCustomerID
    *   strReference
    *   dtDate
    *   decAmount
    *   intCreatedByUserID
    * @return ClsBllPayment in case of success
    *         -1 if no customer id is found with given id.
    *         -2 if no invoices are open for this customer.
    *         -3 if remaining amount is less than payment amount. 
    *         0 if database error occuerd   
    */
    static function CreateManualPayment($arrPayment) {
        $DB = &\ADODB_Connection_Manager::GetConnection('customer');
        $intCustomerID = $arrPayment['intCustomerID'];
        $decPaymentAmount = $arrPayment['decAmount'];
        $objCustomer = new ClsBllCustomer();
        $rslt = $objCustomer->LoadByID($intCustomerID);
        // 0. Check if no customer id is found with given id.
        if(!$rslt){
            return -1;
        }
        $arrInvoiceTotalDueAnount = $objCustomer->arrInvoiceTotalDueAnount;
        $decTotalDueAmount = $objCustomer->decTotalDueAmount;
        $intNumberOfDueInvoices = $objCustomer->intNumberOfDueInvoices;
        // 1. Check if no invoices are open for this customer.
        if(!$intNumberOfDueInvoices){
            return -2;
        }
        // 2. Check if remaining amount is less than payment amount.
        if ($decTotalDueAmount < $decPaymentAmount) {
            return -3;
        }
        $DB->StartTrans();
        // 3. Add payment in inv-payment table
        $objPayment = new self();
        $objPayment->intPaymentMethodID =  $arrPayment['intPaymentMethodID'];
        $objPayment->intCustomerID = $arrPayment['intCustomerID'];
        $objPayment->intPaymentNumber  =  ClsBllPayment::getNextSearialNumber();
        $objPayment->strReference =  $arrPayment['strReference'];
        $objPayment->dtDate =  date('Y-m-d', strtotime($arrPayment['dtDate']));
        $objPayment->decAmount =  $arrPayment['decAmount'];
        $objPayment->intCreatedByUserID = $arrPayment['intCreatedByUserID'];
        $rslt = $objPayment->Save();
        if($rslt){
            $intPaymentID = $objPayment->intID;
        }else{                
            return $DB->CompleteTrans(false);
        } 
        // 4. Assign payment to invoices and stop when payment amount is = 0 or invoices are done!
        foreach($arrInvoiceTotalDueAnount as $intInvoiceID => $decDueAmount){
            $objInvoice = new ClsBllInvoice();
            $objInvoice->LoadByID($intInvoiceID);
            $rslt = $objInvoice->AddPayment($decPaymentAmount, $intPaymentID);
            if (!$rslt) {
                // Operation failed
                return $DB->CompleteTrans(false);
            }
            if ($decPaymentAmount === 0) {
                // No remaining amount for more invoice-payment records
                break;
            }
        }

        $rslt = $DB->CompleteTrans();
        if ($rslt) {
            return $objPayment;
        } else {
            return false;
        }
    }
}