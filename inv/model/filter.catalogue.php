<?php
namespace NsINV;
class ClsFilterCatalogue extends \NsFWK\ClsFilter{
    public function __construct(){
        $this->_data = array(
            'intID'=>-1,
            'strName'=>'',
            'strDescription'=>'',
            'floatPrice'=>-1,
            'strCode'=>'',
            'intTaxTypeID'=>-1
        );
    }

    public function GetWhereStatement(){
        $strWhere = '';

        if($this->_data['intID'] != -1){
            $strWhere .= (($strWhere == '')? '' : ' AND ');
            $strWhere .= "pkServiceID = {$this->_data['intID']}";
        }

        if($this->_data['strName'] != ''){
            $strWhere .= (($strWhere == '')? '' : ' AND ');
            $strKeyword = str_replace(' ', '%', $this->_data['strName']);
            $strWhere .= "fldName LIKE '%$strKeyword%'";
        }

        if($this->_data['strDescription'] != ''){
            $strWhere .= (($strWhere == '')? '' : ' AND ');
            $strKeyword = str_replace(' ', '%', $this->_data['strDescription']);
            $strWhere .= "fldDescription LIKE '%$strKeyword%'";
        }

        if($this->_data['floatPrice'] != ''){
            $strWhere .= (($strWhere == '')? '' : ' AND ');
            $strWhere .= "fldPrice = {$this->_data['floatPrice']}";
        }

        if($this->_data['strCode'] != ''){
            $strWhere .= (($strWhere == '')? '' : ' AND ');
            $strKeyword = str_replace(' ', '%', $this->_data['strCode']);
            $strWhere .= "fldCode LIKE '%$strKeyword%'";
        }

        if($this->_data['intTaxTypeID'] != -1){
            $strWhere .= (($strWhere == '')? '' : ' AND ');
            $strWhere .= "fkTaxTypeID = {$this->_data['intTaxTypeID']}";
        }

        $strWhere = (($strWhere == '')? '1=1' : $strWhere);
        return $strWhere;
    }
}