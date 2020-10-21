<?php
namespace NsINV;

class ClsCtrlPageOnlinePayment extends \NsFWK\ClsCtrlPagePublic {
    protected function do_Default() {}

    protected function before_Add(){
        $intInvoiceID = $this->_data['invoice_id'];
        if(isset($intInvoiceID)){
            $objInvoice = new \NsINV\ClsBllInvoice();
            $intInvoiceID = \NsFWK\ClsHlpHelper::Decrypt($intInvoiceID);
            if($objInvoice->LoadByID($intInvoiceID)){
                $decDueAmount = $objInvoice->_data['decGrossAmount'] - $objInvoice->_data['decTotalPayment'];
                if($decDueAmount != 0){
                    // We have a corner case that will happen if:
                    // Customer had online payment link and operation was done sussefully at cowpay side
                    // but saving the payment failed in our system.
                    // So, we should check the logs to make sure that this invoice has not unsaved payments.
                    
                    return true; 
                } else { // Invoice has no due amount to pay for
                    header('location: index.php?module=inv&page=OnlinePaymentError&action=Default&response=1');
                    return false;                    
                }

            } else { // No such invocie with this id
                header('location: index.php?module=inv&page=OnlinePaymentError&action=Default&response=2');
                return false;
            }
        } else {// Invocie id is not specified
            header('location: index.php?module=err&page=Index&action=Default&response=404');
            return false;
        }
    }

    protected function do_Add() {
        $this->_template = "pages/online.payment.tpl";
    }                                 

    protected function after_Add() {
        $this->_smarty->assign('invoice_id', \NsFWK\ClsHlpHelper::Decrypt($this->_data['invoice_id']));
        $this->_smarty->display($this->_template);
    }

    protected function do_CallBack(){
        $this->_template = "pages/online.payment.callback.tpl";
    }
    
    protected function after_CallBack(){
        $this->_smarty->display($this->_template);
    }                                 
}