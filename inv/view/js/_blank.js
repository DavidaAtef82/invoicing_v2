var app = angular.module('INV',['ui.bootstrap']);

app.controller('ModalInstanceCtrl', ['$scope','$modalInstance','customer',function ($scope, $modalInstance, customer){
    $scope.customer = customer;
}]);

app.controller('CustomerController', ['$scope','$modal',function($scope, $modal) {
    
    $scope.customers = [
        {name: 'Ricky',details: 'Some Details for Ricky'},
        {name: 'Dicky',details: 'Some Dicky Details'},
        {name: 'Nicky',details: 'Some Nicky Details'}
    ];

    // MODAL WINDOW
    $scope.open = function (_customer) {
        var modalInstance = $modal.open({
            controller: "ModalInstanceCtrl",
            templateUrl: 'EditCustomer',
            resolve: {
                customer: function(){
                    return _customer;
                }
            }
        });
    };
}]);