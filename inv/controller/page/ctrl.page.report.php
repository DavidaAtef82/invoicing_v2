<?php
namespace NsINV;

class ClsCtrlPageReport extends \NsINV\ClsCtrlPageInv {

    protected function do_Default() {}

    protected function do_Menu() {                                                                                 
        $arrMenuItems = array(); 
        $arrMenuItems['412'] = array(
            'page'=>'Report',
            'action'=>'InvoiceAging',
            'strName'=>$this->cLang('LNG_MENU_REPORT_INVOICE_AGING', array(), 'AUX_MENU'),
            'strColor'=>'red',
            'strIcon'=>'fa fa-clock-o'
        );
        $this->_smarty->assign('arrMenuItems', $arrMenuItems);
        $this->_template = "pages/report.menu.tpl";
    }                                 
    protected function after_Menu() {
        $this->_smarty->display($this->_template);
    }

    protected function do_InvoiceAging() {
        $this->_template = "pages/report.invoice.aging.tpl";
    }
    protected function after_InvoiceAging() {
        $this->_smarty->display($this->_template);
    }
}