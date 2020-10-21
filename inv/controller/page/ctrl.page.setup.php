<?php
namespace NsINV;

class ClsCtrlPageSetup extends \NsINV\ClsCtrlPageInv {

    protected function do_Default() {}

    protected function do_Menu() {
        $arrMenuItems = array();  

        $arrMenuItems['1'] = array(
            'page'=>'Setup',
            'action'=>'PaymentTerm',
            'strName'=>$this->cLang('LNG_MENU_SETUP_PAYMENT_TERMS', array(), 'AUX_MENU'),
            'strColor'=>'green-jungle',
            'strIcon'=>'fa fa fa-code'
        );
        $arrMenuItems['2'] = array(
            'page'=>'Setup',
            'action'=>'Catalogue',
            'strName'=>$this->cLang('LNG_MENU_SETUP_CATALOGUE', array(), 'AUX_MENU'),
            'strColor'=>'blue-chambray',
            'strIcon'=>'fa fa fa-briefcase'
        );
        $arrMenuItems['3'] = array(
            'page'=>'Setup',
            'action'=>'PaymentMethod',
            'strName'=>$this->cLang('LNG_MENU_SETUP_PAYMENT_METHODS', array(), 'AUX_MENU'),
            'strColor'=>'blue',
            'strIcon'=>'fa fa-money'
        );
        $arrMenuItems['417'] = array(
            'page'=>'Setup',
            'action'=>'TaxType',
            'strName'=>$this->cLang('LNG_MENU_SETUP_TAX_TYPES', array(), 'AUX_MENU'),
            'strColor'=>'red-thunderbird',
            'strIcon'=>'fa fa-bookmark'
        );
        $this->_smarty->assign('arrMenuItems',$arrMenuItems);
        $this->_template = "pages/setup.menu.tpl";
    }                                 
    protected function after_Menu(){
        $this->_smarty->display($this->_template);
    }

    protected function do_PaymentTerm() {
        $this->_template = "pages/setup.payment.term.tpl";
    }
    protected function after_PaymentTerm() {
        $this->_smarty->display($this->_template);
    }

    protected function do_Catalogue() {
        $this->_template = "pages/setup.catalogue.tpl";
    }
    protected function after_Catalogue() {
        $this->_smarty->display($this->_template);
    }

    protected function do_PaymentMethod() {
        $this->_template = "pages/setup.payment.method.tpl";
    }
    protected function after_PaymentMethod() {
        $this->_smarty->display($this->_template);
    }
    protected function do_TaxType() {
        $this->_template = "pages/setup.tax.type.tpl";
    }
    protected function after_TaxType() {
        $this->_smarty->display($this->_template);
    }
}