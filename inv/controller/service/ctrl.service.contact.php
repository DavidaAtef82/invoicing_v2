<?php
namespace NsINV;

class ClsCtrlServiceContact extends \NsINV\ClsCtrlServiceInv {

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

        $objFilter = new \NsINV\ClsFilterContact();
        if(isset($arrFilter['strName']) && $arrFilter['strName'] != ''){
            $objFilter->strName = $arrFilter['strName'];
        }
        if(isset($arrFilter['strEmail']) && $arrFilter['strEmail'] != ''){
            $objFilter->strEmail = $arrFilter['strEmail'];
        }
        if(isset($arrFilter['strPhone']) && $arrFilter['strPhone'] != ''){
            $objFilter->strPhone = $arrFilter['strPhone'];
        }
        if(isset($arrFilter['strNotes']) && $arrFilter['strNotes'] != ''){
            $objFilter->strNotes = $arrFilter['strNotes'];
        }
        if(isset($arrFilter['intCustomerID']) && $arrFilter['intCustomerID'] != -1){
            $objFilter->intCustomerID = $arrFilter['intCustomerID'];
        }

        $objContact = new ClsBllContact();
        $arrPage = $objContact->GetDataPageAssociative($intPageNo, $intPageSize,$objFilter->GetWhereStatement(), '', $intPageCount, $intRowCount);
        $arrData = array('arrData'=>$arrPage, 'intTotal'=>$intRowCount);

        $arr['result'] = true;
        $arr['title'] = 'Success';
        $arr['message'] = 'Contacts filtered successfully';
        $arr['object'] = $arrData;
        print json_encode($arr);
        return true;
    }


    protected function before_Add(){
        if (!isset($this->_payload['objContact'])) {
            $arr['result'] = false;
            $arr['title'] = $this->cLang('LNG_6616');
            $arr['message'] = $this->cLang('LNG_6613');
            print json_encode($arr);
            return false;                   
        } else {
            $arrContact = $this->_payload['objContact'];
            switch (true) {
                case empty($arrContact['strName']):
                case empty($arrContact['intCustomerID']):
                case $arrContact['intCustomerID'] == -1:
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
        $objContact = new ClsBllContact();
        $arrContact = $this->_payload['objContact'];
        $objContact->strName = $arrContact['strName'];
        $objContact->strEmail = $arrContact['strEmail'];   
        $objContact->strPhone = $arrContact['strPhone'];
        $objContact->strNotes = $arrContact['strNotes'];
        $objContact->intCustomerID = $arrContact['intCustomerID'];
        $rslt = $objContact->Save();
        if(!$rslt){
            $arr['result'] = false;
            $arr['title'] = 'Error';
            $arr['message'] = 'Failed to save Contact';
            print json_encode($arr); 
            return;   
        }
        $arr['result'] = true;
        $arr['title'] = 'Success';
        $arr['message'] = 'Contact successfully listed';
        $objContact->objCustomer;
        $arr['object'] = $objContact->ToArray();
        print json_encode($arr);
    }


    protected function before_Update(){
       
        if (!isset($this->_payload['objContact'])) {
            $arr['result'] = false;
            $arr['title'] = $this->cLang('LNG_6616');
            $arr['message'] = $this->cLang('LNG_6613');
            print json_encode($arr);
            return false;                   
        } else {
            $arrContacts = $this->_payload['objContact'];
            switch (true) {
                case empty($arrContacts['strName']):
                case empty($arrContacts['intCustomerID']):
                case $arrContacts['intCustomerID'] == -1:
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
      
         
        $arrContacts = $this->_payload['objContact'];
        $objContact =  new ClsBllContact();
        $rslt = $objContact->LoadByID($arrContacts['intID']);

        if(!$rslt){
            $arr['result'] = false;
            $arr['message'] = 'Contact not found';
            $arr['object'] = $arrContacts;
            print json_encode($arr);
            return;
        }
        $objContact->strName = $arrContacts['strName'];
        $objContact->strEmail = $arrContacts['strEmail'];  
        $objContact->strPhone = $arrContacts['strPhone'];
        $objContact->strNotes = $arrContacts['strNotes'];
        $objContact->intCustomerID = $arrContacts['intCustomerID'];  

        $rslt = $objContact->Save();
        if($rslt){
            $objContact->objCustomer;
            $strMsg = 'Contact has been updated successfully';                                                                                                    
            $arr['title'] = 'Success';
            $arr['result'] = true;
            $arr['message'] = $strMsg;
            $arr['object'] = $objContact->ToArray();
        }else{
            $arr['title'] = 'Error';
            $arr['result'] = false;
            $arr['message'] = 'Error in updating customer';
        }
        print json_encode($arr);
    }

    protected function before_Delete(){
        if (!isset($this->_data['contact_id']) or !is_numeric($this->_data['contact_id'])){
            $arr['result'] = false;
            $arr['message'] = 'No contact id specified.';
            print json_encode($arr);
            return false;                  
        }
        return true;
    }
    protected function do_Delete(){
        $intContactID = $this->_data['contact_id'];
        $objContact =  new ClsBllContact();
        $rslt = $objContact->LoadByID($this->_data['contact_id']);
        if (!$rslt){
            $arr['result'] = false;
            $arr['title'] = 'Error';
            $arr['message'] = "No Contact found with ID #$intContactID";
            print json_encode($arr);
            return false;
        }
        if($rslt){         
            $rslt = $objContact->Delete();
            if($rslt){
                $arr['result'] = true;
                $arr['title'] = 'Success';
                $arr['message'] = 'Contact deleted Successfully';
            }else{
                $arr['result'] = false;
                $arr['title'] = 'Error';
                $arr['message'] = "Contact couldn't be deleted as there is another data related to it";
            }
            print json_encode($arr);
        }
    }


}