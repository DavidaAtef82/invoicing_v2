<?php
namespace NsINV;

class ClsCtrlServiceCatalogue extends \NsINV\ClsCtrlServiceInv {

    protected function do_Default(){}

    protected function do_ListAll(){
        $obj = new \NsINV\ClsBllCatalogue();
        $arrData = $obj->GetAllCatalogues();
        $arr['result'] = true;
        $arr['title'] = 'Success';
        $arr['message'] = 'Catalogues successfully listed';
        $arr['object'] = $arrData;
        print json_encode($arr);
    }

    protected function do_List(){

        $arrPage = $this->_payload['objPage'];
        $arrFilter = $this->_payload['objFilter'];
        $intPageNo = 1;
        if (isset($arrPage['intPageNo'])){
            $intPageNo = $arrPage['intPageNo'];
        }

        $intPageSize = $this->_config['ListingPageSize'];
        if (isset($arrPage['intPageSize'])){
            $intPageSize = $arrPage['intPageSize'];
        }

        $objFilter = new \NsINV\ClsFilterCatalogue();
        if(isset($arrFilter['strName']) && $arrFilter['strName'] != ''){
            $objFilter->strName = $arrFilter['strName'];
        }
        if(isset($arrFilter['strDescription']) && $arrFilter['strDescription'] != ''){
            $objFilter->strDescription = $arrFilter['strDescription'];
        }
        if(isset($arrFilter['floatPrice']) && $arrFilter['floatPrice'] != -1){
            $objFilter->floatPrice = $arrFilter['floatPrice'];
        }
        if(isset($arrFilter['strCode']) && $arrFilter['strCode'] != ''){
            $objFilter->strCode = $arrFilter['strCode'];
        }
        if(isset($arrFilter['intTaxTypeID']) && $arrFilter['intTaxTypeID'] != -1){
            $objFilter->intTaxTypeID = $arrFilter['intTaxTypeID'];
        }

        $objCatalogue = new ClsBllCatalogue();
        $arrPage = $objCatalogue->GetDataPageAssociative($intPageNo, $intPageSize,$objFilter->GetWhereStatement(), '', $intPageCount, $intRowCount);
        $arrData = array('arrData'=>$arrPage, 'intTotal'=>$intRowCount);

        $arr['result'] = true;
        $arr['title'] = 'Success';
        $arr['message'] = 'Catalogues filtered successfully';
        $arr['object'] = $arrData;
        print json_encode($arr);
        return true;
    }

    protected function before_Add(){
        if (!isset($this->_payload['objCatalogue'])) {
            $arr['result'] = false;
            $arr['title'] = $this->cLang('LNG_6616');
            $arr['message'] = $this->cLang('LNG_6613');
            print json_encode($arr);
            return false;                   
        } else {
            $arrCatalogue = $this->_payload['objCatalogue'];
            switch (true) {
                case empty($arrCatalogue['strName']):
                case empty($arrCatalogue['strCode']):
                    $arr['result'] = false;
                    $arr['title'] = 'failure';
                    $arr['message'] = 'You must fill all inputs';
                    print json_encode($arr);
                    return false;                   
                    break;
            }
        }

        return true;
    }
    protected function do_Add(){
        $objCatalogue = new ClsBllCatalogue();
        $arrCatalogue = $this->_payload['objCatalogue'];

        // check if item is exist or not by Name or Code --> 
        //Item name should not accept duplicate values.
        //Item codes should not accept duplicate values.
        $rslt = $objCatalogue->IsExist($arrCatalogue['strCode'] ,$arrCatalogue['strName']);
        if($rslt){
            $arr['result'] = false;
            $arr['title'] = 'Error';
            $arr['message'] = 'your new item is already exist please change the code or the name';
            print json_encode($arr); 
            return;    
        }

        $objCatalogue->_data["strCode"] = $arrCatalogue['strCode'];
        $objCatalogue->_data["strName"] = $arrCatalogue['strName'];
        $objCatalogue->_data["strDescription"] = $arrCatalogue['strDescription'];   
        $objCatalogue->_data["floatPrice"] = $arrCatalogue['floatPrice'];
        $objCatalogue->_data["intTaxTypeID"] = $arrCatalogue['intTaxTypeID'];

        $rslt = $objCatalogue->Save();
        if(!$rslt){
            $arr['result'] = false;
            $arr['title'] = 'Error';
            $arr['message'] = 'Failed to save Catalogue';
            print json_encode($arr); 
            return;   
        }
        $objCatalogue->objTaxType;
        $arr['result'] = true;
        $arr['title'] = 'Success';
        $arr['message'] = 'Catalogue successfully listed';
        $arr['object'] = $objCatalogue->ToArray();
        print json_encode($arr);
    }

