<?php
namespace NsINV;

class ClsCtrlServiceInvoice extends \NsINV\ClsCtrlServiceInv {

    protected function do_Default(){}

   protected function do_List(){
        $arrPage = $this->_payload['objPage'];
        $arrFilter = $this->_payload['objFilter'];
        $intPageNo = 1;
        if (isset($arrPage['intPageNo'])){
            $intPageNo = $arrPage['intPageNo'];
        }

        $intPageSize = $this->_config['ListingPageSize'];
        if (isset($arrPage['intPageSize'])){
            $intPageSize = $arrPage['intPageSize'];
        }

        $objFilter = new \NsINV\ClsFilterInvoice();

        if(isset($arrFilter['strInvoiceNumber']) && $arrFilter['strInvoiceNumber'] != ''){
            $objFilter->strInvoiceNumber = $arrFilter['strInvoiceNumber'];
        }
        if(isset($arrFilter['strReference']) && $arrFilter['strReference'] != ''){
            $objFilter->strReference = $arrFilter['strReference'];
        }
        if(isset($arrFilter['intCreatedByUserID']) && $arrFilter['intCreatedByUserID'] != ''){
            $objFilter->intCreatedByUserID = $arrFilter['intCreatedByUserID'];
        }
        if(isset($arrFilter['strCustomerName']) && $arrFilter['strCustomerName'] != ''){
            $objFilter->strCustomerName = $arrFilter['strCustomerName'];
        }
        if(isset($arrFilter['intCustomerID']) && $arrFilter['intCustomerID'] != ''){
            $objFilter->intCustomerID = $arrFilter['intCustomerID'];
        }
        if(isset($arrFilter['intPaymentTermID']) && $arrFilter['intPaymentTermID'] != ''){
            $objFilter->intPaymentTermID = $arrFilter['intPaymentTermID'];
        }
        if(isset($arrFilter['intStatusID']) && $arrFilter['intStatusID'] != ''){
            $objFilter->intStatusID = $arrFilter['intStatusID'];
        }
        if(isset($arrFilter['dtIssueDateFrom']) && $arrFilter['dtIssueDateFrom'] != ''){
            $objFilter->dtIssueDateFrom = $arrFilter['dtIssueDateFrom'];
        }
        if(isset($arrFilter['dtIssueDateTo']) && $arrFilter['dtIssueDateTo'] != ''){
            $objFilter->dtIssueDateTo = $arrFilter['dtIssueDateTo'];
        }
        if(isset($arrFilter['dtDueDateFrom']) && $arrFilter['dtDueDateFrom'] != ''){
            $objFilter->dtDueDateFrom = $arrFilter['dtDueDateFrom'];
        }
        if(isset($arrFilter['dtDueDateTo']) && $arrFilter['dtDueDateTo'] != ''){
            $objFilter->dtDueDateTo = $arrFilter['dtDueDateTo'];
        } 

        if(isset($arrFilter['boolShowOverdue']) && $arrFilter['boolShowOverdue'] != false){
            $objFilter->boolShowOverdue = $arrFilter['boolShowOverdue'];
        } 


        $objInvoice = new ClsBllInvoice();
        $arrPage = $objInvoice->GetDataPageAssociative($intPageNo, $intPageSize,$objFilter->GetWhereStatement(), '', $intPageCount, $intRowCount);

        $arrData = array('arrData'=>$arrPage, 'intTotal'=>$intRowCount);
        $arr['result'] = true;
        $arr['title'] = 'Success';
        $arr['message'] = 'Invoices filtered successfully';
        $arr['object'] = $arrData;
        print json_encode($arr);
    }

    protected function before_View(){
        if (!isset($this->_data['invoice_id']) or !is_numeric($this->_data['invoice_id'])){
            $arr['result'] = false;
            $arr['message'] = 'No invoice id specified.';
            print json_encode($arr);
            return false;                  
        }
        return true;
    }
    protected function do_View(){
        $intInvoiceID = $this->_data['invoice_id'];
        $objInvoice = new ClsBllInvoice();
        $rslt = $objInvoice->LoadByID($intInvoiceID);
        if(!$rslt){
            $arr['title'] = "Error";
            $arr['result'] = false;
            $arr['message'] = 'Invoice not found';
            print json_encode($arr);
            return;
        }
        $objInvoice->arrInvoiceRow;
        $objInvoice->arrInvoicePayment;
        $objInvoice->objUser;
        $objInvoice->objCustomer;
        $objInvoice->objPaymentTerm;
        $objInvoice->objStatus;
        $arr['result'] = true;
        $arr['title'] = 'Success';
        $arr['message'] = 'Invoice loaded successfully';
        $arr['object'] = $objInvoice->ToArray();
        print json_encode($arr);
    }

