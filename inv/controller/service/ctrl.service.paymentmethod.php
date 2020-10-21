<?php
namespace NsINV;

class ClsCtrlServicePaymentmethod extends \NsINV\ClsCtrlServiceInv {

    protected function do_Default(){}

    protected function do_List(){
        if(isset($this->_data['manual'])){
            $bolManual = $this->_data['manual'];
        }
        $objPaymentMethod = new ClsBllPaymentMethod();
        $arrData = $objPaymentMethod->GetPayments($bolManual);
        $arr['result'] = true;
        $arr['title'] = 'Success';
        $arr['message'] = 'Payment methods retrieved!';
        $arr['object'] = $arrData;
        print json_encode($arr);
        return true;
    }
    protected function before_Add(){
        if (!isset($this->_payload['objMethod'])) {
            $arr['result'] = false;
            $arr['title'] = "payload empty";
            $arr['message'] = $this->cLang('LNG_6613');
            print json_encode($arr);
            return false;                   
        } else {
            $objMethodAdd = $this->_payload['objMethod'];
            switch (true) {
                case($objMethodAdd['strType'] == ''):
                    $arr['result'] = false;
                    $arr['title'] = 'Failure';
                    $arr['message'] = 'You must fill method name';
                    print json_encode($arr);
                    return false;                   
            }
        }
        return true;
    }
    protected function do_Add(){
        $objMethod = new ClsBllPaymentMethod();
        $arrMethodAdd = $this->_payload['objMethod'];
        $objMethod->strType =  $arrMethodAdd['strType'];
        $objMethod->boolManual =  $arrMethodAdd['boolManual'];
        $objMethod->boolDisabled =  $arrMethodAdd['boolDisabled'];
        $objMethod->strDetails =  $arrMethodAdd['strDetails'];
        $rslt = $objMethod->Save();
        if(!$rslt){
            $arr['result'] = false;
            $arr['title'] = 'Error';
            $arr['message'] = 'Failed to add payment method!';
            print json_encode($arr); 
            return;   
        }
        $arr['result'] = true;
        $arr['title'] = 'Success';
        $arr['message'] = 'Method successfully added';
        $arr['object'] = $objMethod->ToArray();
        print json_encode($arr);
    }

    protected function before_Update(){
        if (!isset($this->_payload['objMethod'])) {
            $arr['result'] = false;
            $arr['title'] = $this->cLang('LNG_6616');
            $arr['message'] = $this->cLang('LNG_6613');
            print json_encode($arr);
            return false;                   
        }
        return true;

    }
    protected function do_Update(){
        $arrMethod = $this->_payload['objMethod'];
        $objMethod =  new ClsBllPaymentMethod();
        $rslt = $objMethod->LoadByID($arrMethod['intID']);

        if(!$rslt){
            $arr['result'] = false;
            $arr['message'] = 'Method not found';
            $arr['object'] = $arrMethod;
            print json_encode($arr);
            return;
        }
        $objMethod->strType = $arrMethod['strType'];
        $objMethod->boolManual = $arrMethod['boolManual'];
        $objMethod->boolDisabled =  $arrMethod['boolDisabled'];
        $objMethod->strDetails =  $arrMethod['strDetails'];
        $rslt = $objMethod->Save();
        if($rslt){
            $strMsg = 'Method has been updated successfully';                                                                                                    
            $arr['title'] = 'Success';
            $arr['result'] = true;
            $arr['message'] = $strMsg;
            $arr['object'] = $objMethod->ToArray();
        }else{
            $arr['title'] = 'Error';
            $arr['result'] = false;
            $arr['message'] = 'Error in updating method';
        }
        print json_encode($arr);
    }

    protected function before_Delete(){
        if (!isset($this->_data['method_id']) or !is_numeric($this->_data['method_id'])){
            $arr['result'] = false;
            $arr['message'] = 'No method id specified.';
            print json_encode($arr);
            return false;                  
        }
        return true;
    }
    protected function do_Delete(){
        $intMethodID = $this->_data['method_id'];
        $objMethod =  new ClsBllPaymentMethod();
        $rslt = $objMethod->LoadByID($this->_data['method_id']);
        if (!$rslt){
            $arr['result'] = false;
            $arr['title'] = 'Error';
            $arr['message'] = "No payment method found with ID #$intMethodID";
            print json_encode($arr);
            return false;
        }
        if($rslt){         
            $rslt = $objMethod->Delete();
            if($rslt){
                $arr['result'] = true;
                $arr['title'] = 'Success';
                $arr['message'] = 'Method deleted Successfully';
            }else{
                $arr['result'] = false;
                $arr['title'] = 'Error';
                $arr['message'] = "Method couldn't be deleted as there is another data related to it";
            }
            print json_encode($arr);
        }
    }


}