    protected function before_Update(){

        if (!isset($this->_payload['objCatalogue'])) {
            $arr['result'] = false;
            $arr['title'] = $this->cLang('Error');
            $arr['message'] = $this->cLang('No data found');
            print json_encode($arr);
            return false;                   
        } else {
            $arrCatalogues = $this->_payload['objCatalogue'];
            switch (true) {
                case empty($arrCatalogues['strName']):
                case empty($arrCatalogues['strCode']):
                    $arr['result'] = false;
                    $arr['title'] = $this->cLang('Error ');
                    $arr['message'] = $this->cLang('You must fill all inputs');
                    print json_encode($arr);
                    return false;                   
                    break;
            }
        }
        return true;

    }
    protected function do_Update(){
        $arrCatalogue = $this->_payload['objCatalogue'];
        $objCatalogue =  new ClsBllCatalogue();

        $rslt = $objCatalogue->LoadByID($arrCatalogue['intID']);
        if(!$rslt){
            $arr['result'] = false;
            $arr['message'] = 'Catalogue not found';
            $arr['object'] = $arrCatalogues;
            print json_encode($arr);
            return;
        }

        // check if the new name of Catalogue is already exist or not 
        if($objCatalogue->strCode != $arrCatalogue['strCode'] || $objCatalogue->strName != $arrCatalogue['strName'] ){
            $rslt = $objCatalogue->IsExist($arrCatalogue['strCode'] ,$arrCatalogue['strName']);
            if($rslt){
                $arr['result'] = false;
                $arr['title'] = 'Error';
                $arr['message'] = 'your item is already exist please change the code or the name';
                print json_encode($arr); 
                return;    
            }
        }

        //  check if the catalogue code  is already taken or not
        $objCatalogue->strCode = $arrCatalogue['strCode'];
        if($objCatalogue->strCode != $arrCatalogue['strCode']){
            $arr['title'] = 'Error';
            $arr['result'] = false;
            $arr['message'] = 'the code is already used';
            print json_encode($arr);
            return;
        }
        //  check if the catalogue is name already taken or not
        $objCatalogue->strName = $arrCatalogue['strName'];
        if($objCatalogue->strName != $arrCatalogue['strName']){
            $arr['title'] = 'Error';
            $arr['result'] = false;
            $arr['message'] = 'the name is already used';
            print json_encode($arr);
            return;
        }

        $objCatalogue->strDescription = $arrCatalogue['strDescription'];   
        $objCatalogue->floatPrice = $arrCatalogue['floatPrice'];
        $objCatalogue->intTaxTypeID = $arrCatalogue['intTaxTypeID'];

        $rslt = $objCatalogue->Save();
        if($rslt){
            $objCatalogue->objTaxType;
            $strMsg = 'Catalogue has been updated successfully';                                                                                                    
            $arr['title'] = 'Success';
            $arr['result'] = true;
            $arr['message'] = $strMsg;
            $arr['object'] = $objCatalogue->ToArray();
        }else{
            $arr['title'] = 'Error';
            $arr['result'] = false;
            $arr['message'] = 'Error in updating catalogue';
        }
        print json_encode($arr);
    }

    protected function before_Delete(){
        if (!isset($this->_data['catalogue_id']) or !is_numeric($this->_data['catalogue_id'])){
            $arr['result'] = false;
            $arr['message'] = 'No Catalogue id specified.';
            print json_encode($arr);
            return false;                  
        }
        return true;
    }
    protected function do_Delete(){
        $intCatalogueID = $this->_data['catalogue_id'];
        $objCatalogue =  new ClsBllCatalogue();
        $rslt = $objCatalogue->LoadByID($this->_data['catalogue_id']);
        if (!$rslt){
            $arr['result'] = false;
            $arr['title'] = 'Error';
            $arr['message'] = "No Catalogue found with ID #$intCatalogueID";
            print json_encode($arr);
            return false;
        }
        if($rslt){         
            $rslt = $objCatalogue->Delete();
            if($rslt){
                $arr['result'] = true;
                $arr['title'] = 'Success';
                $arr['message'] = 'Catalogue deleted Successfully';
            }else{
                $arr['result'] = false;
                $arr['title'] = 'Error';
                $arr['message'] = "Catalogue couldn't be deleted as there is another data related to it";
            }
            print json_encode($arr);
        }
    }


}