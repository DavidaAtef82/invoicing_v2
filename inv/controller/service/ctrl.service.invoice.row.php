<?php
namespace NsINV;

class ClsCtrlServiceInvoiceRow extends \NsINV\ClsCtrlServiceInv {

    protected function do_Default(){}
    protected function before_UpdateOrder(){
        if (!isset($this->_data['invoice_row_id']) && !isset($this_data['order'])) {
            $arr['result'] = false;
            $arr['title'] = "Error";
            $arr['message'] = "Can't update this invoice row";
            print json_encode($arr);
            return false;                   
        }
        return true;
    }
    protected function do_UpdateOrder(){
         // check invoice row exist or not
        $objInvoiceRow =  new ClsBllInvoiceRow();
        $rslt = $objInvoiceRow->LoadByID($this->_data['invoice_row_id']);
        if(!$rslt){
            $arr['result'] = false;
            $arr['message'] = 'Invoice row not found';
            print json_encode($arr);
            return;
        }
        $objInvoiceRow->_data['decOrder'] =  $this->_data['order'];
        $rslt = $objInvoiceRow->Save();
        if($rslt){
            $arr['title'] = 'Success';
            $arr['result'] = true;
            $arr['message'] = "Order has been updated successfully";
            $arr['object'] = $objInvoiceRow->ToArray();
        }else{
            $arr['title'] = 'Error';
            $arr['result'] = false;
            $arr['message'] = 'Error in updating order';
        }
        print json_encode($arr);
    }


}