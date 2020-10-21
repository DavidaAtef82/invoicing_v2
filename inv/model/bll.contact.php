<?php
namespace NsINV;

class ClsBllContact extends \NsFWK\ClsBll{
    public function __construct(){
        $this->_strClsDalLoad = '\NsINV\ClsDalContact';
        $this->_strClsDalSave = '\NsINV\ClsDalContact';
        $this->_data = array(
            'intID'=>-1,
            'intCustomerID'=>-1,
            'strName'=>'',
            'strEmail'=>'',
            'strPhone'=>'',
            'strEmail'=>'',
            'strNotes'=>'',
        );
        @parent::__construct(func_get_args());
    }

    public function __get($name){
        switch($name){
            case 'objCustomer':
                if(!isset($this->_data[$name])){
                    $obj = new \NsINV\ClsBllCustomer();
                    $obj->LoadByID($this->_data['intCustomerID']);
                    $this->_data[$name] = $obj;
                }
                break;
        }
        return parent::__get($name);
    }

    protected function _save(\ADODB_Active_Record $objDAL){
        if($this->getIsLoaded()){
            // UPDATE
            $rslt = $objDAL->Load('pkContactID = ?',array($this->_data['intID']));
            if(!$rslt){
                return 'Could not load object!';
            }
        }

        $objDAL->fldName = $this->_data['strName'];
        $objDAL->fldPhone = $this->_data['strPhone'];
        $objDAL->fldEmail = $this->_data['strEmail'];
        $objDAL->fldNotes = $this->_data['strNotes'];
        $objDAL->fkCustomerID = $this->_data['intCustomerID'];  
        $rslt = $objDAL->Save();

        if($rslt){
            $this->_data['intID'] = $objDAL->pkContactID;
        }
        return $rslt;
    }

    protected function _delete(\ADODB_Active_Record $objDAL){
        $DB = &\ADODB_Connection_Manager::GetConnection('customer');
        return $DB->Execute('DELETE FROM inv_customer_contact WHERE pkContactID = ? ', array($this->_data['intID']));
    }

    protected function _load(\ADODB_Active_Record $objDAL){
        $this->_data['intID'] = $objDAL->pkContactID;
        $this->_data['intCustomerID'] = $objDAL->fkCustomerID;
        $this->_data['strName'] = $objDAL->fldName;
        $this->_data['strPhone'] = $objDAL->fldPhone;
        $this->_data['strEmail'] = $objDAL->fldEmail;
        $this->_data['strNotes'] = $objDAL->fldNotes;
    }
    
    public function LoadByID($intID){
        $objFilter = new \NsFWK\ClsFilter();
        $objFilter->intID = "pkContactID = $intID";
        return $this->Load($objFilter);
    }

    public function GetDataPageAssociative($intPageNo, $intPageSize, $strWhere, $strOrder, &$intPageCount, &$intRowCount){
        $arrData = parent::GetDataPageAssociative($intPageNo, $intPageSize, $strWhere, $strOrder, $intPageCount, $intRowCount);
        if (!empty($arrData)) {
            $this->loadBindingAssociative($arrData, 'intCustomerID', 'objCustomer', new \NsINV\ClsBllCustomer(), 'pkCustomerID', 'intID');
        }
        return $arrData;
    }


}