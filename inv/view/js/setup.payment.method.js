controllers.PaymentMethod = ['$scope','SrvPaymentMethod', '$filter','$location','$window', function($scope,SrvPaymentMethod,$filter,$location,$window){
    function init(){
        $scope.objMethodAdd = {
            strType:'',
            boolManual:false,
            boolDisabled:false,
            strDetails:''
        };
        $scope.objMethodEdit = {
            intID:-1,
            strType:'',
            boolManual:false,
            boolDisabled: false,
            strDetails:''
        };
        $scope.intMethodDeleteID = -1;
    }
    function list(){
        SrvPaymentMethod.List(0).then(function(response){
            if (response.data.result){
                $scope.arrPayMethods = response.data.object;
            }
        });  
    }
    $scope.add = function(){
        console.log($scope.objMethodAdd);
        SrvPaymentMethod.Add($scope.objMethodAdd).then(function(response){
            if (response.data.result){
                if(response.data.object.boolManual == true){
                    response.data.object.boolManual = 1;  
                }
                else{
                    response.data.object.boolManual = 0;
                } 
                $scope.arrPayMethods.push(response.data.object);
                AlertSuccess(response.data.title, response.data.message);
            }else{
                AlertError(response.data.title, response.data.message);
            }
        })    
    }
    $scope.edit = function(objMethod, index){
        var myJSON = JSON.stringify(objMethod);
        $scope.objMethodEdit = JSON.parse(myJSON);
        if($scope.objMethodEdit.boolManual == 1){
            $scope.objMethodEdit.boolManual = true;  
        }
        else{
            $scope.objMethodEdit.boolManual = false;
        }
        if($scope.objMethodEdit.boolDisabled == 1){
            $scope.objMethodEdit.boolDisabled = true;  
        }
        else{
            $scope.objMethodEdit.boolDisabled = false;
        }
        $scope.editIndex = index;
    }
    $scope.editMethod = function(){
        SrvPaymentMethod.Update($scope.objMethodEdit).then(function(response){
            if (response.data.result){
                if(response.data.object.boolManual == true){
                    response.data.object.boolManual = 1;  
                }
                else{
                    response.data.object.boolManual = 0;
                }
                if(response.data.object.boolDisabled == true){
                    response.data.object.boolDisabled = 1;  
                }
                else{
                    response.data.object.boolDisabled = 0;
                }                
                $scope.arrPayMethods[$scope.editIndex] = response.data.object;
                AlertSuccess(response.data.title, response.data.message);
            }else{
                AlertError(response.data.title, response.data.message);
            }
        })
    }
    $scope.remove =  function(intMethodID){        
        $scope.intMethodDeleteID = intMethodID;
    }
    $scope.removeMethod = function(){
        SrvPaymentMethod.Delete($scope.intMethodDeleteID).then(function(response){
            if (response.data.result){
                AlertSuccess(response.data.title, response.data.message);
                list();
            }else{
                AlertError(response.data.title, response.data.message);
            }
        })   
    }
    $scope.details = function(strType, strDetails){
        $scope.strType = strType;
        $scope.strDetails = strDetails; 
    }
    init();
    list();

}];

app.controller(controllers);