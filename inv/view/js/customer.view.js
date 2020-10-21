controllers.Customer = ['$scope','SrvCustomer','SrvCountry', 'SrvCity', '$filter','$location','$window', function(
    $scope,SrvCustomer ,SrvCountry ,SrvCity, $filter,$location,$window){

        $scope.objCustomer = null;
        $scope.intID = $location.url($location.$$absUrl).search().customer_id;

        $scope.intTotalTransactions = 0;  // Total Transactions (Sent + Partial + Paid)
        $scope.intTotalPayments = 0;
        $scope.intTotalRemaining = 0; 

        $scope.objEnumStatus = objEnum.ClsBllInvoiceStatus;

        $scope.removeCustomer = function(){
            SrvCustomer.Delete($scope.intID).then(function(response){
                if (response.data.result){
                    AlertSuccess(response.data.title, response.data.message);
                    window.location = "index.php?module="+_strModule+"&page=Customer&action=List";          
                }else{
                    AlertError(response.data.title, response.data.message);
                }
            })
        }

        function loadCustomer(){
            SrvCustomer.View($scope.intID).then(function(response){
                if(response.data.result == false){
                    AlertError(response.data.title,response.data.message);
                    return;
                }
                $scope.objCustomer = response.data.object;
                getTotalAmounts();
            }); 
        }

        function getTotalAmounts(){ 
            if($scope.objCustomer == null){
                return 0;
            }
            angular.forEach($scope.objCustomer.arrInvoice, function (objInvoice) {
                switch(true) {
                    case objInvoice.objStatus.strStatus == $scope.objEnumStatus.STATUS_SENT:
                    case objInvoice.objStatus.strStatus == $scope.objEnumStatus.STATUS_PARTIAL:
                    case objInvoice.objStatus.strStatus == $scope.objEnumStatus.STATUS_PAID:
                        $scope.intTotalTransactions +=  Number(objInvoice.decTotalAmount);
                        $scope.intTotalPayments += Number(objInvoice.decTotalPayment);
                        break;
                }
            });
             $scope.intTotalRemaining = $scope.intTotalTransactions -  $scope.intTotalPayments;
        }

        loadCustomer();
}];

app.controller(controllers);