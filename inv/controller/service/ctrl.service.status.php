<?php
namespace NsINV;

class ClsCtrlServiceStatus extends \NsINV\ClsCtrlServiceInv {

    protected function do_Default(){}

     protected function do_ListAll(){
        $obj = new \NsINV\ClsBllStatus();
        $arrData = $obj->GetAllStatus();
        $arr['result'] = true;
        $arr['title'] = 'Success';
        $arr['message'] = 'Cities successfully listed';
        $arr['object'] = $arrData;
        print json_encode($arr);
    }
 
}