    protected function before_Add(){ 
        if (!isset($this->_payload['objInvoice']) || !isset($this->_payload['arrInvoiceRow'])) {
            $arr['result'] = false;
            $arr['title'] = "Error";
            $arr['message'] = 'You must fill all inputs';
            print json_encode($arr);
            return false;                   
        } else {
            $arrInvoices = $this->_payload['objInvoice'];
            $arrInvoiceRow = $this->_payload['arrInvoiceRow'];
            switch (true) {
                case empty($arrInvoices['objCustomer']):
                case $arrInvoices['objCustomer'] == null:
                case empty($arrInvoices['objCustomer']['intID']):
                case empty($arrInvoices['objPaymentTerm']):
                case $arrInvoices['objPaymentTerm']['intID'] == -1:
                    $arr['result'] = false;
                    $arr['title'] = 'Error';
                    $arr['message'] = 'You must fill all inputs';
                    print json_encode($arr);
                    return false;                   
                    break;
            }
            foreach($arrInvoiceRow as $objInvoiceRow){
                switch (true) {
                    case empty($objInvoiceRow['strType']):
                    case empty($objInvoiceRow['intQuantity']):
                    case empty($objInvoiceRow['intUnitPrice']):
                    case $objInvoiceRow['strType'] == '':
                    case $objInvoiceRow['intQuantity'] == -1:
                    case $objInvoiceRow['intUnitPrice'] == -1:
                        $arr['result'] = false;
                        $arr['title'] = 'Error';
                        $arr['message'] = 'You must fill all inputs';
                        print json_encode($arr);
                        return false;                   
                        break;
                } 
            }
        }
        return true;
    }
    protected function do_Add(){  
        $arrInvoice = $this->_payload['objInvoice'];
        $arrInvoiceRow = $this->_payload['arrInvoiceRow'];
        // get user id 
        $arrInvoice['intCreatedByUserID'] = $this->_session->objUser->intID;

        $objInvoice = ClsBllInvoice::Create($arrInvoice ,$arrInvoiceRow);

        if (!$objInvoice) {
            $arr['result'] = false;
            $arr['title'] = "Error ";
            $arr['message'] = 'Failed to save Invoice';
        }else {
            $arr['result'] = true;
            $arr['title'] = 'Success';
            $arr['message'] = 'Invoice successfully Added';
            $arr['object'] = $objInvoice->ToArray();
        }
        print json_encode($arr);
    }

    protected function before_Update(){
        if (!isset($this->_payload['objInvoice']) || !isset($this->_payload['arrInvoiceRow'])) {
            $arr['result'] = false;
            $arr['title'] = "Error";
            $arr['message'] = 'You must fill all inputs';
            print json_encode($arr);
            return false;                   
        } else {
            $arrInvoices = $this->_payload['objInvoice'];
            $arrInvoiceRow = $this->_payload['arrInvoiceRow'];
            switch (true) {
                case empty($arrInvoices['objCustomer']):
                case $arrInvoices['objCustomer'] == null:
                case empty($arrInvoices['objCustomer']['intID']):
                case empty($arrInvoices['intPaymentTermID']):
                case $arrInvoices['intPaymentTermID'] == -1:
                    $arr['result'] = false;
                    $arr['title'] = 'Error';
                    $arr['message'] = 'You must fill all inputs';
                    print json_encode($arr);
                    return false;                   
                    break;
            }
            foreach($arrInvoiceRow as $objInvoiceRow){
                switch (true) {
                    case empty($objInvoiceRow['strType']):
                    case empty($objInvoiceRow['intQuantity']):
                    case empty($objInvoiceRow['intUnitPrice']):
                    case $objInvoiceRow['strType'] == '':
                    case $objInvoiceRow['intQuantity'] == -1:
                    case $objInvoiceRow['intUnitPrice'] == -1:
                        $arr['result'] = false;
                        $arr['title'] = 'Error';
                        $arr['message'] = 'You must fill all inputs';
                        print json_encode($arr);
                        return false;                   
                        break;
                } 
            }
        }
        return true;
    }
    protected function do_Update(){
        $arrInvoice = $this->_payload['objInvoice'];
        $arrInvoiceRow = $this->_payload['arrInvoiceRow']; 
        // check invoicde existing
        $objInvoice =  new ClsBllInvoice();
        $rslt = $objInvoice->LoadByID($arrInvoice['intID']);
        if(!$rslt){
            $arr['result'] = false;
            $arr['message'] = 'Invoice not found';
            print json_encode($arr);
            return;
        }
        $objInvoice->_data['strReference'] = $arrInvoice['strReference'];
        $objInvoice->_data['dtIssueDate'] = $arrInvoice['dtIssueDate'];
        $objInvoice->_data['dtDueDate'] = $arrInvoice['dtDueDate'];
        $objInvoice->_data['intCustomerID'] = $arrInvoice['objCustomer']['intID'];
        $objInvoice->_data['intPaymentTermID'] =  $arrInvoice['objPaymentTerm']['intID'];

        $rslt = $objInvoice->Update($arrInvoiceRow);

        if (!$rslt) {
            $arr['result'] = false;
            $arr['title'] = "Error ";
            $arr['message'] = 'Failed to update Invoice';
        }else{
            $arr['result'] = true;
            $arr['title'] = 'Success';
            $arr['message'] = 'Invoice and invoice rows successfully Updated';
            $arr['object'] = $objInvoice->ToArray();
        }
        print json_encode($arr);
    }

