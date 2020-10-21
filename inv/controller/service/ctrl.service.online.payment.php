<?php
namespace NsINV;

class ClsCtrlServiceOnlinePayment extends \NsFWK\ClsCtrlServicePublic {

    protected function do_Default(){}
    
    protected function before_Add(){
        $arr = array();
        if (!isset($this->_payload['objPayment'])) {
            $arr['result'] = false;
            $arr['title'] = "payload empty";
            $arr['message'] = $this->cLang('LNG_6613');
            print json_encode($arr);
            return false;                   
        } 

        $objPaymentAdd = $this->_payload['objPayment'];
        switch (true) {
            case ($objPaymentAdd['intInvoiceID'] == ''):
                $arr['result'] = false;
                $arr['title'] = 'Error';
                $arr['message'] = 'Please specify invoice id!';
                break;
            case ($objPaymentAdd['strName'] == ''):
                $arr['result'] = false;
                $arr['title'] = 'Error';
                $arr['message'] = 'Please specify customer name!';
                break;
            case ($objPaymentAdd['strPhone'] == ''):
                $arr['result'] = false;
                $arr['title'] = 'Error';
                $arr['message'] = 'Please specify customer phone!';
                break;
            case ($objPaymentAdd['strCardNumber'] == ''):
                $arr['result'] = false;
                $arr['title'] = 'Error';
                $arr['message'] = 'Please specify card number!';
                break;
            case ($objPaymentAdd['strCVV'] == ''):
                $arr['result'] = false;
                $arr['title'] = 'Error';
                $arr['message'] = 'Please specify CVV number!';
                break;
            case ($objPaymentAdd['strExpiryYear'] == ''):
                $arr['result'] = false;
                $arr['title'] = 'Error';
                $arr['message'] = 'Please specify expiry year!';
                break;
            case ($objPaymentAdd['strExpiryMonth'] == ''):
                $arr['result'] = false;
                $arr['title'] = 'Error';
                $arr['message'] = 'Please specify expiry month!';
                break;
        }

        if (!empty($arr)) {
            print json_encode($arr);
            return false;                   
        }

        return true;
    }
    protected function do_Add(){
        $arrPayment= $this->_payload['objPayment'];
        $rsltRequest = ClsBllCardPayment::Request($arrPayment);
        if(!$rsltRequest){
            $arr['result'] = false;
            $arr['title'] = 'Error';
            $arr['message'] = 'Charge request failed';
        } else {
            $arr['result'] = true;
            $arr['title'] = 'Success';
            $arr['message'] = 'Charge request sent successfully to cowpay!';
        }

        print json_encode($arr); 
    }
}