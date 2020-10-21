controllers.Invoice = ['$scope','SrvInvoice' ,'SrvInvoiceRow' ,'SrvCatalogue' ,'SrvTaxType' ,'SrvCustomer' ,'SrvPaymentTerm' ,'SrvStatus', '$filter','$location','$window', function($scope,SrvInvoice ,SrvInvoiceRow ,SrvCatalogue ,SrvTaxType ,SrvCustomer ,SrvPaymentTerm ,SrvStatus , $filter,$location,$window){ 

    $scope.intInvoiceID = $location.url($location.$$absUrl).search().invoice_id;
    $scope.arrRowTypes = Object.values(objEnum.ClsBllInvoiceRowType);
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

    $scope.arrCatalogues = [];
    $scope.arrTaxTypes = [];
    $scope.arrCustomers = [];
    $scope.arrPaymentTerms = [];

    $scope.arrRow = [];

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

    $scope.newRow = function(){
        $scope.objInvoiceRow = {
            objCatalogue:null,
            objTaxType:null,
            strType:'',
            intUnitPrice:'',
            intQuantity:'',
            intTotalPrice:'',
            intOrder:-1,
            strDescription:'',
        };
        $scope.arrRow.push($scope.objInvoiceRow);  
    }

    $scope.deleteRow = function(row_index){
        res = isEmpty($scope.arrRow[row_index]);
        if(!res){
            res = confirm("Are you sure you want to delete this row ?");
            if(!res){
                return;
            }   
        }
        $scope.arrRow.splice(row_index, 1);
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

    $scope.editInvoice = function(){
        SrvInvoice.Update($scope.objInvoice, $scope.arrRow).then(function(response){
            if (response.data.result){ 
                AlertSuccess(response.data.title, response.data.message);
                window.location = "index.php?module="+_strModule+"&page=Invoice&action=View&invoice_id="+$scope.objInvoice.intID;
            }else{
                AlertError(response.data.title, response.data.message);
            }
        })
    }

    $scope.cancelEditInvoice = function(){
        window.location = "index.php?module="+_strModule+"&page=Invoice&action=List";
    }  

    $scope.getCatalogueInfo = function(row_index){
        switch($scope.arrRow[row_index].strType) {
            case 'LNG_ENUM_ITEM':
                $scope.arrRow[row_index].intUnitPrice =  $scope.arrRow[row_index].objCatalogue.floatPrice;
                break;
            case 'LNG_ENUM_DISCOUNT':
                $scope.arrRow[row_index].intUnitPrice = '';
                break;
            case 'LNG_ENUM_TAX':
                $scope.arrRow[row_index].intUnitPrice =  $scope.arrRow[row_index].objCatalogue.objTaxType.intValue;
                break;
        }
        $scope.arrRow[row_index].objTaxType =  $scope.arrRow[row_index].objCatalogue.objTaxType;
        $scope.arrRow[row_index].strDescription =  $scope.arrRow[row_index].objCatalogue.strDescription; 
    }

    $scope.getTotalCalculations = function(strType){
        objTotal = {intTotalItem:0,intTotalPrice:0,intTotalDiscount:0,intNetAmount:0,intTotalTax:0,intGrandTotal:0};
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
            case 'Grand':
                return objTotal.intTotalPrice - objTotal.intTotalDiscount + objTotal.intTotalTax;
            default:
                return 0;
        }
    }  

    function loadInvoice(){
        SrvInvoice.View($scope.intInvoiceID).then(function(response){
            if(response.data.result == false){
                AlertError(response.data.title,response.data.message);
                return;
            }
            $scope.objInvoice = response.data.object; 
            $scope.objInvoice.dtIssueDate = new Date($scope.objInvoice.dtIssueDate);
            $scope.objInvoice.dtDueDate = new Date($scope.objInvoice.dtDueDate);
            arrAllRow =  $scope.objInvoice.arrInvoiceRow;
            $scope.arrRow = DivideIntoSubRows(arrAllRow);
            if($scope.arrRow == null ||$scope.arrRow.length == 0){
                $scope.arrRow = [$scope.objInvoiceRow];
            }
        }); 
    }

    function  DivideIntoSubRows(arrStraightLine){
        arr = [];
        for(i =0 ; i < arrStraightLine.length ; ){
            intparentRow = i;
            arr[intparentRow] = arrStraightLine[intparentRow];
            i++;
            while (Math.floor(arrStraightLine[i].decOrder) ==  arr[intparentRow].decOrder){
                arr[intparentRow].arrSubRows.push(arrStraightLine[i]);
                i++; 
            }
        }
        return  arr;   
    }

    function isEmpty(obj) {
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

    function init(){  
        GetCatalogues();
        GetTaxTypes();          
        GetCustomers();
        GetPaymentTerms();
        loadInvoice();
    };

    init();
}];

app.controller(controllers);