<?php
namespace NsINV;
class ClsFilterCustomer extends \NsFWK\ClsFilter{
    public function __construct(){
        $this->_data = array(
            'intID'=>-1,
            'strName'=>'',
            'strAddress'=>'',
            'strPhone'=>'',
            'strNotes'=>'',
            'strTimestamp'=>'',
            'intCityID'=>'',
            'intCountryID'=>'',
        );
    }

    public function GetWhereStatement(){
        $strWhere = '';

        if($this->_data['intID'] != -1){
            $strWhere .= (($strWhere == '')? '' : ' AND ');
            $strWhere .= "pkCustomerID = {$this->_data['intID']}";
        }

        if($this->_data['strName'] != ''){
            $strWhere .= (($strWhere == '')? '' : ' AND ');
            $strKeyword = str_replace(' ', '%', $this->_data['strName']);
            $strWhere .= "fldName LIKE '%$strKeyword%'";
        }

        if($this->_data['strAddress'] != ''){
            $strWhere .= (($strWhere == '')? '' : ' AND ');
            $strKeyword = str_replace(' ', '%', $this->_data['strAddress']);
            $strWhere .= "fldAddress LIKE '%$strKeyword%'";
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

        if($this->_data['strTimestamp'] != ''){
            $strWhere .= (($strWhere == '')? '' : ' AND ');
            $strWhere .= "fldTimestamp = {$this->_data['strTimestamp']}";
        }

        if($this->_data['intCityID'] != -1){
            $strWhere .= (($strWhere == '')? '' : ' AND ');
            $strWhere .= "fkCityID = {$this->_data['intCityID']}";
            //$strWhere .= "fkCityID = {$this->_data['intCityID']}"; 
        }

        if($this->_data['intCountryID'] != -1){
            $strWhere .= (($strWhere == '')? '' : ' AND ');
            $strWhere .= "fkCityID IN (SELECT pkCityID FROM cmn_city WHERE fkCountryID = {$this->_data['intCountryID']})";
            //$strWhere .= "fkCityID = {$this->_data['intCityID']}"; 
        }
        $strWhere = (($strWhere == '')? '1=1' : $strWhere);
        return $strWhere;
    }
}