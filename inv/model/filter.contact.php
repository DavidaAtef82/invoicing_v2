<?php
namespace NsINV;
class ClsFilterContact extends \NsFWK\ClsFilter{
    public function __construct(){
        $this->_data = array(
            'intID'=>-1,
            'intCustomerID'=>-1,
            'strName'=>'',
            'strEmail'=>'',
            'strPhone'=>'',
            'strEmail'=>'',
            'strNotes'=>'',
        );
    }

    public function GetWhereStatement(){
        $strWhere = '';

        if($this->_data['intID'] != -1){
            $strWhere .= (($strWhere == '')? '' : ' AND ');
            $strWhere .= "pkContactID = {$this->_data['intID']}";
        }

        if($this->_data['strName'] != ''){
            $strWhere .= (($strWhere == '')? '' : ' AND ');
            $strKeyword = str_replace(' ', '%', $this->_data['strName']);
            $strWhere .= "fldName LIKE '%$strKeyword%'";
        }

        if($this->_data['strEmail'] != ''){
            $strWhere .= (($strWhere == '')? '' : ' AND ');
            $strKeyword = str_replace(' ', '%', $this->_data['strEmail']);
            $strWhere .= "fldEmail LIKE '%$strKeyword%'";
        }

        if($this->_data['strPhone'] != ''){
            $strWhere .= (($strWhere == '')? '' : ' AND ');
            $strKeyword = str_replace(' ', '%', $this->_data['strPhone']);
            $strWhere .= "fldPhone LIKE '%$strKeyword%'";
        }

        if($this->_data['strNotes'] != ''){
            $strWhere .= (($strWhere == '')? '' : ' AND ');
            $strWhere .= "fldNotes = {$this->_data['strNotes']}";
        }

        if($this->_data['intCustomerID'] != -1){
            $strWhere .= (($strWhere == '')? '' : ' AND ');
            $strWhere .= "fkCustomerID = {$this->_data['intCustomerID']}";
        }

        $strWhere = (($strWhere == '')? '1=1' : $strWhere);
        return $strWhere;
    }
}