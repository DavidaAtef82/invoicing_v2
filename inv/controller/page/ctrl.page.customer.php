<?php
namespace NsINV;

class ClsCtrlPageCustomer extends ClsCtrlPageInv {

    protected function do_Default() {}

    protected function before_List() {
        return true;
    }
    protected function do_List() {
        $this->_template = 'pages/customer.list.tpl';
    }
    protected function after_List() {
        $this->_smarty->display($this->_template);
    }

    protected function do_View(){
        $this->_template = 'pages/customer.view.tpl';
    }
    protected function after_View(){
        $this->_smarty->display($this->_template);
    }
 
}