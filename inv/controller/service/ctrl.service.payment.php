<?php
namespace NsINV;

class ClsCtrlServicePayment extends \NsINV\ClsCtrlServiceInv {

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

        $objFilter = new \NsINV\ClsFilterPayment();
        if(isset($arrFilter['intPaymentMethodID']) && $arrFilter['intPaymentMethodID'] != -1){
            $objFilter->intPaymentMethodID = $arrFilter['intPaymentMethodID'];
        }   

        if(isset($arrFilter['strCustomerName']) && $arrFilter['strCustomerName'] != ''){
            $objFilter->strCustomerName = $arrFilter['strCustomerName'];
        }
        if(isset($arrFilter['intPaymentNumber']) && $arrFilter['intPaymentNumber'] != ''){
            $objFilter->intPaymentNumber = $arrFilter['intPaymentNumber'];
        }
        if(isset($arrFilter['strReference']) && $arrFilter['strReference'] != ''){
            $objFilter->strReference = $arrFilter['strReference'];
        }
        if(isset($arrFilter['dtDateBefore']) && $arrFilter['dtDateBefore'] != ''){
            $objFilter->dtDateBefore = $arrFilter['dtDateBefore'];
        }
        if(isset($arrFilter['dtDateAfter']) && $arrFilter['dtDateAfter'] != ''){
            $objFilter->dtDateAfter = $arrFilter['dtDateAfter'];
        }
        if(isset($arrFilter['intCreatedByUserID']) && $arrFilter['intCreatedByUserID'] != -1){
            $objFilter->intCreatedByUserID = $arrFilter['intCreatedByUserID'];
        }
        if(isset($arrFilter['dtCreatedON']) && $arrFilter['dtCreatedON'] != ''){
            $objFilter->dtCreatedON = $arrFilter['dtCreatedON'];
        }
        if(isset($arrFilter['decAmount']) && $arrFilter['decAmount'] != ''){
            $objFilter->decAmount = $arrFilter['decAmount'];
        }
        if(isset($arrFilter['strAmountOperator'])){
            $objFilter->strAmountOperator = $arrFilter['strAmountOperator'];
        }
        $objPayment = new ClsBllManualPayment();
        $arrPage = $objPayment->GetDataPageAssociative($intPageNo, $intPageSize,$objFilter->GetWhereStatement(), 'fkCustomerID ASC', $intPageCount, $intRowCount);
        $arrData = array('arrData'=>$arrPage, 'intTotal'=>$intRowCount);
        $arr['result'] = true;
        $arr['title'] = 'Success';
        $arr['message'] = 'Payments listed successfully';
        $arr['object'] = $arrData;
        print json_encode($arr);
        return true;
    }

    protected function before_Add(){
        if (!isset($this->_payload['objPayment'])) {
            $arr['result'] = false;
            $arr['title'] = "payload empty";
            $arr['message'] = $this->cLang('LNG_6613');
            print json_encode($arr);
            return false;                   
        } 
        $objPaymentAdd = $this->_payload['objPayment'];
        switch (true) {
            case ($objPaymentAdd['intPaymentMethodID'] == ''):
                $arr['result'] = false;
                $arr['title'] = 'Error';
                $arr['message'] = 'You must choose payment method!';
                print json_encode($arr);
                return false;                   
                break;
            case ($objPaymentAdd['dtDate'] == ''):
                $arr['result'] = false;
                $arr['title'] = 'Error';
                $arr['message'] = 'You must choose date!';
                print json_encode($arr);
                return false;                   
                break;
            case ($objPaymentAdd['decAmount'] == ''):
                $arr['result'] = false;
                $arr['title'] = 'Error';
                $arr['message'] = 'You must write payment amount!';
                print json_encode($arr);
                return false;                   
                break;
            case ($objPaymentAdd['intCustomerID'] == ''):
                $arr['result'] = false;
                $arr['title'] = 'Error';
                $arr['message'] = 'You must write customer name!';
                print json_encode($arr);
                return false;                   
                break;
        }
        return true;
    }
    protected function do_Add(){
        $arrPaymentAdd = $this->_payload['objPayment'];
        $rsltAdd = ClsBllManualPayment::CreateManualPayment($arrPaymentAdd);
        if($rsltAdd){
            if($rsltAdd === -1) {
                $arrResult['result'] = false;
                $arrResult['title'] = 'Error';
                $arrResult['message'] = 'Customer id is not correct!';
            }
            if($rsltAdd === -2) {
                $arrResult['result'] = false;
                $arrResult['title'] = 'Error';
                $arrResult['message'] = 'No invoices are open for this customer!';
            }
            else if ($rsltAdd === -3) {
                $arrResult['result'] = false;
                $arrResult['title'] = 'Error';
                $arrResult['message'] = 'Total due amount for this customer is less than payment amount!';
            }
            else{
                $arrResult['result'] = true;
                $arrResult['title'] = 'Success';
                $arrResult['message'] = 'Payment successfully added';
                $arrResult['object'] = $rsltAdd->ToArray(); 
            }
            print json_encode($arrResult);
            return true;
        }else{
            $arrResult['result'] = false;
            $arrResult['title'] = 'Error';
            $arrResult['message'] = 'Failed to add payment!';
            print json_encode($arrResult);
            return true;
        }
    }
    protected function before_Delete(){
        if (!isset($this->_data['payment_id']) or !is_numeric($this->_data['payment_id'])){
            $arr['result'] = false;
            $arr['message'] = 'Please specify correct payment id.';
            print json_encode($arr);                  
            return false;
        }
        $intPaymentID = $this->_data['payment_id'];
        $objPayment =  new ClsBllManualPayment();
        $rslt = $objPayment->LoadByID($intPaymentID);
        if (!$rslt){
            $arr['result'] = false;
            $arr['title'] = 'Error';
            $arr['message'] = "No payment found with ID #$intPaymentID";
            print json_encode($arr);
            return false;
        }
        return true;
    }
    protected function do_Delete(){
        $objPayment =  new ClsBllManualPayment();
        $ObjRslt = $objPayment->DeletePayment($this->_data['payment_id']);
        if($ObjRslt['result']){
            $arr['result'] = true;
            $arr['title'] = 'Success';
            $arr['message'] = 'Payment deleted Successfully';
        }else{
            $arr['result'] = false;
            $arr['title'] = 'Error';
            $arr['message'] = "Payment couldn't be deleted as there is another data related to it";
        }
        print json_encode($arr);
        return true;
    }
    protected function before_ListInvoices(){
        if(!isset($this->_data['payment_id']) or !is_numeric($this->_data['payment_id'])){
            $arr['result'] = false;
            $arr['message'] = 'Please specify correct payment id.';
            print json_encode($arr);                  
            return false; 
        }
        return true;
    }
    protected function do_ListInvoices(){
        $intPaymentID = $this->_data['payment_id'];
        $objPayment =  new ClsBllManualPayment();
        $rslt = $objPayment->LoadByID($intPaymentID);
        if (!$rslt){
            $arr['result'] = false;
            $arr['title'] = 'Error';
            $arr['message'] = "No payment found with ID #$intPaymentID";
            print json_encode($arr);
            return false;
        }
        $rslt = $objPayment->GetInvoices();
        if($rslt) {
            $arr['result'] = true;
            $arr['title'] = 'Success';
            $arr['message'] = 'Invoices listed Successfully';
            $arr['object'] = $rslt;
        }else{
            $arr['result'] = false;
            $arr['title'] = 'Error';
            $arr['message'] = "Couldn't list invoices for this payment!";        
        }
        print json_encode($arr);
        return true;
    }
}