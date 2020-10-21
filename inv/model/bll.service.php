<?php
namespace NsINV;

class ClsBllService extends \NsFWK\ClsBll{
    public function __construct(){
        $this->_strClsDalLoad = '\NsINV\ClsDalService';
        $this->_strClsDalSave = '\NsINV\ClsDalService';
        $this->_data = array(
            'intID'=>-1,
            'strServiceName'=>'',
            'strServiceDescription'=>'',
            'strServiceCode'=>'',
        );
        @parent::__construct(func_get_args());
    }

    public function __get($name){
        switch($name){
        }
        return parent::__get($name);
    }

    protected function _save(\ADODB_Active_Record $objDAL){
        if($this->getIsLoaded()){
            $rslt = $objDAL->Load('pkServiceID = ?',array($this->_data['intID']));
            if(!$rslt){
                return 'Could not load object!';
            }
        }
        $objDAL->fldServiceName = $this->_data['strServiceName'];
        $objDAL->fldServiceDescription = $this->_data['strServiceDescription'];
        $objDAL->fldServiceCode = $this->_data['strServiceCode'];
        
        $rslt = $objDAL->Save();
        if($rslt){
            $this->_data['intID'] = $objDAL->pkCustomerID;
        }
        return $rslt;
    }

    protected function _delete(\ADODB_Active_Record $objDAL){
        $DB = &\ADODB_Connection_Manager::GetConnection('customer');
        return $DB->Execute('DELETE FROM inv_service WHERE pkServiceID = ? ', array($this->_data['intID']));
    }

    protected function _load(\ADODB_Active_Record $objDAL){
        $this->_data['intID'] = $objDAL->pkServiceID;
        $this->_data['strServiceName'] = $objDAL->fldServiceName;
        $this->_data['strServiceDescription'] = $objDAL->fldServiceDescription;
        $this->_data['strServiceCode'] = $objDAL->fldServiceCode;
    }


    public function LoadByID($intID){
        $objFilter = new \NsFWK\ClsFilter();
        $objFilter->intID = "pkServiceID = $intID";
        return $this->Load($objFilter);
    }


}