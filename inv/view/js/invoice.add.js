controllers.Invoice = ['$scope','SrvInvoice' ,'SrvInvoiceRow' ,'SrvCatalogue' ,'SrvTaxType'  ,'SrvCustomer' ,'SrvPaymentTerm' ,'SrvStatus', '$filter','$location','$window', function($scope,SrvInvoice ,SrvInvoiceRow ,SrvCatalogue ,SrvTaxType ,SrvCustomer ,SrvPaymentTerm ,SrvStatus , $filter,$location,$window){ 

    $scope.objInvoice = {
        strInvoiceNumber:'',
        strReference:'',
        dtIssueDate:'',
        dtDueDate:'',
        objCustomer:null,
        objPaymentTerm:null,
        intStatusID:1,
    };

    $scope.objInvoiceRow = {
        strType:'',
        objCatalogue:null,
        objTaxType:null,
        intUnitPrice:0,
        intQuantity:0,
        intTotalPrice:0,
        decOrder:-1,
        strDescription:'',
        arrSubRows:[],
    };

    $scope.objSubRow = {
        strType:'',   // tax or discount
        objTaxType:null,
        intUnitPrice:0,
        intQuantity:0,
        intTotalPrice:0,
        decOrder:-1,
        strDescription:'',
    };

    $scope.objRowType  = objEnum.ClsBllInvoiceRowType;
    $scope.objTaxTypes  = objEnum.ClsBllTaxType;

    $scope.arrRowTypes = Object.values(objEnum.ClsBllInvoiceRowType);
    $scope.arrCatalogues = [];
    $scope.arrTaxTypes = [];
    $scope.arrCustomers = [];
    $scope.arrPaymentTerms = [];

    $scope.arrRow = [];
    $scope.intCurrentIndex = 0;

    $scope.validate = function(){
        switch(true) {
            case $scope.objInvoice.dtDueDate < $scope.objInvoice.dtIssueDate:
            case $scope.objInvoice.objCustomer.intID == null:
            case $scope.getTotalCalculations('Item') <= 0:
            case $scope.getTotalCalculations("Net") < 0:
            case $scope.getTotalCalculations('Gross') < 0:
                return false;
                break;
        }
        return true;
    }

    $scope.setCurrentRowIndex = function(intIndex){
        $scope.intCurrentIndex = intIndex;
    }

    $scope.newRow = function(strRowType , objCatalogue, objTaxType, strRowPosition){
        $scope.objInvoiceRow = {
            strType:'',
            objCatalogue:null,
            objTaxType:null,
            intUnitPrice:0,
            intQuantity:0,
            intTotalPrice:0,
            decOrder:-1,
            strDescription:'',
            arrSubRows:[],
        };
        switch(strRowType) {
            case $scope.objRowType.ROW_TYPE_ITEM:
                $scope.objInvoiceRow.strType = $scope.objRowType.ROW_TYPE_ITEM;
                $scope.objInvoiceRow.objCatalogue = objCatalogue;
                $scope.objInvoiceRow.intUnitPrice = objCatalogue.floatPrice;
                $scope.objInvoiceRow.strDescription = objCatalogue.strDescription;
                // intial sub row from tax type for this item
                $scope.objSubRow = {
                    strType:$scope.objRowType.ROW_TYPE_TAX,   // tax or discount
                    objTaxType:$scope.objInvoiceRow.objCatalogue.objTaxType,
                    intUnitPrice:$scope.objInvoiceRow.objCatalogue.objTaxType.intValue,
                    intQuantity:0,
                    intTotalPrice:0,
                    decOrder:-1,
                    strDescription:'',
                };
                // check tax type fixed or percent
                if($scope.objSubRow.objTaxType.strType == $scope.objTaxTypes.TYPE_PERCENT){
                    $scope.objSubRow.intUnitPrice = 
                    ($scope.objSubRow.objTaxType.intValue*
                       $scope.objInvoiceRow.objCatalogue.floatPrice)/100;
                }
                $scope.objInvoiceRow.arrSubRows.push($scope.objSubRow); 
                break;
            case $scope.objRowType.ROW_TYPE_DISCOUNT:
                $scope.objInvoiceRow.strType = $scope.objRowType.ROW_TYPE_DISCOUNT;
                break;
            case $scope.objRowType.ROW_TYPE_TAX:
                $scope.objInvoiceRow.strType = $scope.objRowType.ROW_TYPE_TAX;
                $scope.objInvoiceRow.objTaxType = objTaxType;
                $scope.objInvoiceRow.intUnitPrice = objTaxType.intValue;

                break; 
        }
        if(strRowPosition == "Befor"){
            $scope.arrRow.splice($scope.intCurrentIndex , 0, $scope.objInvoiceRow);  
        }else{
            $scope.arrRow.splice($scope.intCurrentIndex + 1, 0, $scope.objInvoiceRow); 
        } 
    }

    $scope.newSubRow = function(intRowIndex){
        $scope.objSubRow = {
            strType:'',   // tax or discount
            objTaxType:null,
            intUnitPrice:0,
            intQuantity:0,
            intTotalPrice:0,
            decOrder:-1,
            strDescription:'',
        };
        $scope.arrRow[intRowIndex].arrSubRows.push($scope.objSubRow);  
    }

    $scope.deleteRow = function(row_index){
        res = IsEmpty($scope.arrRow[row_index]);
        if(!res){
            res = confirm("Are you sure you want to delete this row ?");
            if(!res){
                return;
            }   
        }
        $scope.arrRow.splice(row_index, 1);
    }

    $scope.deleteSubRow = function(intRowIndex , intSubRowIndex){
        res = IsEmpty($scope.arrRow[intRowIndex].arrSubRows[intSubRowIndex]);
        if(!res){
            res = confirm("Are you sure you want to delete this row ?");
            if(!res){
                return;
            }   
        }
        $scope.arrRow[intRowIndex].arrSubRows.splice(intSubRowIndex, 1);
    }

    $scope.moveRow = function(row_index ,strType){
        $objTemp = $scope.arrRow[row_index];
        switch(strType) {
            case 'UP':
                $intTempIndex = row_index - 1;
                if($intTempIndex < 0){
                    $intTempIndex = (row_index -1) + $scope.arrRow.length;
                }
                break;
            case 'DOWN':
                $intTempIndex = (row_index + 1)% $scope.arrRow.length;
                break;
            default:
                $intTempIndex = row_index;
        }
        $scope.arrRow[row_index] = $scope.arrRow[$intTempIndex];
        $scope.arrRow[$intTempIndex] = $objTemp;
    }

    $scope.moveSubRow = function(intRowIndex , intSubRowIndex ,strType){
        $objTemp = $scope.arrRow[intRowIndex].arrSubRows[intSubRowIndex];
        switch(strType) {
            case 'UP':
                $intTempIndex = intSubRowIndex - 1;
                if($intTempIndex < 0){
                    $intTempIndex = (intSubRowIndex -1) + $scope.arrRow[intRowIndex].arrSubRows.length;
                }
                break;
            case 'DOWN':
                $intTempIndex = (intSubRowIndex + 1)% $scope.arrRow[intRowIndex].arrSubRows.length;
                break;
            default:
                $intTempIndex = intSubRowIndex;
        }
        $scope.arrRow[intRowIndex].arrSubRows[intSubRowIndex] = $scope.arrRow[intRowIndex].arrSubRows[$intTempIndex];
        $scope.arrRow[intRowIndex].arrSubRows[$intTempIndex] = $objTemp;
    }

    $scope.getRowInfo = function(intIndex){   
        switch($scope.arrRow[intIndex].strType) {
            case $scope.objRowType.ROW_TYPE_ITEM:
                $scope.arrRow[intIndex].intUnitPrice =  $scope.arrRow[intIndex].objCatalogue.floatPrice;
                $scope.arrRow[intIndex].strDescription =  $scope.arrRow[intIndex].objCatalogue.strDescription; 
                break;
            case $scope.objRowType.ROW_TYPE_TAX:
                // $scope.objRowType.ROW_TYPE_TAX =  $scope.arrRow[row_index].objCatalogue.objTaxType.intValue;
                $scope.arrRow[row_index].intUnitPrice = $scope.arrRow[intIndex].objTaxType.intValue;
                break;
        }
        //$scope.arrRow[row_index].objTaxType =  $scope.arrRow[row_index].objCatalogue.objTaxType;

    }

    $scope.getSubRowInfo = function(intRowIndex , intSubRowIndex){   
        switch($scope.arrRow[intRowIndex].arrSubRows[intSubRowIndex].strType) {
            case $scope.objRowType.ROW_TYPE_TAX:
                if($scope.arrRow[intRowIndex].arrSubRows[intSubRowIndex].objTaxType.strType == $scope.objTaxTypes.TYPE_PERCENT){
                    $scope.arrRow[intRowIndex].arrSubRows[intSubRowIndex].intUnitPrice = 
                    ($scope.arrRow[intRowIndex].arrSubRows[intSubRowIndex].objTaxType.intValue*
                        $scope.arrRow[intRowIndex].objCatalogue.floatPrice)/100;
                }else{
                    $scope.arrRow[intRowIndex].arrSubRows[intSubRowIndex].intUnitPrice = 
                    $scope.arrRow[intRowIndex].arrSubRows[intSubRowIndex].objTaxType.intValue;
                }

                break;
        }
    }

    $scope.addInvoice = function(boolContinue){
        // convert all rows and sub rows to one array of rows
        arr = ConvertToStraightArray($scope.arrRow);

        SrvInvoice.Add($scope.objInvoice, arr).then(function(response){
            if (response.data.result){ 
                AlertSuccess(response.data.title, response.data.message);
                if (boolContinue) {
                    ResetForm();
                } else {
                    window.location = 
                    "index.php?module="+_strModule+"&page=Invoice&action=View&invoice_id="+response.data.object.intID;
                }
            }else{
                AlertError(response.data.title, response.data.message);
            }
        })
    }

    $scope.getTotalCalculations = function(strType){
        objTotal = {
            intTotalItem:0,
            intTotalPrice:0,
            intTotalDiscount:0,
            intTotalTax:0
        };
        for (i = 0; i < $scope.arrRow.length; i++) {
            switch($scope.arrRow[i].strType) {
                case 'LNG_ENUM_ITEM':
                    objTotal.intTotalItem += Number($scope.arrRow[i].intQuantity);
                    objTotal.intTotalPrice += $scope.arrRow[i].intUnitPrice * $scope.arrRow[i].intQuantity;
                    break;
                case 'LNG_ENUM_DISCOUNT':
                    objTotal.intTotalDiscount += $scope.arrRow[i].intUnitPrice * $scope.arrRow[i].intQuantity;
                    break;
                case 'LNG_ENUM_TAX':
                    objTotal.intTotalTax += $scope.arrRow[i].intUnitPrice * $scope.arrRow[i].intQuantity;
                    break;
                default:
                    break;
            } 
            for(j=0; j < $scope.arrRow[i].arrSubRows.length; j++){
                switch($scope.arrRow[i].arrSubRows[j].strType) {
                    case 'LNG_ENUM_DISCOUNT':
                        objTotal.intTotalDiscount += $scope.arrRow[i].arrSubRows[j].intUnitPrice * $scope.arrRow[i].arrSubRows[j].intQuantity;
                        break;
                    case 'LNG_ENUM_TAX':
                        objTotal.intTotalTax += $scope.arrRow[i].arrSubRows[j].intUnitPrice * $scope.arrRow[i].arrSubRows[j].intQuantity;
                        break;
                    default:
                    break;
                } 
            }
        }
        switch(strType) {
            case 'Item':
                return objTotal.intTotalItem;
            case 'Price':
                return objTotal.intTotalPrice;
            case 'Discount':
                return objTotal.intTotalDiscount;
            case 'Net':
                return objTotal.intTotalPrice - objTotal.intTotalDiscount;
            case 'Tax':
                return objTotal.intTotalTax;
            case 'Gross':
                return objTotal.intTotalPrice - objTotal.intTotalDiscount + objTotal.intTotalTax;
            default:
                return 0;
        }
    }    

    $scope.getNextInvoiceNumber = function(){
        if($scope.isAuto){
            SrvInvoice.GetNextInvoiceNumber().then(function(response){
                $scope.objInvoice.strInvoiceNumber = response.data.intNextInvoiceNumber;
            })
        }else{

        }
    }

    function ConvertToStraightArray(arr2D){
        arrStraightLine = [];
        decOrderIncremental = 1.0;
        for(i = 0; i < arr2D.length; i++){
            arr2D[i].decOrder = decOrderIncremental;
            arrStraightLine.push(arr2D[i]);
            for(j = 0; j < arr2D[i].arrSubRows.length; j++){
                decOrderIncremental += 0.01;
                arr2D[i].arrSubRows[j].decOrder = decOrderIncremental;
                arrStraightLine.push(arr2D[i].arrSubRows[j]);
            }
            decOrderIncremental = Math.ceil(decOrderIncremental);
        }
        return  arrStraightLine;
    }
    function ResetForm(){
        $scope.objInvoice = {
            strInvoiceNumber:'',
            strReference:'',
            dtIssueDate:'',
            dtDueDate:'',
            tsCreatedOnTimestamp:'',
            objCustomer:null,
            objPaymentTerm:null,
            intStatusID:1,
        };
        $scope.isAuto = false;
        $scope.arrRow = [];
    }

    function IsEmpty(obj) {
        if(obj == null) return false;
        for(var key in obj) {
            if(obj[key] != null && obj[key] != 0 && obj[key] != -1 && key != "$$hashKey")
                return false;
        }
        return true;
    }

    function GetCatalogues() {
        SrvCatalogue.ListAll().then(function(response){
            if (response.data.result){
                $scope.arrCatalogues = response.data.object;
            }else{
                $scope.arrCatalogues = [];
                AlertError(response.data.title, response.data.message);
            }
        })
    }

    function GetTaxTypes() {
        SrvTaxType.List().then(function(response){
            if (response.data.result){
                $scope.arrTaxTypes = response.data.object;
            }else{
                $scope.arrTaxTypes = [];
                AlertError(response.data.title, response.data.message);
            }
        })
    }

    function GetCustomers() {
        SrvCustomer.ListAll().then(function(response){
            if (response.data.result){
                $scope.arrCustomers = response.data.object;
            }else{
                $scope.arrCustomers = [];
                AlertError(response.data.title, response.data.message);
            }
        })
    }

    function GetPaymentTerms() {
        SrvPaymentTerm.ListAll().then(function(response){
            if (response.data.result){
                $scope.arrPaymentTerms = response.data.object;
            }else{
                $scope.arrPaymentTerms = [];
                AlertError(response.data.title, response.data.message);
            }
        })
    }

    function Init(){ 
        GetCatalogues(); 
        GetTaxTypes();         
        GetCustomers();
        GetPaymentTerms();
    };

    Init();
}];

app.controller(controllers);