<?php
namespace NsINV;
class ClsFilterInvoice extends \NsFWK\ClsFilter{
    public function __construct(){
        $this->_data = array(
            'intID'=>-1,
            'strInvoiceNumber'=>'',
            'strReference'=>'',
            'dtIssueDateFrom'=>'',
            'dtIssueDateTo'=>'',
            'dtDueDateFrom'=>'',
            'dtDueDateTo'=>'',
            'tsCreatedOnTimestamp'=>'',
            'intCreatedByUserID'=>-1,
            'intCustomerID'=>-1,
            'strCustomerName'=>'',
            'intPaymentTermID'=>-1,
            'intStatusID'=>-1,
            'boolShowOverdue'=>false
        );
    }

    public function GetWhereStatement(){
        $strWhere = '';

        if($this->_data['intID'] != -1){
            $strWhere .= (($strWhere == '')? '' : ' AND ');
            $strWhere .= "pkInvoiceID = {$this->_data['intID']}";
        }

        if($this->_data['strInvoiceNumber'] != ''){
            $strWhere .= (($strWhere == '')? '' : ' AND ');
            $strKeyword = str_replace(' ', '%', $this->_data['strInvoiceNumber']);
            $strWhere .= "fldInvoiceNumber LIKE '%$strKeyword%'";
        }

        if($this->_data['strReference'] != ''){
            $strWhere .= (($strWhere == '')? '' : ' AND ');
            $strKeyword = str_replace(' ', '%', $this->_data['strReference']);
            $strWhere .= "fldReference LIKE '%$strKeyword%'";
        }

        if($this->_data['tsCreatedOnTimestamp'] != ''){
            $strWhere .= (($strWhere == '')? '' : ' AND ');
            $strWhere .= "fldCreatedOnTimestamp = {$this->_data['tsCreatedOnTimestamp']}";
        }

        if($this->_data['dtIssueDateFrom'] != '' && $this->_data['dtIssueDateTo'] != ''){
            $strDateFrom = $this->_data['dtIssueDateFrom'];
            $strDateTo = $this->_data['dtIssueDateTo'];
            $strWhere .= (($strWhere == '')? '' : ' AND ');
            $strWhere .= "fldIssueDate BETWEEN  '$strDateFrom' AND '$strDateTo'";
        }
        if($this->_data['dtIssueDateFrom'] != '' && $this->_data['dtIssueDateTo'] == ''){
            $strDateFrom = $this->_data['dtIssueDateFrom'];
            $strWhere .= (($strWhere == '')? '' : ' AND ');
            $strWhere .= "fldIssueDate >=  '$strDateFrom' ";
        }
        if($this->_data['dtIssueDateFrom'] == '' && $this->_data['dtIssueDateTo'] != ''){
            $strDateTo = $this->_data['dtIssueDateTo'];
            $strWhere .= (($strWhere == '')? '' : ' AND ');
            $strWhere .= "fldIssueDate <=  '$strDateTo' ";
        }
        if($this->_data['dtDueDateFrom'] != '' && $this->_data['dtDueDateTo'] != ''){
            $strDateFrom = $this->_data['dtDueDateFrom'];
            $strDateTo = $this->_data['dtDueDateTo'];
            $strWhere .= (($strWhere == '')? '' : ' AND ');
            $strWhere .= "fldDueDate BETWEEN  '$strDateFrom' AND '$strDateTo'";
        }
        if($this->_data['dtDueDateFrom'] != '' && $this->_data['dtDueDateTo'] == ''){
            $strDateFrom = $this->_data['dtDueDateFrom'];
            $strWhere .= (($strWhere == '')? '' : ' AND ');
            $strWhere .= "fldDueDate >=  '$strDateFrom' ";
        }
        if($this->_data['dtDueDateFrom'] == '' && $this->_data['dtDueDateTo'] != ''){
            $strDateTo = $this->_data['dtDueDateTo'];
            $strWhere .= (($strWhere == '')? '' : ' AND ');
            $strWhere .= "fldDueDate <=  '$strDateTo' ";
        }

        if($this->_data['intCreatedByUserID'] != -1){
            $strWhere .= (($strWhere == '')? '' : ' AND ');
            $strWhere .= "fkCreatedByUserID = {$this->_data['intCreatedByUserID']}";
        }

        if($this->_data['strCustomerName'] != ''){
            $strWhere .= (($strWhere == '')? '' : ' AND ');
            $strKeyword = str_replace(' ', '%', $this->_data['strCustomerName']);
            $strWhere .= "fkCustomerID IN (SELECT pkCustomerID FROM inv_customer WHERE fldName LIKE '%$strKeyword%')";
        }

        if($this->_data['intCustomerID'] != -1){
            $strWhere .= (($strWhere == '')? '' : ' AND ');
            $strWhere .= "fkCustomerID = {$this->_data['intCustomerID']}";
        }

        if($this->_data['intPaymentTermID'] != -1){
            $strWhere .= (($strWhere == '')? '' : ' AND ');
            $strWhere .= "fkPaymentTermID = {$this->_data['intPaymentTermID']}";
        }
        if($this->_data['intStatusID'] != -1){
            $strWhere .= (($strWhere == '')? '' : ' AND ');
            $strWhere .= "fkStatusID = {$this->_data['intStatusID']}";
        }      
        if($this->_data['boolShowOverdue'] != false){
            $strWhere .= (($strWhere == '')? '' : ' AND ');
            $dtToday = date('Y-m-d H:i:s');
            $strWhere .= "fldDueDate < '$dtToday' AND ";
            $strWhere .= "fkStatusID IN (".ClsBllStatus::STATUS_SENT.','.ClsBllStatus::STATUS_PARTIAL.')' ; 
        }
        $strWhere = (($strWhere == '')? '1=1' : $strWhere);
        return $strWhere;
    }
}