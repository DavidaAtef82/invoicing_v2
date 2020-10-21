controllers.Invoice = ['$scope','SrvInvoice' ,'SrvInvoiceRow','$filter','$location','$window', function($scope,SrvInvoice ,SrvInvoiceRow ,$filter,$location,$window){ 

    $scope.arrRows = [];
    $scope.intInvoiceID = $location.url($location.$$absUrl).search().invoice_id;

    $scope.removeInvoice = function(){
        SrvInvoice.Delete($scope.objInvoice.intID).then(function(response){
            if (response.data.result){
                AlertSuccess(response.data.title, response.data.message);
                window.location = "index.php?module="+_strModule+"&page=Invoice&action=List";          
            }else{
                AlertError(response.data.title, response.data.message);
            }
        })
    }

    function loadInvoice(){
        SrvInvoice.View($scope.intInvoiceID).then(function(response){

            if(response.data.result == false){
                AlertError(response.data.title,response.data.message);
                return;
            }
            $scope.objInvoice = response.data.object;
            $scope.arrRows = $scope.objInvoice.arrInvoiceRow;
            // Check if this invocie accepts payments 
            $scope.boolGenerateLink = false;
            if($scope.objInvoice.intStatusID == 2 || $scope.objInvoice.intStatusID == 4 ){ // Partial or sent
                if($scope.objInvoice.decGrossAmount - $scope.objInvoice.decTotalPayment != 0){
                    $scope.boolGenerateLink = true;
                }
            }
        }); 
    }
    $scope.generateLink = function(intInvoiceID){
        $scope.strGeneratedLink = "http://localhost/ks_invoicing/index.php?module=inv&page=OnlinePayment&action=Add&invoice_id=";
        $scope.strGeneratedLink += $scope.objInvoice['strEncryptedID'];
        console.log($scope.strGeneratedLink);
    }
    loadInvoice();
}];

app.controller(controllers);