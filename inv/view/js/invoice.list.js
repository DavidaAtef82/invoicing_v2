controllers.Invoice = ['$scope','SrvInvoice','SrvUser','SrvCustomer' ,'SrvPaymentTerm' ,'SrvStatus', '$filter','$location','$window', function($scope,SrvInvoice ,SrvUser ,SrvCustomer ,SrvPaymentTerm,SrvStatus , $filter,$location,$window){ 

    $scope.objInvoices = {arrData: [], intTotal: 0};
    $scope.objPaging = {
        intPageNo: 1,
        intPageSize: $scope.$parent.arrConfig.ListingPageSize,
        intMaxSize: $scope.$parent.arrConfig.MaxPaginationNumber
    };
    $scope.objFilter = {
        strInvoiceNumber:'',
        strReference:'',
        dtIssueDateFrom:'',
        dtIssueDateTo:'',    
        dtDueDateFrom:'',
        dtDueDateTo:'',
        tsCreatedOnTimestamp:'',
        intCreatedByUserID:-1,
        intCustomerID:-1,
        strCustomerName:'',
        intPaymentTermID:-1,
        intStatusID:-1,
        boolShowOverdue:false
    };
    $scope.arrUsers = [];
    $scope.arrCustomers = [];
    $scope.arrPaymentTerms = [];
    $scope.arrStatus = [];
    $scope.objEnumStatus = objEnum.ClsBllInvoiceStatus;

    arrStatusManualChangeable = {   
        1 : {'ID':2,'Status':'LNG_ENUM_STATUS_SENT'},
        2 : {'ID':7,'Status':'LNG_ENUM_STATUS_VOID'},
        4 : {'ID':6,'Status':'LNG_ENUM_STATUS_WRITE_OFF'}};
    intCurrentIndex = -1;
    intRemoveInvoiceID = -1;

    $scope.filter = function(){  
        if($scope.objFilter.intCreatedByUserID == null ||$scope.objFilter.intCustomerID == undefined){
            $scope.objFilter.intCreatedByUserID = -1;
        }
        if($scope.objFilter.intCustomerID == null ||$scope.objFilter.intCustomerID == undefined){
            $scope.objFilter.intCustomerID = -1;
        }
        if($scope.objFilter.intPaymentTermID == null ||$scope.objFilter.intPaymentTermID == undefined){
            $scope.objFilter.intPaymentTermID = -1;
        } 
        if($scope.objFilter.intStatusID == null ||$scope.objFilter.intStatusID == undefined){
            $scope.objFilter.intStatusID = -1;
        }

        $scope.objPaging.intPageNo = 1;
        SrvInvoice.List($scope.objFilter, $scope.objPaging).then(function(response){
            if (response.data.result){
                $scope.objInvoices = response.data.object;
            }else{
                $scope.objInvoices = {arrData: [], intTotal: 0}
                AlertError(response.data.title, response.data.message);
            }
        })
    }
    $scope.reset = function(){
        $scope.objPaging = {
            intPageNo: 1,
            intPageSize: $scope.$parent.arrConfig.ListingPageSize,
            intMaxSize: $scope.$parent.arrConfig.MaxPaginationNumber
        };
        $scope.objFilter = {
            strInvoiceNumber:'',
            strReference:'',
            dtIssueDate:'',
            dtDueDate:'',
            tsCreatedOnTimestamp:'',
            intCreatedByUserID:-1,
            intCustomerID:-1,
            strCustomerName:'',
            intPaymentTermID:-1,
            intStatusID:-1,
        };      
        SrvInvoice.List($scope.objFilter, $scope.objPaging).then(function(response){
            if (response.data.result){
                $scope.objInvoices = response.data.object;
            }else{
                $scope.objInvoices = {arrData: [], intTotal: 0}
                AlertError(response.data.title, response.data.message);
            }
        })    
    }
    $scope.remove = function(intInvoiceID, index){
        intRemoveInvoiceID = intInvoiceID;
        intCurrentIndex = index;
    }
    $scope.removeInvoice = function(){
        SrvInvoice.Delete(intRemoveInvoiceID).then(function(response){
            if (response.data.result){
                AlertSuccess(response.data.title, response.data.message);
                $scope.objInvoices.arrData.splice(intCurrentIndex, 1);            
            }else{
                AlertError(response.data.title, response.data.message);
            }
        })
    }
    $scope.isStatusDisabled = function(strStatusType){
        switch(strStatusType){
            case 'LNG_ENUM_STATUS_DRAFT':
                return false;
                break;
            case 'LNG_ENUM_STATUS_SENT':
                return false;
                break;
            case 'LNG_ENUM_STATUS_PARTIAL':
                return false;
                break;
            default:
                return true;
                break;
        }
    }
    $scope.changeStatus = function(index){
        intCurrentStatusID =  $scope.objInvoices.arrData[index].intStatusID; 
        $scope.objNewStatus = arrStatusManualChangeable[intCurrentStatusID];
        intCurrentIndex =  index;
    }
    $scope.updateStatus = function(){
        SrvInvoice.UpdateStatus($scope.objInvoices.arrData[intCurrentIndex].intID, $scope.objNewStatus.ID).then(function(response){
            if (response.data.result){
                AlertSuccess(response.data.title, response.data.message);
                $scope.objInvoices.arrData[intCurrentIndex].intStatusID = response.data.object.intStatusID; 
                $scope.objInvoices.arrData[intCurrentIndex].objStatus = response.data.object.objStatus;     
            }else{
                AlertError(response.data.title, response.data.message);
            }
        })
    }

    $scope.nextPage = function(){
        SrvInvoice.List($scope.objFilter, $scope.objPaging).then(function(response){
            if (response.data.result){
                $scope.objInvoices = response.data.object;
            }else{
                $scope.objInvoices = {arrData: [], intTotal: 0}
                AlertError(response.data.title, response.data.message);
            }
        });   
    }
     
     
    function listInvoices(){
        SrvInvoice.List($scope.objFilter, $scope.objPaging).then(function(response){
            if (response.data.result){
                $scope.objInvoices = response.data.object;
            }else{
                $scope.objInvoices = {arrData: [], intTotal: 0}
                AlertError(response.data.title, response.data.message);
            }
        });  
    }

    function GetUsers() {
        SrvUser.ListAll().then(function(response){
            if (response.data.result){
                $scope.arrUsers = response.data.object;
            }else{
                $scope.arrUsers = {arrData: [], intTotal: 0}
                AlertError(response.data.title, response.data.message);
            }
        })
    }
    function GetCustomers() {
        SrvCustomer.ListAll().then(function(response){
            if (response.data.result){
                $scope.arrCustomers = response.data.object;
            }else{
                $scope.arrCustomers = {arrData: [], intTotal: 0}
                AlertError(response.data.title, response.data.message);
            }
        })
    }
    function GetPaymentTerms() {
        SrvPaymentTerm.ListAll().then(function(response){
            if (response.data.result){
                $scope.arrPaymentTerms = response.data.object;
            }else{
                $scope.arrPaymentTerms = {arrData: [], intTotal: 0}
                AlertError(response.data.title, response.data.message);
            }
        })
    }
    function GetStatus() {
        SrvStatus.ListAll().then(function(response){
            if (response.data.result){
                $scope.arrStatus = response.data.object;
            }else{
                $scope.arrStatus = {arrData: [], intTotal: 0}
                AlertError(response.data.title, response.data.message);
            }
        })
    }

    function init(){            
        listInvoices();
        GetUsers();
        GetCustomers();
        GetPaymentTerms();
        GetStatus();
    };

    init();
}];
app.controller(controllers);
 