controllers.Customer = ['$scope','SrvCustomer','SrvCountry', 'SrvCity', '$filter','$location','$window', function($scope,SrvCustomer ,SrvCountry ,SrvCity, $filter,$location,$window){ 
    $scope.objCustomers = {arrData: [], intTotal: 0}
    $scope.objPaging = {
        intPageNo: 1,
        intPageSize: $scope.$parent.arrConfig.ListingPageSize,
        intMaxSize: $scope.$parent.arrConfig.MaxPaginationNumber
    };
    $scope.objFilter = {
        strName: '',
        strAddress : '',
        strPhone: '',
        strEmail:'',
        strNotes: '',
        intCityID:-1,
        intCountryID:-1
    };
    $scope.objAddCustomer = {
        strName: '',
        strAddress : '',
        strPhone: '',
        strEmail:'',
        strNotes: '',
        intCityID:-1,
        intCountryID:-1
    };
    $scope.intRemoveCustomer = -1;
    $scope.objCurrentCustomer = $scope.objFilter;
    $scope.okDelete = false;
    $scope.arrCountries = [];
    $scope.arrCities = [];

    $scope.filter = function(){
        if($scope.objFilter.intCountryID == null){
            $scope.objFilter.intCountryID = -1;
        }
        if($scope.objFilter.intCityID === null ||$scope.objFilter.intCityID === undefined){
            $scope.objFilter.intCityID = -1;
        }  
        $scope.objPaging.intPageNo = 1;
        SrvCustomer.List($scope.objFilter, $scope.objPaging).then(function(response){
            if (response.data.result){
                $scope.objCustomers = response.data.object;
            }else{
                $scope.objCustomers = {arrData: [], intTotal: 0}
                AlertError(response.data.title, response.data.message);
            }
        })
    }
    $scope.reset = function(){
        $scope.objPaging = {intPageNo: 1,intPageSize: $scope.$parent.arrConfig.ListingPageSize,intMaxSize: $scope.$parent.arrConfig.MaxPaginationNumber};
        $scope.objFilter = {
            strName: '',
            strAddress : '',
            strPhone: '',
            strNotes: '',
            intCityID:-1,
            intCountryID:-1
        };      
        SrvCustomer.List($scope.objFilter, $scope.objPaging).then(function(response){
            if (response.data.result){
                $scope.objCustomers = response.data.object;
            }else{
                $scope.objCustomers = {arrData: [], intTotal: 0}
                AlertError(response.data.title, response.data.message);
            }
        })    
    }
    $scope.addCustomer = function(){
        $scope.objAddCustomer.intCountryID = $scope.intAddCountry;
        $scope.objAddCustomer.intCityID = $scope.intAddCity;
        SrvCustomer.Add($scope.objAddCustomer).then(function(response){
            if (response.data.result){ 
                $scope.objCustomers.arrData.push(response.data.object);
                AlertSuccess(response.data.title, response.data.message);
            }else{
                AlertError(response.data.title, response.data.message);
            }
        })
    }

    $scope.edit = function(objCustomer ,index){
        var myJSON = JSON.stringify(objCustomer);
        $scope.objEditCustomer = JSON.parse(myJSON);
        $scope.objEditIndex = index;
        $scope.intEditCountry = $scope.objEditCustomer.objCity.objCountry.intID;
        $scope.GetCities($scope.intEditCountry);
        $scope.intEditCity =  $scope.objEditCustomer.intCityID;
    }
    $scope.editCustomer = function() {
        $scope.objEditCustomer.intCityID = $scope.intEditCity;
        SrvCustomer.Update($scope.objEditCustomer).then(function(response){
            if (response.data.result){ 
                $scope.objCustomers.arrData[$scope.objEditIndex] = response.data.object;
                $scope.objEditCustomer = '';
                $scope.objEditIndex = '' ;
                AlertSuccess(response.data.title, response.data.message);
            }else{
                AlertError(response.data.title, response.data.message);
            }
        })
    }
    $scope.remove = function(intCustomerID, index){
        $scope.intRemoveCustomer =  intCustomerID;
        $scope.intRemoveIndex =  index;
    }
    $scope.removeCustomer = function(){
        SrvCustomer.Delete($scope.intRemoveCustomer).then(function(response){
            if (response.data.result){
                //delete $scope.objCustomers.arrData[$scope.intRemoveIndex];
                AlertSuccess(response.data.title, response.data.message);
                listCustomers();
            }else{
                AlertError(response.data.title, response.data.message);
            }
        })
    }
    $scope.GetCities = function(intCountryID) {
        if(intCountryID == null){
            $scope.arrCities = [];
        }else{
            SrvCity.ListByCountryID(intCountryID).then(function(response){
                if (response.data.result){
                    $scope.arrCities = response.data.object; 
                }else{
                    $scope.arrCities = {arrData: [], intTotal: 0}
                    AlertError(response.data.title, response.data.message);
                }
            })
        }
    }

    function listCustomers(){
        SrvCustomer.List($scope.objFilter, $scope.objPaging).then(function(response){
            if (response.data.result){
                $scope.objCustomers = response.data.object;
            }else{
                $scope.objCustomers = {arrData: [], intTotal: 0}
                AlertError(response.data.title, response.data.message);
            }
        });  
    }
    function init(){            
        listCustomers();
        GetCountries();
    };
    function GetCountries() {
        SrvCountry.List().then(function(response){
            if (response.data.result){
                $scope.arrCountries = response.data.object;
            }else{
                $scope.arrCountries = {arrData: [], intTotal: 0}
                AlertError(response.data.title, response.data.message);
            }
        })
    }

    init();
}];

app.controller(controllers);