controllers.PaymentTerm = ['$scope','SrvPaymentTerm' , '$filter','$location','$window', function($scope,SrvPaymentTerm , $filter,$location,$window){ 
    $scope.objPaymentTerms= {arrData: [], intTotal: 0};
    $scope.objPaging = {
        intPageNo: 1,
        intPageSize: $scope.$parent.arrConfig.ListingPageSize,
        intMaxSize: $scope.$parent.arrConfig.MaxPaginationNumber
    };
    $scope.objFilter = {strName: ''};
    $scope.objAddPaymentTerm = {
        strName: '',
        strDescription: ''
    };
    $scope.intRemovePaymentTerm = -1;
    $scope.objCurrentPaymentTerm = $scope.objFilter;

    $scope.filter = function(){
        $scope.objPaging.intPageNo = 1;
        SrvPaymentTerm.List($scope.objFilter, $scope.objPaging).then(function(response){
            if (response.data.result){
                $scope.objPaymentTerms = response.data.object;
            }else{
                $scope.objPaymentTerms = {arrData: [], intTotal: 0}
                AlertError(response.data.title, response.data.message);
            }
        })
    }
    $scope.reset = function(){
        $scope.objPaging = {intPageNo: 1,intPageSize: $scope.$parent.arrConfig.ListingPageSize,intMaxSize: $scope.$parent.arrConfig.MaxPaginationNumber};
        $scope.objFilter = {strName: ''};      
        SrvPaymentTerm.List($scope.objFilter, $scope.objPaging).then(function(response){
            if (response.data.result){
                $scope.objPaymentTerms = response.data.object;
            }else{
                $scope.objPaymentTerms = {arrData: [], intTotal: 0}
                AlertError(response.data.title, response.data.message);
            }
        })    
    }
    
    $scope.addPaymentTerm = function(){
        SrvPaymentTerm.Add($scope.objAddPaymentTerm).then(function(response){
            if (response.data.result){ 
                $scope.objPaymentTerms.arrData.push(response.data.object);
                AlertSuccess(response.data.title, response.data.message);
            }else{
                AlertError(response.data.title, response.data.message);
            }
        })
    }

    $scope.edit = function(objPaymentTerm ,index){
        var myJSON = JSON.stringify(objPaymentTerm);
        $scope.objEditPaymentTerm = JSON.parse(myJSON);
        $scope.objEditIndex = index;
    }
    $scope.editPaymentTerm = function() {
        SrvPaymentTerm.Update($scope.objEditPaymentTerm).then(function(response){
            if (response.data.result){ 
                $scope.objPaymentTerms.arrData[$scope.objEditIndex] = response.data.object;
                $scope.objEditPaymentTerm = '';
                $scope.objEditIndex = '' ;
                AlertSuccess(response.data.title, response.data.message);
            }else{
                AlertError(response.data.title, response.data.message);
            }
        })
    }
    $scope.remove = function(intPaymentTermID, index){
        $scope.intRemovePaymentTerm =  intPaymentTermID;
        $scope.intRemoveIndex =  index;
    }
    $scope.removePaymentTerm = function(){
        SrvPaymentTerm.Delete($scope.intRemovePaymentTerm).then(function(response){
            if (response.data.result){
                AlertSuccess(response.data.title, response.data.message);
                listPaymentTerms();
            }else{
                AlertError(response.data.title, response.data.message);
            }
        })
    }

    function listPaymentTerms(){
        SrvPaymentTerm.List($scope.objFilter, $scope.objPaging).then(function(response){
            if (response.data.result){
                $scope.objPaymentTerms = response.data.object;
            }else{
                $scope.objPaymentTerms = {arrData: [], intTotal: 0}
                AlertError(response.data.title, response.data.message);
            }
        });  
    }
    function init(){            
        listPaymentTerms();
    };
    init();
}];

app.controller(controllers);