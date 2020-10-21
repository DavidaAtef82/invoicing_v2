<?php
namespace NsINV;

class ClsCtrlServiceCustomer extends \NsINV\ClsCtrlServiceInv {

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

        $objFilter = new \NsINV\ClsFilterCustomer();
        if(isset($arrFilter['strName']) && $arrFilter['strName'] != ''){
            $objFilter->strName = $arrFilter['strName'];
        }
        if(isset($arrFilter['strAddress']) && $arrFilter['strAddress'] != ''){
            $objFilter->strAddress = $arrFilter['strAddress'];
        }
        if(isset($arrFilter['strPhone']) && $arrFilter['strPhone'] != ''){
            $objFilter->strPhone = $arrFilter['strPhone'];
        }
        if(isset($arrFilter['strNotes']) && $arrFilter['strNotes'] != ''){
            $objFilter->strNotes = $arrFilter['strNotes'];
        }
        if(isset($arrFilter['strTimestamp']) && $arrFilter['strTimestamp'] != ''){
            $objFilter->strTimestamp = $arrFilter['strTimestamp'];
        }
        if(isset($arrFilter['intCityID']) && $arrFilter['intCityID'] != ''){
            $objFilter->intCityID = $arrFilter['intCityID'];
        }
        if(isset($arrFilter['intCountryID']) && $arrFilter['intCountryID'] != ''){
            $objFilter->intCountryID = $arrFilter['intCountryID'];
        }

        $objCustomer = new ClsBllCustomer();
        $arrPage = $objCustomer->GetDataPageAssociative($intPageNo, $intPageSize,$objFilter->GetWhereStatement(), '', $intPageCount, $intRowCount);
        $arrData = array('arrData'=>$arrPage, 'intTotal'=>$intRowCount);

