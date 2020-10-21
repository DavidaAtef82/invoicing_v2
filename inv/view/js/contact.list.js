controllers.Contact = ['$scope','SrvContact','SrvCustomer' , '$filter','$location','$window', function($scope,SrvContact ,SrvCustomer , $filter,$location,$window){ 
    $scope.objContacts = {arrData: [], intTotal: 0};
    $scope.objPaging = {
        intPageNo: 1,
        intPageSize: $scope.$parent.arrConfig.ListingPageSize,
        intMaxSize: $scope.$parent.arrConfig.MaxPaginationNumber
    };
    $scope.objFilter = {
        strName: '',
        strPhone: '',
        strEmail:'',
        strNotes: '',
        intCustomerID: -1,
    };
    $scope.objAddContact = {
        strName: '',
        strPhone: '',
        strEmail:'',
        strNotes: '',
        intCustomerID: -1,
    };
    $scope.intRemoveContact = -1;
    $scope.objCurrentContact = $scope.objFilter;
    $scope.okDelete = false;

    $scope.arrCustomers = [];

    $scope.filter = function(){
        if($scope.objFilter.intCustomerID == null ||$scope.objFilter.intCustomerID === undefined){
            $scope.objFilter.intCustomerID = -1;
        }
        $scope.objPaging.intPageNo = 1;
        SrvContact.List($scope.objFilter, $scope.objPaging).then(function(response){
            if (response.data.result){
                $scope.objContacts = response.data.object;
            }else{
                $scope.objContacts = {arrData: [], intTotal: 0}
                AlertError(response.data.title, response.data.message);
            }
        })
    }
    $scope.reset = function(){
        $scope.objPaging = {intPageNo: 1,intPageSize: $scope.$parent.arrConfig.ListingPageSize,intMaxSize: $scope.$parent.arrConfig.MaxPaginationNumber};
        $scope.objFilter = {
            strName: '',
            strPhone: '',
            strEmail:'',
            strNotes: '',
            intCustomerID: -1,
        };      
        SrvContact.List($scope.objFilter, $scope.objPaging).then(function(response){
            if (response.data.result){
                $scope.objContacts = response.data.object;
            }else{
                $scope.objContacts = {arrData: [], intTotal: 0}
                AlertError(response.data.title, response.data.message);
            }
        })    
    }

    $scope.addContact = function(){
       // $scope.objAddContact.intCustomerID = $scope.intAddCustomer;
        SrvContact.Add($scope.objAddContact).then(function(response){
            if (response.data.result){ 
                $scope.objContacts.arrData.push(response.data.object);
                AlertSuccess(response.data.title, response.data.message);
            }else{
                AlertError(response.data.title, response.data.message);
            }
        })
    }

    $scope.edit = function(objContact ,index){
        var myJSON = JSON.stringify(objContact);
        $scope.objEditContact = JSON.parse(myJSON);
        $scope.objEditIndex = index;
        $scope.intEditCustomer = $scope.objEditContact.objCustomer.intID;
    }
    $scope.editContact = function() {
        //$scope.objEditContact.intCustomerID = $scope.intEditCustomer;
        SrvContact.Update($scope.objEditContact).then(function(response){
            if (response.data.result){ 
                $scope.objContacts.arrData[$scope.objEditIndex] = response.data.object;
                $scope.objEditContact = '';
                $scope.objEditIndex = '' ;
                AlertSuccess(response.data.title, response.data.message);
            }else{
                AlertError(response.data.title, response.data.message);
            }
        })
    }
    $scope.remove = function(intContactID, index){
        $scope.intRemoveContact =  intContactID;
        $scope.intRemoveIndex =  index;
    }
    $scope.removeContact = function(){
        SrvContact.Delete($scope.intRemoveContact).then(function(response){
            if (response.data.result){
                AlertSuccess(response.data.title, response.data.message);
                listContacts();
            }else{
                AlertError(response.data.title, response.data.message);
            }
        })
    }

    function listContacts(){
        SrvContact.List($scope.objFilter, $scope.objPaging).then(function(response){
            if (response.data.result){
                $scope.objContacts = response.data.object;
            }else{
                $scope.objContacts = {arrData: [], intTotal: 0}
                AlertError(response.data.title, response.data.message);
            }
        });  
    }
    function init(){            
        listContacts();
        GetCustomers();
    };
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

    init();
}];

app.controller(controllers);