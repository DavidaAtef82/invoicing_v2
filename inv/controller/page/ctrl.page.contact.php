<?php
namespace NsINV;

class ClsCtrlPageContact extends ClsCtrlPageInv {

    protected function do_Default() {}
    
    protected function before_List() {
        return true;
    }
    protected function do_List() {
        $this->_template = 'pages/contact.list.tpl';
    }
    protected function after_List() {
        $this->_smarty->display($this->_template);
    }
}