        $arr['result'] = true;
        $arr['title'] = 'Success';
        $arr['message'] = 'Customers filtered successfully';
        $arr['object'] = $arrData;
        print json_encode($arr);
        return true;
    }

    protected function do_ListAll(){
        $obj = new \NsINV\ClsBllCustomer();
        $arrData = $obj->GetAllCustomers();
        $arr['result'] = true;
        $arr['title'] = 'Success';
        $arr['message'] = 'Customers successfully listed';
        $arr['object'] = $arrData;
        print json_encode($arr);
    }

    protected function before_View(){
        if (!isset($this->_data['customer_id']) or !is_numeric($this->_data['customer_id'])){
            $arr['result'] = false;
            $arr['message'] = 'No customer id specified.';
            print json_encode($arr);
            return false;                  
        }
        return true;
    }
    protected function do_View(){
        $intCustomerID = $this->_data['customer_id'];
        $objCustomer = new ClsBllCustomer();
        $rslt = $objCustomer->LoadByID($intCustomerID);
        if(!$rslt){
            $arr['title'] = "Error";
            $arr['result'] = false;
            $arr['message'] = 'Customer not found';
            print json_encode($arr);
            return;
        }
        $objCustomer->objCity;
        $objCustomer->arrInvoice;
        $objCustomer->arrPayment;
        $arr['result'] = true;
        $arr['title'] = 'Success';
        $arr['message'] = 'Customer loaded successfully';
        $arr['object'] = $objCustomer->ToArray();
        print json_encode($arr);
    }



    protected function before_Add(){
        if (!isset($this->_payload['objCustomer'])) {
            $arr['result'] = false;
            $arr['title'] = $this->cLang('LNG_6616');
            $arr['message'] = $this->cLang('LNG_6613');
            print json_encode($arr);
            return false;                   
        } else {
            $arrCustomer = $this->_payload['objCustomer'];
            switch (true) {
                case empty($arrCustomer['strName']):
                case empty($arrCustomer['intCityID']):
                case $arrCustomer['intCityID'] == -1:
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
        $arrCustomer = $this->_payload['objCustomer'];

        $objCustomer =  new ClsBllCustomer();
        $rslt = $objCustomer->LoadByName($arrCustomer['strName']);

        if($rslt){
            $arr['result'] = false;
            $arr['message'] = 'Customer is already exist';
            $arr['object'] = $arrCustomer;
            print json_encode($arr);
            return;
        } 
        $objCustomer->strName = $arrCustomer['strName'];
        $objCustomer->strAddress = $arrCustomer['strAddress'];
        $objCustomer->strEmail = $arrCustomer['strEmail'];   
        $objCustomer->strPhone = $arrCustomer['strPhone'];
        $objCustomer->strNotes = $arrCustomer['strNotes'];
        $objCustomer->intCityID = $arrCustomer['intCityID'];
        $rslt = $objCustomer->Save();
        if(!$rslt){
            $arr['result'] = false;
            $arr['title'] = 'Error';
            $arr['message'] = 'Failed to save Customer';
            print json_encode($arr); 
            return;   
        }
        $arr['result'] = true;
        $arr['title'] = 'Success';
        $arr['message'] = 'Customer successfully listed';
        $objCustomer->objCity;
        $arr['object'] = $objCustomer->ToArray();
        print json_encode($arr);
    }


    protected function before_Update(){
        if (!isset($this->_payload['objCustomer'])) {
            $arr['result'] = false;
            $arr['title'] = $this->cLang('LNG_6616');
            $arr['message'] = $this->cLang('LNG_6613');
            print json_encode($arr);
            return false;                   
        } else {
            $arrCustomer = $this->_payload['objCustomer'];
            switch (true) {
                case empty($arrCustomer['strName']):
                case empty($arrCustomer['intCityID']):
                case $arrCustomer['intCityID'] == -1:
                    $arr['result'] = false;
                    $arr['title'] = $this->cLang('failure');
                    $arr['message'] = $this->cLang('You must fill all inputs');
                    print json_encode($arr);
                    return false;                   
                    break;
            }
        }
        return true;

    }
    protected function do_Update(){
        $arrCustomer = $this->_payload['objCustomer'];
        $objCustomer =  new ClsBllCustomer();
        $rslt = $objCustomer->LoadByID($arrCustomer['intID']);

        if(!$rslt){
            $arr['result'] = false;
            $arr['message'] = 'Customer not found';
            $arr['object'] = $arrCustomer;
            print json_encode($arr);
            return;
        }
        $objCustomer->strName = $arrCustomer['strName'];
        $objCustomer->strAddress = $arrCustomer['strAddress'];
        $objCustomer->strEmail = $arrCustomer['strEmail'];  
        $objCustomer->strPhone = $arrCustomer['strPhone'];
        $objCustomer->strNotes = $arrCustomer['strNotes'];
        $objCustomer->intCityID = $arrCustomer['intCityID'];  

        $rslt = $objCustomer->Save();
        if($rslt){
            $objCustomer->objCity;
            $strMsg = 'Customer has been updated successfully';                                                                                                    
            $arr['title'] = 'Success';
            $arr['result'] = true;
            $arr['message'] = $strMsg;
            $arr['object'] = $objCustomer->ToArray();
        }else{
            $arr['title'] = 'Error';
            $arr['result'] = false;
            $arr['message'] = 'Error in updating customer';
        }
        print json_encode($arr);
    }

    protected function before_Delete(){
        if (!isset($this->_data['customer_id']) or !is_numeric($this->_data['customer_id'])){
            $arr['result'] = false;
            $arr['message'] = 'No customer id specified.';
            print json_encode($arr);
            return false;                  
        }
        return true;
    }
    protected function do_Delete(){
        $intCustomerID = $this->_data['customer_id'];
        $objCustomer =  new ClsBllCustomer();
        $rslt = $objCustomer->LoadByID($this->_data['customer_id']);
        if (!$rslt){
            $arr['result'] = false;
            $arr['title'] = 'Error';
            $arr['message'] = "No Customer found with ID #$intCustomerID";
            print json_encode($arr);
            return false;
        }
        if($rslt){         
            $rslt = $objCustomer->Delete();
            if($rslt){
                $arr['result'] = true;
                $arr['title'] = 'Success';
                $arr['message'] = 'Customer deleted Successfully';
            }else{
                $arr['result'] = false;
                $arr['title'] = 'Error';
                $arr['message'] = "Customer couldn't be deleted as there is another data related to it";
            }
            print json_encode($arr);
        }
    }


}