    protected function before_Delete(){
        if (!isset($this->_data['invoice_id']) or !is_numeric($this->_data['invoice_id'])){
            $arr['result'] = false;
            $arr['message'] = 'No invoice id specified.';
            print json_encode($arr);
            return false;                  
        }
        return true;
    }
    protected function do_Delete(){
        $intInvoiceID = $this->_data['invoice_id'];
        $objInvoice =  new ClsBllInvoice();
        $rslt = $objInvoice->LoadByID($this->_data['invoice_id']);
        if (!$rslt){
            $arr['result'] = false;
            $arr['title'] = 'Error';
            $arr['message'] = "No Invoice found with ID #$intInvoiceID";
            print json_encode($arr);
            return false;
        }
        if($rslt){         
            $rslt = $objInvoice->Delete();
            if($rslt){
                $arr['result'] = true;
                $arr['title'] = 'Success';
                $arr['message'] = 'Invoice deleted Successfully';
            }else{
                $arr['result'] = false;
                $arr['title'] = 'Error';
                $arr['message'] = "Invoice couldn't be deleted as there is another data related to it";
            }
            print json_encode($arr);
        }
    }

    protected function before_UpdateStatus(){
        if (!isset($this->_data['invoice_id']) or !is_numeric($this->_data['invoice_id'])){
            $arr['result'] = false;
            $arr['message'] = 'No invoice id specified.';
            print json_encode($arr);
            return false;                  
        }
        if (!isset($this->_data['status_id']) or !is_numeric($this->_data['status_id'])){
            $arr['result'] = false;
            $arr['message'] = 'No status id specified.';
            print json_encode($arr);
            return false;                  
        }
        return true;
    }
    protected function do_UpdateStatus(){
        $intInvoiceID = $this->_data['invoice_id'];
        $intStatusID = $this->_data['status_id'];
        $objInvoice =  new ClsBllInvoice();
        $rslt = $objInvoice->LoadByID($intInvoiceID);
        if(!$rslt){
            $arr['title'] = "Error";
            $arr['result'] = false;
            $arr['message'] = 'Invoice not found';
            print json_encode($arr);
            return;
        }  
        $objInvoice->_data['intStatusID'] = $intStatusID;
        $rslt = $objInvoice->Save();
        if($rslt){       
            $objInvoice->objStatus;
            $arr['title'] = 'Success';
            $arr['result'] = true;
            $arr['message'] = 'Invoice Status has been updated successfully';    
            $arr['object'] = $objInvoice->ToArray();
        }else{
            $arr['title'] = 'Error';
            $arr['result'] = false;
            $arr['message'] = "Can't update Statut";
        }
        print json_encode($arr);
    }

    protected function do_GetNextInvoiceNumber(){
        $intMaxInvoiceNum = ClsBllInvoice::GetMaxNumber();
        $arr['intNextInvoiceNumber'] =  str_pad($intMaxInvoiceNum + 1 ,strlen($intMaxInvoiceNum) , "0", STR_PAD_LEFT );
        print json_encode($arr);
    }


    protected function do_ListByCustomerID(){
        $intCustomerID = $this->_data['customer_id'];
        $objInvocie = new ClsBllInvoice();
        $rslt = $objInvocie->getCustomerInvoices($intCustomerID); 
        if (!$rslt){
            $arr['result'] = false;
            $arr['title'] = 'Error';
            $arr['message'] = "No Invoices found for customer ID # $intCustomerID";
            print json_encode($arr);
            return false;
        }
        if($rslt){        
            if($rslt){
                $arr['result'] = true;
                $arr['title'] = 'Success';
                $arr['message'] = 'Invoice retirieved Successfully';
                $arr['object'] = $rslt;
                print json_encode($arr);
                return true;
            }
        } 
    }

    protected function do_GetByID(){
        $intInvoiceID = $this->_data['invoice_id'];
        $objInvocie = new ClsBllInvoice();
        $rslt = $objInvocie->LoadByID($intInvoiceID);
        if (!$rslt){
            $arr['result'] = false;
            $arr['title'] = 'Error';
            $arr['message'] = "No Invoice found!";
            print json_encode($arr);
            return false;
        }
        if($rslt){
            $arr['result'] = true;
            $arr['title'] = 'Success';
            $arr['message'] = 'Invoice retirieved Successfully';
            $arr['object'] = ($objInvocie->_data);
            print json_encode($arr);
            return true;
        }
    }
}