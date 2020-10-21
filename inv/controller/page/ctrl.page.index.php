<?php
namespace NsINV;

class ClsCtrlPageIndex extends ClsCtrlPageInv{

    protected function do_Default(){
        $this->_smarty->display('pages/_blank.tpl');
    }
}