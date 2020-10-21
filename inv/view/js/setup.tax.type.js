controllers.TaxType = ['$scope','SrvTaxType', '$filter','$location','$window', function($scope,SrvTaxType,$filter,$location,$window){
    $scope.intTaxDeleteID = null;

    function init(){
        $scope.objTaxAdd = {
            strType:'',
            strTaxType:'',
            intValue:''
        };
        $scope.objTaxEdit = {
            intID:-1,
            strType:'',
            strTaxType:'',
            intValue:''
        };
        $scope.intTaxDeleteID = -1;
        $scope.arrTaxType = Object.values(objEnum.ClsBllTaxType);
        console.log($scope.arrTaxType);
    }
    function list(){
        SrvTaxType.List().then(function(response){
            if (response.data.result){
                $scope.arrTaxTypes = response.data.object;
            }
        });  
    }
    $scope.add = function(){
        SrvTaxType.Add($scope.objTaxAdd).then(function(response){
            if (response.data.result){ 
                $scope.arrTaxTypes.push(response.data.object);
                console.log( $scope.arrTaxTypes);
                AlertSuccess(response.data.title, response.data.message);
            }else{
                AlertError(response.data.title, response.data.message);
            }
        })    
    }
    $scope.edit = function(objTax, index){
        var myJSON = JSON.stringify(objTax);
        $scope.objTaxEdit = JSON.parse(myJSON);
        $scope.editIndex = index;
    }
    $scope.editTax = function(){
        SrvTaxType.Update($scope.objTaxEdit).then(function(response){
            if (response.data.result){               
                $scope.arrTaxTypes[$scope.editIndex] = response.data.object;
                AlertSuccess(response.data.title, response.data.message);
            }else{
                AlertError(response.data.title, response.data.message);
            }
        })
    }
    $scope.remove =  function(intTaxID){
        $scope.intTaxDeleteID = intTaxID;
    }
    $scope.removeTax = function($intID){
        SrvTaxType.Delete($scope.intTaxDeleteID).then(function(response){
            if (response.data.result){
                AlertSuccess(response.data.title, response.data.message);
                list();
            }else{
                AlertError(response.data.title, response.data.message);
            }
        })   
    }

    init();
    list();

}];

app.controller(controllers);