<?php
namespace NsINV;

class ClsCtrlServiceTaxtype extends \NsINV\ClsCtrlServiceInv {

    protected function do_Default(){}

    protected function do_ListAll(){
        $objTax = new ClsBllTaxType();
        $arrData = $objTax->GetTaxes();
        $arr['result'] = true;
        $arr['title'] = 'Success';
        $arr['message'] = 'Taxes retrieved!';
        $arr['object'] = $arrData;
        print json_encode($arr);
        return true;
    }
    protected function before_Add(){
        if (!isset($this->_payload['objTaxType'])) {
            $arr['result'] = false;
            $arr['title'] = "payload empty";
            $arr['message'] = $this->cLang('LNG_6613');
            print json_encode($arr);
            return false;                   
        } else {
            $objTax = $this->_payload['objTaxType'];
            switch (true) {
                case $objTax['strType'] == '':
                    $arr['result'] = false;
                    $arr['title'] = 'Failure';
                    $arr['message'] = 'You must fill type';
                    print json_encode($arr);
                    return false;                   
                    break;
            }
        }
        return true;
    }
    protected function do_Add(){
        $objTax = new ClsBllTaxType();
        $arrTax = $this->_payload['objTaxType'];

        // check if item is exist or not by Name --> 
        $rslt = $objTax->IsExist($arrTax['strTaxType']);
        if($rslt){
            $arr['result'] = false;
            $arr['title'] = 'Error';
            $arr['message'] = 'your new tax type is already exist please change the name';
            print json_encode($arr); 
            return;    
        }

        $objTax->strTaxType =  $arrTax['strTaxType'];
        $objTax->strType =  $arrTax['strType'];
        $objTax->intValue =  $arrTax['intValue'];
        $rslt = $objTax->Save();

        if(!$rslt){
            $arr['result'] = false;
            $arr['title'] = 'Error';
            $arr['message'] = 'Failed to add tax!';
            print json_encode($arr); 
            return;   
        }
        $arr['result'] = true;
        $arr['title'] = 'Success';
        $arr['message'] = 'Tax successfully added';
        $arr['object'] = $objTax->ToArray();
        print json_encode($arr);
    }

    protected function before_Update(){
        if (!isset($this->_payload['objTaxType'])) {
            $arr['result'] = false;
            $arr['title'] = $this->cLang('LNG_6616');
            $arr['message'] = $this->cLang('LNG_6613');
            print json_encode($arr);
            return false;                   
        }
        return true;

    }
    protected function do_Update(){
        $arrTax = $this->_payload['objTaxType'];
        $objTax =  new ClsBllTaxType();

        $rslt = $objTax->LoadByID($arrTax['intID']);
        if(!$rslt){
            $arr['result'] = false;
            $arr['message'] = 'Tax not found';
            $arr['object'] = $arrMethod;
            print json_encode($arr);
            return;
        }
        // check if the new name of tax type is already exist or not 
        if($objTax->strTaxType != $arrTax['strTaxType']){
            $rslt = $objTax->IsExist($arrTax['strTaxType']);
            if($rslt){
                $arr['result'] = false;
                $arr['title'] = 'Error';
                $arr['message'] = 'your Tax Type is already exist please change the tax type name';
                print json_encode($arr); 
                return;    
            }
        }
        // check if the tax type is already taken or not
        $objTax->strTaxType = $arrTax['strTaxType'];
        if($objTax->strTaxType != $arrTax['strTaxType']){
            $arr['title'] = 'Error';
            $arr['result'] = false;
            $arr['message'] = 'the tax type is already used';
            print json_encode($arr);
            return;
        }

        $objTax->strType =  $arrTax['strType'];
        $objTax->intValue =  $arrTax['intValue'];
        $rslt = $objTax->Save();
        if($rslt){
            $strMsg = 'Tax has been updated successfully';                                                                                                    
            $arr['title'] = 'Success';
            $arr['result'] = true;
            $arr['message'] = $strMsg;
            $arr['object'] = $objTax->ToArray();
        }else{
            $arr['title'] = 'Error';
            $arr['result'] = false;
            $arr['message'] = 'Error in updating tax';
        }
        print json_encode($arr);
    }

    protected function before_Delete(){
        if (!isset($this->_data['tax_type_id']) or !is_numeric($this->_data['tax_type_id'])){
            $arr['result'] = false;
            $arr['message'] = 'No tax id specified.';
            print json_encode($arr);
            return false;                  
        }
        return true;
    }
    protected function do_Delete(){
        $intTaxID = $this->_data['tax_type_id'];
        $objTax =  new ClsBllTaxType();
        $rslt = $objTax->LoadByID($this->_data['tax_type_id']);
        if (!$rslt){
            $arr['result'] = false;
            $arr['title'] = 'Error';
            $arr['message'] = "No tax found with ID #$intTaxID";
            print json_encode($arr);
            return false;
        }
        if($rslt){         
            $rslt = $objTax->Delete();
            if($rslt){
                $arr['result'] = true;
                $arr['title'] = 'Success';
                $arr['message'] = 'Tax deleted Successfully';
            }else{
                $arr['result'] = false;
                $arr['title'] = 'Error';
                $arr['message'] = "Tax couldn't be deleted as there is another data related to it";
            }
            print json_encode($arr);
        }
    }


}
