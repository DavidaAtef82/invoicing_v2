<?php
namespace NsINV;

class ClsCtrlApiOnlinePayment extends \NsFWK\ClsCtrlApi{
    protected function before_Default(){
        if (!isset($this->_payload)) {
            return false;                   
        } 
        $objRequest= $this->_payload;
        switch (true) {
            case (!isset($objRequest['cowpay_reference_id'])):
                return false;
            case (!isset($objRequest['merchant_reference_id'])):
                return false;

            case (!isset($objRequest['order_status'])):
                return false;

            case (!isset($objRequest['signature'])):
                return false;

        }
        return true;
    }
    protected function do_Default(){
        $objRequest= $this->_payload;
        $arrRequest = array(
            "cowpay_reference_id" => $objRequest['cowpay_reference_id'],
            "merchant_reference_id" => $objRequest['merchant_reference_id'],
            "order_status" => $objRequest['order_status'],
            "signature" => $objRequest['signature']
        );
        $rslt = ClsBllCardPayment::HandleCallback($arrRequest);
        if(!$rslt) {
            return false;
        } else {
            return true;   
        }

    }
    public function __construct($arrParameters){
        parent::__construct($arrParameters);
    }
}
