<?php
namespace NsINV;

class ClsCtrlServicePaymentTerm extends \NsINV\ClsCtrlServiceInv {

    protected function do_Default(){}

    protected function do_ListAll(){
        $obj = new \NsINV\ClsBllPaymentTerm();
        $arrData = $obj->GetAllPaymentTerms();
        $arr['result'] = true;
        $arr['title'] = 'Success';
        $arr['message'] = 'Cities successfully listed';
        $arr['object'] = $arrData;
        print json_encode($arr);
    }

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
        $objFilter = new \NsFWK\ClsFilter();
        if(isset($arrFilter['strName']) && $arrFilter['strName'] != ''){
            $strName = $arrFilter['strName'];
            $objFilter->strName = "fldPaymentTermName LIKE '%$strName%'";
        }
        $objPaymentterm = new ClsBllPaymentTerm();
        $arrPage = $objPaymentterm->GetDataPageAssociative($intPageNo, $intPageSize,$objFilter->GetWhereStatement(), '', $intPageCount, $intRowCount);
        $arrData = array('arrData'=>$arrPage, 'intTotal'=>$intRowCount);

        $arr['result'] = true;
        $arr['title'] = 'Success';
        $arr['message'] = 'Payment Term filtered successfully';
        $arr['object'] = $arrData;
        print json_encode($arr);
        return true;
    }


    protected function before_Add(){
        if (!isset($this->_payload['objPaymentTerm'])) {
            $arr['result'] = false;
            $arr['title'] = $this->cLang('LNG_6616');
            $arr['message'] = $this->cLang('LNG_6613');
            print json_encode($arr);
            return false;                   
        } else {
            $arrPaymentTerm = $this->_payload['objPaymentTerm'];
            switch (true) {
                case empty($arrPaymentTerm['strName']):
                    $arr['result'] = false;
                    $arr['title'] = 'failure';
                    $arr['message'] = 'You must fill all inputs';
                    print json_encode($arr);
                    return false;                   
                    break;
            }
        }

        return true;
    }
    protected function do_Add(){
        $objPaymentTerm = new ClsBllPaymentTerm();
        $arrPaymentTerm = $this->_payload['objPaymentTerm'];
        $objPaymentTerm->strName = $arrPaymentTerm['strName'];
        $objPaymentTerm->strDescription = $arrPaymentTerm['strDescription'];
        $rslt = $objPaymentTerm->Save();
        if(!$rslt){
            $arr['result'] = false;
            $arr['title'] = 'Error';
            $arr['message'] = 'Failed to save Payment Term';
            print json_encode($arr); 
            return;   
        }
        $arr['result'] = true;
        $arr['title'] = 'Success';
        $arr['message'] = 'Payment Term successfully listed';
        $arr['object'] = $objPaymentTerm->ToArray();
        print json_encode($arr);
    }
     
    protected function before_Update(){

        if (!isset($this->_payload['objPaymentTerm'])) {
            $arr['result'] = false;
            $arr['title'] = $this->cLang('LNG_6616');
            $arr['message'] = $this->cLang('LNG_6613');
            print json_encode($arr);
            return false;                   
        } else {
            $arrPaymentTerm = $this->_payload['objPaymentTerm'];
            switch (true) {
                case empty($arrPaymentTerm['strName']):
                case $arrPaymentTerm['boolIsReserved'] == true:
                    $arr['result'] = false;
                    $arr['title'] = $this->cLang('Error');
                    $arr['message'] = $this->cLang('You must fill all inputs');
                    print json_encode($arr);
                    return false;                   
                    break;
            }
        }
        return true;

    }
    protected function do_Update(){
        $arrPaymentTerm = $this->_payload['objPaymentTerm'];
        $objPaymentTerm =  new ClsBllPaymentTerm();
        $rslt = $objPaymentTerm->LoadByID($arrPaymentTerm['intID']);

        if(!$rslt){
            $arr['result'] = false;
            $arr['message'] = 'Payment Term not found';
            $arr['object'] = $arrPaymentTerm;
            print json_encode($arr);
            return;
        }
        $objPaymentTerm->strName = $arrPaymentTerm['strName'];
        $objPaymentTerm->strDescription = $arrPaymentTerm['strDescription'];

        $rslt = $objPaymentTerm->Save();
        if($rslt){
            $strMsg = 'Payment Term has been updated successfully';                                                                                                    
            $arr['title'] = 'Success';
            $arr['result'] = true;
            $arr['message'] = $strMsg;
            $arr['object'] = $objPaymentTerm->ToArray();
        }else{
            $arr['title'] = 'Error';
            $arr['result'] = false;
            $arr['message'] = 'Error in updating Payment Term';
        }
        print json_encode($arr);
    }

    protected function before_Delete(){
        if (!isset($this->_data['paymentterm_id']) or !is_numeric($this->_data['paymentterm_id'])){
            $arr['result'] = false;
            $arr['message'] = 'No payment term id specified.';
            print json_encode($arr);
            return false;                  
        }
        return true;
    }
    protected function do_Delete(){
        $intPaymentTerm = $this->_data['paymentterm_id'];
        $objPaymentTerm =  new ClsBllPaymentTerm();
        $rslt = $objPaymentTerm->LoadByID($this->_data['paymentterm_id']);
        if (!$rslt){
            $arr['result'] = false;
            $arr['title'] = 'Error';
            $arr['message'] = "No PaymentTerm found with ID #$intPaymentTerm";
            print json_encode($arr);
            return false;
        }
        if($rslt){         
            $rslt = $objPaymentTerm->Delete();
            if($rslt){
                $arr['result'] = true;
                $arr['title'] = 'Success';
                $arr['message'] = 'PaymentTerm deleted Successfully';
            }else{
                $arr['result'] = false;
                $arr['title'] = 'Error';
                $arr['message'] = "PaymentTerm couldn't be deleted as there is another data related to it";
            }
            print json_encode($arr);
        }
    }



}