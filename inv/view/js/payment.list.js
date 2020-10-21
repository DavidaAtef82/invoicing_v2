controllers.Payment = ['$scope','SrvPayment','SrvPaymentMethod','SrvUser','SrvCustomer','SrvInvoice','$filter','$location','$window', function($scope,SrvPayment,SrvPaymentMethod,SrvUser,SrvCustomer,SrvInvoice,$filter,$location,$window){ 
    function init(){
        $style = true;
        $scope.objPaging = {
            intPageNo: 1,
            intPageSize: $scope.$parent.arrConfig.ListingPageSize,
            intMaxSize: $scope.$parent.arrConfig.MaxPaginationNumber
        };
        $scope.objFilter = {
            intPaymentMethodID:-1,
            intCustomerID :-1,
            strReference:'',
            dtDateBefore:'',
            dtDateAfter:'',
            decAmount:'',
            strAmountOperator:'',
            intCreatedByUserID:-1
        };
        $scope.objPaymentAdd = {
            intPaymentMethodID:'',
            intCustomerID :'',
            strReference:'',
            dtDate:'',
            decAmount:'',
            intCreatedByUserID:-1 
        }
        $scope.objPayments = {arrData: [], intTotal: 0};
        $scope.listPayments();
        $scope.arrPaymentMethod=[];
        $scope.arrPaymentMethodAdd=[];
        $scope.intPaymentDeleteID =-1;
        $scope.intPaymentDeleteIndex =-1;
        listPaymentmethods(1);
        listPaymentmethods(0);
        listUsers();
        listCustomers();
    }
    function listPaymentmethods(bolManual){
        SrvPaymentMethod.List(bolManual).then(function(response){
            if (response.data.result){
                if(!bolManual){ // Get all payment methodss to list it in filter dropdwon menu.
                    $scope.arrPaymentMethod = response.data.object;                
                }
                if(bolManual){ // Get manual payment methodss to list it in add dropdwon menu.
                    $scope.arrPaymentMethodAdd = response.data.object;
                }
            }
        });    
    }
    function listUsers(){
        SrvUser.ListAll().then(function(response){
            if (response.data.result){
                $scope.arrUsers = response.data.object;
            }
        });  
    }
    function listCustomers(){
        SrvCustomer.ListAll().then(function(response){
            if (response.data.result){
                $scope.arrCustomer = response.data.object;
            }  
        })
    }

    $scope.listPayments = function(){
        SrvPayment.List($scope.objFilter, $scope.objPaging).then(function(response){
            if (response.data.result){
                $scope.objPayments = response.data.object;
            }else{
                $scope.objPayments = {arrData: [], intTotal: 0}
                AlertError(response.data.title, response.data.message);
            }
        });  
    }
    $scope.nextPage = function(){
      SrvPayment.List($scope.objFilter, $scope.objPaging).then(function(response){
            if (response.data.result){
                $scope.objPayments = response.data.object;
            }else{
                $scope.objPayments = {arrData: [], intTotal: 0}
                AlertError(response.data.title, response.data.message);
            }
        });   
    }
    $scope.reset = function (){
        init();
    }
    $scope.remove =  function(intPaymentID, intPaymentIndex){        
        $scope.intPaymentDeleteID = intPaymentID;
        $scope.intPaymentDeleteIndex = intPaymentIndex;

    }
    $scope.removePayment = function(){
        SrvPayment.Delete($scope.intPaymentDeleteID).then(function(response){
            if (response.data.result){
                AlertSuccess(response.data.title, response.data.message);
                $scope.listPayments();
            }else{
                AlertError(response.data.title, response.data.message);
            }
        })             
    }

    $scope.add = function(){
        SrvPayment.Add($scope.objPaymentAdd).then(function(response){
            if (response.data.result){
                $scope.listPayments();
                AlertSuccess(response.data.title, response.data.message);
            }else{
                AlertError(response.data.title, response.data.message);
            }
        })
    }
    $scope.completeCustomerName = function(strCustomrName){
        $scope.customerList = [];  
        angular.forEach($scope.arrCustomer, function (obj){
            if(obj.strName.toLowerCase().indexOf(strCustomrName.toLowerCase())!=-1){
                $scope.customerList.push(obj);
            }
        });
    }
    $scope.getInvoices = function (intPaymentID){
        SrvPayment.ListInvoices(intPaymentID).then(function(response){
            if (response.data.result){
                $scope.arrInvoice=response.data.object;
            }else{
                $scope.arrInvoice=[]; 
            }
        }) 
    }
    init();
}];

app.controller(controllers);