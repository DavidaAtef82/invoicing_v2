<?php
namespace NsINV;

class ClsCtrlPageInvoice extends ClsCtrlPageInv {

    protected function do_Default() {}

    protected function before_List() {
        return true;
    }
    protected function do_List() {
        $this->_template = 'pages/invoice.list.tpl';
    }
    protected function after_List() {
        $this->_smarty->display($this->_template);
    }

    protected function do_View(){
        $this->_template = 'pages/invoice.view.tpl';
    }
    protected function after_View(){
        $this->_smarty->display($this->_template);
    }

    protected function do_Add(){
        $this->_template = "pages/invoice.add.tpl";
    }                                 
    protected function after_Add(){
        $this->_smarty->display($this->_template);
    }

    protected function do_Edit(){
        $this->_template = "pages/invoice.edit.tpl";
    }                                 
    protected function after_Edit(){
        $this->_smarty->display($this->_template);
    }


}