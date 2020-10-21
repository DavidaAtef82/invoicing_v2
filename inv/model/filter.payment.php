<?php
namespace NsINV;
class ClsFilterPayment extends \NsFWK\ClsFilter{
    public function __construct(){
        $this->_data = array(
            'intPaymentMethodID'=>-1,
            'strCustomerName'=>'',
            'intPaymentNumber'=>-1,
            'strReference'=>'',
            'dtDateBefore'=>'',
            'dtDateAfter'=>'',
            'decAmount'=>-1,
            'strAmountOperator'=>'',
            'intCreatedByUserID'=>-1,
            'dtCreatedON'=>''
        );
    }

    public function GetWhereStatement(){
        $strWhere = '';

        if($this->_data['intPaymentMethodID'] != -1){
            $strWhere .= (($strWhere == '')? '' : ' AND ');
            $strWhere .= "fkPaymentMethodID = {$this->_data['intPaymentMethodID']}";
        }

        if($this->_data['strCustomerName'] != ''){
            $strWhere .= (($strWhere == '')? '' : ' AND ');
            $strName = $this->_data['strCustomerName'];
            $strWhere .= "fkCustomerID = (SELECT pkCustomerID FROM inv_customer WHERE fldName LIKE '%$strName%')"; 
        }

        if($this->_data['intPaymentNumber'] != -1){
            $strWhere .= (($strWhere == '')? '' : ' AND ');
            $strWhere .= "fldPaymentNumber = {$this->_data['intPaymentNumber']}";
        }

        if($this->_data['strReference'] != ''){
            $strWhere .= (($strWhere == '')? '' : ' AND ');
            $strReference = $this->_data['strReference'];
            $strWhere .= "fldReference  LIKE '%$strReference%'";
        }
        if($this->_data['dtDateBefore'] != ''){
            $strWhere .= (($strWhere == '')? '' : ' AND ');
            $strWhere .= "fldDate < '{$this->_data['dtDateBefore']}'";
        }
        if($this->_data['dtDateAfter'] != ''){
            $strWhere .= (($strWhere == '')? '' : ' AND ');
            $strWhere .= "fldDate > '{$this->_data['dtDateAfter']}'";
        }

        if($this->_data['decAmount'] != -1){
            $strWhere .= (($strWhere == '')? '' : ' AND ');
            if($this->_data['strAmountOperator'] == ''){
                $strWhere .= "fldAmount = {$this->_data['decAmount']}";
            }else{
                  $strWhere .= "fldAmount".$this->_data['strAmountOperator'].$this->_data['decAmount'];
            }
        }

        if($this->_data['intCreatedByUserID'] != -1){
            $strWhere .= (($strWhere == '')? '' : ' AND ');
            $strWhere .= "fkCreatedByUserID = {$this->_data['intCreatedByUserID']}";
        }

        if($this->_data['dtCreatedON'] != ''){
            $strWhere .= (($strWhere == '')? '' : ' AND ');
            $strWhere .= "fkCreatedByTimestamp = {$this->_data['fkCreatedByTimestamp']})";
        }
        $strWhere = (($strWhere == '')? '1=1' : $strWhere);
        return $strWhere;
    }
}