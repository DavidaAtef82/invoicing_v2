<?php
namespace NsINV;

class ClsCtrlPageOnlinePaymentError extends \NsFWK\ClsCtrlPagePublic {
    protected function do_Default() {
        $intResponseType = $this->_data['response'];
        if($intResponseType == 1){
            $this->_smarty->assign('response', 'This invoice has no due amount to pay for!');
        }
        else if($intResponseType == 2){
            $this->_smarty->assign('response', "Invoice Not Found!");
        }
        $this->_template = "pages/online.payment.error.tpl";
        $this->_smarty->display($this->_template);
    }
}